<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Models\Directory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                FileUpload::make('path')
                    ->directory(function ($record) {
                        return $record->directory ? Directory::getDirectoryPath($record->directory) : '/';
                    })
                    ->disk('public'),
                Group::make([
                    TextInput::make('file_name')
                        ->required(),
                    SpatieTagsInput::make('tags'),
                    TextInput::make('file_type')
                        ->disabled(),
                    TextInput::make('file_size')
                        ->disabled(),
                    TextInput::make('mime_type')
                        ->disabled(),
                    TextInput::make('extension')
                        ->disabled(),
                ]),
                Group::make([
                    Repeater::make('properties')
                        ->columns(2)
                        ->relationship('properties')
                        ->schema([
                            TextInput::make('name'),
                            TextInput::make('value'),
                        ]),
                ])->columnSpan(2),
                ViewEntry::make('path')
                    ->columnSpanFull()
                    ->hiddenLabel()
                    ->view('comments.assets.comment-handler'),
            ]);
    }
}
