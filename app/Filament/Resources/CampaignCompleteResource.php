<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignCompleteResource\Pages;
use App\Filament\Resources\CampaignResource\RelationManagers\UsersRelationManager;
use App\Models\Campaign;
use App\Models\CampaignComplete;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// HACER ACTION PARA CAMBIAR DIETICIAN
class CampaignCompleteResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Campañas';
    protected static ?string $pluralLabel = 'Campañas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->label('Nombre'),
                Forms\Components\TextInput::make('description')
                    ->label('Descripción'),
                Forms\Components\DatePicker::make('end_date')->native(false)->displayFormat('d/m/Y')->label('Fecha de finalización'),
                Forms\Components\Placeholder::make('url')
                    ->label('URL')
                    ->content(fn ($record): string => $record->url ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('url')->label('URL')->copyable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fecha de finalización')
                    ->date('d/m/Y'),

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
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaignCompletes::route('/'),
            'create' => Pages\CreateCampaignComplete::route('/create'),
            'edit' => Pages\EditCampaignComplete::route('/{record}/edit'),
        ];
    }
}
