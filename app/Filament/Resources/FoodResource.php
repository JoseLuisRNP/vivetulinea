<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\FoodResource\Pages\ListFood;
use App\Filament\Resources\FoodResource\Pages\CreateFood;
use App\Filament\Resources\FoodResource\Pages\EditFood;
use App\Filament\Resources\FoodResource\Pages;
use App\Filament\Resources\FoodResource\RelationManagers;
use App\Models\Food;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FoodResource extends Resource
{
    protected static ?string $model = Food::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->name('Nombre')
                    ->autofocus()
                    ->required()
                    ->maxLength(50)
                    ->placeholder('Nombre del alimento'),
                TextInput::make('points')
                    ->name('Puntos')
                    ->required()
                    ->numeric()
                    ->placeholder('Puntos'),
                TextInput::make('quantity')
                    ->name('Cantidad')
                    ->required()
                    ->numeric()
                    ->placeholder('100'),
                TextInput::make('unit')
                    ->name('Unidad')
                    ->default('gr')
                    ->placeholder('gr'),
                Select::make('color')
                    ->required()
                    ->options([
                        'green' => 'Verde',
                        'blue' => 'Azul',
                        'red' => 'Rojo',
                        'yellow' => 'Amarillo',
                        'black' => 'Negro'
                    ]),
                Toggle::make('no_count')->label('Alimento día de no contar'),
                Toggle::make('special_no_count')->label('Alimento max 3 del día de no contar (rosa)'),
                Toggle::make('oil_no_count')->label('Alimento max 2 del día de no contar (verde)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('points')->alignCenter()->label('Puntos'),
                TextColumn::make('quantity')->alignCenter()->label('Cantidad en gramos'),
                TextColumn::make('unit')->alignCenter()->label('Unidad'),
                ColorColumn::make('color')->alignCenter()
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListFood::route('/'),
            'create' => CreateFood::route('/create'),
            'edit' => EditFood::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Alimentos';
    }
}
