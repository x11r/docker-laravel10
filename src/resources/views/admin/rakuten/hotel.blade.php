@extends('layouts.admin')

@section('title', '楽天')

@section('content')

    <div class="container">
        <div class="h1">楽天API</div>
        @foreach ($hotels['hotels'] as $hotel)
            <div>
                <div class="h3">
                    {{ $hotel['hotel'][0]['hotelBasicInfo']['hotelNo'] }}
                    {{ $hotel['hotel'][0]['hotelBasicInfo']['hotelName'] }}
                </div>
                {{ $hotel['hotel'][0]['hotelBasicInfo']['hotelSpecial'] }}
                {{ $hotel['hotel'][0]['hotelBasicInfo']['address2'] }}

                <a href="{{ route('admin.rakuten.hotel-detail',
                    ['hotel' => $hotel['hotel'][0]['hotelBasicInfo']['hotelNo']]) }}">
                    詳細
                </a>
            </div>
        @endforeach
    </div>
@endsection
