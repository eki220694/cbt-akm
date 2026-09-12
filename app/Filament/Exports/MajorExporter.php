<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Major;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class MajorExporter extends Exporter
{
    protected static ?string $model = Major::class;

    public static function getColumns(): array
    {
        return [
            // ponytail: tanpa created_at agar hasil export bisa diimpor ulang; tambah kembali bila perlu audit.
            ExportColumn::make('code')->label('Kode Jurusan'),
            ExportColumn::make('name')->label('Nama Jurusan'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Ekspor data jurusan selesai. '.
            number_format($export->successful_rows).
            ' baris berhasil diunduh.';
    }
}
