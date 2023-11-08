@extends('layouts.rakuten')

@section('title', '楽天鳥ベル一覧 エリア一覧')

@section('content')
    <div class="container">
        <div class="h1 bg-red-100 p-2">
            楽天トラベルAPI エリア一覧
        </div>
        @foreach ($areas['areaClasses']['largeClasses'][0]['largeClass'][1]['middleClasses'] as $middle)
            <div class="bg-red-50 pt-1 pb-1">
                <!-- 都道府県 -->
                <div class="h2 pl-2">
                    {{ $middle['middleClass'][0]['middleClassName'] }}
                </div>
                @foreach ($middle['middleClass'][1]['smallClasses'] as $small)

                    <div class="row m-1 bg-gray-50">
                        @if (isset($small['smallClass'][1]['detailClasses']))
                            <!-- 詳細がある場合 -->
                            <div class="">
                                {{ $small['smallClass'][0]['smallClassName'] }}
                            </div>

                            <ol>
                            @foreach ($small['smallClass'][1]['detailClasses'] as $detail)
                                <li class="ml-3 col">
                                <a href="{{ route('rakuten.area-detail', [
                                    'middle' => $middle['middleClass'][0]['middleClassCode'],
                                    'small' => $small['smallClass'][0]['smallClassCode'],
                                    'detail' => $detail['detailClass']['detailClassCode']]) }}">

                                    {{ $detail['detailClass']['detailClassName'] }}
                                    </a>
                                </li>
                            @endforeach
                            </ol>

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
