@extends('layouts.admin')

@section('title', '楽天')

@section('content')
    <div class="container">
        <div class="h1"><a href="{{ route('admin.rakuten.index')  }}">楽天API</a></div>
        <div class="h2">ホテル</div>
        @foreach ($hotels['hotels'] as $hotel)
            <div class="row py-3 border border-gray-100">
                <div class="h4">
                    <a href="{{ route('admin.rakuten.hotel-detail', ['hotel' => $hotel['hotel'][0]['hotelBasicInfo']['hotelNo']]) }}">
                        {{ $hotel['hotel'][0]['hotelBasicInfo']['hotelName'] }}
                    </a>
                </div>
                <div>
                    {{ $hotel['hotel'][0]['hotelBasicInfo']['postalCode'] }}
                    {{ $hotel['hotel'][0]['hotelBasicInfo']['address1'] }}
                    {{ $hotel['hotel'][0]['hotelBasicInfo']['address2'] }}
                </div>
                <div class="border">Tel:{{ $hotel['hotel'][0]['hotelBasicInfo']['telephoneNo'] }} </div>
                <div class="border">Access{{ $hotel['hotel'][0]['hotelBasicInfo']['access'] }} </div>
                <div class="border">Tel:{{ $hotel['hotel'][0]['hotelBasicInfo']['parkingInformation'] }} </div>
                <div class="border">{{ $hotel['hotel'][0]['hotelBasicInfo']['hotelSpecial'] }} </div>
                <div class="border">
                    緯度{{ $hotel['hotel'][0]['hotelBasicInfo']['longitude'] / 10000 }}
                    経度{{ $hotel['hotel'][0]['hotelBasicInfo']['latitude'] / 1000 }}
                </div>
            </div>
        @endforeach
    </div>
@endsection
