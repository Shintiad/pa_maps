<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\KasusPenyakit;
use App\Models\Kecamatan;
use App\Models\Penyakit;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KasusPenyakitController extends Controller
{
    public function showKasus(Request $request)
    {
        $query = KasusPenyakit::query();

        if ($request->has('tahun_id') && !empty($request->tahun_id)) {
            $query->where('tahun_id', $request->tahun_id);
        }

        if ($request->has('kecamatan_id') && !empty($request->kecamatan_id)) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }

        if ($request->has('penyakit_id') && !empty($request->penyakit_id)) {
            $query->where('penyakit_id', $request->penyakit_id);
        }

        $sort = $request->query('sort');
        $direction = $request->query('direction', 'asc');

        if ($sort) {
            switch ($sort) {
                case 'tahun':
                    $query->join('tahuns', 'kasus_penyakits.tahun_id', '=', 'tahuns.id')
                        ->orderBy('tahuns.tahun', $direction);
                    break;
                case 'nama_kecamatan':
                    $query->join('kecamatans', 'kasus_penyakits.kecamatan_id', '=', 'kecamatans.id')
                        ->orderBy('kecamatans.nama_kecamatan', $direction);
                    break;
                case 'nama_penyakit':
                    $query->join('penyakits', 'kasus_penyakits.penyakit_id', '=', 'penyakits.id')
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
            $query->join('tahuns', 'kasus_penyakits.tahun_id', '=', 'tahuns.id')
                ->orderBy('tahuns.tahun', 'asc');
        }

        $kasus = $query->select('kasus_penyakits.*')->paginate(10);

        $tahun = Tahun::all();
        $kecamatan = Kecamatan::orderBy('id')->get();
        $penyakit = Penyakit::orderBy('id')->get();
        $about = About::pluck('value', 'part_name')->toArray();

        return view("pages.kasus-penyakit", compact("kasus", "tahun", "kecamatan", "penyakit", "sort", "direction", "about"));
    }
    public function create()
    {
        if (auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            $tahun = Tahun::all();
            $kecamatan = Kecamatan::all();
            $penyakit = Penyakit::all();

            return view("add.add-kasus-penyakit", compact("tahun", "kecamatan", "penyakit"));
        } else {
            return redirect()->route('kasus')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'tahun_id' => [
                'required',
                'exists:tahuns,id',
            ],
            'kecamatan_id' => [
                'required',
                'exists:kecamatans,id',
            ],
            'penyakit_id' => [
                'required',
                'exists:penyakits,id',
                // Validasi unique untuk kombinasi ketiga field
                Rule::unique('kasus_penyakits')
                    ->where(function ($query) use ($request) {
                        return $query->where('tahun_id', $request->tahun_id)
                            ->where('kecamatan_id', $request->kecamatan_id)
                            ->where('penyakit_id', $request->penyakit_id);
                    })
            ],
            'terjangkit' => 'required|integer|min:0',
            'meninggal' => 'nullable|integer|min:0',
        ], [
            // Pesan error kustom
            'penyakit_id.unique' => 'Data kasus penyakit untuk kombinasi tahun, kecamatan, dan penyakit ini sudah ada!'
        ]);

        try {
            KasusPenyakit::create($request->all());
            return redirect()->route('kasus')->with('success', 'Data kasus penyakit berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('kasus')->with('error', 'Gagal menambahkan data kasus penyakit: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        if (auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            $tahun = Tahun::all();
            $kecamatan = Kecamatan::all();
            $penyakit = Penyakit::all();
            $kasus = KasusPenyakit::find($id);

            return view("edit.edit-kasus-penyakit", compact("tahun", "kecamatan", "penyakit", "kasus"));
        } else {
            return redirect()->route('kasus')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function update(Request $request, $id)
    {
        $kasus = KasusPenyakit::find($id);

        try {
            $request->validate([
                'tahun_id' => [
                    'required',
                    'exists:tahuns,id',
                ],
                'kecamatan_id' => [
                    'required',
                    'exists:kecamatans,id',
                ],
                'penyakit_id' => [
                    'required',
                    'exists:penyakits,id',
                    Rule::unique('kasus_penyakits')
                        ->where(function ($query) use ($request) {
                            return $query->where('tahun_id', $request->tahun_id)
                                ->where('kecamatan_id', $request->kecamatan_id)
                                ->where('penyakit_id', $request->penyakit_id);
                        })->ignore($id)
                ],
                'terjangkit' => 'required|integer|min:0',
                'meninggal' => 'nullable|integer|min:0',
            ], [
                'penyakit_id.unique' => 'Data kasus penyakit untuk kombinasi tahun, kecamatan, dan penyakit ini sudah ada!'
            ]);

            $kasus->update($request->all());
            return redirect()->route('kasus')->with('success', 'Data kasus penyakit berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('kasus')->with('error', 'Gagal memperbarui data kasus: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $kasus = KasusPenyakit::find($id);

        try {
            $kasus->delete();

            return redirect()->route('kasus')->with('success', 'Data kasus penyakit berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('kasus')->with('error', 'Gagal menghapus data kasus penyakit: ' . $e->getMessage());
        }
    }
}
