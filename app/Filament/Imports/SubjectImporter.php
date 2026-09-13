<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Models\Subject;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Str;

class SubjectImporter extends Importer
{
    protected static ?string $model = Subject::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code')
                ->label('Kode Mapel')
                ->requiredMapping()
                ->rules(['required', 'max:10']),
            ImportColumn::make('name')
                ->label('Nama Mapel')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Subject
    {
        $code = Str::upper(trim($this->data['code'] ?? ''));
        $subject = Subject::firstOrNew(['code' => $code]);
        $subject->name = trim($this->data['name'] ?? $subject->name ?? '');

        return $subject;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Impor data mapel selesai. '.number_format($import->successful_rows).' baris berhasil.';
    }
}
