<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Question;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class QuestionExporter extends Exporter
{
    protected static ?string $model = Question::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make("content")->label("Butir Pertanyaan"),
            ExportColumn::make("type")->label("Tipe Soal"),
            ExportColumn::make("points")->label("Bobot Skor"),
            ExportColumn::make("answer_key")->label("Kunci Utama"),
            ExportColumn::make("options")->label("Struktur Pilihan JSON"),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return "Ekspor Bank Soal sukses. Berhasil memindahkan " .
            number_format($export->successful_rows) .
            " butir soal.";
    }
}
