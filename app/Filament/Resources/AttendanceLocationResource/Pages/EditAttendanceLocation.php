<?php

namespace App\Filament\Resources\AttendanceLocationResource\Pages;

use App\Filament\Resources\AttendanceLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceLocation extends EditRecord
{
    protected static string $resource = AttendanceLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
