<?php

namespace App\Filament\Resources\CampaignCompleteResource\Pages;

use App\Filament\Resources\CampaignCompleteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCampaignCompletes extends ListRecords
{
    protected static string $resource = CampaignCompleteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
