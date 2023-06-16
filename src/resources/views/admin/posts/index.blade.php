@extends('layouts.admin')

@section('title', '投稿情報')

@section('content')
    <div class="h1">投稿一覧</div>
    @livewireScripts

    <livewire:post-list-livewire />

    <div class="h2 mt-5">新規投稿</div>
    <div>
        {{ html()->form('post', route('admin.posts.store'))->acceptsFiles()->open() }}
        <div>タイトル</div>
        <div>
            {{ html()->text('title')->class('form-control') }}
        </div>
        <div>本文</div>
        <div>
            {{ html()->textarea('content')->class('form-control') }}
        </div>
        <div>
            {{ html()->file('image')->class('form-control') }}
        </div>
        <div>
            {{ html()->button('登録')->class('btn btn-primary') }}
        </div>
        {{ html()->form()->close() }}
    </div>

    <div>
        <img src="/1.jpg" alt="00">
        <img src="/storage/1.jpg" alt="01">
        <img src="/storage/image/1.jpg" alt="02">
{{--        <img src="/public/1.jpg" alt="100">--}}
{{--        <img src="/public/storage/1.jpg" alt="101">--}}
{{--        <img src="/public/storage/image/1.jpg" alt="101">--}}
    </div>
@endsection
