<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use App\Models\AssetComment;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Livewire\Component;

class Comments extends Component implements HasForms, HasActions
{
    use InteractsWithForms, InteractsWithActions;

    public Asset $asset;
    public bool $isCommenting = false;
    public array $commentForm = [
        'comment' => ''
    ];
    public bool $showReplyForm = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                RichEditor::make('comment')
                    ->label('Comment')
                    ->required()
            ])
            ->statePath('commentForm');
    }

    public function toggleCommentAction(): Action
    {
        return Action::make('toggleComment')
            ->label(function () {
                return $this->isCommenting ? 'Cancel' : 'Comment';
            })
            ->size(function () {
                return $this->isCommenting ? 'base' : 'lg';
            })
            ->extraAttributes([
                'class' => 'mb-2'
            ])
            ->color(function () {
                return $this->isCommenting ? 'danger' : 'gray';
            })
            ->action(function () {
                $this->isCommenting = !$this->isCommenting;
            });
    }

    public function postAction(): Action
    {
        return Action::make('post')
            ->label('Post Comment')
            ->action(function () {
                $data = $this->form->getState();
                $comment = new AssetComment();
                $comment->comment = $data['comment'];
                $comment->user_id = auth()->id();
                $comment->asset_id = $this->asset->id;
                $this->asset->comments()->save($comment);
                $this->dispatch('refresh');
            });
    }

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <h1 class="text-3xl font-bold">
                Comments @if(!$this->isCommenting){{ $this->toggleComment }}@endif
            </h1>
            <hr class="py-4">

            <div class="space-y-2">
                @forelse($asset->comments()->whereNull('asset_comment_id')->get() as $comment)
                    <livewire:asset.comment :comment="$comment" />
                @empty
                    <div class="text-center">No Comments.</div>
                @endforelse
            </div>
            @if( $this->isCommenting )
                <div class="mt-4 pt-4 border-t-2 border-gray-300 dark:border-gray-700">
                    <div>
                        {{ $this->form }}
                    </div>
                    <div class="mt-4">
                        {{ $this->post }} {{ $this->toggleComment }}
                    </div>
                </div>
            @endif
        </div>
        HTML;
    }
}
