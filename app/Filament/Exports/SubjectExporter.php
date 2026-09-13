<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Subject;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SubjectExporter extends Exporter
{
    protected static ?string $model = Subject::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('code')->label('Kode Mapel'),
            ExportColumn::make('name')->label('Nama Mapel'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Ekspor data mapel selesai. '.number_format($export->successful_rows).' baris berhasil diunduh.';
    }
}
