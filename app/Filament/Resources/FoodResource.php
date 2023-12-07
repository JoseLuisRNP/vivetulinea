<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodResource\Pages;
use App\Filament\Resources\FoodResource\RelationManagers;
use App\Models\Food;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FoodResource extends Resource
{
    protected static ?string $model = Food::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->name('Nombre')
                    ->autofocus()
                    ->required()
                    ->maxLength(50)
                    ->unique(Food::class, 'name')
                    ->placeholder('Nombre del alimento'),
                TextInput::make('points')
                    ->name('Puntos')
                    ->required()
                    ->numeric()
                    ->placeholder('Puntos'),
                TextInput::make('quantity')
                    ->name('Cantidad en gramos')
                    ->required()
                    ->numeric()
                    ->placeholder('100'),
                Select::make('color')
                    ->required()
                    ->options([
                        'green' => 'Verde',
                        'blue' => 'Azul',
                        'red' => 'Rojo',
                        'yellow' => 'Amarillo',
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('points')->alignCenter()->label('Puntos'),
                Tables\Columns\TextColumn::make('quantity')->alignCenter()->label('Cantidad en gramos'),
                Tables\Columns\ColorColumn::make('color')->alignCenter()
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
            'index' => Pages\ListFood::route('/'),
            'create' => Pages\CreateFood::route('/create'),
            'edit' => Pages\EditFood::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Alimentos';
    }
}
