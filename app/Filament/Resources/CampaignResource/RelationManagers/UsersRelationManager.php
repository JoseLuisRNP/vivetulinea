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
use Filament\Tables\Filters\SelectFilter;

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
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de registro')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('dietician.name')
                    ->label('Dietista')
            ])
            ->filters([
                SelectFilter::make('dietician_id')
                    ->label('Dietista')
                    ->options(
                        collect(['sin_asignar' => 'Sin asignar'])
                            ->union(User::whereIn('role', ['dietician', 'admin'])->pluck('name', 'id'))
                    )
                    ->query(function (Builder $query, array $data): Builder {
                        // dd($data);
                        if (!isset($data['value']) || $data['value'] === null || !$data['value']) {
                            return $query;
                        }
                        
                        if ($data['value'] === 'sin_asignar') {
                            return $query->whereNull('dietician_id');
                        }
                        
                        return $query->where('dietician_id', $data['value']);
                    })
                    ->preload()
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

