<?php

namespace App\Http\Controllers;

use App\Models\About;
use Illuminate\Http\Request;
use App\Models\KasusPenyakit;
use App\Models\Tahun;
use App\Models\Kecamatan;
use App\Models\Penyakit;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class UnduhDataController extends Controller
{
    public function showData(Request $request)
    {
        $query = KasusPenyakit::query();

        // Use filled() for proper empty value checking
        if ($request->filled('tahun_id')) {
            $query->where('tahun_id', $request->tahun_id);
        }

        if ($request->filled('kecamatan_id')) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }

        if ($request->filled('penyakit_id')) {
            $query->where('penyakit_id', $request->penyakit_id);
        }

        $kasus = $query->with(['tahunKasus', 'kecamatanKasus', 'penyakitKasus'])
            ->get();

        $tahun = Tahun::all();
        $kecamatan = Kecamatan::orderBy('id')->get();
        $penyakit = Penyakit::orderBy('id')->get();
        $about = About::pluck('value', 'part_name')->toArray();

        return view("pages.unduh-data", compact("kasus", "tahun", "kecamatan", "penyakit", "about"));
    }

    public function exportExcel(Request $request)
    {
        try {
            Log::info('Excel export request received with params: ' . json_encode($request->all()));

            $query = KasusPenyakit::query();

            if ($request->filled('tahun_id')) {
                $query->where('tahun_id', $request->tahun_id);
            }

            if ($request->filled('kecamatan_id')) {
                $query->where('kecamatan_id', $request->kecamatan_id);
            }

            if ($request->filled('penyakit_id')) {
                $query->where('penyakit_id', $request->penyakit_id);
            }

            $data = $query->with(['tahunKasus', 'kecamatanKasus', 'penyakitKasus'])
                ->get();

            if ($data->isEmpty()) {
                Log::warning('Excel export - No data found for the selected filters');
                return back()->with('error', 'Tidak ada data untuk diunduh dengan filter yang dipilih.');
            }

            $formattedData = $data->map(function ($item, $key) {
                // Konversi nilai 0 ke string '0' untuk menghindari sel kosong di Excel
                $terjangkit = $item->terjangkit;
                if ($terjangkit === 0 || $terjangkit === '0') {
                    $terjangkit = '0';
                }

                $meninggal = $item->meninggal;
                if ($meninggal === 0 || $meninggal === '0') {
                    $meninggal = '0';
                } else if ($meninggal === null) {
                    $meninggal = '0';
                }

                return [
                    'No' => $key + 1,
                    'Tahun' => $item->tahunKasus->tahun,
                    'Kecamatan' => $item->kecamatanKasus->nama_kecamatan,
                    'Nama Penyakit' => $item->penyakitKasus->nama_penyakit,
                    'Jumlah Terjangkit' => $terjangkit,
                    'Jumlah Meninggal' => $meninggal
                ];
            });

            Log::info('Excel export data count: ' . $formattedData->count());

            $filename = 'data_kasus_penyakit_' . date('Ymd_His') . '.xlsx';
            return Excel::download(new \App\Exports\KasusPenyakitExport($formattedData), $filename);
        } catch (\Exception $e) {
            Log::error('Excel export error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh file Excel: ' . $e->getMessage());
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            Log::info('PDF export request received with params: ' . json_encode($request->all()));

            $query = KasusPenyakit::query();

            if ($request->filled('tahun_id')) {
                $query->where('tahun_id', $request->tahun_id);
            }

            if ($request->filled('kecamatan_id')) {
                $query->where('kecamatan_id', $request->kecamatan_id);
            }

            if ($request->filled('penyakit_id')) {
                $query->where('penyakit_id', $request->penyakit_id);
            }

            $data = $query->with(['tahunKasus', 'kecamatanKasus', 'penyakitKasus'])
                ->get();

            if ($data->isEmpty()) {
                Log::warning('PDF export - No data found for the selected filters');
                return back()->with('error', 'Tidak ada data untuk diunduh dengan filter yang dipilih.');
            }

            $formattedData = $data->map(function ($item, $key) {
                return [
                    'no' => $key + 1,
                    'tahun' => $item->tahunKasus->tahun,
                    'kecamatan' => $item->kecamatanKasus->nama_kecamatan,
                    'penyakit' => $item->penyakitKasus->nama_penyakit,
                    'terjangkit' => $item->terjangkit,
                    'meninggal' => $item->meninggal ?? '-'
                ];
            });

            Log::info('PDF export data count: ' . count($formattedData));

            $filename = 'data_kasus_penyakit_' . date('Ymd_His') . '.pdf';
            $pdf = PDF::loadView('pdf.kasus-penyakit', ['data' => $formattedData]);
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF export error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh file PDF: ' . $e->getMessage());
        }
    }
}
