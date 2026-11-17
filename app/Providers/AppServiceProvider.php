<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Human-readable dates ("5 menit yang lalu") in Indonesian.
        Carbon::setLocale('id');
    }
}
