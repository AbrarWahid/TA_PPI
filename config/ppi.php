<?php

return [
    // Program studi bawaan bila prodi mahasiswa tidak diketahui.
    'default_prodi' => env('PPI_DEFAULT_PRODI', 'TRO'),

    // Prodi di Internal (users.prodi / user_roles.prodi)  =>  kode di External (program_studis.nama_prodi)
    'prodi_map' => [
        'informatika' => 'TRIN',
        'otomasi'     => 'TRO',
        'mekatronika' => 'TRMO',
    ],
];
