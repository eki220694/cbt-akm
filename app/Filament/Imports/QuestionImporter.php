<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Enums\QuestionType;
use App\Models\Question;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class QuestionImporter extends Importer
{
    protected static ?string $model = Question::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('content')
                ->label('Butir Soal')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('stimulus')
                ->label('Stimulus/Bacaan')
                ->rules(['nullable', 'string'])
                ->ignoreBlankState(true),
            ImportColumn::make('type')
                ->label('Tipe Soal')
                ->requiredMapping()
                ->rules([
                    'required',
                    'in:'.implode(',', array_map(fn (QuestionType $c): string => $c->value, QuestionType::cases())),
                ]),
            ImportColumn::make('points')
                ->label('Bobot Nilai')
                ->requiredMapping()
                ->rules(['required', 'integer', 'min:1']),
            ImportColumn::make('answer_key')
                ->label('Kunci Jawaban')
                ->rules(['nullable', 'string', 'max:255'])
                ->ignoreBlankState(true),
            // ponytail: kolom virtual, resolveRecord() isi manual; tambah cast array bila format berubah.
            ImportColumn::make('options_json_format')
                ->label('Format Opsi JSON')
                ->rules(['nullable', 'json'])
                ->fillRecordUsing(static fn () => null),
        ];
    }

    public function resolveRecord(): ?Question
    {
        $question = Question::firstOrNew([
            'content' => trim($this->data['content'] ?? ''),
        ]);

        $question->stimulus = $this->data['stimulus'] ?? null;
        $question->type = $this->data['type'];
        $question->points = (int) ($this->data['points'] ?? 1);
        $question->answer_key = $this->data['answer_key'] ?? null;

        // Parsing String Teks Excel ke JSON Array Laravel secara aman
        $optionsRaw = $this->data['options_json_format'] ?? null;
        if (! empty($optionsRaw)) {
            $decoded = json_decode((string) $optionsRaw, true);
            $question->options = is_array($decoded) ? $decoded : null;
        } else {
            $question->options = null;
        }

        return $question;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Impor Bank Soal selesai. '.
            number_format($import->successful_rows).
            ' butir soal masuk antrean.';
    }
}
