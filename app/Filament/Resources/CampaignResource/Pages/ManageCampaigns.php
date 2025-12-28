<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\CampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCampaigns extends ManageRecords
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
