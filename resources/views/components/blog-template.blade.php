@props([
    // every prop is being assigned with the $
    'profileName' => 'Default Name',
    'title', 
    'content', 
    'createdAt', 
    'post', 
    'showSubpageName' => false, // Default to false since it might not be always provided.
    'subpageName' => null, // Default to null since it might not be always provided.
    'subpage_slug', // Add this to accept the subpage slug
    'post_slug'
])


<div class="box w-1">
    <div class="titles">
        <h3 class="h-1">{{ $subpageName }}</h3> <!-- Subpage name included -->
        <h2 class="h">{{ $title }}</h2>
    </div>
    <div class="b-1">
        <h3 class="h-1">{{ $profileName }}</h3>
        <p class="p-1">{{ $content }}</p>
        <span class="p-2">{{ $createdAt->diffForHumans() }}</span>
    </div>
    
    <div class="b-2">
        <!-- 'slug' is the one in the route and gets its value from '$subpage_slug '. The '$subpage_slug' value is being transfered from the parrent blade via the props at the top. -->
        <x-secondary-button type="button" 
                class="like-button button-space {{ $post->isLikedByUser(Auth::user()) ? 'liked' : 'not-liked' }}"
                data-post-id="{{ $post->id }}"
                data-subpage-slug="{{ $subpage_slug }}"
                data-post-slug="{{ $post_slug }}">
            {{ $post->likes->count() }} {{ __('Like') }}
        </x-secondary-button>


        <!-- Show/Hide Comment Section -->
        <x-secondary-button class="button-space blog-comment-btn" type="button" data-post-id="{{ $post->id }}">
            {{ __('Comment') }}
        </x-secondary-button>

        <x-secondary-button class="button-space" type="button">
            {{ __('Share') }} <!-- Placeholder for share functionality -->
        </x-secondary-button>

        
        
        @if (auth()->check() && auth()->id() === $post->user_id)
            <!-- Form for deleting a post -->
            <form method="POST" id="delete-form-{{ $post->id }}" action="{{ route('subpages.posts.destroy', ['slug' => $subpage_slug, 'postSlug' => $post->slug]) }}">
                @csrf
                @method('DELETE')
                <x-danger-button class="button-space red end delete-post-btn" type="button" data-form-id="delete-form-{{ $post->id }}">
                    {{ __('Delete') }}
                </x-danger-button>
            </form>
        @endif


    </div>

    <!-- Hidden Comment Section -->
    <div id="comment-section-{{ $post->id }}" class="comment-section" style="display: none;">
        <!-- 'slug' is the one in the route and gets its value from '$subpage_slug '. The '$subpage_slug' value is being transfered from the parrent blade via the props at the top. -->
       <form method="POST" action="{{ route('posts.comments.store', ['slug' => $subpage_slug, 'postSlug' => $post_slug]) }}">
           @csrf
           <x-textarea-input name="content" class="block mt-1 w-full" rows="3" placeholder="Write a comment..."></x-textarea-input>
           <x-primary-button class="ms-3" type="submit">
               {{ __('Post Comment') }}
           </x-primary-button>
       </form>
       @foreach($post->comments as $comment)
       <x-comment-template 
           :profileName='$comment->user->name'
           :content='$comment->content'
           :createdAt='$comment->created_at->diffForHumans()'
           :comment='$comment'
       ></x-comment-template>
       @endforeach
   </div>
</div>


