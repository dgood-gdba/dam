<?php

namespace App\Livewire\Asset;

use App\Models\AssetComment;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Component;


class Comment extends Component implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    public AssetComment $comment;

    public bool $isEditing = false;
    public bool $isCommenting = false;

    public array $newForm = [
        'comment' => ''
    ];

    public array $editForm = [
        'comment' => ''
    ];

    public function mount(): void
    {
        $this->editForm['comment'] = $this->comment->comment;
        $this->editCommentForm->fill($this->editForm);
    }

    public function editCommentForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('editForm')
            ->schema([
                RichEditor::make('comment')
                    ->label('Comment')
                    ->required()
                    ->maxLength(255)
                    ->default($this->comment->comment)
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('newForm')
            ->schema([
                RichEditor::make('comment')
                    ->label('Reply')
                    ->required()
                    ->maxLength(255)
            ]);
    }

    public function toggleCommentAction(): Action
    {
        return Action::make('toggleComment')
            ->label(fn() => $this->isCommenting ? 'Cancel' : 'Reply')
            ->color(fn() => $this->isCommenting ? 'danger' : 'gray')
            ->action(function () {
                $this->isCommenting = !$this->isCommenting;
            });
    }

    public function postAction(): Action
    {
        return Action::make('post')
            ->label('Post Comment')
            ->color('success')
            ->action(function () {
                $data = $this->form->getState();
                $comment = new AssetComment();
                $comment->comment = $data['comment'];
                $comment->user_id = auth()->id();
                $comment->asset_comment_id = $this->comment->id;
                $comment->asset_id = $this->comment->asset_id;
                $this->comment->comments()->save($comment);
                $this->isCommenting = false;
                $this->newForm['comment'] = '';
                $this->form->fill($this->newForm);
                $this->dispatch('refresh');
                $this->comment->refresh()->load('comments');
            });
    }

    public function toggleEditAction(): Action
    {
        return Action::make('toggleEdit')
            ->size('xs')
            ->outlined()
            ->color(function () {
                return $this->isEditing ? 'danger' : 'gray';
            })
            ->extraAttributes([
                'class' => 'ml-4'
            ])
            ->label(function () {
                return $this->isEditing ? 'Cancel' : 'Edit';
            })
            ->action(function () {
                $this->editForm['comment'] = $this->comment->comment;
                $this->editCommentForm->fill($this->editForm);
                $this->isEditing = !$this->isEditing;
            })
            ->visible(function () {
                return $this->comment->user_id === auth()->id();
            });
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->size('xs')
            ->outlined()
            ->color('success')
            ->label('Edit Comment')
            ->action(function () {
                $data = $this->editCommentForm->getState();
                $this->comment->update([
                    'comment' => $data['comment'] ?? $this->comment->comment,
                ]);
                $this->isEditing = false;
                $this->dispatch('refresh');
            })
            ->visible(fn() => $this->isEditing);
    }

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <div class="min-h-16 bg-gray-900/10 dark:bg-gray-50/10 p-4 rounded-lg">
                <div class="flex items-center gap-4 mb-4">
                    <div>
                        <x-filament-panels::avatar.user size="w-16 h-16" :user="auth()->user()" loading="lazy" />
                    </div>
                    <div>
                        <div>
                            {{ $comment->user->name }}
                            @if( $comment->user_id === auth()->id() )
                                @if( $this->toggleEdit->isVisible() )
                                    {{ $this->toggleEdit }}
                                @endif
                                @if( $this->edit->isVisible() )
                                    {{ $this->edit }}
                                @endif
                            @endif
                        </div>
                        <div>{{ $comment->created_at->diffForHumans() }}</div>
                        @if( $comment->created_at != $comment->updated_at )
                            <div class="italic">Edited: {{ $comment->updated_at->diffForHumans() }}</div>
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    @if( $this->isEditing )
                        {{ $this->editCommentForm }}
                    @else
                        {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($comment->comment) }}
                    @endif
                </div>
                @if( $this->isCommenting )
                    <div class="mt-4 pt-4 border-t-2 border-gray-300 dark:border-gray-700">
                        <div>
                            {{ $this->form }}
                        </div>
                        <div class="mt-4">
                            {{ $this->post }}
                            {{ $this->toggleComment }}
                        </div>
                    </div>
                @else
                    <div class="mt-4">
                        {{ $this->toggleComment }}
                    </div>
                @endif

                <div class="indent-2 mt-2 space-y-2">
                    @foreach($comment->comments as $child)
                        <livewire:asset.comment :comment="$child" :key="$child->id"/>
                    @endforeach
                </div>
            </div>

        </div>
        HTML;
    }
}
