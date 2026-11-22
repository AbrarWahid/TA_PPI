<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Pengaman: berhenti sebelum RefreshDatabase menjalankan migrate:fresh
     * kalau database yang dipakai bukan database khusus tes (*_test).
     */
    protected function refreshApplication(): void
    {
        parent::refreshApplication();

        $default  = config('database.default');
        $database = (string) config("database.connections.{$default}.database");

        if ($database !== ':memory:' && ! str_ends_with($database, '_test')) {
            throw new \RuntimeException(
                "Tes dihentikan: database '{$database}' bukan database tes (harus berakhiran _test). " .
                'Periksa DB_DATABASE di phpunit.xml.'
            );
        }
    }
}
