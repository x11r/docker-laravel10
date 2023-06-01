@extends('layouts.admin')
@section('title', '投稿内容編集')
@section('content')
    <div class="container mx-auto">
        <div class="h1">投稿内容編集</div>
        {{ html()->form('put', route('admin.posts.update', $post))->open() }}
        <div class="mb-3">
            {{ html()->label('タイトル', 'post-title')->class(['form-label']) }}
            {{ html()->text('title', $post->title)->id('post-title')->class(['form-control']) }}
        </div>

        <div class="md-3">
            {{ html()->label('本文', 'post-content')->class(['form-label']) }}
            {{ html()->textarea('content', $post->content)->id('post-content')->class(['form-control']) }}
        </div>
        <div>
            {{ html()->button('編集実行')->class('btn btn-primary') }}
        </div>
        {{ html()->form()->close() }}
    </div>
@endsection
