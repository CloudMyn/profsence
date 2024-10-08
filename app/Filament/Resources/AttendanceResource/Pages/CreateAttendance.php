<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected static bool $canCreateAnother = false;

    public function getTitle(): string | Htmlable
    {
        $label  =   request()->type === 'izin' ? 'Izin Kehadiran' : 'Absen Kehadiran';

        static::$title = $label;

        return __('filament-panels::resources/pages/create-record.title', [
            'label' => $label,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {

        if(isset($data['status_absent'])) return $data;

        $input_map  =   $data['input_map'];

        if (!$input_map['in_marker_radius']) {

            Notification::make()
                ->warning()
                ->title('Peringatan')
                ->body('Tidak dapat melakukan absensi, Lokasi anda tidak berada di dalam radius lokasi absensi!')
                ->send();

            $this->halt();
        }

        unset($data['input_map']);

        $data['latitude']       =   $input_map['lat'];
        $data['longitude']      =   $input_map['lng'];
        $data['location_id']    =   $input_map['location_id'];

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        try {

            $record = new ($this->getModel())($data);

            if (
                static::getResource()::isScopedToTenant() &&
                ($tenant = Filament::getTenant())
            ) {
                return $this->associateRecordWithTenant($record, $tenant);
            }

            $record->save();

            return $record;
        } catch (\Throwable $th) {

            report($th);

            Notification::make()
                ->warning()
                ->title('Terjadi Kesalahan')
                ->body($th->getMessage())
                ->send();

            $this->halt();
        }
    }
}
