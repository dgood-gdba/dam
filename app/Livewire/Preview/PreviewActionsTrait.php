<?php

namespace App\Livewire\Preview;

use App\Filament\Resources\Assets\AssetResource;
use App\Services\AuditLogger;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
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
            ->label('Quick Edit')
            ->icon(Heroicon::Pencil)
            ->schema([
                TextInput::make('name')
                    ->label('File Name'),
                SpatieTagsInput::make('tags'),
            ])
            ->extraAttributes([
                'class' => 'w-full rounded-none outline-0 outline-none ring-0 box-shadow-none shadow-none',
            ])
            ->outlined()
            ->color('edit')
            ->action(function ($data) {
                dd($data);
            });
    }

    public function downloadAction(): Action
    {
        return Action::make('download')
            ->label('Download')
            ->extraAttributes([
                'class' => 'w-full rounded-none outline-0 outline-none ring-0 box-shadow-none shadow-none',
                'download' => $this->asset->file_name . '.' . $this->asset->extension,
            ])
            ->icon(Heroicon::CloudArrowDown)
            ->color('download')
            ->outlined()
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
                'class' => 'w-full rounded-none outline-0 outline-none ring-0 box-shadow-none shadow-none',
            ])
            ->color('select')
            ->outlined()
            ->icon(Heroicon::Square3Stack3d)
            ->action(function () {
                $this->selected = !$this->selected;
                $this->dispatch('toggleSelectedItem', $this->asset);
            });
    }

    #[On('processSelect')]
    public function processSelect(): void
    {
        $this->selected = !$this->selected;
        $this->dispatch('toggleSelectedItem', $this->asset);
    }

    public function shareItemAction(): Action
    {
        return Action::make('shareItem')
            ->label('Share')
            ->extraAttributes([
                'class' => 'w-full rounded-none outline-0 outline-none ring-0 box-shadow-none shadow-none',
            ])
            ->color('share')
            ->outlined()
            ->icon(Heroicon::Link)
            ->action(function () {
                $url = URL::temporarySignedRoute(
                    'share.asset',
                    Carbon::now()->addDays(30),
                    ['asset' => $this->asset->id]
                );

                $this->js('navigator.clipboard.writeText("' . $url . '");console.log("Copied!");');

                AuditLogger::log('shared_asset', $this->asset, ['asset' => $this->asset]);

                Notification::make()
                    ->title('Shareable Link')
                    ->body('Shareable link copied to clipboard, if you need to manually copy it, here is the link: ' . $url)
                    ->success()
                    ->persistent() // keep it visible until closed
                    ->send();
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label('Delete Image')
            ->requiresConfirmation()
            ->extraAttributes([
                'class' => 'w-full rounded-none outline-0 outline-none ring-0 box-shadow-none shadow-none',
            ])
            ->color('delete')
            ->outlined()
            ->icon(Heroicon::Trash)
            ->action(function () {
                //We need to load this somehow now...

                \Storage::disk('private')->delete($this->asset->path);
                $this->asset->delete();

                $this->dispatch('refresh');
            });
    }
}
