<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ViewUserDiary;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->rules('required', 'numeric', 'digits:9')
                    ->label('Teléfono'),
                TextInput::make('daily_points')->numeric()->label('Puntos diarios'),
                TextInput::make('sugars')->numeric()->label('Hidratos'),
                TextInput::make('proteins')->numeric()->label('Proteínas'),
                TextInput::make('fats')->numeric()->label('Grasas'),
                TextInput::make('weekly_points')->numeric()->label('Extras semanales'),
                TextInput::make('target_weight')->numeric()->label('Peso objetivo'),
                Checkbox::make('is_actived')->label('Activo')
                    ->label('Activo'),
                DatePicker::make('created_at')->native(false)->displayFormat('d/m/Y')->label('Fecha de alta'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable(),
                TextColumn::make('email')->label('Teléfono')->searchable(),
                TextColumn::make('dietician.name')->label('Dietista'),
                IconColumn::make('is_actived')
                    ->label('Activo')
                    ->icon(fn (string $state): string => match ($state) {
                        '1' => 'heroicon-o-check-circle',
                        '0' => 'heroicon-m-x-mark',
                    })
            ])
            ->filters([
                Filter::make('Solo mis socias')->query( fn (Builder $query) => $query->where('dietician_id', auth()->user()->id))->toggle()->label('Solo mis socias'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('Ver diario')
                    ->icon('heroicon-o-calendar-days')
                    ->url(fn (User $record): string => static::getUrl('diary', ['record' => $record]))
                    ->visible(fn (User $user): bool => $user->dietician_id === auth()->id() || auth()->user()->isSuperAdmin()),
                Action::make('Cambiar password')
                    ->icon('heroicon-o-key')
                    ->requiresConfirmation()
                    ->schema([TextInput::make('password')->label('Nueva contraseña')->required()])
                ->action(function (array $data, User $user): void {
                    $user->update([
                        'password' => Hash::make($data['password']),
                    ]);
                })
                ->visible(fn (User $user): bool => $user->dietician_id === auth()->id() || $user->id === auth()->id() || auth()->user()->isSuperAdmin()),
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
            'diary' => ViewUserDiary::route('/{record}/diary'),
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
