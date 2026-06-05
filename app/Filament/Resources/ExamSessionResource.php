<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ExamSessionResource\Pages;
use App\Models\ExamSession;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ExamSessionResource extends Resource
{
    protected static ?string $model = ExamSession::class;

    protected static string|BackedEnum|null $navigationIcon = "heroicon-o-clock";

    protected static UnitEnum|string|null $navigationGroup = "Manajemen Akademik";

    protected static ?string $navigationLabel = "Data Sesi";

    protected static ?string $modelLabel = "Sesi Ujian";

    protected static ?string $pluralModelLabel = "Data Sesi";

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Detail Sesi Ujian")
                ->description(
                    "Atur nama dan rentang waktu durasi untuk setiap sesi pelaksanaan ujian.",
                )
                ->components([
                    TextInput::make("name")
                        ->label("Nama Sesi")
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true)
                        ->placeholder("Contoh: Sesi 1, Sesi Pagi"),

                    TimePicker::make("start_time")
                        ->label("Jam Mulai")
                        ->required()
                        ->seconds(false)
                        ->placeholder("07:00"),

                    TimePicker::make("end_time")
                        ->label("Jam Selesai")
                        ->required()
                        ->seconds(false)
                        ->placeholder("09:30")
                        ->rules(["after_or_equal:start_time"])
                        ->helperText(
                            "Waktu selesai harus sama atau setelah waktu jam mulai.",
                        ),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("name")
                    ->label("Nama Sesi")
                    ->searchable()
                    ->sortable()
                    ->weight("bold")
                    ->color("primary"),

                Tables\Columns\TextColumn::make("start_time")
                    ->label("Mulai")
                    ->time("H:i")
                    ->sortable()
                    ->badge()
                    ->color("success"),

                Tables\Columns\TextColumn::make("end_time")
                    ->label("Selesai")
                    ->time("H:i")
                    ->sortable()
                    ->badge()
                    ->color("danger"),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListExamSessions::route("/"),
            "create" => Pages\CreateExamSession::route("/create"),
            "edit" => Pages\EditExamSession::route("/{record}/edit"),
        ];
    }
}
