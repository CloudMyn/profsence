<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Exports\AttendanceExporter;
use App\Filament\Resources\AttendanceResource;
use App\Filament\Widgets\AttendanceOverview;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Actions\ExportAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Builder;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-finger-print')
                ->label('Input Kehadiran'),

            ActionGroup::make([

                Actions\Action::make('pdf_pelanggaran')
                    ->label('Eksport PDF')
                    ->icon('heroicon-o-document-text')
                    ->url(route('pdf-export.pelanggaran', auth()->user()->id), true),

                ExportAction::make()
                    ->label('Eksport Excel')
                    ->icon('heroicon-o-document-chart-bar')
                    ->exporter(AttendanceExporter::class),
            ])
                ->label('Eksport Laporan')
                ->icon('heroicon-o-arrow-up-on-square-stack')
                ->color('info')
                ->button()
        ];
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
            'Izin Sakit' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'sakit')),
            'Izin Cuti' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'cuti')),
            'Izin Dinas' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'dinas_luar')),
        ];
    }
}
