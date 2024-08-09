<?php

namespace App\Filament\Resources\AttendanceLocationResource\Pages;

use App\Filament\Resources\AttendanceLocationResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Support\Enums\VerticalAlignment;

class CreateAttendanceLocation extends CreateRecord
{
    protected static string $resource = AttendanceLocationResource::class;

    protected static ?string $title = 'Tambah Lokasi Kehadiran';

    protected static bool $canCreateAnother = false;

    protected function getActions(): array
    {
        return [];
    }
}
