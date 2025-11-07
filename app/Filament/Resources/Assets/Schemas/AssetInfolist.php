<?php

namespace App\Filament\Resources\Assets\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\SpatieTagsEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class AssetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                TextEntry::make('disk')
                    ->state(function ($record) {
                        switch ($record->file_type) {
                            case 'image':
                                return new HtmlString('<img src="' . url(\Storage::disk('public')->url($record->path)) . '"/>');
                            case 'document':
                                $url = url(\Storage::disk('public')->url($record->path));
                                $ext = $record->extension;
                                $isPdf = $ext === 'pdf';
                                $isDoc = in_array($ext, ['doc', 'docx']);
                                $isXls = in_array($ext, ['xls', 'xlsx', 'csv']);
                                $isTxt = in_array($ext, ['txt', 'md', 'log']);
                                if( $isPdf){
                                    return new HtmlString('<iframe
                                        src="' . $url . '#toolbar=0&navpanes=0&scrollbar=0&page=1"
                                        class="w-40 h-52 border rounded bg-white dark:bg-gray-800"
                                        loading="lazy"
                                    ></iframe>');
                                }
                                if( $isDoc){
                                    return new HtmlString('<div class="w-40 h-52 border rounded bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6zM8.7 16.9l-1.5-6.8h1.6l.8 4.5.9-3.6h1.6l.9 3.6.8-4.5h1.6l-1.5 6.8h-1.6l-.9-3.5-.9 3.5H8.7z"/>
                                        </svg>
                                    </div>');
                                }
                                if($isXls){
                                    return new HtmlString('<div class="w-40 h-52 border rounded bg-green-50 dark:bg-green-900/20 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-green-600 dark:text-green-400" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6zM8 10h8v2H8v-2zm0 4h8v2H8v-2z"/>
                                        </svg>
                                    </div>');
                                }
                                if($isTxt){
                                    return new HtmlString('<div class="w-40 h-52 border rounded bg-gray-50 dark:bg-gray-800 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-gray-600 dark:text-gray-300" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6zM8 10h8v2H8v-2zm0 4h5v2H8v-2z"/>
                                        </svg>
                                    </div>');
                                }

                                return new HtmlString('<div class="w-40 h-52 border rounded bg-gray-50 dark:bg-gray-800 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-gray-500 dark:text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                                    </svg>
                                </div>');
                                break;
                            case 'video':
                                $url = url(\Storage::disk('public')->url($record->path));
                                return new HtmlString('<div class="mx-auto p-2 max-w-full flex flex-col items-center justify-center">
                                    <video
                                        src="' . $url . '"
                                        class="bg-black object-cover"
                                        preload="metadata"
                                        muted
                                        playsinline
                                        controls
                                    ></video>
                                </div>');
                                break;
                            default:
                                return new HtmlString('<div class="items-center">No Preview<br>' . $record->file_type . '</div>');
                        }
                        return new HtmlString('<img src="' . url(\Storage::disk('public')->url($record->path)) . '"/>');
                    }),
                Group::make([
                    TextEntry::make('file_name'),
                    SpatieTagsEntry::make('tags'),
                    TextEntry::make('file_type'),
                    TextEntry::make('file_size')
                        ->numeric(),
                    TextEntry::make('mime_type')
                        ->placeholder('-'),
                    TextEntry::make('extension')
                        ->placeholder('-'),
                    TextEntry::make('path'),
                ]),
                Group::make([
                    RepeatableEntry::make('properties')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('name'),
                            TextEntry::make('value'),
                        ])
                ])->columnSpan(2),
                ViewEntry::make('path')
                    ->columnSpanFull()
                    ->hiddenLabel()
                    ->view('comments.assets.comment-handler'),
            ]);
    }
}
