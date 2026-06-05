<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExamSessionResource\Pages;

use App\Filament\Resources\ExamSessionResource;
use App\Filament\Imports\ExamSessionImporter;
use App\Filament\Exports\ExamSessionExporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListExamSessions extends ListRecords
{
    protected static string $resource = ExamSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label("Tambah Sesi Baru"),

            Action::make("downloadTemplate")
                ->label("Unduh Format Template")
                ->icon("heroicon-o-arrow-down-tray")
                ->color("gray")
                ->url(
                    fn() => route("admin.templates.download", [
                        "module" => "exam_sessions",
                    ]),
                ),

            ImportAction::make("importData")
                ->label("Impor Data Sesi")
                ->icon("heroicon-o-document-arrow-up")
                ->color("info")
                ->importer(ExamSessionImporter::class)
                ->modalHeading("Impor Waktu Sesi Ujian"),

            ExportAction::make("exportData")
                ->label("Ekspor Data Sesi")
                ->icon("heroicon-o-document-arrow-down")
                ->color("success")
                ->exporter(ExamSessionExporter::class),
        ];
    }
}
