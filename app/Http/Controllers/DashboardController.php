<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Kecamatan;
use App\Models\Penyakit;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function showAll() {
        $data = [
            'tahun_count' => Tahun::count(),
            'kecamatan_count' => Kecamatan::count(),
            'penyakit_count' => Penyakit::count(),
            'user_count' => User::where('role', 'user')->count(),
            'trend_penyakit' => Penyakit::whereNotNull('link_metabase')->get(),
            'about' => About::pluck('value', 'part_name')->toArray()
        ];

        return view('pages.dashboard', $data);
    }
}
