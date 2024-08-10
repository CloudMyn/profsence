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

    protected function getStats(): array
    {

        if (User::isAdmin()) {

            $attendance_in  =   Attendance::where('type', 'check_in')->count();
            $attendance_out =   Attendance::where('type', 'check_out')->count();

            $attendance_voilation = Attendance::where('check_violation', 1)->count();
        } else {

            $attendance_in  =   Attendance::where('user_id', auth()->user()->id)->where('type', 'check_in')->count();
            $attendance_out =   Attendance::where('user_id', auth()->user()->id)->where('type', 'check_out')->count();

            $attendance_voilation = Attendance::where('user_id', auth()->user()->id)->where('check_violation', 1)->count();
        }

        return [
            Stat::make('Absen Masuk', $attendance_in)
                ->icon('heroicon-o-check-circle'),
            Stat::make('Absen Keluar', $attendance_out)
                ->icon('heroicon-o-check-circle'),
            Stat::make('Pelanggaran Absen', $attendance_voilation . " Kali")
                ->icon('heroicon-o-exclamation-circle'),
        ];
    }
}
