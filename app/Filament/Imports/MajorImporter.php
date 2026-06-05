<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\Major;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class MajorImporter extends Importer
{
    protected static ?string $model = Major::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make("code")
                ->label("Kode Jurusan")
                ->requiredMapping()
                ->rules(["required", "max:10"]),
            ImportColumn::make("name")
                ->label("Nama Jurusan")
                ->requiredMapping()
                ->rules(["required", "max:255"]),
        ];
    }

    public function resolveRecord(): ?Major
    {
        $code = Str::upper(trim($this->data["code"] ?? ""));

        return Major::firstOrNew([
            "code" => $code,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body =
            "Impor data jurusan selesai. " .
            number_format($import->successful_rows) .
            " baris berhasil.";
        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= " " . number_format($failedRowsCount) . " baris gagal.";
        }
        return $body;
    }
}
