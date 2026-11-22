<?php

namespace App\Providers;

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
        // Sinkronisasi profil mahasiswa & dosen antara modul Internal dan External
        foreach ([
            \App\Models\Mahasiswa::class,
            \App\External\Models\Mahasiswa::class,
            \App\Models\Dosen::class,
            \App\External\Models\Dosen::class,
            \App\Models\User::class,
            \App\External\Models\User::class,
        ] as $model) {
            $model::observe(\App\Observers\ProfileSyncObserver::class);
        }
    }
}
