<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->rules('required', 'numeric', 'digits:9')
                    ->label('Teléfono'),
                Forms\Components\TextInput::make('daily_points')->numeric()->label('Puntos diarios'),
                Forms\Components\TextInput::make('sugars')->numeric()->label('Hidratos'),
                Forms\Components\TextInput::make('proteins')->numeric()->label('Proteínas'),
                Forms\Components\TextInput::make('fats')->numeric()->label('Grasas'),
                Forms\Components\TextInput::make('weekly_points')->numeric()->label('Extras semanales'),
                Forms\Components\TextInput::make('target_weight')->numeric()->label('Peso objetivo'),
                Forms\Components\Checkbox::make('is_actived')->label('Activo')
                    ->label('Activo'),
                Forms\Components\DatePicker::make('created_at')->native(false)->displayFormat('d/m/Y')->label('Fecha de alta'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Teléfono')->searchable(),
                Tables\Columns\TextColumn::make('dietician.name')->label('Dietista'),
                Tables\Columns\IconColumn::make('is_actived')
                    ->label('Activo')
                    ->icon(fn (string $state): string => match ($state) {
                        '1' => 'heroicon-o-check-circle',
                        '0' => 'heroicon-m-x-mark',
                    })
            ])
            ->filters([
                Tables\Filters\Filter::make('Solo mis socias')->query( fn (Builder $query) => $query->where('dietician_id', auth()->user()->id))->toggle()->label('Solo mis socias'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Cambiar password')
                    ->icon('heroicon-o-key')
                    ->requiresConfirmation()
                    ->form([Forms\Components\TextInput::make('password')->label('Nueva contraseña')->required()])
                ->action(function (array $data, User $user): void {
                    $user->update([
                        'password' => Hash::make($data['password']),
                    ]);
                })
                ->visible(fn (User $user): bool => $user->dietician_id === auth()->id() || $user->id === auth()->id() || auth()->user()->isSuperAdmin()),
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

    public static function getModelLabel(): string
    {
        return 'Socias';
    }

//    public static function getEloquentQuery(): Builder
//    {
//        return parent::getEloquentQuery()->when(auth()->user()->isDietician(), function (Builder $query) {
//            $query->where('dietician_id', auth()->id());
//        });
//    }
}
