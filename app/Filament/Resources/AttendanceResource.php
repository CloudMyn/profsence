<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Forms\Components\MapInput;
use App\Models\Attendance;
use App\Models\AttendanceLocation;
use App\Models\User;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label =   'Tabel Kehadiran';

    protected static ?string $navigationLabel = 'Kehadiran Pengguna';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Manajemen Kehadiran';


    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return User::isAdmin();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query()->orderBy('created_at', 'DESC');

        if (User::isDosen()) {
            $query->where('user_id', auth()->user()->id);
        }

        return $query;
    }

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Input Kehadiran')
                ->url(static::getUrl('create'))
                ->icon('heroicon-o-plus')
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn() => request()->routeIs(static::getRouteBaseName() . '.create'))
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->sort(static::getNavigationSort()),

            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(function () {
                    return request()->routeIs(static::getRouteBaseName() . '.*') && !request()->routeIs(static::getRouteBaseName() . '.create');
                })
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->sort(static::getNavigationSort())
                ->url(static::getNavigationUrl()),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                FileUpload::make('photo')
                    ->label('Bukti Kehadiran')
                    ->image()
                    ->directory('bukti_kehadiran')
                    ->required()
                    ->columnSpanFull()
                    ->imageEditor()
                    ->minSize(12)
                    ->maxSize(1024  * 10),

                TextInput::make('note')
                    ->label('Catatan')
                    ->minLength(3)
                    ->maxLength(255),

                TextInput::make('status_')
                    ->label('Status Absensi')
                    ->hiddenOn('view')
                    ->disabled(),


                TextInput::make('type')
                    ->label('Jenis Absensi')
                    ->disabled()
                    ->hiddenOn('create'),

                TextInput::make('violation_note')
                    ->label('Pelanggaran Absensi')
                    ->columnSpanFull()
                    ->afterStateHydrated(function ($record) {
                        return "aa";
                    })
                    ->disabled()
                    ->hiddenOn('create'),

                MapInput::make('input_map')
                    ->label('Lokasi Saya')
                    ->hiddenOn('view')
                    ->default([
                        'lat' => 1.226580,
                        'lng' => 124.819360,
                        'in_marker_radius'  =>  false,
                    ])
                    ->afterStateUpdated(function (Set $set, ?array $state): void {

                        if ($state['in_marker_radius']) {
                            $location_name  =   AttendanceLocation::find($state['location_id'])->name;

                            $status = 'Anda berada di dalam zona absensi ' . $location_name;
                        } else {
                            $status = 'Anda tidak berada di dalam zona absensi!';
                        }

                        $set('status_', ucwords($status));
                    })
                    ->afterStateHydrated(function ($state, $record, Set $set): void {
                        if (is_null($record)) return;
                        $set('location', ['lat' => $record->latitude, 'lng' => $record->longitude]);
                    })
                    ->setHeight('40vh')
                    ->draggable(false)
                    ->setMarkers(AttendanceLocation::getLocations())
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('user.name')
                    ->label("Nama Pengguna")
                    ->sortable()
                    ->searchable(),

                TextColumn::make('type')
                    ->label("Jenis Absensi")
                    ->state(function ($record) {
                        return $record->type === 'check_in' ? 'Absen Masuk' : 'Absen Keluar';
                    })
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Absen Masuk'  => 'info',
                        'Absen Keluar' => 'danger',
                    })
                    ->searchable(),

                TextColumn::make('check_violation')
                    ->label("Pelanggaran Absensi")
                    ->state(function ($record) {
                        return $record->check_violation === true ? 'Terdapat Pelanggaran' : 'Tidak Ada Pelanggaran';
                    })
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Tidak Ada Pelanggaran' => 'success',
                        'Terdapat Pelanggaran'  => 'danger',
                    }),

                TextColumn::make('time')
                    ->label('Jam Input')
                    ->state(function ($record) {
                        $date   =   Carbon::parse($record->time)->setTimezone('Asia/Makassar')->isoFormat('hh:mm a');
                        return ucwords($date);
                    }),

                TextColumn::make('date')
                    ->label('Tanggal Kehadiran')
                    ->state(function ($record) {
                        $date   =   Carbon::parse($record->date)->locale('id')->isoFormat('dddd, D MMMM YYYY');
                        return ucwords($date);
                    }),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
