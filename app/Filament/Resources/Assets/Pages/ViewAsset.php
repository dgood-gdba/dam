<?php

namespace App\Filament\Resources\Assets\Pages;

use App\Filament\Pages\AssetManagement;
use App\Filament\Resources\Assets\AssetResource;
use App\Models\Directory;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAsset extends ViewRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('download')
                ->label('Download')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('success')
                ->url(function () {
                    $record = $this->getRecord();
                    return url(\Storage::url($record->path));
                })
        ];
    }

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
