<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Pages\AssetManagement;
use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
//            DeleteAction::make(),
        ];
    }

    protected ?string $heading = '';

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();
        //Lets get our directories.
        $breadcrumbs = [
            '' => $record->file_name,
        ];
        $currentDirectory = $record->directory;
        if ($currentDirectory) {
            $directory = $currentDirectory;
            while ($directory) {
                $breadcrumbs[AssetManagement::getUrl(['directory' => $directory->id])] = $directory->name;
                $directory = $directory->parent;
            }
        }
        $breadcrumbs[AssetManagement::getUrl()] = 'Root';
        return array_reverse($breadcrumbs);
    }
}
