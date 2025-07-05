<?php

namespace App\Http\Controllers;

use App\Models\About;
use Illuminate\Http\Request;
use App\Models\Penyakit;
use Illuminate\Support\Facades\Storage;

class InfoPenyakitController extends Controller
{
    public function showInfo(Request $request)
    {
        $penyakit = Penyakit::all();
        $about = About::pluck('value', 'part_name')->toArray();

        return view("pages.info-penyakit", compact("penyakit", "about"));
    }
    public function createInfo(Request $request, $id = null)
    {
        if (auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            if ($id) {
                $penyakit = Penyakit::findOrFail($id);

                if ($penyakit->pengertian) {
                    $nextAvailablePenyakit = Penyakit::whereNull('pengertian')->first();

                    if ($nextAvailablePenyakit) {
                        return redirect()->route('add-info', $nextAvailablePenyakit->id)
                            ->with('info', 'Penyakit ' . $penyakit->nama_penyakit . ' sudah memiliki informasi. Dialihkan ke penyakit lain.');
                    } else {
                        return redirect()->route('info-penyakit')
                            ->with('error', 'Semua penyakit sudah memiliki informasi lengkap.');
                    }
                }

                return view("add.add-info", compact('penyakit'));
            } else {
                $availablePenyakit = Penyakit::whereNull('pengertian')->get();

                if ($availablePenyakit->isEmpty()) {
                    return redirect()->route('info-penyakit')
                        ->with('error', 'Semua penyakit sudah memiliki informasi lengkap.');
                }

                $penyakit = $availablePenyakit->first();
                return view("add.add-info", compact('penyakit', 'availablePenyakit'));
            }
        } else {
            return redirect()->route('info-penyakit')
                ->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function storeInfo(Request $request)
    {
        $request->validate([
            'penyakit_id' => 'required|exists:penyakits,id',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'sumber_informasi' => 'nullable|url'
        ]);

        $contentFields = [
            'pengertian',
            'penyebab',
            'gejala',
            'diagnosis',
            'komplikasi',
            'pengobatan',
            'pencegahan'
        ];

        $hasContent = false;
        foreach ($contentFields as $field) {
            if (!empty($request->$field)) {
                $hasContent = true;
                break;
            }
        }

        if (!$hasContent) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['content_error' => 'Minimal satu field (pengertian, penyebab, gejala, dll) harus diisi.']);
        }

        try {
            $penyakit = Penyakit::findOrFail($request->penyakit_id);

            // $dataToUpdate = [
            //     'pengertian' => $request->pengertian,
            //     'penyebab' => $request->penyebab,
            //     'gejala' => $request->gejala,
            //     'diagnosis' => $request->diagnosis,
            //     'komplikasi' => $request->komplikasi,
            //     'pengobatan' => $request->pengobatan,
            //     'pencegahan' => $request->pencegahan,
            //     'sumber_informasi' => $request->sumber_informasi,
            // ];

            $dataToUpdate = [
                'pengertian' => $this->normalizeNewlines($request->pengertian),
                'penyebab' => $this->normalizeNewlines($request->penyebab),
                'gejala' => $this->normalizeNewlines($request->gejala),
                'diagnosis' => $this->normalizeNewlines($request->diagnosis),
                'komplikasi' => $this->normalizeNewlines($request->komplikasi),
                'pengobatan' => $this->normalizeNewlines($request->pengobatan),
                'pencegahan' => $this->normalizeNewlines($request->pencegahan),
                'sumber_informasi' => $request->sumber_informasi,
            ];

            if ($request->hasFile('gambar')) {
                $fileName = time() . '_' . $request->file('gambar')->getClientOriginalName();
                $path = $request->file('gambar')->storeAs('upload', $fileName, 'public');
                $dataToUpdate['gambar'] = $path;
            }

            $penyakit->update($dataToUpdate);

            return redirect()->route('info-penyakit')->with('success', 'Informasi penyakit berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('info-penyakit')->with('error', 'Gagal menambahkan informasi penyakit: ' . $e->getMessage());
        }
    }
    private function normalizeNewlines($text) {
        if (empty($text)) {
            return null;
        }
        // Normalisasi newline menjadi \n
        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\r", "\n", $text);
        return $text;
    }
    public function editInfo($id)
    {
        if (auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            $infoPenyakit = Penyakit::find($id);

            if (!$infoPenyakit) {
                return redirect()->route('info-penyakit')->with('error', 'Penyakit tidak ditemukan.');
            }

            if (
                !$infoPenyakit->pengertian && !$infoPenyakit->penyebab && !$infoPenyakit->gejala &&
                !$infoPenyakit->diagnosis && !$infoPenyakit->komplikasi && !$infoPenyakit->pengobatan &&
                !$infoPenyakit->pencegahan
            ) {
                return redirect()->route('info-penyakit.add', $id)->with('info', 'Penyakit belum memiliki informasi lengkap. Silakan tambahkan informasi terlebih dahulu.');
            }

            return view("edit.edit-info", compact("infoPenyakit"));
        } else {
            return redirect()->route('info-penyakit')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function updateInfo(Request $request, $id)
    {
        $infoPenyakit = Penyakit::find($id);

        if (!$infoPenyakit) {
            return redirect()->route('info-penyakit')->with('error', 'Penyakit tidak ditemukan.');
        }

        $request->validate([
            'pengertian' => 'nullable',
            'penyebab' => 'nullable',
            'gejala' => 'nullable',
            'diagnosis' => 'nullable',
            'komplikasi' => 'nullable',
            'pengobatan' => 'nullable',
            'pencegahan' => 'nullable',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'sumber_informasi' => 'nullable'
        ]);

        try {
            // $dataToUpdate = $request->only([
            //     'pengertian',
            //     'penyebab',
            //     'gejala',
            //     'diagnosis',
            //     'komplikasi',
            //     'pengobatan',
            //     'pencegahan',
            //     'sumber_informasi'
            // ]);
            $dataToUpdate = [
                'pengertian' => $this->normalizeNewlines($request->pengertian),
                'penyebab' => $this->normalizeNewlines($request->penyebab),
                'gejala' => $this->normalizeNewlines($request->gejala),
                'diagnosis' => $this->normalizeNewlines($request->diagnosis),
                'komplikasi' => $this->normalizeNewlines($request->komplikasi),
                'pengobatan' => $this->normalizeNewlines($request->pengobatan),
                'pencegahan' => $this->normalizeNewlines($request->pencegahan),
                'sumber_informasi' => $request->sumber_informasi,
            ];

            if ($request->hasFile('gambar')) {
                if ($infoPenyakit->gambar && Storage::disk('public')->exists($infoPenyakit->gambar)) {
                    Storage::disk('public')->delete($infoPenyakit->gambar);
                }

                $fileName = time() . '_' . $request->file('gambar')->getClientOriginalName();
                $path = $request->file('gambar')->storeAs('upload', $fileName, 'public');
                $dataToUpdate['gambar'] = $path;
            }

            $infoPenyakit->update($dataToUpdate);

            return redirect()->route('info-penyakit')->with('success', 'Informasi penyakit berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('info-penyakit')->with('error', 'Gagal memperbarui informasi penyakit: ' . $e->getMessage());
        }
    }
    public function destroyInfo($id)
    {
        $infoPenyakit = Penyakit::find($id);

        if (!$infoPenyakit) {
            return redirect()->route('info-penyakit')->with('error', 'Penyakit tidak ditemukan.');
        }

        try {
            if ($infoPenyakit->gambar && Storage::disk('public')->exists($infoPenyakit->gambar)) {
                Storage::disk('public')->delete($infoPenyakit->gambar);
            }

            $infoPenyakit->update([
                'pengertian' => null,
                'penyebab' => null,
                'gejala' => null,
                'diagnosis' => null,
                'komplikasi' => null,
                'pengobatan' => null,
                'pencegahan' => null,
                'gambar' => null,
                'sumber_informasi' => null
            ]);

            return redirect()->route('info-penyakit')->with('success', 'Informasi penyakit berhasil direset!');
        } catch (\Exception $e) {
            return redirect()->route('info-penyakit')->with('error', 'Gagal mereset informasi penyakit: ' . $e->getMessage());
        }
    }
}
