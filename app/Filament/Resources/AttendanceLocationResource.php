<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceLocationResource\Pages;
use App\Filament\Resources\AttendanceLocationResource\RelationManagers;
use App\Models\AttendanceLocation;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\VerticalAlignment;

class AttendanceLocationResource extends Resource
{
    protected static ?string $model = AttendanceLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label =   'Tabel Lokasi Kehadiran';

    protected static ?string $navigationLabel = 'Lokasi Kehadiran';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Manajemen Kehadiran';

    public static function form(Form $form): Form
    {

        Actions::make([
            Action::make('Set Default Location')
                ->icon('heroicon-m-map-pin')
                ->action(function (Set $set, $state, $livewire): void {
                    $set('location', ['lat' => '52.35510989541003', 'lng' => '4.883422851562501']);
                    $set('latitude', '52.35510989541003');
                    $set('longitude', '4.883422851562501');
                    $livewire->dispatch('refreshMap');
                })
        ])->verticalAlignment(VerticalAlignment::Start);

        return $form
            ->schema([

                FileUpload::make('photo')
                    ->label('Gambar')
                    ->directory('lokasi_absensi')
                    ->minSize(24)
                    ->maxSize(1024 * 10)
                    ->image()
                    ->required()
                    ->columnSpanFull()
                    ->imageEditor(),

                TextInput::make('name')
                    ->label('Nama Tempat')
                    ->required()
                    ->minLength(3)
                    ->maxLength(80),

                TextInput::make('address')
                    ->label('Alamat')
                    ->required()
                    ->minLength(3)
                    ->maxLength(199),


                Fieldset::make('Aturan Absensi')
                    ->columns(2)
                    ->schema([

                        TextInput::make('allowance')
                            ->label('Toleransi Waktu Absensi')
                            ->minLength(0)
                            ->default(15)
                            ->suffix('Menit')
                            ->maxLength(99999)
                            ->required(),

                        TextInput::make('radius')
                            ->label('Radius Lokasi Absensi')
                            ->minLength(0)
                            ->default(20)
                            ->suffix('Meter')
                            ->maxLength(99999)
                            ->required(),

                        TimePicker::make('time_in')
                            ->label('Waktu Masuk')
                            ->format('H:i:s')
                            ->timezone('Asia/Makassar')
                            ->required(),


                        TimePicker::make('time_out')
                            ->label('Waktu Keluar')
                            ->format('H:i:s')
                            ->timezone('Asia/Makassar')
                            ->required(),


                        Select::make('color')
                            ->label('Warna Penanda')
                            ->columnSpanFull()
                            ->options([
                                'red' => 'Merah',
                                'green' => 'Hijau',
                                'yellow' => 'Kuning',
                                'orange' => 'Oranye',
                                'blue' => 'Biru',
                                'black' => 'Hitam',
                                'grey' => 'Abu-abu',
                                'violet' => 'Ungu'
                            ])
                            ->suffixIcon('heroicon-m-globe-alt')
                            ->required(),
                    ]),


                Fieldset::make('Peta Lokasi')
                    ->schema([
                        TextInput::make('latitude')
                            ->label('Kordinat Lintang ( Latitude )')
                            ->required(),

                        TextInput::make('longitude')
                            ->label('Kordinat Bujut ( Longitude )')
                            ->required(),

                        Map::make('location')
                            ->disabled()
                            ->label('Pilih Lokasi')
                            ->columnSpanFull()
                            ->default([
                                'lat' => 1.226580,
                                'lng' => 124.819360
                            ])
                            ->afterStateUpdated(function (Set $set, ?array $state): void {
                                $set('latitude', $state['lat']);
                                $set('longitude', $state['lng']);
                            })
                            ->afterStateHydrated(function ($state, $record, Set $set): void {
                                if (is_null($record)) return;
                                $set('location', ['lat' => $record->latitude, 'lng' => $record->longitude]);
                            })
                            ->extraStyles([
                                'min-height: 40vh',
                                'border-radius: 10px'
                            ])
                            ->liveLocation()
                            ->showMarker()
                            ->markerColor("#FF0000")
                            ->showFullscreenControl()
                            ->showZoomControl()
                            ->showMyLocationButton()
                            ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                            ->zoom(15)
                            ->draggable(true)
                            ->detectRetina()
                            ->extraTileControl([])
                            ->extraControl([
                                'zoomDelta'           => 1,
                                'zoomSnap'            => 2,
                            ]),

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('photo')
                    ->label('Gambar')
                    ->defaultImageUrl('/images/no-image.avif'),

                TextColumn::make('name')
                    ->label('Nama Tempat')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('address')
                    ->label('Alamat')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->label('Diubah Pada')
                    ->sortable()
                    ->since()
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->sortable()
                    ->date('d/m/Y H:i')
                    ->toggleable()
                    ->searchable(),


            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListAttendanceLocations::route('/'),
            'create' => Pages\CreateAttendanceLocation::route('/create'),
            'edit' => Pages\EditAttendanceLocation::route('/{record}/edit'),
        ];
    }
}
