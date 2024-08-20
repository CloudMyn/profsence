<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Exports\PermissionExporter;
use App\Filament\Resources\PermissionResource;
use App\Filament\Resources\PermissionResource\Widgets\PermissionOverview;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Input Izin'),
            ExportAction::make()
                ->label('Eksport Data Izin')
                ->exporter(PermissionExporter::class),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PermissionOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'Sudah Di Approve' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('approved_at')),
            'Belum Di Approve' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('approved_at')),
        ];
    }
}
