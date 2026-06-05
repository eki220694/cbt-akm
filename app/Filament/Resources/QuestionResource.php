<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Question;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static string|BackedEnum|null $navigationIcon = "heroicon-o-document-text";

    protected static UnitEnum|string|null $navigationGroup = "Manajemen Akademik";

    protected static ?string $navigationLabel = "Data Soal AKM";

    protected static ?string $modelLabel = "Butir Soal";

    protected static ?string $pluralModelLabel = "Data Soal AKM";

    protected static ?string $recordTitleAttribute = "content";

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record ? strip_tags($record->content) : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Isi Soal & Pertanyaan")
                ->description(
                    "Tuliskan soal di sini. Gunakan tombol berlogo akar (√x) untuk memasukkan rumus matematika visual.",
                )
                ->components([
                    TinyEditor::make("content")
                        ->label("Butir Soal")
                        ->required()
                        ->columnSpanFull()
                        ->profile("default")
                        ->language("id"),
                ]),

            Section::make("Konfigurasi Jawaban & Skor")
                ->components([
                    Select::make("type")
                        ->label("Tipe Soal AKM")
                        ->options([
                            "pg" => "Pilihan Ganda",
                            "pg_kompleks" => "Pilihan Ganda Kompleks",
                            "isian_singkat" => "Isian Singkat",
                            "essay" => "Essay/Uraian",
                            "menjodohkan" => "Menjodohkan",
                        ])
                        ->required()
                        ->live()
                        ->native(false),

                    TextInput::make("points")
                        ->label("Bobot Nilai")
                        ->numeric()
                        ->default(1)
                        ->required(),

                    Repeater::make("options")
                        ->label(
                            fn(Get $get): string => $get("type") ===
                            "menjodohkan"
                                ? "Daftar Pasangan Menjodohkan"
                                : "Opsi Pilihan",
                        )
                        ->visible(
                            fn(Get $get): bool => in_array($get("type"), [
                                "pg",
                                "pg_kompleks",
                                "menjodohkan",
                            ]),
                        )
                        ->schema(
                            fn(Get $get): array => $get("type") ===
                            "menjodohkan"
                                ? [
                                    TextInput::make("left")
                                        ->label("Sisi Kiri")
                                        ->required(),
                                    TextInput::make("right")
                                        ->label("Sisi Kanan")
                                        ->required(),
                                ]
                                : [
                                    TextInput::make("key")
                                        ->label("Opsi (A, B...)")
                                        ->required(),
                                    TextInput::make("value")
                                        ->label("Isi Jawaban")
                                        ->required(),
                                ],
                        )
                        ->columns(2)
                        ->columnSpanFull(),

                    TextInput::make("answer_key")
                        ->label("Kunci Jawaban Utama")
                        ->visible(
                            fn(Get $get): bool => !in_array($get("type"), [
                                "essay",
                                "menjodohkan",
                            ]),
                        )
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("type")
                    ->label("Tipe")
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make("content")
                    ->label("Pertanyaan")
                    ->formatStateUsing(
                        fn(string $state): string => strip_tags($state),
                    )
                    ->limit(80),
                Tables\Columns\TextColumn::make("points")->label("Bobot"),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListQuestions::route("/"),
            "create" => Pages\CreateQuestion::route("/create"),
            "edit" => Pages\EditQuestion::route("/{record}/edit"),
        ];
    }
}
