<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

    protected static bool $canCreateAnother = false;


    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {

            $data['user_id']    =   auth()->user()->id;

            if (auth()->user()->permissions()->whereNull('approved_at')->count() >= 1) {
                throw new \Exception('Anda sudah memiliki perizinan yang belum disetujui!');
            }

            return $data;
        } catch (\Throwable $th) {

            Notification::make()
                ->title('Terjadi Keslahan')
                ->danger()
                ->body($th->getMessage())
                ->send();

            $this->halt();
        }
    }
}
