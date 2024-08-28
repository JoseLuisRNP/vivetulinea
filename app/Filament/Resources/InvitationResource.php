<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvitationResource\Pages;
use App\Filament\Resources\InvitationResource\RelationManagers;
use App\Models\Invitation;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Sin activar App';
    protected static ?string $pluralLabel = 'Sin activar App';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->label('Nombre'),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->rules('required', 'numeric')
                    ->label('Teléfono'),
                Forms\Components\TextInput::make('daily_points')->default(30)->numeric()->inputMode('decimal')->label('Puntos diarios'),
                Forms\Components\TextInput::make('sugars')->default(13.5)->numeric()->inputMode('decimal')->label('Hidratos'),
                Forms\Components\TextInput::make('proteins')->default(10.5)->numeric()->inputMode('decimal')->label('Proteínas'),
                Forms\Components\TextInput::make('fats')->default(6)->numeric()->inputMode('decimal')->label('Grasas'),
                Forms\Components\TextInput::make('weekly_points')->default(30)->numeric()->inputMode('decimal')->label('Extras semanales'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')->label('Teléfono')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('dietician.name')->label('Dietista'),

            ])
            ->filters([
                Tables\Filters\Filter::make('Solo mis invitaciones')->query( fn (Builder $query) => $query->where('dietician_id', auth()->user()->id))->toggle()->label('Solo mis invitaciones'),
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
            'index' => Pages\ListInvitations::route('/'),
            'create' => Pages\CreateInvitation::route('/create'),
            'edit' => Pages\EditInvitation::route('/{record}/edit'),
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
