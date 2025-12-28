<?php

namespace App\Filament\Resources\CampaignResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;
use Filament\Tables\Filters\SelectFilter;

// Action Cambiar dietician
class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('email')->searchable(),
                TextColumn::make('created_at')
                    ->label('Fecha de registro')
                    ->date('d/m/Y'),
                TextColumn::make('dietician.name')
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
            ->recordActions([
                Action::make('assignDietician')
                    ->label('Asignar Dietista')
                    ->visible(fn () => auth()->user()->isAdmin())
                    ->schema([
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
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('assignDietician')
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

