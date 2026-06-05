<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\ExamSession;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ExamSessionImporter extends Importer
{
    protected static ?string $model = ExamSession::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make("name")
                ->label("Nama Sesi")
                ->requiredMapping()
                ->rules(["required", "max:100"]),
            ImportColumn::make("start_time")
                ->label("Jam Mulai")
                ->requiredMapping()
                ->rules(["required", 'regex:/^\d{2}:\d{2}$/']),
            ImportColumn::make("end_time")
                ->label("Jam Selesai")
                ->requiredMapping()
                ->rules(["required", 'regex:/^\d{2}:\d{2}$/']),
        ];
    }

    public function resolveRecord(): ?ExamSession
    {
        return ExamSession::firstOrNew([
            "name" => trim($this->data["name"] ?? ""),
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return "Impor data sesi selesai. " .
            number_format($import->successful_rows) .
            " baris sukses terproses.";
    }
}
