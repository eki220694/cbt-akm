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
            ExportColumn::make('stimulus')->label('Stimulus/Bacaan'),
            ExportColumn::make('content')->label('Butir Soal'),
            ExportColumn::make('type')->label('Tipe Soal'),
            ExportColumn::make('points')->label('Bobot Nilai'),
            ExportColumn::make('answer_key')->label('Kunci Jawaban'),
            ExportColumn::make('options_json_format')
                ->label('Format Opsi JSON')
                ->state(fn (Question $record): mixed => $record->options)
                ->formatStateUsing(fn ($state): ?string => is_array($state) ? json_encode($state) : $state),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Ekspor Bank Soal sukses. Berhasil memindahkan '.
            number_format($export->successful_rows).
            ' butir soal.';
    }
}
