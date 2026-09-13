<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

class CetakController extends Controller
{
    public function kartuUjian()
    {
        return Pdf::loadView('cetak.kartu-ujian')->stream('kartu-ujian.pdf');
    }

    public function daftarHadir()
    {
        return Pdf::loadView('cetak.daftar-hadir')->stream('daftar-hadir.pdf');
    }

    public function beritaAcara()
    {
        return Pdf::loadView('cetak.berita-acara')->stream('berita-acara.pdf');
    }
}
