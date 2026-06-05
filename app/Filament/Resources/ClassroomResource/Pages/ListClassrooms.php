<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClassroomResource\Pages;

use App\Filament\Resources\ClassroomResource;
use App\Filament\Imports\ClassroomImporter;
use App\Filament\Exports\ClassroomExporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListClassrooms extends ListRecords
{
    protected static string $resource = ClassroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            Action::make("downloadTemplate")
                ->label("Unduh Format Template")
                ->icon("heroicon-o-arrow-down-tray")
                ->color("gray")
                ->url(
                    fn() => route("admin.templates.download", [
                        "module" => "classrooms",
                    ]),
                ),

            ImportAction::make("importData")
                ->label("Impor Data Kelas")
                ->icon("heroicon-o-document-arrow-up")
                ->color("info")
                ->importer(ClassroomImporter::class)
                ->modalHeading("Impor Rombongan Belajar")
                ->modalDescription(
                    "Kolom exam_session_name harus sesuai dengan nama yang ada di menu Data Sesi.",
                ),

            ExportAction::make("exportData")
                ->label("Ekspor Data Kelas")
                ->icon("heroicon-o-document-arrow-down")
                ->color("success")
                ->exporter(ClassroomExporter::class),
        ];
    }
}
