<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Filament\Widgets\AttendanceOverview;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AttendanceOverview::class,
        ];
    }


    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'Absen Masuk' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'check_in')),
            'Absen Keluar' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'check_out')),
        ];
    }
}
