<?php

namespace App\Livewire;

use App\Models\Directory as DirectoryModel;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class Directory extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public int $directoryId;
    public DirectoryModel $directory;

    public function mount()
    {
        $this->directory = DirectoryModel::find($this->directoryId);
    }

    public function renameAction(): Action
    {
        return Action::make('rename')
            ->extraAttributes([
                'class' => 'w-full rounded-none text-black dark:text-white bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-800 text-left '
            ])
            ->label('Rename Directory')
            ->schema([
                TextInput::make('name')
            ])
            ->fillForm([
                'name' => $this->directory->name,
            ])
            ->action(function ($data) {
                $this->directory->name = $data['name'];
                $this->directory->save();
                $this->dispatch('closeContext');
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label('Delete Directory')
            ->requiresConfirmation()
            ->extraAttributes([
                'class' => 'w-full rounded-none text-left '
            ])
            ->color('danger')
            ->action(function () {
                $this->directory->delete();
                $this->dispatch('refresh');
                Notification::make()
                    ->title('Directory Deleted')
                    ->success()
                    ->send();
            });
    }

    public function render(): string
    {
        return <<<'HTML'
        <div
            class="w-full items-center"
            x-data="{
                contextMenu: false,
                menuX: 0,
                menuY: 0,
                openMenu(e) {
                    $dispatch('closeContext');
                    this.menuX = e.clientX;
                    this.menuY = e.clientY;
                    this.contextMenu = true;
                },
                closeMenu() {
                    this.contextMenu = false;
                }
            }"
            @click.away="closeMenu()"
            @contextmenu.prevent.stop="openMenu($event)"
            @closeContext.window="closeMenu()"
        >
            <div
                class="p-2 rounded-lg cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-800 select-none"
                wire:click="dispatch('openDirectory', { 'directoryId' :{{ $directory->id }} })"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                <div class="text-center">
                    {{ $directory->name }}
                </div>
            </div>

            <div
                x-show="contextMenu"
                x-cloak
                class="fixed z-50 w-48 rounded-md bg-white shadow-lg dark:bg-gray-700"
                :style="`top: ${menuY}px; left: ${menuX}px;`"
            >
                <div class="py-1" @click="closeMenu()">
                    {{ $this->rename }}
                    {{ $this->delete }}
                </div>
            </div>

            <x-filament-actions::modals />
        </div>
        HTML;
    }
}
