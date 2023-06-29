@extends('layouts.admin')

@section('title', '楽天')

@section('content')
    <div class="container">
        <div class="h1">楽天API エリア</div>
        @foreach ($areas['areaClasses']['largeClasses'][0]['largeClass'][1]['middleClasses'] as $middle)
            <div>
                <div class="h4">{{ $middle['middleClass'][0]['middleClassName'] }}</div>

                @foreach ($middle['middleClass'][1]['smallClasses'] as $small)
                    <div class="row pl-3">
                        @if (isset($small['smallClass'][1]['detailClasses']))
                            {{ $small['smallClass'][0]['smallClassName'] }}
                            @foreach ($small['smallClass'][1]['detailClasses'] as $detail)
                                <div class="ml-3 col">
                                <a href="{{ route('rakuten.area-detail', [
                                    'middle' => $middle['middleClass'][0]['middleClassCode'],
                                    'small' => $small['smallClass'][0]['smallClassCode'],
                                    'detail' => $detail['detailClass']['detailClassCode']]) }}">

                                    {{ $detail['detailClass']['detailClassName'] }}
                                    </a>
                                </div>
                            @endforeach
                        @else
                            <div class="col">
                                <a href="{{ route('rakuten.area-small', [
                                    'middle' => $middle['middleClass'][0]['middleClassCode'],
                                    'small' => $small['smallClass'][0]['smallClassCode']
                                    ]) }}">
                                    {{ $small['smallClass'][0]['smallClassName'] }}
                                </a>
                            </div>
                        @endif
                    </div>

                @endforeach
            </div>
        @endforeach
    </div>
@endsection
