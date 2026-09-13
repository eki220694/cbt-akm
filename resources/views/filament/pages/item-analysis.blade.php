<x-filament-panels::page>
    <div class="mb-4 max-w-sm">
        <label class="text-sm font-medium" for="item-session">Sesi ujian</label>
        <select id="item-session" wire:model.live="exam_session_id" class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm dark:border-white/10 dark:bg-white/5">
            <option value="">-- Pilih sesi --</option>
            @foreach ($sessions as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>

    @if ($rows->isEmpty())
        <p class="text-sm text-gray-500">Pilih sesi untuk melihat % benar tiap butir soal.</p>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left">
                    <th class="py-2 pr-4">Soal #</th>
                    <th class="py-2 pr-4">Soal</th>
                    <th class="py-2 pr-4">Responden</th>
                    <th class="py-2 pr-4">% Benar</th>
                    <th class="py-2">Rata-rata poin</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr class="border-b">
                        <td class="py-2 pr-4">{{ $row->question_id }}</td>
                        <td class="py-2 pr-4">{{ $row->question ? \Illuminate\Support\Str::limit(strip_tags($row->question->content), 60) : '-' }}</td>
                        <td class="py-2 pr-4">{{ $row->total }}</td>
                        <td class="py-2 pr-4">{{ number_format((float) $row->pct_correct * 100, 1) }}%</td>
                        <td class="py-2">{{ number_format((float) $row->avg_points, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</x-filament-panels::page>
