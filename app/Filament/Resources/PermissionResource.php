<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use App\Models\Attendance;
use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $label =   'Tabel Izin';

    protected static ?string $navigationLabel = 'Izin Kehadiran';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Manajemen Kehadiran';

    public static function getNavigationGroup(): ?string
    {
        return User::isDosen() ? null : static::$navigationGroup;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = static::getModel()::query()->orderBy('created_at', 'DESC');

        if (\App\Models\User::isDosen()) {
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

            NavigationItem::make('Input izin')
                ->url(static::getUrl('create'))
                ->icon('heroicon-o-pencil-square')
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->visible(function () {
                    return User::isDosen();
                })
                ->isActiveWhen(function () {
                    return request()->routeIs(static::getRouteBaseName() . '.create');
                })
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->badgeTooltip(static::getNavigationBadgeTooltip())
                ->sort(3),

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
                ->sort(5)
                ->url(static::getUrl('index')),
        ];
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::getFormIzin());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('#')
                    ->rowIndex(isFromZero: false),


                ImageColumn::make('document_proof')
                    ->label('Bukti Dokumen'),

                TextColumn::make('type')
                    ->label('Jenis')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('description')
                    ->label('Catatan')
                    ->sortable()
                    ->limit(65),

                TextColumn::make('start_date')
                    ->label('Dari Tanggal')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('end_date')
                    ->label('Sampai Tanggal')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('duration')
                    ->label('Durasi')
                    ->sortable()
                    ->suffix(' Hari'),


                TextColumn::make('approved_at')
                    ->label('Di Approve Pada')
                    ->sortable()
                    ->default('Menunggu Persetujuan')
                    ->icon(function ($state) {
                        return ($state == 'Menunggu Persetujuan') ? 'heroicon-o-clock' : 'heroicon-o-check';
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->sortable()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\Action::make('setujui')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(function ($record) {
                        return \App\Models\User::isAdmin() && !$record->approved_at;
                    })
                    ->action(function ($record) {
                        try {

                            DB::beginTransaction();

                            $user   =   auth()->user();

                            $record->update([
                                'approved_at' => Carbon::now(),
                                'approved_by' => $user->name,
                            ]);

                            for ($i = 0; $i < $record->duration; $i++) {

                                $start_date = Carbon::parse($record->start_date)->addDays($i);

                                Attendance::create([
                                    'user_id'   =>  $record->user_id,
                                    'photo'     =>  $record->document_proof,
                                    'type'      =>  $record->type,
                                    'note'      =>  $record->description,
                                    'date'      =>  $start_date->format('Y-m-d'),
                                    'time'      =>  $start_date->format('H:i:s'),
                                ]);
                            }

                            Notification::make()
                                ->title('Berhasil')
                                ->success()
                                ->body('Persetujuan Izin Berhasil di approve')
                                ->send();

                            Notification::make()
                                ->title('Approval izin')
                                ->success()
                                ->body('Persetujuan Izin anda telah di setujui oleh ' . $user->name)
                                ->sendToDatabase($record->user, true);

                            DB::commit();
                        } catch (\Throwable $th) {

                            report($th);

                            Notification::make()
                                ->title('Gagal')
                                ->danger()
                                ->body('Terdapat kesalahah!!, Persetujuan Izin gagal di lakukan Error : ' . $th->getMessage())
                                ->send();

                            DB::rollBack();
                        }
                    })
                    ->icon('heroicon-o-check-circle'),

                Tables\Actions\Action::make('tolak')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(function ($record) {
                        return \App\Models\User::isAdmin() && !$record->approved_at;
                    })
                    ->action(function ($record) {
                        try {

                            DB::beginTransaction();

                            $user   =   auth()->user();

                            $record->delete();

                            Notification::make()
                                ->title('Berhasil')
                                ->success()
                                ->body('Persetujuan Izin Berhasil di tolak')
                                ->send();

                            Notification::make()
                                ->title('Approval izin')
                                ->success()
                                ->body('Persetujuan Izin anda telah di tolak oleh ' . $user->name)
                                ->sendToDatabase($record->user, true);

                            DB::commit();
                        } catch (\Throwable $th) {

                            report($th);

                            Notification::make()
                                ->title('Gagal')
                                ->danger()
                                ->body('Terdapat kesalahah!!, Persetujuan Izin gagal di lakukan Error : ' . $th->getMessage())
                                ->send();

                            DB::rollBack();
                        }
                    })
                    ->icon('heroicon-o-x-circle'),

                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    public static function getFormIzin(): array
    {
        return [
            FileUpload::make('document_proof')
                ->label('Bukti Izin berupa Surat Sakit/Surat Dinas')
                ->image()
                ->directory('bukti_kehadiran')
                ->required()
                ->columnSpanFull()
                ->imageEditor()
                ->minSize(12)
                ->maxSize(1024  * 10),

            Select::make('type')
                ->label('Jenis Izin')
                ->required()
                ->options([
                    'cuti' => 'Cuti',
                    'sakit' => 'Sakit',
                    'dinas_luar' => 'Dinas Keluar Kota'
                ]),

            TextInput::make('description')
                ->label('Keterangan izin')
                ->required()
                ->minLength(3)
                ->maxLength(255),

            Fieldset::make('Tanggal Izin')
                ->columns(3)
                ->schema([

                    DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $record, Set $set, Get $get): void {

                            if (!$get('end_date')) {

                                $set('duration', 1);
                                return;
                            }

                            $start_date = Carbon::parse($get('start_date'));

                            $end_date = Carbon::parse($get('end_date'));

                            if ($end_date->isBefore($start_date)) {

                                $set('end_date', $start_date->format('Y-m-d'));

                                $set('duration', 1);
                                return;
                            }

                            $duration = $start_date->diffInDays($end_date) + 1;

                            $set('duration', $duration);
                        })
                        ->minDate(now()->format('Y-m-d')),

                    DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $record, Set $set, Get $get): void {

                            if (!$get('end_date')) {

                                $set('duration', 1);
                                return;
                            }

                            $start_date = Carbon::parse($get('start_date'));

                            $end_date = Carbon::parse($get('end_date'));

                            if ($end_date->isBefore($start_date)) {

                                $set('end_date', $start_date->format('Y-m-d'));

                                $set('duration', 1);
                                return;
                            }

                            $duration = $start_date->diffInDays($end_date) + 1;

                            $set('duration', $duration);
                        })
                        ->minDate(function (Get $get) {
                            return $get('start_date') ?? now();
                        }),

                    TextInput::make('duration')
                        ->label('Durasi Izin')
                        ->required()
                        ->numeric()
                        ->integer()
                        ->readOnly(),
                ])
        ];
    }
}
