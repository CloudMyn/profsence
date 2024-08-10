<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceLocation;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AppOverview extends BaseWidget
{

    public static function canView(): bool
    {
        return User::isAdmin();
    }

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $users = User::all()->count();

        $location   =   AttendanceLocation::all()->count();

        return [
            Stat::make('Jumlah Pengguna', $users . " Orang")->icon('heroicon-o-users')->color('success'),
            Stat::make('Total Lokasi Absen', $location . " Tempat")->icon('heroicon-o-map')->color('success'),
        ];
    }
}
