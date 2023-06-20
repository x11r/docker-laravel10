@extends('layouts.admin')
@section('title', '楽天トラベルAPIホテル')
@section('content')
    <div class="container">
        <div class="">
            <div class="h3">楽天トラベルAPIホテル</div>
            @if (isset($hotel['hotel'][0]['hotelBasicInfo']['hotelName']))
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
                    @else
                    <div class="p-2 border border-gray-200">
                        {{ $value['roomInfo'][0]['roomBasicInfo']['roomName'] }}<br />
                        {{ $value['roomInfo'][0]['roomBasicInfo']['planName'] }}
                    </div>
                    @endif
                @endforeach
            @else
                @foreach ($hotel['hotels'][0]['hotel'] as $key => $info)
                    @if ($key === 0)
                        <div class="h3">
                            {{ $info['hotelBasicInfo']['hotelNo'] }}
                            {{ $info['hotelBasicInfo']['hotelName'] }}
                        </div>
                    @else
                        <div class="bg-light">
                            <div class="h5">部屋情報その{{ $key  }}</div>
                            <div class="ms-3 my-2">{{ $info['roomInfo'][0]['roomBasicInfo']['roomName'] }}</div>
                            <div class="ms-3 my-2">{{ $info['roomInfo'][0]['roomBasicInfo']['planName'] }}</div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
@endsection
