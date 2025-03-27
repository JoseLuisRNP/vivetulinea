<?php

namespace App\Filament\Resources\CampaignResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use App\Models\User;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;

// Action Cambiar dietician
class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de registro')
                    ->date('d/m/Y'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Action::make('assignDietician')
                    ->label('Asignar Dietista')
                    ->visible(fn () => auth()->user()->isAdmin())
                    ->form([
                        Select::make('dietician_id')
                            ->label('Dietista')
                            ->options(
                                User::whereIn('role', ['dietician', 'admin'])->pluck('name', 'id')
                                )
                            ->required()
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update([
                            'dietician_id' => $data['dietician_id']
                        ]);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('assignDietician')
                        ->label('Asignar Dietista')
                        ->visible(fn () => auth()->user()->isAdmin())
                        ->form([
                            Select::make('dietician_id')
                                ->label('Dietista')
                                ->options(
                                    User::whereIn('role', ['dietician', 'admin'])->pluck('name', 'id')
                                    )
                                ->required()
                        ])

                        ->action(function (Collection $records, array $data): void {
                            $records->each(function (User $record) use ($data): void {
                                $record->update([
                                    'dietician_id' => $data['dietician_id']
                                ]);
                            });
                        })
                ]),
            ]);
    }
}

