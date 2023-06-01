@extends('layouts.admin')

@section('title', '投稿情報')

@section('content')
    <div class="h1">投稿一覧</div>
    @foreach ($posts as $post)
        <div class="p-2 m-2  bg-gray-100">
            <div class="row">
                <div class="col">
                    {{ html()->a(route('admin.posts.edit',$post), $post->title)->class(['fw-bold', 'text-decoration-underline']) }}
                </div>
                <div class="col text-end">
                    投稿者：{{ $post->user->name }}
                    日付：{{ $post->created_at->format('Y-m-d H:i:s') }}
                </div>
            </div>
            <div>{{ Str::limit($post->content, 100) }}</div>
        </div>
    @endforeach
    <div>
        {{ html()->form('post', route('admin.posts.store'))->open() }}
        <div>タイトル</div>
        <div>
            {{ html()->text('title')->class('form-control') }}
        </div>
        <div>本文</div>
        <div>
            {{ html()->textarea('content')->class('form-control') }}
        </div>
        <div>
            {{ html()->button('登録')->class('btn btn-primary') }}
        </div>
        {{ html()->form()->close() }}
    </div>
@endsection
