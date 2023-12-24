@props(['profileName' => 'Default Name', 'content', 'createdAt', 'comment'])


<div class="c">
    <div class="c-1">
        <strong class="c-profile c-2">{{ $profileName}}</strong>
        <p class="c-content c-2">{{ $content }}</p>
        <span class="c-span c-2">{{ $createdAt }}</span>
    </div>
    <div class="b-2">
        <form method="POST" action="{{ route('comments.like.toggle', $comment->id) }}">
            @csrf
            <x-secondary-button type="submit" class="button-space {{ $comment->isLikedByUser(Auth::user()) ? 'liked' : 'not-liked' }}">
                {{ $comment->likes->count() }} {{__('Like') }}
            </x-secondary-button>
            
        </form>

        @if (auth()->check() && auth()->id() === $comment->user_id)
            <form method="POST" action="{{ route('comments.destroy', $comment->id) }}" id="delete-comment-form-{{ $comment->id }}">
                @csrf
                @method('DELETE')
                <x-danger-button class="button-space red delete-comment-btn" type="button" data-form-id="delete-comment-form-{{ $comment->id }}">
                    {{ __('Delete') }}
                </x-danger-button>
            </form>
        @endif
    </div>
</div>