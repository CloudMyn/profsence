<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label =   'Tabel Pengguna';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Pengguna';

    public static function canAccess(): bool
    {
        return \App\Models\User::isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('name')
                    ->label('Nama Lenkap')
                    ->required()
                    ->minLength(3)
                    ->maxLength(199),


                TextInput::make('email')
                    ->label('Alamat Email')
                    ->required()
                    ->email()
                    ->minLength(3)
                    ->maxLength(199),

                Select::make('role')
                    ->label('Peran Pengguna')
                    ->required()
                    ->columnSpanFull()
                    ->options(['dosen' => 'Dosen', 'admin' => 'Admin']),


                Fieldset::make('Privasi')->schema([

                    TextInput::make('password')
                        ->label('Katasandi')
                        ->password()
                        ->revealable()
                        ->required(function ($record) {
                            return !$record;
                        })
                        ->minLength(3)
                        ->maxLength(199),

                    TextInput::make('confirm_password')
                        ->label('Konfirmasi Katasandi')
                        ->revealable()
                        ->same('password'),

                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->formatStateUsing(fn(User $record): string => ucwords($record->name))
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
