<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Desa;
use App\Models\DetailKasusPenyakit;
use App\Models\Kecamatan;
use App\Models\Penyakit;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DetailKasusPenyakitController extends Controller
{
    public function showDetailKasus(Request $request)
    {
        $query = DetailKasusPenyakit::query();

        if ($request->has('tahun_id') && !empty($request->tahun_id)) {
            $query->where('tahun_id', $request->tahun_id);
        }

        if ($request->has('desa_id') && !empty($request->desa_id)) {
            $query->where('desa_id', $request->desa_id);
        }

        if ($request->has('penyakit_id') && !empty($request->penyakit_id)) {
            $query->where('penyakit_id', $request->penyakit_id);
        }

        $sort = $request->query('sort');
        $direction = $request->query('direction', 'asc');

        if ($sort) {
            switch ($sort) {
                case 'tahun':
                    $query->join('tahuns', 'detail_kasus_penyakits.tahun_id', '=', 'tahuns.id')
                        ->orderBy('tahuns.tahun', $direction);
                    break;
                case 'nama_desa':
                    $query->join('desas', 'detail_kasus_penyakits.desa_id', '=', 'desas.id')
                        ->orderBy('desas.nama_desa', $direction);
                    break;
                case 'nama_penyakit':
                    $query->join('penyakits', 'detail_kasus_penyakits.penyakit_id', '=', 'penyakits.id')
                        ->orderBy('penyakits.nama_penyakit', $direction);
                    break;
                case 'terjangkit':
                    $query->orderBy('terjangkit', $direction);
                    break;
                case 'meninggal':
                    $query->orderBy('meninggal', $direction);
                    break;
                default:
                    $query->orderBy($sort, $direction);
            }
        } else {
            $query->join('tahuns', 'detail_kasus_penyakits.tahun_id', '=', 'tahuns.id')
                ->orderBy('tahuns.tahun', 'asc');
        }

        $detailKasus = $query->select('detail_kasus_penyakits.*')->paginate(10);

        $tahun = Tahun::all();
        $kecamatan = Kecamatan::orderBy('id')->get();

        // Ambil desa berdasarkan kecamatan yang dipilih
        $desa = collect();
        if ($request->has('kecamatan_id') && !empty($request->kecamatan_id)) {
            $desa = Desa::where('kecamatan_id', $request->kecamatan_id)
                ->with('kecamatanDesa')
                ->orderBy('nama_desa')
                ->get();
        }

        $penyakit = Penyakit::orderBy('id')->get();
        $about = About::pluck('value', 'part_name')->toArray();

        return view("pages.detail-kasus-penyakit", compact("detailKasus", "tahun", "kecamatan", "desa", "penyakit", "sort", "direction", "about"));
    }
    public function getDesaByKecamatan(Request $request)
    {
        $kecamatanId = $request->query('kecamatan_id');

        if (!$kecamatanId) {
            return response()->json([]);
        }

        $desa = Desa::where('kecamatan_id', $kecamatanId)
            ->orderBy('nama_desa')
            ->get(['id', 'nama_desa']);

        return response()->json($desa);
    }
    public function create()
    {
        if (auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            $tahun = Tahun::all();
            // $desa = Desa::all();
            $desa = Desa::with('kecamatanDesa')->get();
            $penyakit = Penyakit::all();

            return view("add.add-detail-kasus-penyakit", compact("tahun", "desa", "penyakit"));
        } else {
            return redirect()->route('detail-kasus')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'tahun_id' => [
                'required',
                'exists:tahuns,id',
            ],
            'desa_id' => [
                'required',
                'exists:desas,id',
            ],
            'penyakit_id' => [
                'required',
                'exists:penyakits,id',
                // Validasi unique untuk kombinasi ketiga field
                Rule::unique('detail_kasus_penyakits')
                    ->where(function ($query) use ($request) {
                        return $query->where('tahun_id', $request->tahun_id)
                            ->where('desa_id', $request->desa_id)
                            ->where('penyakit_id', $request->penyakit_id);
                    })
            ],
            'terjangkit' => 'required|integer|min:0',
            'meninggal' => 'nullable|integer|min:0',
        ], [
            // Pesan error kustom
            'penyakit_id.unique' => 'Data detail kasus penyakit untuk kombinasi tahun, desa, dan penyakit ini sudah ada!'
        ]);

        try {
            DetailKasusPenyakit::create($request->all());
            return redirect()->route('detail-kasus')->with('success', 'Data detail kasus penyakit berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('detail-kasus')->with('error', 'Gagal menambahkan data detail kasus penyakit: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        if (auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            $tahun = Tahun::all();
            // $desa = Desa::all();
            $desa = Desa::with('kecamatanDesa')->get();
            $penyakit = Penyakit::all();
            $detailKasus = DetailKasusPenyakit::find($id);

            return view("edit.edit-detail-kasus-penyakit", compact("tahun", "desa", "penyakit", "detailKasus"));
        } else {
            return redirect()->route('detail-kasus')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function update(Request $request, $id)
    {
        $detailKasus = DetailKasusPenyakit::find($id);

        try {
            $request->validate([
                'tahun_id' => [
                    'required',
                    'exists:tahuns,id',
                ],
                'desa_id' => [
                    'required',
                    'exists:desas,id',
                ],
                'penyakit_id' => [
                    'required',
                    'exists:penyakits,id',
                    Rule::unique('detail_kasus_penyakits')
                        ->where(function ($query) use ($request) {
                            return $query->where('tahun_id', $request->tahun_id)
                                ->where('desa_id', $request->desa_id)
                                ->where('penyakit_id', $request->penyakit_id);
                        })->ignore($id)
                ],
                'terjangkit' => 'required|integer|min:0',
                'meninggal' => 'nullable|integer|min:0',
            ], [
                'penyakit_id.unique' => 'Data detail kasus penyakit untuk kombinasi tahun, kecamatan, dan penyakit ini sudah ada!'
            ]);

            $detailKasus->update($request->all());
            return redirect()->route('detail-kasus')->with('success', 'Data detail kasus penyakit berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('detail-kasus')->with('error', 'Gagal memperbarui data detail kasus penyakit: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $detailKasus = DetailKasusPenyakit::find($id);

        try {
            $detailKasus->delete();

            return redirect()->route('detail-kasus')->with('success', 'Data detail kasus penyakit berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('detail-kasus')->with('error', 'Gagal menghapus data detail kasus penyakit: ' . $e->getMessage());
        }
    }
}
