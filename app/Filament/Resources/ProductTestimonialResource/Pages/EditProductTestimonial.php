<?php

namespace App\Filament\Resources\ProductTestimonialResource\Pages;

use App\Filament\Resources\ProductTestimonialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductTestimonial extends EditRecord
{
    protected static string $resource = ProductTestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
