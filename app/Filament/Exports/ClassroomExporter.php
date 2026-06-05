<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Classroom;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ClassroomExporter extends Exporter
{
    protected static ?string $model = Classroom::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make("name")->label("Nama Rombel Kelas"),
            ExportColumn::make("examSession.name")->label(
                "Sesi Pelaksanaan Terikat",
            ),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return "Ekspor data kelas selesai. " .
            number_format($export->successful_rows) .
            " baris sukses diunduh.";
    }
}
