<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Desa;
use App\Models\Kecamatan;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class DesaController extends Controller
{
    public function showDesa(Request $request)
    {
        $query = Desa::query();

        if ($request->has('kecamatan_id') && !empty($request->kecamatan_id)) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }

        $sort = $request->query('sort');
        $direction = $request->query('direction');
        $kecamatan = Kecamatan::orderBy('id')->get();
        $about = About::pluck('value', 'part_name')->toArray();

        // if ($sort === 'nama_desa' && in_array($direction, ['asc', 'desc'])) {
        //     $query->orderBy('nama_desa', $direction);
        // }
        if ($sort) {
            switch ($sort) {
                case 'nama_kecamatan':
                    $query->join('kecamatans', 'desas.kecamatan_id', '=', 'kecamatans.id')
                        ->orderBy('kecamatans.nama_kecamatan', $direction);
                    break;
                case 'nama_desa':
                    $query->orderBy('nama_desa', $direction);
                    break;
                default:
                    $query->orderBy($sort, $direction);
            }
        } else {
            $query->join('kecamatans', 'desas.kecamatan_id', '=', 'kecamatans.id')
                ->orderBy('kecamatans.nama_kecamatan', 'asc');
        }

        $desa = $query->select('desas.*')->paginate(10);

        return view("pages.desa", compact("kecamatan", "desa", "sort", "direction", "about"));
    }
    public function create()
    {
        if (auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            $kecamatan = Kecamatan::all();

            return view("add.add-desa", compact("kecamatan"));
        } else {
            return redirect()->route('desa')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'kecamatan_id' => [
                'required',
                'exists:kecamatans,id',
            ],
            'nama_desa' => [
                'required',
                'max:255',
                Rule::unique('desas')
                    ->where(function ($query) use ($request) {
                        return $query->where('kecamatan_id', $request->kecamatan_id);
                    })
            ]
        ]);

        try {
            Desa::create($request->only(['kecamatan_id', 'nama_desa']));

            return redirect()->route('desa')->with('success', 'Data desa berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('desa')->with('error', 'Gagal menambahkan data desa: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        if (auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            $kecamatan = Kecamatan::all();
            $desa = Desa::find($id);
            return view("edit.edit-desa", compact("kecamatan", "desa"));
        } else {
            return redirect()->route('desa')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function update(Request $request, $id)
{
    $desa = Desa::find($id);

    $request->validate([
        'kecamatan_id' => [
            'required',
            'exists:kecamatans,id',
        ],
        'nama_desa' => [
            'required',
            'max:255',
            Rule::unique('desas')
                ->where(function ($query) use ($request) {
                    return $query->where('kecamatan_id', $request->kecamatan_id);
                })
                ->ignore($id)
        ]
    ]);

    try {
        $desa->update($request->only(['kecamatan_id', 'nama_desa']));
        return redirect()->route('desa')->with('success', 'Data desa berhasil diperbarui!');
    } catch (\Exception $e) {
        return redirect()->route('desa')->with('error', 'Gagal memperbarui data desa: ' . $e->getMessage());
    }
}
    public function destroy($id)
    {
        $desa = Desa::find($id);

        try {
            $desa->desaKasus()->delete();

            $desa->delete();
            return redirect()->route('desa')->with('success', 'Data desa berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('desa')->with('error', 'Gagal menghapus data desa: ' . $e->getMessage());
        }
    }
}
