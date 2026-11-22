<?php

namespace App\Http\Controllers;

/**
 * Halaman informasi statis yang ditautkan dari footer & tombol bantuan.
 */
class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function help()
    {
        return view('pages.help');
    }
}
