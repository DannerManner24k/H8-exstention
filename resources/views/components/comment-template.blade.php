@props(['profileName' => 'Default Name', 'content', 'createdAt', 'comment'])


<div class="c">
    <div class="c-1">
        <strong class="c-profile c-2">{{ $profileName}}</strong>
        <p class="c-content c-2">{{ $content }}</p>
        <span class="c-span c-2">{{ $createdAt }}</span>
    </div>
    <div class="b-2">
        <form>
            @csrf
            <x-secondary-button type="button" 
                    class="like-comment-button button-space {{ $comment->isLikedByUser(Auth::user()) ? 'liked' : 'not-liked' }}"
                    data-comment-id="{{ $comment->id }}">
                {{ $comment->likes->count() }} {{ __('Like') }}
            </x-secondary-button>
        </form>
        
        @if (auth()->check() && auth()->id() === $comment->user_id)
            <x-danger-button type="button" 
                    class="button-space red delete-comment-btn"
                    data-comment-id="{{ $comment->id }}">
                {{ __('Delete') }}
            </x-danger-button>
        @endif

    </div>
</div>