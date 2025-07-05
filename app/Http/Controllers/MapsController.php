<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Desa;
use App\Models\DetailKasusPenyakit;
use App\Models\DetailMapsPenyakit;
use App\Models\KasusPenyakit;
use App\Models\Kecamatan;
use App\Models\MapsPenyakit;
use App\Models\Penduduk;
use App\Models\Penyakit;
use App\Models\Tahun;
use App\Services\MetabasePendudukService;
use App\Services\MetabaseKasusService;
use App\Services\MetabaseDetailKasusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MapsController extends Controller
{
    private $metabasePendudukService;
    private $metabaseKasusService;
    private $metabaseDetailKasusService;

    public function __construct(MetabasePendudukService $metabasePendudukService, MetabaseKasusService $metabaseKasusService, MetabaseDetailKasusService $metabaseDetailKasusService)
    {
        $this->metabasePendudukService = $metabasePendudukService;
        $this->metabaseKasusService = $metabaseKasusService;
        $this->metabaseDetailKasusService = $metabaseDetailKasusService;
    }

    public function showAllPenduduk()
    {
        $tahun = Tahun::with(['tahunPenduduk' => function ($query) {
            $query->select('tahun_id')
                ->groupBy('tahun_id');
        }])->get();

        $totalKecamatan = Kecamatan::count();
        $about = About::pluck('value', 'part_name')->toArray();

        $tahunData = $tahun->map(function ($item) use ($totalKecamatan) {
            $pendudukCount = Penduduk::where('tahun_id', $item->id)->count();

            return [
                'id' => $item->id,
                'tahun' => $item->tahun,
                'link_metabase' => $item->link_metabase,
                'data_status' => $pendudukCount === 0 ? 'no_data' : ($pendudukCount < $totalKecamatan ? 'incomplete' : 'complete'),
                'status_message' => $pendudukCount === 0 ? 'Belum ada data' : ($pendudukCount < $totalKecamatan ? 'Data belum lengkap' : '')
            ];
        });

        return view("pages.maps-penduduk", compact("tahunData", "totalKecamatan", "about"));
    }

    public function showAllPenyakit()
    {
        $tahun = Tahun::all();
        $penyakit = Penyakit::all();
        $about = About::pluck('value', 'part_name')->toArray();

        return view("pages.maps-penyakit", compact("tahun", "penyakit", "about"));
    }

    public function getMapLink(Request $request)
    {
        $tahunId = $request->input('tahun_id');
        $penyakitId = $request->input('penyakit_id');

        $totalKecamatan = Kecamatan::count();

        $kasusPenyakitCount = KasusPenyakit::where('tahun_id', $tahunId)
            ->where('penyakit_id', $penyakitId)
            ->count();

        $mapLink = MapsPenyakit::where('tahun_id', $tahunId)
            ->where('penyakit_id', $penyakitId)
            ->first();

        $response = [
            'link_metabase' => $mapLink->link_metabase ?? null,
            'status' => 'no_map',
            'data_availability' => []
        ];

        if ($kasusPenyakitCount === 0) {
            $response['status'] = 'no_data';
        } elseif ($kasusPenyakitCount < $totalKecamatan) {
            $response['status'] = 'incomplete_data';
        } elseif ($mapLink && $mapLink->link_metabase) {
            $response['status'] = 'has_map';
        }

        $allDataAvailability = KasusPenyakit::selectRaw('tahun_id, penyakit_id, COUNT(*) as count')
            ->groupBy('tahun_id', 'penyakit_id')
            ->get();

        foreach ($allDataAvailability as $data) {
            $key = $data->tahun_id . '-' . $data->penyakit_id;
            $response['data_availability'][$key] = [
                'has_data' => $data->count > 0,
                'is_complete' => $data->count >= $totalKecamatan
            ];
        }

        return response()->json($response);
    }

    public function regenerateMapForYear($tahunId)
    {
        try {
            $tahun = Tahun::findOrFail($tahunId);

            $question = $this->metabasePendudukService->createQuestion($tahunId);

            if (isset($question['id'])) {
                $embedUrl = $this->metabasePendudukService->getEmbedUrl($question['id']);

                if ($embedUrl) {
                    $tahun->update([
                        'link_metabase' => $embedUrl
                    ]);
                    
                    // return response()->json([
                    //     'success' => true,
                    //     'embed_url' => $embedUrl,
                    //     'message' => "Map for year {$tahun->tahun} regenerated successfully"
                    // ]);
                    return redirect()->back()->with('success', "Peta sebaran penduduk untuk tahun {$tahun->tahun} berhasil dibuat");
                }
            }

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Failed to regenerate map'
            // ], 500);
            return redirect()->back()->with('error', 'Gagal membuat peta');
        } catch (\Exception $e) {
            Log::error("Error regenerating map: " . $e->getMessage());

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Failed to regenerate map: ' . $e->getMessage()
            // ], 500);
            return redirect()->back()->with('error', 'Gagal membuat peta: ' . $e->getMessage());
        }
    }

    public function regenerateMapForDisease($tahunId, $penyakitId)
    {
        try {
            $tahun = Tahun::find($tahunId);
            if (!$tahun) {
                // return response()->json([
                //     'success' => false,
                //     'message' => "Data tahun {$tahun->tahun} tidak ditemukan"
                // ], 404);
                return redirect()->back()->with('error', "Data tahun {$tahun->tahun} tidak ditemukan");
            }

            $penyakit = Penyakit::find($penyakitId);
            if (!$penyakit) {
                // return response()->json([
                //     'success' => false,
                //     'message' => "Data penyakit {$penyakit->nama_penyakit} tidak ditemukan"
                // ], 404);
                return redirect()->back()->with('error', "Data penyakit {$penyakit->nama_penyakit} tidak ditemukan");
            }

            $kasusExists = KasusPenyakit::where('tahun_id', $tahunId)
                ->where('penyakit_id', $penyakitId)
                ->exists();

            if (!$kasusExists) {
                // return response()->json([
                //     'success' => false,
                //     'message' => "Data kasus tidak ditemukan untuk penyakit {$penyakit->nama_penyakit} tahun {$tahun->tahun}"
                // ], 404);
                return redirect()->back()->with('error', "Data kasus tidak ditemukan untuk penyakit {$penyakit->nama_penyakit} pada tahun {$tahun->tahun}");
            }

            $expectedKecamatanCount = Kecamatan::count();

            $actualKasusCount = KasusPenyakit::where('tahun_id', $tahunId)
                ->where('penyakit_id', $penyakitId)
                ->count();

            if ($actualKasusCount !== $expectedKecamatanCount) {
                $message = sprintf(
                    "Data kasus tidak lengkap untuk tahun %s dan penyakit %s. " .
                        "Dibutuhkan data untuk %d kecamatan, tetapi hanya ditemukan %d data.",
                    $tahun->tahun,
                    $penyakit->nama_penyakit,
                    $expectedKecamatanCount,
                    $actualKasusCount
                );

                Log::warning('Incomplete disease case data', [
                    'tahun_id' => $tahunId,
                    'penyakit_id' => $penyakitId,
                    'expected' => $expectedKecamatanCount,
                    'actual' => $actualKasusCount
                ]);

                // return response()->json([
                //     'success' => false,
                //     'message' => $message
                // ], 422);
                return redirect()->back()->with('error', $message);
            }

            DB::beginTransaction();

            try {
                $existingMap = MapsPenyakit::where('tahun_id', $tahunId)
                    ->where('penyakit_id', $penyakitId)
                    ->first();

                $question = $this->metabaseKasusService->createDiseaseQuestion($tahunId, $penyakitId);

                if (!isset($question['id'])) {
                    throw new \Exception('Gagal membuat question di Metabase');
                }

                $embedUrl = $this->metabasePendudukService->getEmbedUrl($question['id']);

                if (empty($embedUrl)) {
                    throw new \Exception('Gagal generate embed URL');
                }

                if ($existingMap) {
                    $existingMap->update([
                        'link_metabase' => $embedUrl,
                        'updated_at' => now()
                    ]);

                    $message = sprintf(
                        "Berhasil membuat peta untuk penyakit %s tahun %s",
                        $penyakit->nama_penyakit,
                        $tahun->tahun
                    );
                } else {
                    MapsPenyakit::create([
                        'tahun_id' => $tahunId,
                        'penyakit_id' => $penyakitId,
                        'link_metabase' => $embedUrl,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $message = sprintf(
                        "Berhasil membuat peta untuk penyakit %s tahun %s",
                        $penyakit->nama_penyakit,
                        $tahun->tahun
                    );
                }

                DB::commit();

                // return response()->json([
                //     'success' => true,
                //     'message' => $message,
                //     'embed_url' => $embedUrl,
                //     'action' => $existingMap ? 'updated' : 'created'
                // ]);
                return redirect()->back()->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error("Error dalam generate peta penyakit", [
                'tahun_id' => $tahunId,
                'penyakit_id' => $penyakitId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Gagal generate peta: ' . $e->getMessage()
            // ], 500);
            return redirect()->back()->with('error', 'Gagal membuat peta: ' . $e->getMessage());
        }
    }

    public function showAllDetailPenyakit()
    {
        $tahun = Tahun::all();
        $kecamatan = Kecamatan::all();
        $penyakit = Penyakit::all();
        $about = About::pluck('value', 'part_name')->toArray();
        return view("pages.detail-maps-penyakit", compact("tahun", "kecamatan", "penyakit", "about"));
    }

    public function getDetailMapLink(Request $request)
    {
        try {
            $tahunId = $request->input('tahun_id');
            $penyakitId = $request->input('penyakit_id');
            $kecamatanId = $request->input('kecamatan_id');

            // Log untuk debugging
            Log::info('getDetailMapLink called with params:', [
                'tahun_id' => $tahunId,
                'penyakit_id' => $penyakitId,
                'kecamatan_id' => $kecamatanId
            ]);

            // Get available maps from detail_maps_penyakits
            $availableMaps = DB::table('detail_maps_penyakits')
                ->whereNotNull('link_metabase')
                ->where('link_metabase', '!=', '')
                ->select('tahun_id', 'penyakit_id', 'kecamatan_id', 'link_metabase')
                ->get();

            // Get available case data from detail_kasus_penyakits - UBAH: untuk semua kombinasi tahun-penyakit
            $allAvailableCaseData = DB::table('detail_kasus_penyakits as dkp')
                ->join('desas as d', 'dkp.desa_id', '=', 'd.id')
                ->join('kecamatans as k', 'd.kecamatan_id', '=', 'k.id')
                ->whereNotNull('dkp.terjangkit')
                ->whereNotNull('dkp.meninggal')
                ->select('dkp.tahun_id', 'dkp.penyakit_id', 'k.id as kecamatan_id')
                ->groupBy('dkp.tahun_id', 'dkp.penyakit_id', 'k.id')
                ->get();
            
            // Get case data for specific combination if parameters provided
            $availableCaseData = $allAvailableCaseData;
            if ($tahunId && $penyakitId) {
                $availableCaseData = $allAvailableCaseData->where('tahun_id', $tahunId)
                    ->where('penyakit_id', $penyakitId);
            }

            Log::info('Available maps count:', ['count' => $availableMaps->count()]);
            Log::info('Available case data count:', ['count' => $availableCaseData->count()]);

            $response = [
                'link_metabase' => null,
                'status' => 'no_map',
                'data_availability' => [],
                'sorted_districts' => [],
                'case_data_availability' => [] // Tambahan untuk case data
            ];

            // Build data availability for maps
            $groupedMapsData = [];
            foreach ($availableMaps as $map) {
                $key = $map->tahun_id . '-' . $map->penyakit_id;
                if (!isset($groupedMapsData[$key])) {
                    $groupedMapsData[$key] = [];
                }
                $groupedMapsData[$key][] = (int)$map->kecamatan_id;
            }

            // Build case data availability - UNTUK SEMUA KOMBINASI
            $groupedCaseData = [];
            foreach ($allAvailableCaseData as $caseData) {
                $key = $caseData->tahun_id . '-' . $caseData->penyakit_id;
                if (!isset($groupedCaseData[$key])) {
                    $groupedCaseData[$key] = [];
                }
                $groupedCaseData[$key][] = (int)$caseData->kecamatan_id;
            }

            // PRIORITAS: Data availability berdasarkan case data (untuk tab tahun & penyakit)
            // Ini memastikan tab tahun dan penyakit aktif jika ada data case, meskipun belum ada map
            $allAvailableDistricts = [];
            foreach ($groupedCaseData as $key => $districts) {
                $allAvailableDistricts[$key] = array_unique($districts);
            }

            // Tambahkan data maps jika ada (untuk mengetahui mana yang sudah punya map)
            foreach ($groupedMapsData as $key => $districts) {
                if (!isset($allAvailableDistricts[$key])) {
                    $allAvailableDistricts[$key] = [];
                }
                // Gabungkan, tapi case data tetap prioritas
                $allAvailableDistricts[$key] = array_unique(array_merge($allAvailableDistricts[$key], $districts));
            }

            // Build response data availability
            foreach ($allAvailableDistricts as $key => $districts) {
                sort($districts);
                $response['data_availability'][$key] = [
                    'has_data' => true,
                    'districts' => $districts
                ];
            }

            // Build case data availability (for determining if "Buat Peta" button should show)
            foreach ($groupedCaseData as $key => $districts) {
                sort($districts);
                $response['case_data_availability'][$key] = [
                    'has_data' => true,
                    'districts' => array_unique($districts)
                ];
            }
            
            // Build maps availability (untuk tracking mana yang sudah ada map)
            foreach ($groupedMapsData as $key => $districts) {
                sort($districts);
                $response['maps_availability'][$key] = [
                    'has_data' => true,
                    'districts' => array_unique($districts)
                ];
            }

            // Get all districts with any kind of data
            $allDistrictsWithData = collect($allAvailableDistricts)
                ->flatten()
                ->unique()
                ->values()
                ->toArray();

            // Get district names for sorting
            $districtNames = DB::table('kecamatans')
                ->whereIn('id', $allDistrictsWithData)
                ->pluck('nama_kecamatan', 'id')
                ->toArray();

            // Sort districts by name
            $sortedDistricts = collect($allDistrictsWithData)
                ->map(function ($id) use ($districtNames) {
                    return [
                        'id' => (int)$id,
                        'name' => $districtNames[$id] ?? 'Unknown'
                    ];
                })
                ->sortBy('name')
                ->values()
                ->toArray();

            $response['sorted_districts'] = $sortedDistricts;

            Log::info('Built data availability:', ['data_availability' => $response['data_availability']]);
            Log::info('Built case data availability:', ['case_data_availability' => $response['case_data_availability']]);

            // If specific district is selected
            if ($kecamatanId && $kecamatanId !== '') {
                // First, check if map exists
                $specificMap = $availableMaps->where('tahun_id', $tahunId)
                    ->where('penyakit_id', $penyakitId)
                    ->where('kecamatan_id', $kecamatanId)
                    ->first();

                if ($specificMap) {
                    $response['link_metabase'] = $specificMap->link_metabase;
                    $response['status'] = 'has_map';
                    Log::info('Map found:', ['link' => $specificMap->link_metabase]);
                } else {
                    // Check if case data exists for this combination
                    $caseDataExists = $availableCaseData->where('tahun_id', $tahunId)
                        ->where('penyakit_id', $penyakitId)
                        ->where('kecamatan_id', $kecamatanId)
                        ->isNotEmpty();

                    if ($caseDataExists) {
                        $response['status'] = 'no_map_but_has_case_data';
                        Log::info('No map found but case data exists - show create button');
                    } else {
                        $response['status'] = 'no_data';
                        Log::info('No map and no case data found');
                    }
                }
            } else {
                // No district selected
                $key = $tahunId . '-' . $penyakitId;
                if (
                    isset($response['data_availability'][$key]) &&
                    !empty($response['data_availability'][$key]['districts'])
                ) {
                    $response['status'] = 'select_district';
                    Log::info('Data available, waiting for district selection');
                } else {
                    $response['status'] = 'no_data_for_combination';
                    Log::info('No data available for this year-disease combination');
                }
            }

            Log::info('Final response:', $response);

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error in getDetailMapLink: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'link_metabase' => null,
                'status' => 'error',
                'data_availability' => [],
                'sorted_districts' => [],
                'case_data_availability' => [],
                'maps_availability' => [],
                'error' => $e->getMessage()
            ], 200);
        }
    }

    public function regenerateDetailMapForDisease($tahunId, $penyakitId, $kecamatanId)
    {
        try {
            $tahun = Tahun::find($tahunId);
            if (!$tahun) {
                return redirect()->back()->with('error', "Data tahun tidak ditemukan");
            }

            $penyakit = Penyakit::find($penyakitId);
            if (!$penyakit) {
                return redirect()->back()->with('error', "Data penyakit tidak ditemukan");
            }

            $kecamatan = Kecamatan::find($kecamatanId);
            if (!$kecamatan) {
                return redirect()->back()->with('error', "Data kecamatan tidak ditemukan");
            }

            // Check if detail cases exist for the specified parameters
            // Join with desas table to access kecamatan_id
            $kasusExists = DetailKasusPenyakit::join('desas', 'detail_kasus_penyakits.desa_id', '=', 'desas.id')
                ->where('detail_kasus_penyakits.tahun_id', $tahunId)
                ->where('detail_kasus_penyakits.penyakit_id', $penyakitId)
                ->where('desas.kecamatan_id', $kecamatanId)
                ->exists();

            if (!$kasusExists) {
                return redirect()->back()->with(
                    'error',
                    "Data kasus tidak ditemukan untuk penyakit {$penyakit->nama_penyakit} di kecamatan {$kecamatan->nama_kecamatan} pada tahun {$tahun->tahun}"
                );
            }

            // Get expected village count for the kecamatan
            $expectedDesaCount = Desa::where('kecamatan_id', $kecamatanId)->count();

            // Get actual cases count - join with desas table
            $actualKasusCount = DetailKasusPenyakit::join('desas', 'detail_kasus_penyakits.desa_id', '=', 'desas.id')
                ->where('detail_kasus_penyakits.tahun_id', $tahunId)
                ->where('detail_kasus_penyakits.penyakit_id', $penyakitId)
                ->where('desas.kecamatan_id', $kecamatanId)
                ->count();

            if ($actualKasusCount !== $expectedDesaCount) {
                $message = sprintf(
                    "Data kasus tidak lengkap untuk tahun %s, penyakit %s, dan kecamatan %s. " .
                        "Dibutuhkan data untuk %d desa, tetapi hanya ditemukan %d data.",
                    $tahun->tahun,
                    $penyakit->nama_penyakit,
                    $kecamatan->nama_kecamatan,
                    $expectedDesaCount,
                    $actualKasusCount
                );

                Log::warning('Incomplete detail disease case data', [
                    'tahun_id' => $tahunId,
                    'penyakit_id' => $penyakitId,
                    'kecamatan_id' => $kecamatanId,
                    'expected' => $expectedDesaCount,
                    'actual' => $actualKasusCount
                ]);

                return redirect()->back()->with('error', $message);
            }

            DB::beginTransaction();

            try {
                // Check if existing detail map exists
                $existingMap = DetailMapsPenyakit::where('tahun_id', $tahunId)
                    ->where('penyakit_id', $penyakitId)
                    ->where('kecamatan_id', $kecamatanId)
                    ->first();

                // If existing map exists, delete the old Metabase question first
                if ($existingMap && !empty($existingMap->link_metabase)) {
                    // Extract card ID from the existing URL
                    if (preg_match('/\/public\/question\/([a-f0-9-]+)/', $existingMap->link_metabase, $matches)) {
                        // Get card ID from public UUID - this would require additional API call
                        // For now, we'll create a new question and let the old one remain
                    }
                }

                // Create new question in Metabase with dynamic parameters
                $question = $this->metabaseDetailKasusService->createDetailDiseaseQuestion($tahunId, $penyakitId, $kecamatanId);

                if (!isset($question['id'])) {
                    throw new \Exception('Gagal membuat question di Metabase');
                }

                // Get embed URL
                $embedUrl = $this->metabaseDetailKasusService->getEmbedUrl($question['id']);

                if (empty($embedUrl)) {
                    throw new \Exception('Gagal generate embed URL');
                }

                if ($existingMap) {
                    // Update existing map
                    $existingMap->update([
                        'link_metabase' => $embedUrl,
                        'updated_at' => now()
                    ]);

                    $message = sprintf(
                        "Berhasil memperbarui peta detail untuk penyakit %s di kecamatan %s tahun %s",
                        $penyakit->nama_penyakit,
                        $kecamatan->nama_kecamatan,
                        $tahun->tahun
                    );
                } else {
                    // Create new map
                    DetailMapsPenyakit::create([
                        'tahun_id' => $tahunId,
                        'penyakit_id' => $penyakitId,
                        'kecamatan_id' => $kecamatanId,
                        'link_metabase' => $embedUrl,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $message = sprintf(
                        "Berhasil membuat peta detail untuk penyakit %s di kecamatan %s tahun %s",
                        $penyakit->nama_penyakit,
                        $kecamatan->nama_kecamatan,
                        $tahun->tahun
                    );
                }

                DB::commit();

                return redirect()->back()->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error("Error dalam generate peta detail penyakit", [
                'tahun_id' => $tahunId,
                'penyakit_id' => $penyakitId,
                'kecamatan_id' => $kecamatanId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Gagal membuat peta detail: ' . $e->getMessage());
        }
    }
}
