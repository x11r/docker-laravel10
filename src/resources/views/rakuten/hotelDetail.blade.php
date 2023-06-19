@extends('layouts.admin')

@section('title', '楽天')

@section('content')
    <div class="container">
        <div class="h1"><a href="{{ route('rakuten.areas') }}">楽天API</a></div>
        <div class="h1">{{ $hotel['hotels'][0]['hotel'][0]['hotelBasicInfo']['hotelName'] }}</div>
        @foreach ([1, 2, 3, 4] as $i)
            @if (isset($hotel['hotels'][0]['hotel'][$i]))
                <div>
                    <div>部屋名 - {{ $hotel['hotels'][0]['hotel'][$i]['roomInfo'][0]['roomBasicInfo']['roomName'] }}</div>
                    <div>プラン名 - {{ $hotel['hotels'][0]['hotel'][$i]['roomInfo'][0]['roomBasicInfo']['planName'] }}</div>
                </div>
            @endif
        @endforeach
    </div>
@endsection
