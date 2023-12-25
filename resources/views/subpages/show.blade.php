<x-app-layout>
    <div class="py-12 box w-1" x-data="{ showCreatePostForm: false }">
        <h1 class="h">{{ $subpage->name }}</h1>
        <p class="p-1">{{ $subpage->description }}</p>
        
        <!-- Subscription Button -->
        @auth
            @if(auth()->user()->isSubscribedTo($subpage))
                <!-- 'slug' is the one in the route and gets its value from '$subpage->slug'.-->
                <form action="{{ route('unsubscribe', [ 'slug' => $subpage->slug ]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <x-primary-button class="ms-3" type="submit">
                        {{ __('Unsubscribe') }}
                    </x-primary-button>
                </form>
            @else
                <!-- 'slug' is the one in the route and gets its value from '$subpage->slug'.-->
                <form action="{{ route('subscribe', [ 'slug' => $subpage->slug ]) }}" method="POST">
                    @csrf
                    <x-primary-button class="ms-3" type="submit">
                        {{ __('Subscribe') }}
                    </x-primary-button>
                </form>
            @endif
        @endauth

        <!-- Toggle Button for Create Post Form -->
        @auth
        <div>
            <!-- Toggle button -->
            <x-primary-button class="ms-3" @click="showCreatePostForm = !showCreatePostForm" type="button">
                    <span x-text="showCreatePostForm ? 'Hide Form' : 'Create Post'"></span>
            </x-primary-button>
        </div>
        @endauth

        <div class="w">
            <!-- This div will show/hide based on the Alpine.js state -->
            <div class="w" x-show="showCreatePostForm" x-cloak>
                <form action="{{ route('subpages.posts.store', $subpage) }}" class="mt-4">
                @csrf
                    <div>
                        <h2 class="p-1"> Create a H8 post </h2>
                        <div class="content-text">
                            <x-text-input id="title" class="block mt-1 w" style="width: 100%" type="text" name="title" placeholder="Title" required autofocus required class="form-control" />
                            <x-textarea-input id="content" class="block mt-1 w-1" style="resize: none;" name="content" placeholder="Write your blog post here..." rows="4" required required class="form-control"></x-textarea-input>
                            <x-primary-button class="ms-3 create-post-btn" type="button">
                                {{ __('Post') }}
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    

    
    <div>
        <!-- Posts Section -->
        <div class="py-12 posts-section">
            @forelse ($subpage->posts as $post)
            <!-- Passing values into the child-template -->
            <x-blog-template
                :profileName="$post->user->name"
                :title="$post->title"
                :content="$post->content"
                :createdAt="$post->created_at"
                :post="$post"
                :subpage_slug="$subpage->slug"
                :post_slug="$post->slug"
            >
            </x-blog-template>
            @empty
                <div class="p-1">
                    No posts yet.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>




 <!-- Collapsible Create Post Form -->
