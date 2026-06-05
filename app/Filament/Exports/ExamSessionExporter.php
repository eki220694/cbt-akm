<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\ExamSession;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ExamSessionExporter extends Exporter
{
    protected static ?string $model = ExamSession::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make("name")->label("Nama Sesi Ujian"),
            ExportColumn::make("start_time")->label("Jam Mulai"),
            ExportColumn::make("end_time")->label("Jam Selesai"),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return "Ekspor data sesi selesai. " .
            number_format($export->successful_rows) .
            " data berhasil diekspor.";
    }
}
