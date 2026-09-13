<?php

namespace Tests\Feature;

use Tests\TestCase;

class CetakTest extends TestCase
{
    public function test_cetak_butuh_auth(): void
    {
        foreach (['/cetak/kartu-ujian', '/cetak/daftar-hadir', '/cetak/berita-acara'] as $url) {
            $this->get($url)->assertRedirect();
        }
    }
}
