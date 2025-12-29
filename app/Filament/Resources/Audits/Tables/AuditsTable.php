<?php

namespace App\Filament\Resources\Audits\Tables;

use App\Models\Audit;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Jenssegers\Agent\Agent;

class AuditsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('action')
                    ->formatStateUsing(fn($state) => \Str::of($state)->title()->replace('_', ' '))
                    ->searchable(),
                TextColumn::make('description')
                    ->state(function ($record) {

                        switch ($record->action) {
                            case 'view_root_directory':
                                return 'Root Directory';
                            case 'updated_directory':
                            case 'created_directory':
                            case 'view_directory':
                                if ($record->subject_type && $record->subject ) {
                                    return $record->subject?->name ?? 'Unknown Directory';
                                }
                                return 'Unknown Directory';
                            case 'view_asset':
                            case 'edit_asset':
                            case 'updated_asset':
                            case 'created_asset':
                            case 'shared_asset':
                            case 'downloaded_asset':
                                if ($record->subject_type && $record->subject ) {
                                    return $record->subject->file_name ?? 'Unknown Asset';
                                }
                                return 'Unknown Asset';
                            case 'deleted_asset':
                                if (!empty($record->properties['asset']['file_name'])) {
                                    return $record->properties['asset']['file_name'];
                                }
                                return 'Unknown Asset';
                            case 'deleted_directory':
                                if (!empty($record->properties['asset']['name'])) {
                                    return $record->properties['asset']['name'];
                                }
                                return 'Unknown Directory';
                            default:
                                dd('unknown:', $record);
                        }
                    }),
                TextColumn::make('subject_type')
                    ->searchable(),
                TextColumn::make('subject_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ip')
                    ->searchable(),
                TextColumn::make('user_agent')
                    ->formatStateUsing(function ($state) {
                        $agent = new Agent();
                        $agent->setUserAgent($state);

                        $browser = $agent->browser();
                        $browserVersion = $agent->version($browser);
                        $platform = $agent->platform();
                        $platformVersion = $agent->version($platform);
                        $isMobile = $agent->isMobile();

                        return $browser . ' ' . $browserVersion . ' on ' . $platform . ' ' . $platformVersion . ($isMobile ? ' (Mobile)' : '');
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Date of Action')
                    ->dateTime()
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('Details')
                    ->modal(true)
                    ->modalHeading('Change Details')
                    ->modalContent(function (Audit $record) {
                        $dataArray = [];
                        foreach ($record->properties['original'] as $property => $value) {
                            $dataArray[$property] = [
                                'original' => $value,
                                'updated' => ''
                            ];
                        }
                        foreach ($record->properties['updated'] as $property => $value) {
                            $dataArray[$property]['updated'] = $value;
                        }
                        $html = "
                        <div class='grid grid-cols-5 border-b-gray-400 dark:border-gray-700'>
                            <div class='text-lg'>Field</div>
                            <div class='col-span-2 text-lg'>Original</div>
                            <div class='col-span-2 text-lg'>Updated</div>
                        </div>";
                        $c = 0;
                        foreach ($dataArray as $property => $data) {
                            if ($c % 2 === 0) {
                                $html .= "<div class='grid grid-cols-5 bg-gray-950/5 dark:bg-gray-50/5'>";
                            } else {
                                $html .= "<div class='grid grid-cols-5 '>";
                            }
                            $html .= "<div class='col-span-1'>$property</div>";
                            $html .= "<div class='col-span-2'>$data[original]</div>";
                            $html .= "<div class='col-span-2'>$data[updated]</div>";
                            $html .= "</div>";
                        }
                        return new HtmlString($html);
                    })
                    ->modalFooterActions([])
                    ->visible(fn(Audit $record) => match ($record->action) {
                        'updated_asset',
                        'updated_directory' => true,
                        default => false
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
