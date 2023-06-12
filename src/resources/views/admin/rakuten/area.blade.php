@extends('layouts.admin')

@section('title', '楽天')

@section('content')

    <div class="container">
        <div class="h1">楽天API</div>
        @foreach ($areas_array['areaClasses']['largeClasses'] as $area)
            @foreach ($area['largeClass'][1]['middleClasses'] as $middle)
                <div class="row">
                    <div class="col">
                        {{ $middle['middleClass'][0]['middleClassName'] }}
                    </div>
                    @foreach ($middle['middleClass'][1]['smallClasses'] as $small)
                        <div class="col">
                            <a
                                href="{{ route('admin.rakuten.area-small',
                                    ['middle' => $middle['middleClass'][0]['middleClassCode'], 'small' => $small['smallClass'][0]['smallClassCode']]) }}">                           {{ $small['smallClass'][0]['smallClassName'] }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endforeach

    </div>
@endsection
