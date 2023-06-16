<div>
    @foreach ($posts as $post)
        <div class="p-2 m-2 bg-gray-100">
            <div class="row">
                <div class="col">
                    {{ html()->a(route('admin.posts.edit', $post), $post->title)
                    ->class(['fw-bold', 'text-decoration-underline'])
                    }}
                </div>
                <div class="col text-end">
                    投稿者：{{ $post->user->name }}
                    日付：{{ $post->created_at->format('Y-m-d H:i') }}
                </div>
                <div>
                    {{ Str::limit($post->content, 100) }}
                </div>
                <div>
                    @if ($post->like->count() > 0)
                        <button
                            type="button"
                            class="btn btn-info"
                            wire:click="dislike({{ $post->id }})"
                        >
                            Dislike
                        </button>
                    @else
                        <button
                            type="button"
                            class="btn btn-warning"
                            wire:click="like({{ $post->id }})"
                        >
                            Like
                        </button>
                    @endif

                    @if ($post->image_path)
{{--                        {{ html()->img('/' . $post->image_path) }}--}}
                        <img src="{{ asset('storage/public/image/' . basename($post->image_path)) }}"
                    @endif

                </div>
            </div>

        </div>
    @endforeach


        <div>


        </div>
</div>
