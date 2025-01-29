<?php

namespace App\Filament\Resources\FAQAccordionResource\Pages;

use App\Filament\Resources\FAQAccordionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFAQAccordions extends ManageRecords
{
    protected static string $resource = FAQAccordionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
