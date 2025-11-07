<?php

namespace App\Filament\Pages;

use App\Models\Asset;
use App\Models\Directory;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Support\Enums\GridDirection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class AssetManagement extends Page implements HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected string $view = 'filament.pages.asset-management-2';
    protected ?string $heading = '';

    // UI state
    #[Url]
    public ?int $directory = null;
    public array $items = [];
    public array $breadcrumbs = [];
    public array $filters = [
        'file_name' => '',
        'tags' => [],
        'property_name' => '',
        'property_value' => '',
        'extension' => '',
        'created_at_window' => '',
        'created_at' => [
            'from' => '',
            'to' => ''
        ],
        'updated_at_window' => '',
        'updated_at' => [
            'from' => '',
            'to' => ''
        ],
    ];

    public ?int $directoryIdContextMenu = null;

    public function mount(): void
    {
        $this->reload();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('filters')
            ->schema([
                TextInput::make('file_name'),
                SpatieTagsInput::make('tags')->dehydrated(true),
                TextInput::make('property_name'),
                TextInput::make('property_value'),
                TextInput::make('extension'),
                Group::make([
                    ToggleButtons::make('created_at_window')
                        ->columnSpanFull()
                        ->options([
                            'today' => 'Today',
                            'yesterday' => 'Yesterday',
                            'this_week' => 'This Week',
                            'this_month' => 'This Month',
                            'last_month' => 'Last Month',
                            'last_3_months' => 'Last 3 Months',
                            'last_6_months' => 'Last 6 Months',
                            'this_year' => 'This Year',
                        ])
                        ->gridDirection(GridDirection::Row)
                        ->extraAttributes(['style' => 'width: 100%'])
                        ->live()
                        ->afterStateUpdated(static function ($state, callable $set) {
                            switch ($state) {
                                case 'today':
                                    $set('created_at.from', now()->startOfDay()->format('Y-m-d'));
                                    $set('created_at.to', now()->endOfDay()->format('Y-m-d'));
                                    break;
                                case 'yesterday':
                                    $set('created_at.from', now()->subDay()->startOfDay()->format('Y-m-d'));
                                    $set('created_at.to', now()->subDay()->endOfDay()->format('Y-m-d'));
                                    break;
                                case 'this_week':
                                    $set('created_at.from', now()->startOfWeek()->format('Y-m-d'));
                                    $set('created_at.to', now()->endOfWeek()->format('Y-m-d'));
                                    break;
                                case 'this_month':
                                    $set('created_at.from', now()->startOfMonth()->format('Y-m-d'));
                                    $set('created_at.to', now()->endOfMonth()->format('Y-m-d'));
                                    break;
                                case 'last_month':
                                    $set('created_at.from', now()->subMonth()->startOfMonth()->format('Y-m-d'));
                                    $set('created_at.to', now()->subMonth()->endOfMonth()->format('Y-m-d'));
                                    break;
                                case 'last_3_months':
                                    $set('created_at.from', now()->subMonths(3)->startOfMonth()->format('Y-m-d'));
                                    $set('created_at.to', now()->subMonths(3)->endOfMonth()->format('Y-m-d'));
                                    break;
                                case 'last_6_months':
                                    $set('created_at.from', now()->subMonths(6)->startOfMonth()->format('Y-m-d'));
                                    $set('created_at.to', now()->subMonths(6)->endOfMonth()->format('Y-m-d'));
                                    break;
                                case 'this_year':
                                    $set('created_at.from', now()->startOfYear()->format('Y-m-d'));
                                    $set('created_at.to', now()->endOfYear()->format('Y-m-d'));
                                    break;

                            }
                        })
                        ->columns(2),
                    TextInput::make('created_at.from')
                        ->label('Created At')
                        ->placeholder('From')
                        ->hiddenLabel(),
                    TextInput::make('created_at.to')
                        ->label('Created At')
                        ->placeholder('To')
                        ->hiddenLabel(),
                ])->columns(2),
                Group::make([
                    ToggleButtons::make('updated_at_window')
                        ->columnSpanFull()
                        ->options([
                            'today' => 'Today',
                            'yesterday' => 'Yesterday',
                            'this_week' => 'This Week',
                            'this_month' => 'This Month',
                            'last_month' => 'Last Month',
                            'last_3_months' => 'Last 3 Months',
                            'last_6_months' => 'Last 6 Months',
                            'this_year' => 'This Year',
                        ])
                        ->gridDirection(GridDirection::Row)
                        ->extraAttributes(['style' => 'width: 100%'])
                        ->live(onBlur: true)
                        ->afterStateUpdated(static function ($state, callable $set) {
                            switch ($state) {
                                case 'today':
                                    $set('updated_at.from', now()->startOfDay()->format('Y-m-d'));
                                    $set('updated_at.to', now()->endOfDay()->format('Y-m-d'));
                                    break;
                                case 'yesterday':
                                    $set('updated_at.from', now()->subDay()->startOfDay()->format('Y-m-d'));
                                    $set('updated_at.to', now()->subDay()->endOfDay()->format('Y-m-d'));
                                    break;
                                case 'this_week':
                                    $set('updated_at.from', now()->startOfWeek()->format('Y-m-d'));
                                    $set('updated_at.to', now()->endOfWeek()->format('Y-m-d'));
                                    break;
                                case 'this_month':
                                    $set('updated_at.from', now()->startOfMonth()->format('Y-m-d'));
                                    $set('updated_at.to', now()->endOfMonth()->format('Y-m-d'));
                                    break;
                                case 'last_month':
                                    $set('updated_at.from', now()->subMonth()->startOfMonth()->format('Y-m-d'));
                                    $set('updated_at.to', now()->subMonth()->endOfMonth()->format('Y-m-d'));
                                    break;
                                case 'last_3_months':
                                    $set('updated_at.from', now()->subMonths(3)->startOfMonth()->format('Y-m-d'));
                                    $set('updated_at.to', now()->subMonths(3)->endOfMonth()->format('Y-m-d'));
                                    break;
                                case 'last_6_months':
                                    $set('updated_at.from', now()->subMonths(6)->startOfMonth()->format('Y-m-d'));
                                    $set('updated_at.to', now()->subMonths(6)->endOfMonth()->format('Y-m-d'));
                                    break;
                                case 'this_year':
                                    $set('updated_at.from', now()->startOfYear()->format('Y-m-d'));
                                    $set('updated_at.to', now()->endOfYear()->format('Y-m-d'));
                                    break;

                            }
                        })
                        ->columns(2),
                    TextInput::make('updated_at.from')
                        ->placeholder('From')
                        ->hiddenLabel(),
                    TextInput::make('updated_at.to')
                        ->placeholder('To')
                        ->hiddenLabel(),
                ])->columns(2),
            ]);
    }

    #[On('refresh')]
    public function reload(): void
    {
        $this->loadDirectoryItems();
        $this->buildBreadcrumbs();
    }

    #[On('openDirectory')]
    public function openDirectory(int|null $directoryId): void
    {
        $this->directory = $directoryId;
        $this->reload();
    }

    protected function loadDirectoryItems(): void
    {
        $filters = $this->form->getState();

        $this->items = [];
        $directories = Directory::query()
            ->where('parent_id', $this->directory)
            ->orderBy('name')
            ->get();

        $directories->each(function ($directory) {
            $directory->file_type = 'directory';
        });

        $assets = Asset::query()
            ->where('directory_id', $this->directory)
            ->when(!empty($filters['file_name']), function ($query) use ($filters) {
                $query->where('file_name', 'like', '%' . $filters['file_name'] . '%');
            })
            ->when(!empty($filters['property_name']), function ($query) use ($filters) {
                $query->whereHas('properties', function ($query) use ($filters) {
                    $query->where('name', $filters['property_name']);
                    if (!empty($filters['property_value'])) {
                        $query->where('value', $filters['property_value']);
                    }
                });

            })
            ->when(!empty($filters['extension']), function ($query) use ($filters) {
                $query->where('extension', $filters['extension']);
            })
            ->when(!empty($filters['tags']), function ($query) use ($filters) {
                $query->withAnyTagsOfAnyType($filters['tags']);
            })
//            ->when(!empty($filters['created_at']['from']), function ($query) use ($filters) {
//                $query->whereBetween('created_at', [
//                    $filters['created_at']['from'],
//                    $filters['created_at']['to']
//                ]);
//            })
//            ->when(!empty($filters['updated_at']['from']), function ($query) use ($filters) {
//                $query->whereBetween('updated_at', [
//                    $filters['updated_at']['from'],
//                    $filters['updated_at']['to']
//                ]);
//            })
            ->orderBy('file_name')
            ->get();

        //I want to combine directories and assets into one collection ordered by name
        $items = [];
        foreach ($directories as $directory) {
            $items[$directory->name . ' (directory)'] = $directory->toArray();
        }

        foreach ($assets as $asset) {
            $items[$asset->file_name . ' (file)'] = $asset->toArray();
        }

        $this->items = $items;
    }

    #[Computed]
    public function records()
    {
        $filters = $this->form->getState();

        $this->items = [];
        $directories = Directory::query()
            ->where('parent_id', $this->directory)
            ->orderBy('name')
            ->get();

        $directories->each(function ($directory) {
            $directory->file_type = 'directory';
        });

        $assets = Asset::query()
            ->where('directory_id', $this->directory)
            ->when(!empty($filters['file_name']), function ($query) use ($filters) {
                $query->where('file_name', 'like', '%' . $filters['file_name'] . '%');
            })
            ->when(!empty($filters['property_name']), function ($query) use ($filters) {
                $query->whereHas('properties', function ($query) use ($filters) {
                    $query->where('name', $filters['property_name']);
                    if (!empty($filters['property_value'])) {
                        $query->where('value', $filters['property_value']);
                    }
                });

            })
            ->when(!empty($filters['extension']), function ($query) use ($filters) {
                $query->where('extension', $filters['extension']);
            })
            ->when(!empty($filters['tags']), function ($query) use ($filters) {
                $query->withAnyTagsOfAnyType($filters['tags']);
            })
//            ->when(!empty($filters['created_at']['from']), function ($query) use ($filters) {
//                $query->whereBetween('created_at', [
//                    $filters['created_at']['from'],
//                    $filters['created_at']['to']
//                ]);
//            })
//            ->when(!empty($filters['updated_at']['from']), function ($query) use ($filters) {
//                $query->whereBetween('updated_at', [
//                    $filters['updated_at']['from'],
//                    $filters['updated_at']['to']
//                ]);
//            })
            ->orderBy('file_name')
            ->get();

        //I want to combine directories and assets into one collection ordered by name
        $items = [];
        foreach ($directories as $directory) {
            $items[$directory->name . ' (directory)'] = $directory->toArray();
        }

        foreach ($assets as $asset) {
            $items[$asset->file_name . ' (file)'] = $asset->toArray();
        }
        return $items;
    }

    protected function buildBreadcrumbs(): void
    {
        $this->breadcrumbs = [];
        $breadcrumbs = [];
        $currentDirectory = Directory::with('parent')->find($this->directory);
        if ($currentDirectory) {
            $directory = $currentDirectory;
            while ($directory) {
                $breadcrumbs[] = [
                    'name' => $directory->name,
                    'id' => $directory->id,
                ];
                $directory = $directory->parent;
            }
        }
        $this->breadcrumbs = array_reverse($breadcrumbs);
    }

    public function setContextDirectoryId(?int $directoryId): void
    {
        dump($directoryId);
        $this->directoryIdContextMenu = $directoryId;
    }

    public function makeDirectoryAction(): Action
    {
        return Action::make('makeDirectory')
            ->schema([
                TextInput::make('name')
            ])
            ->extraAttributes([
                'class' => 'w-full rounded-none text-black dark:text-white bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-800 text-left '
            ])
            ->action(function ($data) {
                $directory = new Directory();
                $directory->name = $data['name'];
                $directory->parent_id = $this->directory;
                $directory->save();

                //Emit event to close context menu
                $this->dispatch('closeContext');
                $this->reload();
            });
    }

    public function uploadAssetAction(): Action
    {
        $path = '';
        foreach ($this->breadcrumbs as $breadcrumb) {
            $path .= $breadcrumb['name'] . DIRECTORY_SEPARATOR;
        }

        return Action::make('uploadAsset')
            ->schema([
                FileUpload::make('file')
                    ->preserveFilenames()
                    ->disk('public')
                    ->directory($path),
                TextInput::make('name')
                    ->label('File Name')
                    ->hint('Leave blank to use the original file name'),
                SpatieTagsInput::make('tags')->label('Tags')
                    ->dehydrated(true),
            ])
            ->extraAttributes([
                'class' => 'w-full rounded-none text-black dark:text-white bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-800 text-left '
            ])
            ->action(function ($data, \Filament\Actions\Action $action) {
                $path = '';
                foreach ($this->breadcrumbs as $breadcrumb) {
                    $path .= $breadcrumb['name'] . DIRECTORY_SEPARATOR;
                }

                $fileData = pathinfo(\Storage::disk('public')->path($data['file']));
                $extension = $fileData['extension'];

                $updateFileName = true;
                if (empty($data['name'])) {
                    $data['name'] = $fileData['filename'];
                    $updateFileName = false;
                }

                $safeName = $data['name'];
                $numberModifier = 1;
                while (Asset::query()->where('directory_id', $this->directory)->where('file_name', $safeName)->exists()) {
                    $safeName = $data['name'] . '(' . $numberModifier . ')';
                    $numberModifier++;
                    $updateFileName = true;
                }
                $data['name'] = $safeName;

                if ($updateFileName) {
                    $storage = Storage::disk('public');
                    $oldPath = $data['file'];
                    $newPath = $path . $data['name'] . '.' . $extension;
                    if ($storage->exists($oldPath)) {
                        $storage->move($oldPath, $newPath);
                        $data['file'] = $newPath;
                    }
                }

                $asset = new Asset();
                $asset->directory_id = $this->directory;
                $asset->file_name = $data['name'];
                $asset->extension = $extension;
                $asset->path = $data['file'];

                // Get file information
                //$storage = \Illuminate\Support\Facades\Storage::disk('public');
                $asset->file_size = \Storage::disk('public')->size($data['file']);
                $asset->mime_type = \Storage::disk('public')->mimeType($data['file']);

                if (str_starts_with($asset->mime_type, 'image/')) {
                    $asset->file_type = 'image';
                } elseif (str_starts_with($asset->mime_type, 'video/')) {
                    $asset->file_type = 'video';
                } elseif (str_starts_with($asset->mime_type, 'audio/')) {
                    $asset->file_type = 'audio';
                } else {
                    $asset->file_type = 'document';
                }
                $asset->save();

                $asset->attachTags($data['tags']);

                //Emit event to close context menu
                $this->dispatch('closeContext');
                $this->reload();
            });
    }

    public function applyFiltersAction(): Action
    {
        return Action::make('applyFilters')
            ->label('Apply Filters')
            ->action(function () {
                $this->dispatch('close-modal', id: 'filters');
                $this->dispatch('refresh');
            });
    }

    public function smallClearFiltersAction(): Action
    {
        return Action::make('smallClearFilters')
            ->label('Clear Filters')
            ->icon('heroicon-s-x-mark')
            ->color('gray')
            ->extraAttributes([
                'class' => 'rounded-l-none'
            ])
            ->action(function () {
                $this->filters = [
                    'file_name' => '',
                    'tags' => [],
                    'property_name' => '',
                    'property_value' => '',
                    'extension' => '',
                    'created_at_window' => '',
                    'created_at' => [
                        'from' => '',
                        'to' => ''
                    ],
                    'updated_at_window' => '',
                    'updated_at' => [
                        'from' => '',
                        'to' => ''
                    ]
                ];
                $this->form->fill($this->filters);
                $this->dispatch('refresh');
            })
            ->disabled(fn() => !$this->hasFilters);
    }

    public function clearFiltersAction(): Action
    {
        return Action::make('clearFilters')
            ->label('Clear Filters')
            ->color('danger')
            ->action(function () {
                $this->filters = [
                    'file_name' => '',
                    'tags' => [],
                    'property_name' => '',
                    'property_value' => '',
                    'extension' => '',
                    'created_at_window' => '',
                    'created_at' => [
                        'from' => '',
                        'to' => ''
                    ],
                    'updated_at_window' => '',
                    'updated_at' => [
                        'from' => '',
                        'to' => ''
                    ]
                ];

                $this->form->fill($this->filters);
                $this->dispatch('refresh');
            });
    }

    #[Computed]
    public function hasFilters(): bool
    {
        $data = $this->form->getState();
        return !empty($data['file_name']) || !empty($data['tags']) || !empty($data['property_name']) || !empty($data['property_value']) || !empty($data['extension']) || !empty($data['created_at']['from']) || !empty($data['created_at']['to']) || !empty($data['updated_at']['from']) || !empty($data['updated_at']['to']);
    }
}
