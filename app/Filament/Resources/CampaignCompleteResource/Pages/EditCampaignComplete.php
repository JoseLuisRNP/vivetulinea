<?php

namespace App\Filament\Resources\CampaignCompleteResource\Pages;

use App\Filament\Resources\CampaignCompleteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampaignComplete extends EditRecord
{
    protected static string $resource = CampaignCompleteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
