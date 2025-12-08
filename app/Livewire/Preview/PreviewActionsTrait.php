<?php

namespace App\Livewire\Preview;

use App\Filament\Resources\Assets\AssetResource;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\On;

trait PreviewActionsTrait
{
    use InteractsWithActions;

    #[On('editFile')]
    public function editFile(): void
    {
        redirect(AssetResource::getUrl('view', ['record' => $this->asset]));
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->label('Quick Edit Asset')
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

                \Storage::disk('private')->delete($this->asset->path);
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
                return url($this->asset->preview_url);
            });
    }

    public function selectItemAction(): Action
    {
        return Action::make('selectItem')
            ->label(function () {
                return $this->selected ? 'Deselect' : 'Select';
            })
            ->extraAttributes([
                'class' => 'w-full rounded-none text-left '
            ])
            ->color('success')
            ->action(function () {
                $this->selected = !$this->selected;
                $this->dispatch('toggleSelectedItem', $this->asset);
            });
    }

    public function shareItemAction(): Action
    {
        return Action::make('shareItem')
            ->label('Share')
            ->extraAttributes([
                'class' => 'w-full rounded-none text-left '
            ])
            ->color('success')
            ->action(function () {
                $url = URL::temporarySignedRoute(
                    'share.asset',
                    Carbon::now()->addMinutes(15), // expires in 15 minutes
                    ['asset' => $this->asset->id]
                );

                $this->js('navigator.clipboard.writeText("' . $url . '");console.log("Copied!");');


                Notification::make()
                    ->title('Shareable Link')
                    ->body('Shareable link copied to clipboard, if you need to manually copy it, here is the link: ' . $url)
                    ->success()
                    ->persistent() // keep it visible until closed
                    ->send();
            });
    }

}
