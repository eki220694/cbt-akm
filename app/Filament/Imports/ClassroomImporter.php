<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\Classroom;
use App\Models\ExamSession;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ClassroomImporter extends Importer
{
    protected static ?string $model = Classroom::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make("name")
                ->label("Nama Kelas")
                ->requiredMapping()
                ->rules(["required", "max:255"]),
            ImportColumn::make("exam_session_name")
                ->label("Nama Sesi Ujian")
                ->requiredMapping()
                ->rules(["required"]),
        ];
    }

    public function resolveRecord(): ?Classroom
    {
        // Hubungan pengait otomatis: mencari Sesi berdasarkan String Teks di Excel
        $session = ExamSession::where(
            "name",
            trim($this->data["exam_session_name"] ?? ""),
        )->first();

        if (!$session) {
            return null; // Menggagalkan baris jika nama sesi tidak valid (Orphan Guard)
        }

        $classroom = Classroom::firstOrNew([
            "name" => trim($this->data["name"] ?? ""),
        ]);

        $classroom->exam_session_id = $session->id;

        return $classroom;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return "Impor data kelas selesai. " .
            number_format($import->successful_rows) .
            " rombel berhasil dimasukkan.";
    }
}
