<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\MajorResource\Pages;
use App\Models\Major;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class MajorResource extends Resource
{
    protected static ?string $model = Major::class;

    protected static string|BackedEnum|null $navigationIcon = "heroicon-o-rectangle-stack";

    protected static UnitEnum|string|null $navigationGroup = "Manajemen Akademik";

    protected static ?string $navigationLabel = "Data Jurusan";

    protected static ?string $modelLabel = "Jurusan";

    protected static ?string $pluralModelLabel = "Data Jurusan";

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Detail Jurusan")
                ->description(
                    "Informasi dasar mengenai data program keahlian atau jurusan sekolah.",
                )
                ->components([
                    TextInput::make("code")
                        ->label("Kode Jurusan")
                        ->required()
                        ->maxLength(10)
                        ->extraInputAttributes([
                            "style" => "text-transform: uppercase",
                        ])
                        ->unique(ignoreRecord: true)
                        ->helperText(
                            "Maksimal 10 karakter, otomatis dikonversi ke huruf kapital.",
                        ),

                    TextInput::make("name")
                        ->label("Nama Lengkap Jurusan")
                        ->required()
                        ->maxLength(255)
                        ->helperText(
                            "Contoh: Rekayasa Perangkat Lunak atau Ilmu Pengetahuan Alam",
                        ),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("code")
                    ->label("Kode")
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color("primary"),

                Tables\Columns\TextColumn::make("name")
                    ->label("Nama Jurusan")
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListMajors::route("/"),
            "create" => Pages\CreateMajor::route("/create"),
            "edit" => Pages\EditMajor::route("/{record}/edit"),
        ];
    }
}
