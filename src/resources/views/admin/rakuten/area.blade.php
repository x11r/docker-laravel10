@extends('layouts.admin')

@section('title', '楽天')

@section('content')
    <div class="container">
        <div class="h1">楽天API</div>
        @foreach ($hotels['hotels'] as $hotel)
            <div class="row">
                <div class="h4">
                    {{ $hotel['hotel'][0]['hotelBasicInfo']['hotelName'] }}
                </div>
                <div class="">
                    {{ $hotel['hotel'][0]['hotelBasicInfo']['hotelNo'] }}
                </div>
            </div>
        @endforeach
    </div>
@endsection
