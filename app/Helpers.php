<?php

if (!function_exists('labelProdi')) {
    function labelProdi(string $prodi): string
    {
        return match($prodi) {
            'informatika' => 'D4 TRIN - Teknologi Rekayasa Informatika Industri',
            'otomasi'     => 'D4 TRO - Teknologi Rekayasa Otomasi',
            'mekatronika' => 'D4 TRMO - Teknologi Rekayasa Mekatronika',
            default       => $prodi,
        };
    }
}

if (!function_exists('singkatProdi')) {
    function singkatProdi(string $prodi): string
    {
        return match($prodi) {
            'informatika' => 'D4 TRIN',
            'otomasi'     => 'D4 TRO',
            'mekatronika' => 'D4 TRMO',
            default       => $prodi,
        };
    }
}