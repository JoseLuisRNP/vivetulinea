<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\CampaignCompleteResource\Pages\ListCampaignCompletes;
use App\Filament\Resources\CampaignCompleteResource\Pages\CreateCampaignComplete;
use App\Filament\Resources\CampaignCompleteResource\Pages\EditCampaignComplete;
use App\Filament\Resources\CampaignCompleteResource\Pages;
use App\Filament\Resources\CampaignResource\RelationManagers\UsersRelationManager;
use App\Models\Campaign;
use App\Models\CampaignComplete;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CampaignCompleteResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Campañas';
    protected static ?string $pluralLabel = 'Campañas';

    public static function canCreate(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->label('Nombre'),
                TextInput::make('description')
                    ->label('Descripción'),
                TextInput::make('free_days')
                    ->label('Días gratis')
                    ->default(3)
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                DatePicker::make('end_date')->native(false)->displayFormat('d/m/Y')->label('Fecha de finalización'),
                Placeholder::make('url')
                    ->label('URL')
                    ->content(fn ($record): string => $record->url ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable(),
                TextColumn::make('url')->label('URL')->copyable(),
                TextColumn::make('end_date')
                    ->label('Fecha de finalización')
                    ->date('d/m/Y'),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => auth()->user()->isAdmin()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->isAdmin()),
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
            'index' => ListCampaignCompletes::route('/'),
            'create' => CreateCampaignComplete::route('/create'),
            'edit' => EditCampaignComplete::route('/{record}/edit'),
        ];
    }
}
