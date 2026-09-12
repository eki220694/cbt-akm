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
            // ponytail: nama kolom disamakan dgn importer+template agar round-trip; tambah formatStateUsing bila perlu sanitasi formula.
            ExportColumn::make("content")->label("Butir Soal"),
            ExportColumn::make("type")->label("Tipe Soal"),
            ExportColumn::make("points")->label("Bobot Nilai"),
            ExportColumn::make("answer_key")->label("Kunci Jawaban"),
            ExportColumn::make("options")->label("Format Opsi JSON"),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return "Ekspor Bank Soal sukses. Berhasil memindahkan " .
            number_format($export->successful_rows) .
            " butir soal.";
    }
}
