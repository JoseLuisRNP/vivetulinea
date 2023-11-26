<?php

namespace App\Filament\Resources\InvitationResource\Pages;

use App\Filament\Resources\InvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInvitation extends CreateRecord
{
    protected static string $resource = InvitationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create([...$data, 'dietician_id' => auth()->id()]);
    }
}
