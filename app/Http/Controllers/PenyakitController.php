<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Services\MetabasePenyakitService;
use App\Models\Penyakit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PenyakitController extends Controller
{
    private $metabasePenyakitService;

    public function __construct(MetabasePenyakitService $metabasePenyakitService)
    {
        $this->metabasePenyakitService = $metabasePenyakitService;
    }
    public function showPenyakit(Request $request)
    {
        $sort = $request->query('sort');
        $direction = $request->query('direction');
        $about = About::pluck('value', 'part_name')->toArray();

        $query = Penyakit::query();

        if ($sort === 'nama_penyakit' && in_array($direction, ['asc', 'desc'])) {
            $query->orderBy('nama_penyakit', $direction);
        }

        $penyakit = $query->paginate(10);

        return view("pages.penyakit", compact("penyakit", "sort", "direction", "about"));
    }
    public function create()
    {
        if (auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            return view("add.add-penyakit");
        } else {
            return redirect()->route('penyakit')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_penyakit' => 'required|max:255'
        ]);

        try {
            Penyakit::create([
                'nama_penyakit' => $request->nama_penyakit
            ]);

            return redirect()->route('penyakit')->with('success', 'Data penyakit berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('penyakit')->with('error', 'Gagal menambahkan data penyakit: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        if (auth()->check() && auth()->user()->role == 'superadmin' || auth()->user()->role == 'admin') {
            $penyakit = Penyakit::find($id);
            return view("edit.edit-penyakit", compact("penyakit"));
        } else {
            return redirect()->route('penyakit')->with('error', 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
    }
    public function update(Request $request, $id)
    {
        $penyakit = Penyakit::find($id);

        try {
            $penyakit->update($request->all());
            return redirect()->route('penyakit')->with('success', 'Data penyakit berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('penyakit')->with('error', 'Gagal memperbarui data penyakit: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $penyakit = Penyakit::find($id);

        try {
            $penyakit->namaPenyakit()->delete();

            $penyakit->delete();
            return redirect()->route('penyakit')->with('success', 'Data penyakit berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('penyakit')->with('error', 'Gagal menghapus data penyakit: ' . $e->getMessage());
        }
    }
    public function regenerateTrendForDisease($penyakitId)
    {
        try {
            $penyakit = Penyakit::findOrFail($penyakitId);

            $question = $this->metabasePenyakitService->createTrendQuestion($penyakitId);

            if (isset($question['id'])) {
                $embedUrl = $this->metabasePenyakitService->getEmbedUrl($question['id']);

                if ($embedUrl) {
                    $penyakit->update([
                        'link_metabase' => $embedUrl
                    ]);

                    // return response()->json([
                    //     'success' => true,
                    //     'embed_url' => $embedUrl,
                    //     'message' => "Trend chart for {$penyakit->nama_penyakit} regenerated successfully"
                    // ]);
                    return redirect()->back()->with('success', "Grafik trend untuk penyakit {$penyakit->nama_penyakit} berhasil dibuat");
                }
            }

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Failed to regenerate trend chart'
            // ], 500);
            return redirect()->back()->with('error', 'Gagal membuat grafik trend penyakit');
        } catch (\Exception $e) {
            Log::error("Error regenerating trend chart: " . $e->getMessage());

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Failed to regenerate trend chart: ' . $e->getMessage()
            // ], 500);
            return redirect()->back()->with('error', 'Gagal membuat grafik trend penyakit: ' . $e->getMessage());
        }
    }
}
