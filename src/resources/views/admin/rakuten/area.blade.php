@extends('layouts.admin')

@section('title', '楽天')

@section('content')

    <div class="container">
        <div class="h1">楽天API</div>

{{--        @foreach ($areas->areaClasses->largeClasses[0]->largeClass[1]->middleClasses as $key1 => $middleClass)--}}
{{--            {{ dd($middleClass) }}--}}
{{--            {{ $middleClass[0]->middleClass[0]->middleClassName }}--}}

{{--        @endforeach--}}

        @foreach ($areas_array['areaClasses']['largeClasses'] as $area)
{{--            <div>--}}
{{--                {{ $area['largeClass'][0]['largeClassName'] }}--}}
{{--                {{ $area['largeClass'][0]['largeClassCode'] }}--}}
{{--            </div>--}}
            @foreach ($area['largeClass'][1]['middleClasses'] as $middle)
                <div class="row">
                    <div class="col">
{{--                        <a href="{{ route('admin.rakuten.area-middle',['middle' => $middle['middleClass'][0]['middleClassCode']]) }}">--}}
{{--                            {{ $middle['middleClass'][0]['middleClassName'] }}--}}
{{--                        </a>--}}
                        {{ $middle['middleClass'][0]['middleClassName'] }}
                    </div>
                    @foreach ($middle['middleClass'][1]['smallClasses'] as $small)
                        <div class="col">
                            <a href="{{ route('admin.rakuten.area-small', ['middle' => $middle['middleClass'][0]['middleClassCode'], 'small' => $small['smallClass'][0]['smallClassCode']]) }}">                           {{ $small['smallClass'][0]['smallClassName'] }}
                            </a>
{{--                        {{ $small['smallClass'][0]['smallClassCode'] }}--}}
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endforeach

    </div>
@endsection
