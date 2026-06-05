<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ClassroomResource\Pages;
use App\Models\Classroom;
use App\Models\ExamSession;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class ClassroomResource extends Resource
{
    protected static ?string $model = Classroom::class;

    protected static string|BackedEnum|null $navigationIcon = "heroicon-o-building-office-2";

    protected static UnitEnum|string|null $navigationGroup = "Manajemen Akademik";

    protected static ?string $navigationLabel = "Data Kelas";

    protected static ?string $modelLabel = "Kelas";

    protected static ?string $pluralModelLabel = "Data Kelas";

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Detail Ruang Kelas")
                ->description(
                    "Manajemen data ruang kelas dan alokasi kelompok sesi ujian.",
                )
                ->components([
                    TextInput::make("name")
                        ->label("Nama Kelas")
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText(
                            "Harus unik, contoh: XII MIPA 1 atau Lab Komputer 1",
                        ),

                    Select::make("exam_session_id")
                        ->label("Sesi Ujian Terikat")
                        ->relationship("examSession", "name")
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText(
                            "Pilih konfigurasi sesi ujian dinamis dari master database.",
                        ),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("name")
                    ->label("Nama Kelas")
                    ->searchable()
                    ->sortable()
                    ->weight("bold"),

                Tables\Columns\TextColumn::make("examSession.name")
                    ->label("Sesi Pelaksanaan")
                    ->badge()
                    ->color("info")
                    ->searchable()
                    ->sortable()
                    ->default("Belum Diatur"),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make("exam_session_id")
                    ->label("Filter Sesi")
                    ->relationship("examSession", "name"),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make("assignSessionMassal")
                        ->label("Tetapkan Sesi Massal")
                        ->icon("heroicon-o-clock")
                        ->form([
                            Select::make("exam_session_id")
                                ->label("Pilih Sesi Baru")
                                ->options(ExamSession::pluck("name", "id"))
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records) {
                            $records->each(
                                fn(Classroom $record) => $record->update([
                                    "exam_session_id" =>
                                        $data["exam_session_id"],
                                ]),
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            "index" => Pages\ListClassrooms::route("/"),
            "create" => Pages\CreateClassroom::route("/create"),
            "edit" => Pages\EditClassroom::route("/{record}/edit"),
        ];
    }
}
