<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    // Tambahkan fungsi ini agar setelah CREATE balik ke halaman list
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
