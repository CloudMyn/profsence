<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceOverview extends BaseWidget
{

    public static function canView(): bool
    {
        return true;
    }


    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {

        if (User::isAdmin()) {

            $attendance_in  =   Attendance::where('type', 'check_in')->count();
            $attendance_out =   Attendance::where('type', 'check_out')->count();

        } else {

            $attendance_in  =   Attendance::where('user_id', auth()->user()->id)->where('type', 'check_in')->count();
            $attendance_out =   Attendance::where('user_id', auth()->user()->id)->where('type', 'check_out')->count();

        }

        return [
            Stat::make('Absen Masuk', $attendance_in)
                ->icon('heroicon-o-check-circle'),
            Stat::make('Absen Keluar', $attendance_out)
                ->icon('heroicon-o-check-circle'),
        ];
    }
}
