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
    :key="'image-preview-' . $asset->id . '-' . now()->timestamp"
>
    <div
        class="mx-auto p-2 rounded-lg cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-800 select-none"
        wire:click="dispatchSelf('editFile')"
    >
        <img
            src="{{ url(\Storage::disk('public')->url($asset->path)) }}"
            alt="{{ $asset->file_name}}"
            class="mx-auto p-2 max-w-full"
        ><br>
        <div class="text-center max-w-full truncate">
            {{ $asset->file_name}}
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
            {{ $this->delete }}
        </div>
    </div>

    <x-filament-actions::modals />
</div>
