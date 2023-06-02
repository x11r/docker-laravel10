<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class PostListLivewire extends Component
{
    public int $counter = 10;

    public $posts;

    protected $listeners = [
        'refresh' => '$refresh',
        'destroy' => 'destroy',
        'like' => 'like',
        'dislike' => 'dislike',
    ];

    protected $rules = [
        'post_likes.user_id' => 'required',
        'post_likes.post_id' => 'required',
        'posts.title' => 'required',
        'posts.content' => 'required',
    ];

    public function render()
    {
        Log::debug(__LINE__ . ' ' . __METHOD__);

        $params = [
            'posts' => $this->posts
        ];

        return view('livewire.post-list-livewire', $params);
    }

    public function mount()
    {
        $this->posts = Post::orderByDesc('created_at')->get();
    }

    public function edit()
    {
        Log::debug(__LINE__ . ' '. __METHOD__);
    }

    public function like($id)
    {
        $auth = Auth::user();
        Log::debug(__LINE__ . ' ' .__METHOD__
            . ' [id] ' . $id
            . ' [user_id] ' . $auth->id
        );

        PostLike::upsert(
            [
                'user_id' => $auth->id,
                'post_id' => $id
            ],
            []
        );
    }

    public function dislike($id)
    {
        $auth =  Auth::user();

        Log::debug(__LINE__ . ' ' .__METHOD__
            . ' [id] ' . $id
            . ' [user_id] ' . $auth->id
        );

        PostLike::where('user_id', $auth->id)->where('post_id', $id)->delete();
    }

    public function increase()
    {
        Log::debug(__LINE__ .' ' . __METHOD__);
        $this->counter ++;
    }
}
