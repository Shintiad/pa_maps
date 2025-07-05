<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Kecamatan;
use App\Models\Penduduk;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PendudukController extends Controller
{
    public function showPenduduk(Request $request)
    {
        $query = Penduduk::query();

        if ($request->has('tahun_id') && !empty($request->tahun_id)) {
            $query->where('tahun_id', $request->tahun_id);
        }

        if ($request->has('kecamatan_id') && !empty($request->kecamatan_id)) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }

        $sort = $request->query('sort');
        $direction = $request->query('direction', 'asc');

        if ($sort) {
            switch ($sort) {
                case 'tahun':
                    $query->join('tahuns', 'penduduks.tahun_id', '=', 'tahuns.id')
                        ->orderBy('tahuns.tahun', $direction);
                    break;
                case 'nama_kecamatan':
                    $query->join('kecamatans', 'penduduks.kecamatan_id', '=', 'kecamatans.id')
                        ->orderBy('kecamatans.nama_kecamatan', $direction);
                    break;
                case 'jumlah_penduduk':
                    $query->orderBy('jumlah_penduduk', $direction);
                    break;
                default:
                    $query->orderBy($sort, $direction);
            }
        } else {
            $query->join('tahuns', 'penduduks.tahun_id', '=', 'tahuns.id')
                ->orderBy('tahuns.tahun', 'asc');
        }

        $penduduk = $query->select('penduduks.*')->paginate(10);

        $tahun = Tahun::all();
        $kecamatan = Kecamatan::orderBy('id')->get();
        $about = About::pluck('value', 'part_name')->toArray();

        return view("pages.penduduk", compact("penduduk", "tahun", "kecamatan", "sort", "direction", "about"));
    }
    public function create()
    {
        if (auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            $tahun = Tahun::all();
            $kecamatan = Kecamatan::all();

            return view("add.add-penduduk", compact("tahun", "kecamatan"));
        } else {
            return redirect()->route('penduduk')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
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
                // Validasi unique untuk kombinasi tahun_id dan kecamatan_id
                Rule::unique('penduduks')->where(function ($query) use ($request) {
                    return $query->where('tahun_id', $request->tahun_id)
                        ->where('kecamatan_id', $request->kecamatan_id);
                })
            ],
            'jumlah_penduduk' => 'required|integer|min:0',
        ], [
            // Pesan error kustom
            'kecamatan_id.unique' => 'Data penduduk untuk kombinasi tahun dan kecamatan ini sudah ada!'
        ]);

        try {
            Penduduk::create($request->all());
            return redirect()->route('penduduk')->with('success', 'Data penduduk berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('penduduk')->with('error', 'Gagal menambahkan data penduduk: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        if (auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            $tahun = Tahun::all();
            $kecamatan = Kecamatan::all();
            $penduduk = Penduduk::find($id);

            return view("edit.edit-penduduk", compact("tahun", "kecamatan", "penduduk"));
        } else {
            return redirect()->route('penduduk')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function update(Request $request, $id)
    {
        $penduduk = Penduduk::find($id);

        try {
            $request->validate([
                'tahun_id' => [
                    'required',
                    'exists:tahuns,id',
                ],
                'kecamatan_id' => [
                    'required',
                    'exists:kecamatans,id',
                    // Validasi unique untuk kombinasi tahun_id dan kecamatan_id, kecuali untuk record yang sedang diupdate
                    Rule::unique('penduduks')->where(function ($query) use ($request) {
                        return $query->where('tahun_id', $request->tahun_id)
                            ->where('kecamatan_id', $request->kecamatan_id);
                    })->ignore($id)
                ],
                'jumlah_penduduk' => 'required|integer|min:0',
            ], [
                // Pesan error kustom
                'kecamatan_id.unique' => 'Data penduduk untuk kombinasi tahun dan kecamatan ini sudah ada!'
            ]);

            $penduduk->update($request->all());
            return redirect()->route('penduduk')->with('success', 'Data penduduk berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('penduduk')->with('error', 'Gagal memperbarui data penduduk: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $penduduk = Penduduk::find($id);

        try {
            $penduduk->delete();

            return redirect()->route('penduduk')->with('success', 'Data penduduk berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('penduduk')->with('error', 'Gagal menghapus data penduduk: ' . $e->getMessage());
        }
    }
}
