@extends('layouts.rakuten')

@section('title', '楽天トラベルAPI 地域別ホテル一覧')

@section('content')
    <div class="container">
        <div class="h1 bg-red-50 p-2">
            <a href="{{ route('app.rakuten.areas')  }}">楽天トラベルAPI</a> 地域別ホテル一覧
        </div>
        @foreach ($hotels['hotels'] as $hotel)
            @php
                $hotelBasicInfo = $hotel['hotel'][0]['hotelBasicInfo']
            @endphp

            <div class="row bg-red-50 pt-1 pb-1">

                <div class="row m-1 p-2 bg-white">
                    <div class="col">
                        <div class="h4">
                            <a href="{{ route('app.rakuten.hotel-detail', ['hotelNo' => $hotelBasicInfo['hotelNo']]) }}">
                                {{ $hotelBasicInfo['hotelName'] }}
                            </a>
                        </div>
                        <div>
                            {{ $hotelBasicInfo['postalCode'] }}
                            {{ $hotelBasicInfo['address1'] }}
                            {{ $hotelBasicInfo['address2'] }}
                        </div>
                        <div>Tel:{{ $hotelBasicInfo['telephoneNo'] }} </div>
                        <div>Access{{ $hotelBasicInfo['access'] }} </div>
                        <div>Tel:{{ $hotelBasicInfo['parkingInformation'] }} </div>
                        <div>{{ $hotelBasicInfo['hotelSpecial'] }} </div>

                    </div>
                    <div class="col">
                        @if (! empty($hotelBasicInfo['hotelThumbnailUrl']))
                            <img src="{{ $hotelBasicInfo['hotelThumbnailUrl'] }}" alt="">
                        @endif
                    </div>
                </div>
            </div>

{{--            <pre>{{ print_r($hotel) }}</pre>--}}
        @endforeach
    </div>
@endsection
