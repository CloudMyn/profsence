<?php

namespace App\Filament\Exports;

use App\Models\Permission;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PermissionExporter extends Exporter
{
    protected static ?string $model = Permission::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('type')
                ->label('Tipe'),
            ExportColumn::make('description')
                ->label('Deskripsi'),
            ExportColumn::make('start_date')
                ->label('Tanggal Mulai'),
            ExportColumn::make('end_date')
                ->label('Tanggal Selesai'),
            ExportColumn::make('duration')
                ->label('Durasi'),
            ExportColumn::make('approved_at')
                ->label('Tanggal Disetujui'),
            ExportColumn::make('approved_by')
                ->label('Disetujui Oleh'),
            ExportColumn::make('user_id')
                ->label('Pengguna'),
            ExportColumn::make('created_at')
                ->label('Tanggal Dibuat'),
            ExportColumn::make('updated_at')
                ->label('Tanggal Diubah'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor izin Anda telah selesai dan ' . number_format($export->successful_rows) . ' ' . str('baris')->plural($export->successful_rows) . ' berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diekspor.';
        }

        return $body;
    }
}
