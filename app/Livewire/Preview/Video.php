<?php

namespace App\Livewire\Preview;

use App\Models\Asset;
use App\Models\Directory;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Attributes\On;
use Livewire\Component;

class Video extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms, PreviewActionsTrait;

    public ?Asset $asset;
    public ?Directory $directory;
    public $assetId;
    public bool $selected = false;

    public function mount(): void
    {
        $this->asset = Asset::findOrFail($this->assetId);
        $this->directory = $this->asset->directory;
    }

    public function updatedAssetId($value): void
    {
        $this->asset = Asset::find($value);
        $this->directory = $this->asset?->directory;
    }

    #[On('refresh')]
    public function onRefresh(): void
    {
        $this->asset = Asset::find($this->assetId);
        $this->directory = $this->asset?->directory;
    }

    public function render(): string
    {

        return <<<'HTML'
        <div
            class="w-full items-center {{$selected ? 'border' : '' }}"
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
            class="{{$selected ? 'border' : '' }}"
        >
            <div
                class="mx-auto p-2 rounded-lg cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-800 select-none"
                wire:click="dispatchSelf('editFile')"
            >
                   <div class="mx-auto p-2 max-w-full flex flex-col items-center justify-center">
                        <video
                            src="{{ url($asset->preview_url) }}"
                            class="w-40 h-28 rounded bg-black object-cover"
                            preload="metadata"
                            muted
                            playsinline
                            controls
                            controlsList="nodownload, noplaybackspeed, noremoteplayback"
                        ></video>
                    </div>
                    <div class="text-center max-w-full truncate">
                        {{ $asset->file_name }}
                    </div>
            </div>

            <div
                x-show="contextMenu"
                x-cloak
                class="fixed z-50 w-48 rounded-md bg-white shadow-lg dark:bg-gray-700"
                :style="`top: ${menuY}px; left: ${menuX}px;`"
            >
                <div class="py-1" @click="closeMenu()">
                    {{ $this->edit }}
                    {{ $this->download }}
                    {{ $this->selectItem }}
                    {{ $this->shareItem }}
                    {{ $this->delete }}
                </div>
            </div>

            <x-filament-actions::modals />
        </div>
        HTML;
    }
}
