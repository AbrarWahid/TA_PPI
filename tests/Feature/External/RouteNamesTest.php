<?php

namespace Tests\Feature\External;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteNamesTest extends TestCase
{
    public function test_semua_nama_route_di_view_dan_controller_external_terdaftar(): void
    {
        $files = array_merge(
            File::allFiles(resource_path('views/external')),
            File::allFiles(app_path('External')),
        );
        $files[] = new \SplFileInfo(resource_path('views/layouts/app.blade.php'));

        $hilang = [];
        foreach ($files as $file) {
            $isi = file_get_contents($file->getPathname());
            if (! preg_match_all('/route\(\s*[\'"]([A-Za-z0-9_.\-]+)[\'"]/', $isi, $m)) {
                continue;
            }
            foreach (array_unique($m[1]) as $nama) {
                if (str_ends_with($nama, '.')) {
                    continue; // nama dinamis, mis. route('external.' . $x)
                }
                if (! Route::has($nama)) {
                    $hilang[] = str_replace(base_path() . '/', '', $file->getPathname()) . '  →  ' . $nama;
                }
            }
        }

        $this->assertSame([], $hilang, "Nama route berikut tidak terdaftar:\n" . implode("\n", $hilang));
    }

    public function test_semua_route_di_menu_sidebar_external_terdaftar(): void
    {
        $isi = file_get_contents(resource_path('views/layouts/partials/external-menu.blade.php'));
        preg_match_all("/'(external\.[a-z_.]+)'/", $isi, $m);

        $this->assertNotEmpty($m[1], 'Tidak ada route di external-menu.blade.php');
        foreach (array_unique($m[1]) as $nama) {
            $this->assertTrue(Route::has($nama), "Route menu '{$nama}' tidak terdaftar");
        }
    }
}
