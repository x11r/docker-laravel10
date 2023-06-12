@extends('layouts.admin')
@section('title', '楽天トラベルAPIホテル')
@section('content')
    <div class="container">
        <div class="">
            楽天トラベルAPIホテル
            <div class="h3">{{ $hotel['hotel'][0]['hotelBasicInfo']['hotelName'] }}</div>
            <div>

                住所：{{ $hotel['hotel'][0]['hotelBasicInfo']['postalCode'] }}<br />
                {{ $hotel['hotel'][0]['hotelBasicInfo']['address1'] }}<br />
                {{ $hotel['hotel'][0]['hotelBasicInfo']['address2'] }}<br />
                緯度：{{ $hotel['hotel'][0]['hotelBasicInfo']['latitude'] }}<br />
                軽度：{{ $hotel['hotel'][0]['hotelBasicInfo']['longitude'] }}
            </div>

            @foreach ($hotel['hotel'] as $i => $value)
                @if ($i === 0)
{{--                    <div>{{ $i }}--}}
{{--                        {{ $value['hotelBasicInfo']['hotelName'] }}--}}
{{--                    </div>--}}
                @else
                <div class="p-2 border border-gray-200">
                    {{ $value['roomInfo'][0]['roomBasicInfo']['roomName'] }}<br />
                    {{ $value['roomInfo'][0]['roomBasicInfo']['planName'] }}
                </div>
                @endif
            @endforeach

        </div>
    </div>
@endsection
