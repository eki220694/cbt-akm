<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Resources\Questions\QuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestion extends EditRecord
{
    protected static string $resource = QuestionResource::class;

    /**
     * Tombol aksi di bagian header (kanan atas)
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * PERBAIKAN: Mengarahkan kembali ke halaman daftar (index) setelah simpan
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * OPSIONAL: Memberikan notifikasi kustom saat berhasil disimpan
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Soal berhasil diperbarui, Pak!';
    }
}