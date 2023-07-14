@extends('layouts.rakuten')

@section('title', '楽天トラベルAPI エリア一覧')

@section('content')
    <div class="container">
        <div class="h1 bg-red-100 p-2">
            楽天トラベルAPI エリア一覧
        </div>

        @if (! $areas)
            取得エラーが発生しました。
        @else

            @php
                $middleClasses = $areas->areaClasses->largeClasses[0]->largeClass[1]->middleClasses;
            @endphp

            @foreach ($middleClasses as $middle)
                <div class="bg-red-50 pt-1 pb-1">
                    <!-- 都道府県 -->
                    <div class="h2 px-2 ">
                        {{ $middle->middleClass[0]->middleClassName }}
                    </div>
                    <ul class="row pr-5">
                        @foreach ($middle->middleClass[1]->smallClasses as $small)
                            @if (isset($small->smallClass[1]->detailClasses))
                                <li class="bg-white">
                                    <!-- 詳細がある場合 -->
                                    <div class="bg-white">
                                        <div class="h5">{{ $small->smallClass[0]->smallClassName }}</div>

                                        <ul class="row my-2 pr-5">
                                            @foreach ($small->smallClass[1]->detailClasses as $detail)
                                                <li class="col-md ml-1 text-nowrap">
                                                    <a href="{{ route('rakuten.area-detail', [
                                                        'middle' => $middle->middleClass[0]->middleClassCode,
                                                        'small' => $small->smallClass[0]->smallClassCode,
                                                        'detail' => $detail->detailClass->detailClassCode]) }}"
                                                    >
                                                        {{ $detail->detailClass->detailClassName }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                            @else
                                <li class="bg-gray-50 col text-nowrap">
                                    <a href="{{ route('rakuten.area-small', [
                                        'middle' => $middle->middleClass[0]->middleClassCode,
                                        'small' => $small->smallClass[0]->smallClassCode
                                    ]) }}">
                                        {{ $small->smallClass[0]->smallClassName }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endforeach
        @endif
    </div>
@endsection
