<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExamResultResource\RelationManagers;

use App\Filament\Resources\ExamResultResource;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    protected static ?string $title = 'Jawaban';

    public function form(Schema $schema): Schema
    {
        // Koreksi manual (essay): set poin + status benar/salah.
        return $schema->components([
            TextInput::make('points')
                ->label('Poin')
                ->numeric()
                ->minValue(0)
                ->required(),
            Toggle::make('is_correct')
                ->label('Benar'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question_id')
                    ->label('Soal #'),
                Tables\Columns\TextColumn::make('question.content')
                    ->label('Soal')
                    ->formatStateUsing(fn (string $state): string => strip_tags($state))
                    ->limit(60),
                Tables\Columns\TextColumn::make('answer')
                    ->label('Jawaban')
                    ->limit(60)
                    ->default('-'),
                Tables\Columns\IconColumn::make('is_correct')
                    ->label('Benar')
                    ->boolean(),
                Tables\Columns\TextColumn::make('points')
                    ->label('Poin'),
            ])
            ->headerActions([])
            ->actions([
                EditAction::make()
                    ->label('Koreksi')
                    ->after(fn ($livewire) => ExamResultResource::recalcScore($livewire->getOwnerRecord())),
            ])
            ->bulkActions([]);
    }
}
