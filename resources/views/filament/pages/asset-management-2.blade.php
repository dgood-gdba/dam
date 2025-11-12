<x-filament-panels::page>

    <div class="space-y-4">
        {{-- Breadcrumbs --}}

        {{-- Main panel --}}
        <div
            class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 min-h-32"
            x-data="{
                showMenu: false,
                menuX: 0,
                menuY: 0,
                openMenu(e) {
                    $dispatch('closeContext');
                    this.menuX = e.clientX;
                    this.menuY = e.clientY;
                    this.showMenu = true;
                },
                closeMenu() {
                    this.showMenu = false;
                }
            }"
            @click.away="closeMenu()"
            @contextmenu.prevent="openMenu($event)"
            @closeContext.window="closeMenu()"
        >
            <div class="flex justify-between">
                <nav class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300" aria-label="Breadcrumb">
                    <button
                        type="button"
                        class="hover:text-gray-900 dark:hover:text-white focus:outline-none"
                        wire:click="dispatch('openDirectory', {directoryId: null})"
                    >
                        Root
                    </button>
                    <template x-if="false"></template> {{-- keeps Alpine happy if needed --}}
                    <span class="text-gray-400 dark:text-gray-500">/</span>
                    @foreach ($this->breadcrumbs as $i => $bc)
                        @if ($i > 0)
                            <span class="text-gray-400 dark:text-gray-500">/</span>
                        @endif
                        <button
                            type="button"
                            class="hover:text-gray-900 dark:hover:text-white focus:outline-none"
                            wire:click="dispatch('openDirectory', {directoryId: {{ $bc['id'] }}})"
                        >
                            {{ $bc['name'] }}
                        </button>
                    @endforeach
                </nav>

                <div class="flex">

                    {{ $this->topMakeDirectory }}
                    {{ $this->topUploadAsset }}
                    <div>&nbsp;&nbsp;&nbsp;</div>
                    <x-filament::modal slide-over id="filters">
                        <x-slot name="trigger">
                            <x-filament::button
                                icon="heroicon-o-funnel"
                                color="gray"
                                class="rounded-r-none"
                            >
                                Filters
                                @if( $this->hasFilters )
                                    <sup class="block">
                                        •
                                    </sup>
                                @endif
                            </x-filament::button>
                        </x-slot>

                        <x-slot name="heading">
                            File Filters
                        </x-slot>

                        <div>
                            {{ $this->form }}
                        </div>
                        <div class="mt-4">
                            {{ $this->applyFilters }}
                            {{ $this->clearFilters }}
                        </div>
                    </x-filament::modal>
                    {{ $this->smallClearFilters }}

                </div>
            </div>


            <div
                x-show="showMenu"
                x-cloak
                class="fixed z-50 w-48 rounded-md bg-white shadow-lg dark:bg-gray-700"
                :style="`top: ${menuY}px; left: ${menuX}px;`"
            >
                <div class="py-1" @click="closeMenu()">
                    {{ $this->makeDirectory }}
                    {{ $this->uploadAsset }}
                </div>
            </div>

            <div class="grid grid-cols-8 gap-4 items-center justify-items-center">
                @forelse($this->records as $item)
                    @switch($item['file_type'])
                        @case('directory')
                            <livewire:directory
                                :directory-id="$item['id']"
                                :key="'directory-' . $item['id']"
                            />
                            @break
                        @case('image')
                            <livewire:preview.image
                                :asset-id="$item['id']"
                                :key="'image-' . $item['id'] . '-' . time()"
                            />
                            @break
                        @case('document')
                            <livewire:preview.document
                                :asset-id="$item['id']"
                                :key="'document-' . $item['id'] . '-' . time()"
                            />
                            @break
                        @case('video')
                            <livewire:preview.video
                                :asset-id="$item['id']"
                                :key="'document-' . $item['id'] . '-' . time()"
                            />
                            @break
                        @default
                            <div class="items-center">
                                {{ $item['file_name'] }} - {{ $item['file_type'] }}
                            </div>
                            @break
                    @endswitch
                @empty
                    <div class="col-span-full text-center text-2xl mt-8">No Items In This Directory</div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
