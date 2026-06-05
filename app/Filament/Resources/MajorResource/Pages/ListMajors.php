<?php

declare(strict_types=1);

namespace App\Filament\Resources\MajorResource\Pages;

use App\Filament\Resources\MajorResource;
use App\Filament\Imports\MajorImporter;
use App\Filament\Exports\MajorExporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListMajors extends ListRecords
{
    protected static string $resource = MajorResource::class;

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
                        "module" => "majors",
                    ]),
                ),

            ImportAction::make("importData")
                ->label("Impor Data Jurusan")
                ->icon("heroicon-o-document-arrow-up")
                ->color("info")
                ->importer(MajorImporter::class)
                ->modalHeading("Impor Data Jurusan massal"),

            ExportAction::make("exportData")
                ->label("Ekspor Data Jurusan")
                ->icon("heroicon-o-document-arrow-down")
                ->color("success")
                ->exporter(MajorExporter::class),
        ];
    }
}
