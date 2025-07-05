<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class KecamatanController extends Controller
{
    public function showKecamatan(Request $request)
    {
        $sort = $request->query('sort');
        $direction = $request->query('direction');
        $about = About::pluck('value', 'part_name')->toArray();

        $query = Kecamatan::query();

        if ($sort === 'nama_kecamatan' && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy('nama_kecamatan', $direction);
        }

        $kecamatan = $query->paginate(10);

        return view("pages.kecamatan", compact("kecamatan", "sort", "direction", "about"));
    }
    public function create()
    {
        if(auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            return view("add.add-kecamatan");
        } else {
            return redirect()->route('kecamatan')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_kecamatan' => 'required|max:255'
        ]);

        try {
            Kecamatan::create([
                'nama_kecamatan' => $request->nama_kecamatan
            ]);

            return redirect()->route('kecamatan')->with('success', 'Data kecamatan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('kecamatan')->with('error', 'Gagal menambahkan data kecamatan: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        if(auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            $kecamatan = Kecamatan::find($id);
            return view("edit.edit-kecamatan", compact("kecamatan"));
        } else {
            return redirect()->route('kecamatan')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function update(Request $request, $id)
    {
        $kecamatan = Kecamatan::find($id);

        try {
            $kecamatan->update($request->all());
            return redirect()->route('kecamatan')->with('success', 'Data kecamatan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('kecamatan')->with('error', 'Gagal memperbarui data kecamatan: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $kecamatan = Kecamatan::find($id);

        try {
            $kecamatan->kecamatanPenduduk()->delete();
            $kecamatan->kecamatanKasus()->delete();
            $kecamatan->kecamatanDesa()->delete();
            $kecamatan->kecamatanDetailMaps()->delete();

            $kecamatan->delete();
            return redirect()->route('kecamatan')->with('success', 'Data kecamatan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('kecamatan')->with('error', 'Gagal menghapus data kecamatan: ' . $e->getMessage());
        }
    }
}
