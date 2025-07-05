<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Tahun;
use Illuminate\Http\Request;

class TahunController extends Controller
{
    public function showTahun(Request $request)
    {
        $sort = $request->query('sort');
        $direction = $request->query('direction');
        $about = About::pluck('value', 'part_name')->toArray();

        $query = Tahun::query();

        if ($sort === 'tahun' && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy('tahun', $direction);
        }

        $tahun = $query->paginate(10);

        return view("pages.tahun", compact("tahun", "sort", "direction", "about"));
    }
    public function create()
    {
        if(auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            return view("add.add-tahun");
        } else {
            return redirect()->route('tahun')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|digits:4|integer|min:1900|max:2100'
        ]);

        try {
            Tahun::create([
                'tahun' => $request->tahun
            ]);
    
            return redirect()->route('tahun')->with('success', 'Data tahun berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('tahun')->with('error', 'Gagal menambahkan data tahun: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        if(auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            $tahun = Tahun::find($id);
            return view("edit.edit-tahun", compact("tahun"));
        } else {
            return redirect()->route('tahun')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function update(Request $request, $id)
    {
        $tahun = Tahun::find($id);

        try {
            $tahun->update($request->all());
    
            return redirect()->route('tahun')->with('success', 'Data tahun berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('tahun')->with('error', 'Gagal memperbarui data tahun: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $tahun = Tahun::findOrFail($id);

        try {
            $tahun->tahunPenduduk()->delete();
            $tahun->tahunPenyakit()->delete();
    
            $tahun->delete();
    
            return redirect()->route('tahun')->with('success', 'Data tahun berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('tahun')->with('error', 'Gagal menghapus data tahun: ' . $e->getMessage());
        }
    }
}
