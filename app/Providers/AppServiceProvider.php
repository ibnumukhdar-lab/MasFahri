<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Post;
use App\Observers\DaftarkanMediaKeGaleri;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Post::observe(DaftarkanMediaKeGaleri::class);
        Page::observe(DaftarkanMediaKeGaleri::class);
        //
    }
}
