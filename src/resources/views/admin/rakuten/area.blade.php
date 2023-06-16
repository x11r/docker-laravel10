@extends('layouts.admin')

@section('title', '楽天')

@section('content')
    <div class="container">
        <div class="h1">楽天API</div>
        @foreach ($hotels['hotels'] as $hotel)
            <div class="row py-3">
                <div class="h4">
                    {{ $hotel['hotel'][0]['hotelBasicInfo']['hotelName'] }}
                </div>
                <div>
                    {{ $hotel['hotel'][0]['hotelBasicInfo']['postalCode'] }}
                    {{ $hotel['hotel'][0]['hotelBasicInfo']['address1'] }}
                    {{ $hotel['hotel'][0]['hotelBasicInfo']['address2'] }}
                </div>
                <div>Tel:{{ $hotel['hotel'][0]['hotelBasicInfo']['telephoneNo'] }} </div>
                <div>Access{{ $hotel['hotel'][0]['hotelBasicInfo']['access'] }} </div>
                <div>Tel:{{ $hotel['hotel'][0]['hotelBasicInfo']['parkingInformation'] }} </div>
                <div>{{ $hotel['hotel'][0]['hotelBasicInfo']['hotelSpecial'] }} </div>
                <div class="border border-gray-100">
                    緯度{{ $hotel['hotel'][0]['hotelBasicInfo']['longitude'] / 10000 }}
                    経度{{ $hotel['hotel'][0]['hotelBasicInfo']['latitude'] / 1000 }}
                </div>
{{--                <div class="">--}}
{{--                    {{ $hotel['hotel'][0]['hotelBasicInfo']['hotelNo'] }}--}}
{{--                </div>--}}
            </div>
        @endforeach
    </div>
@endsection
