<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StoreRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $params = [];

        return view('admin.posts.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $post = new Post();
        $all = $request->all();
        $post->fill($all);

        $user = Auth::user();
        $post->user_id = $user->id;

        $post->save();

        return redirect()->route('admin.posts.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::where('id', $id)->first();

        $params = [
            'post' => $post,
        ];

        return view('admin.posts.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::where('id', $id)->first();
        $all = $request->all();

        // 画像
        if (isset($all['image'])) {
            $path = $request->file('image')->store('pubilc/image');
            $post->image_path = basename($path);
        } else {
            $post->image_path = null;
        }

        $post->fill($all);
        $post->save();

        return redirect()->route('admin.posts.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
