<?php

namespace App\Http\Controllers;

use App\Models\DisplaySlide;

class DisplayController extends Controller
{
    public function index()
    {
        $slides = DisplaySlide::aktif()->get();

        return view('display', [
            'slides' => $slides,
            'dataJs' => $slides->map(fn ($s) => [
                'url' => $s->url,
                'durasi' => max(3, (int) $s->durasi),
            ])->values(),
        ]);
    }
}
