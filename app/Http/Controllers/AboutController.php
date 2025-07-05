<?php

namespace App\Http\Controllers;

use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function show()
    {
        $about = About::pluck('value', 'part_name')->toArray();

        return view("pages.about", compact("about"));
    }
}
