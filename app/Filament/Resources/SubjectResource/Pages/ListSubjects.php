<?php

declare(strict_types=1);

namespace App\Filament\Resources\SubjectResource\Pages;

use App\Filament\Exports\SubjectExporter;
use App\Filament\Imports\SubjectImporter;
use App\Filament\Resources\SubjectResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListSubjects extends ListRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            Action::make('downloadTemplate')
                ->label('Unduh Format Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn () => route('admin.templates.download', ['module' => 'subjects'])),

            ImportAction::make('importData')
                ->label('Impor Data Mapel')
                ->icon('heroicon-o-document-arrow-up')
                ->color('info')
                ->importer(SubjectImporter::class)
                ->modalHeading('Impor Data Mapel massal'),

            ExportAction::make('exportData')
                ->label('Ekspor Data Mapel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->exporter(SubjectExporter::class),
        ];
    }
}
