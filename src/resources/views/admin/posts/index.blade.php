@extends('layouts.admin')

@section('title', '投稿情報')

@section('content')
    <div class="h1">投稿一覧</div>
    @livewireScripts

    <livewire:post-list-livewire />

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
