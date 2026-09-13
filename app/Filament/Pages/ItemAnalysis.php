<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\ExamSession;
use App\Models\StudentAnswer;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use UnitEnum;

class ItemAnalysis extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static UnitEnum|string|null $navigationGroup = 'Hasil & Analisis';

    protected static ?string $navigationLabel = 'Analisis Butir';

    protected static ?string $title = 'Analisis Butir Soal';

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.pages.item-analysis';

    #[Url]
    public ?string $exam_session_id = null;

    public static function canAccess(): bool
    {
        return PanelAccess::isAdmin() || PanelAccess::isGuru();
    }

    /** % benar per soal: group by question_id, avg is_correct. */
    public static function rows(?string $examSessionId): Collection
    {
        if (blank($examSessionId)) {
            return collect();
        }

        return StudentAnswer::query()
            ->whereHas('progress', fn ($q) => $q->where('exam_session_id', $examSessionId))
            ->with('question')
            ->selectRaw('question_id, COUNT(*) as total, AVG(is_correct) as pct_correct, AVG(points) as avg_points')
            ->groupBy('question_id')
            ->get();
    }

    protected function getViewData(): array
    {
        return [
            'sessions' => ExamSession::orderBy('name')->pluck('name', 'id'),
            'rows' => static::rows($this->exam_session_id),
        ];
    }
}
