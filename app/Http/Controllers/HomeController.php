<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\KasusPenyakit;
use App\Models\Kecamatan;
use App\Models\MapsPenyakit;
use App\Models\Penyakit;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function showAllPenyakit()
    {
        try {
            // Ambil semua data penyakit
            $penyakit = Penyakit::all();
            $trend_penyakit = Penyakit::whereNotNull('link_metabase')->get();
            $about = About::pluck('value', 'part_name')->toArray();

            // Find latest year with complete data
            $totalKecamatan = Kecamatan::count();
            $latestYearData = KasusPenyakit::select('tahun_id')
                ->groupBy('tahun_id')
                ->havingRaw('COUNT(DISTINCT kecamatan_id) >= ?', [$totalKecamatan])
                ->orderBy('tahun_id', 'desc')
                ->first();

            // Fallback if no complete data found
            if (!$latestYearData) {
                $latestYear = Tahun::orderBy('tahun', 'desc')->first();
                $currentYearId = $latestYear ? $latestYear->id : null;
            } else {
                $currentYearId = $latestYearData->tahun_id;
            }

            $currentYear = Tahun::find($currentYearId);
            $totalKasusAktif = KasusPenyakit::where('tahun_id', $currentYearId)->sum('terjangkit');

            // Pastikan $penyakit tidak kosong
            if ($penyakit->isEmpty()) {
                return view("landing", compact("currentYearId", "currentYear", "totalKecamatan", "trend_penyakit", "about", "totalKasusAktif"))
                    ->with('error', 'Tidak ada data penyakit tersedia.');
            }

            return view("landing", compact("penyakit", "currentYearId", "currentYear", "totalKecamatan", "trend_penyakit", "about", "totalKasusAktif"))
                ->with('success', 'Data penyakit berhasil dimuat.');
        } catch (\Exception $e) {
            Log::error('Error in showAllPenyakit: ' . $e->getMessage());
            return view("landing")->with('error', 'Terjadi kesalahan saat memuat data. Silakan coba lagi.');
        }
    }

    public function getMapLink(Request $request)
    {
        try {
            $tahunId = $request->input('tahun_id');
            $penyakitId = $request->input('penyakit_id');

            if (!$tahunId || !$penyakitId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing required parameters'
                ], 400);
            }

            $totalKecamatan = Kecamatan::count();

            // Basic data for response
            $response = [
                'status' => 'no_data',
                'link_metabase' => null,
                'disease_availability' => []
            ];

            // Get count of records for current disease
            $kasusPenyakitCount = KasusPenyakit::where('tahun_id', $tahunId)
                ->where('penyakit_id', $penyakitId)
                ->count();

            // Check if map exists
            $mapLink = MapsPenyakit::where('tahun_id', $tahunId)
                ->where('penyakit_id', $penyakitId)
                ->first();

            if ($mapLink && $mapLink->link_metabase) {
                $response['link_metabase'] = $mapLink->link_metabase;
                $response['status'] = 'has_map';
            } elseif ($kasusPenyakitCount >= $totalKecamatan) {
                $response['status'] = 'no_map';
            } elseif ($kasusPenyakitCount > 0) {
                $response['status'] = 'incomplete_data';
            }

            // Get data for all diseases
            $diseaseData = KasusPenyakit::selectRaw('penyakit_id, COUNT(*) as count')
                ->where('tahun_id', $tahunId)
                ->groupBy('penyakit_id')
                ->get();

            foreach ($diseaseData as $data) {
                $response['disease_availability'][$data->penyakit_id] = [
                    'has_data' => true,
                    'is_complete' => ($data->count >= $totalKecamatan)
                ];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error in getMapLink: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
