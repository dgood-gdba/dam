<?php

namespace App\Livewire\Preview;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\Action;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

trait PreviewActionsTrait
{
    #[On('editFile')]
    public function editFile(): void
    {
        redirect(AssetResource::getUrl('view', ['record' => $this->asset]));
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->label('Quick Edit Image')
            ->schema([
                TextInput::make('name')
                    ->label('File Name'),
                SpatieTagsInput::make('tags'),
            ])
            ->extraAttributes([
                'class' => 'w-full rounded-none text-black dark:text-white bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-800 text-left '
            ])
            ->action(function ($data) {
                dd($data);
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label('Delete Image')
            ->requiresConfirmation()
            ->extraAttributes([
                'class' => 'w-full rounded-none text-left '
            ])
            ->color('danger')
            ->action(function () {
                //We need to load this somehow now...

                \Storage::disk('public')->delete($this->asset->path);
                $this->asset->delete();

                $this->dispatch('refresh');
            });
    }

    public function downloadAction(): Action
    {
        return Action::make('download')
            ->label('Download')
            ->extraAttributes([
                'class' => 'w-full rounded-none text-left ',
                'download' => $this->asset->file_name . '.' . $this->asset->extension
            ])
            ->url(function () {
                return url(Storage::url($this->asset->path));
            });
    }
}
