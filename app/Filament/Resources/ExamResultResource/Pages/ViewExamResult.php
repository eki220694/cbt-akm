<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExamResultResource\Pages;

use App\Filament\Resources\ExamResultResource;
use Filament\Resources\Pages\ViewRecord;

class ViewExamResult extends ViewRecord
{
    protected static string $resource = ExamResultResource::class;

    protected function getHeaderActions(): array
    {
        return []; // read-only: tanpa tombol edit
    }
}
