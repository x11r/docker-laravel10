@extends('layouts.admin')
@section('title', '投稿内容編集')
@section('content')
    <div class="container mx-auto">
        {{ html()->form('put', route('admin.posts.update', $post))->open() }}
        <div>タイトル</div>
        <div>
            {{ html()->text('title', $post->title)->class('form-control') }}
        </div>
        <div>本文</div>
        <div>
            {{ html()->textarea('content', $post->content)->class('form-control') }}
        </div>
        <div>
            {{ html()->button('編集実行')->class('btn btn-primary') }}
        </div>
        {{ html()->form()->close() }}
    </div>
@endsection
