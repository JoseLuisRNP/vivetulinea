<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\InvitationResource\Pages\ListInvitations;
use App\Filament\Resources\InvitationResource\Pages\CreateInvitation;
use App\Filament\Resources\InvitationResource\Pages\EditInvitation;
use App\Filament\Resources\InvitationResource\Pages;
use App\Filament\Resources\InvitationResource\RelationManagers;
use App\Models\Invitation;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Sin activar App';
    protected static ?string $pluralLabel = 'Sin activar App';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->label('Nombre'),
                TextInput::make('email')
                    ->required()
                    ->rules('required', 'numeric')
                    ->label('Teléfono'),
                TextInput::make('daily_points')->default(30)->numeric()->inputMode('decimal')->label('Puntos diarios'),
                TextInput::make('sugars')->default(13.5)->numeric()->inputMode('decimal')->label('Hidratos'),
                TextInput::make('proteins')->default(10.5)->numeric()->inputMode('decimal')->label('Proteínas'),
                TextInput::make('fats')->default(6)->numeric()->inputMode('decimal')->label('Grasas'),
                TextInput::make('weekly_points')->default(30)->numeric()->inputMode('decimal')->label('Extras semanales'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')->label('Teléfono')->searchable()->sortable(),
                TextColumn::make('dietician.name')->label('Dietista'),

            ])
            ->filters([
                Filter::make('Solo mis invitaciones')->query( fn (Builder $query) => $query->where('dietician_id', auth()->user()->id))->toggle()->label('Solo mis invitaciones'),
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
            'index' => ListInvitations::route('/'),
            'create' => CreateInvitation::route('/create'),
            'edit' => EditInvitation::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'nuevo';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->when(auth()->user()->isDietician(), function (Builder $query) {
            $query->where('dietician_id', auth()->id());
        });
    }
}
