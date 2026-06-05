<?php

declare(strict_types=1);

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use App\Filament\Imports\QuestionImporter;
use App\Filament\Exports\QuestionExporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListQuestions extends ListRecords
{
    protected static string $resource = QuestionResource::class;

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
                        "module" => "questions",
                    ]),
                ),

            ImportAction::make("importData")
                ->label("Impor Soal AKM")
                ->icon("heroicon-o-document-arrow-up")
                ->color("info")
                ->importer(QuestionImporter::class)
                ->modalHeading("Impor Butir Soal AKM Massal")
                ->modalDescription(
                    "Pilihan tipe wajib diisi: pg, pg_kompleks, isian_singkat, essay, atau menjodohkan.",
                ),

            ExportAction::make("exportData")
                ->label("Ekspor Soal AKM")
                ->icon("heroicon-o-document-arrow-down")
                ->color("success")
                ->exporter(QuestionExporter::class),
        ];
    }
}
