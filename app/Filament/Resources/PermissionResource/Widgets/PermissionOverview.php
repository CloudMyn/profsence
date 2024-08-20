<?php

namespace App\Filament\Resources\PermissionResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PermissionOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $this_year = date('Y');

        if (\App\Models\User::isDosen()) {

            $user   =   auth()->user();

            $count_cuti         =   \App\Models\Permission::where('user_id', $user->id)->where('type', 'cuti')->whereYear('created_at', $this_year)->count();

            $count_sakit        =   \App\Models\Permission::where('user_id', $user->id)->where('type', 'sakit')->whereYear('created_at', $this_year)->count();

            $count_dinas_luar   =   \App\Models\Permission::where('user_id', $user->id)->where('type', 'dinas_luar')->whereYear('created_at', $this_year)->count();
        } else {


            $count_cuti         =   \App\Models\Permission::where('type', 'cuti')->whereYear('created_at', $this_year)->count();

            $count_sakit        =   \App\Models\Permission::where('type', 'sakit')->whereYear('created_at', $this_year)->count();

            $count_dinas_luar   =   \App\Models\Permission::where('type', 'dinas_luar')->whereYear('created_at', $this_year)->count();
        }

        if ($count_cuti >= 1) $count_cuti = $count_cuti . " Kali";
        if ($count_sakit >= 1) $count_sakit = $count_sakit . " Kali";
        if ($count_dinas_luar >= 1) $count_dinas_luar = $count_dinas_luar . " Kali";

        if ($count_cuti == 0) $count_cuti = 'Belum Ada';
        if ($count_sakit == 0) $count_sakit = 'Belum Ada';
        if ($count_dinas_luar == 0) $count_dinas_luar = 'Belum Ada';


        return [
            Stat::make('Perizinan Cuti', $count_cuti)->color('success'),
            Stat::make('Perizinan Sakit', $count_sakit)->color('success'),
            Stat::make('Perizinan Dinas', $count_dinas_luar)->color('success'),
        ];
    }
}
