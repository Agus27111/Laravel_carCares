<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\CarStore;
use Filament\Forms\Form;
use App\Models\CarService;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use App\Filament\Resources\CarStoreResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CarStoreResource\RelationManagers;
use App\Filament\Resources\CarStoreResource\RelationManagers\StorePhotosRelationManager;

class CarStoreResource extends Resource
{
    protected static ?string $model = CarStore::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('thumbnail')
                    ->image()
                    ->required(),
                Forms\Components\Select::make('is_open')
                    ->options([
                        true=>'Open',
                        false=>'Not Open',
                    ])
                    ->required(),
                Forms\Components\Select::make('is_full')
                    ->options([
                        true=>'Full Booked',
                        false=>'Avaluable',
                    ])
                    ->required(),
                TinyEditor::make('address')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('phone_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('cs_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('city_id')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                 // 🛠 ALTERNATIF UNTUK STORE PHOTOS
                Repeater::make('storePhotos')
                ->relationship('storePhotos') // Pastikan relasi ini sesuai dengan model
                ->schema([
                    Forms\Components\FileUpload::make('photo')
                        ->image()
                        ->required()
                        ->openable(),
                ])
                ->columns(1) // Opsional, untuk tata letak
                ->collapsible(), // Agar lebih rapi

                // 🛠 ALTERNATIF UNTUK STORE SERVICES
                Repeater::make('storeServices')
                    ->relationship('storeServices')
                    ->schema([
                        Forms\Components\Select::make('car_service_id')
                            ->relationship('carService', 'name')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_open')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->label('Buka?'),
                Tables\Columns\IconColumn::make('is_full')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->label('Tersedia?'),
                Tables\Columns\TextColumn::make('city.name')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('city_id')
                ->label('City')
                    ->relationship('city', 'name'),

                SelectFilter::make('car_service_id')
                ->label('Service')
                ->options(CarService::pluck('name', 'id'))
                ->query(function (Builder $query, array $data) {
                    if($data['value']){
                        $query->whereHas('storeServices', function ($query) use ($data){
                            $query->where('car_service_id', $data['value']);
                        });
                    }
                })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarStores::route('/'),
            'create' => Pages\CreateCarStore::route('/create'),
            'edit' => Pages\EditCarStore::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
