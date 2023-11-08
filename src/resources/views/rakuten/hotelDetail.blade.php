@extends('layouts.rakuten')

@section('title', '楽天トラベルAPI ホテル詳細')

@section('content')
    <div class="container">
        <div class="h1 bg-red-100 p-2">
            <a href="{{ route('app.rakuten.areas') }}">楽天トラベルAPI</a> ホテル詳細
        </div>
        <div class="h2">{{ $hotels[0]->hotel[0]->hotelBasicInfo->hotelName }}</div>
        <div class="bg-red-100 m-1">
            @foreach ($hotels[0]->hotel as $hotelKey => $hotelDetail)
                @if ($hotelKey === 0)
                    <div class="bg-white m-1">
                        <div>
                            {{ $hotelDetail->hotelBasicInfo->hotelName }}
                            <span class="small">({{ $hotelDetail->hotelBasicInfo->hotelKanaName }})</span>
                        </div>
                        <div>
                            <ul>
                                <li>
                                    <a href="{{ $hotelDetail->hotelBasicInfo->hotelInformationUrl }}">
                                    hotelInformationUrl
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ $hotelDetail->hotelBasicInfo->planListUrl }}">
                                        planListUrl
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ $hotelDetail->hotelBasicInfo->dpPlanListUrl }}">
                                        dpPlanListUrl
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ $hotelDetail->hotelBasicInfo->reviewUrl }}">
                                        reviewUrl
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div>special {{ $hotelDetail->hotelBasicInfo->hotelSpecial }}</div>
                        <div>最低料金:{{ $hotelDetail->hotelBasicInfo->hotelMinCharge }}</div>
                        <div>緯度：{{ $hotelDetail->hotelBasicInfo->latitude }}  軽度：{{ $hotelDetail->hotelBasicInfo->longitude }}</div>
                        <div>{{ $hotelDetail->hotelBasicInfo->postalCode }}</div>
                        <div>{{ $hotelDetail->hotelBasicInfo->address1 }}</div>
                        <div>{{ $hotelDetail->hotelBasicInfo->address2 }}</div>
                        <div>{{ $hotelDetail->hotelBasicInfo->telephoneNo }}</div>
                        <div>{{ $hotelDetail->hotelBasicInfo->faxNo }}</div>
                        <div>{{ $hotelDetail->hotelBasicInfo->access }}</div>
                        <div>{{ $hotelDetail->hotelBasicInfo->parkingInformation }}</div>
                        <div>最寄り駅：{{ $hotelDetail->hotelBasicInfo->nearestStation }}</div>
                        <div><img src="{{ $hotelDetail->hotelBasicInfo->hotelImageUrl }}" alt=""></div>
{{--                            <div><img src="{{ $hotelDetail->hotelBasicInfo->hotelThumbnailUrl }}" alt=""></div>--}}
                        <div><img src="{{ $hotelDetail->hotelBasicInfo->roomImageUrl }}" alt=""></div>
{{--                            <div><img src="{{ $hotelDetail->hotelBasicInfo->roomThumbnailUrl }}" alt=""></div>--}}
                        <div><img src="{{ $hotelDetail->hotelBasicInfo->hotelMapImageUrl }}" alt=""></div>
                        <div>レビュー数：{{ $hotelDetail->hotelBasicInfo->reviewCount }}</div>
                        <div>平均レビュー：{{ $hotelDetail->hotelBasicInfo->reviewAverage }}</div>
                        <div>ユーザーレビュー：{{ $hotelDetail->hotelBasicInfo->userReview }}</div>
                    </div>
                @else
                    <div class="bg-white m-2">
                        @php
                            $roomBasicInfo = $hotelDetail->roomInfo[0]->roomBasicInfo;
							$dailyCharge = $hotelDetail->roomInfo[1]->dailyCharge;
                        @endphp

                        <div>部屋クラス = {{ $roomBasicInfo->roomClass }}</div>
                        <div>部屋名：{{ $roomBasicInfo->roomName }}</div>
                        <div>プランID：{{ $roomBasicInfo->planId }}</div>
                        <div>プラン名：{{ $roomBasicInfo->planName }}</div>
                        <div>ポイントレート；{{ $roomBasicInfo->pointRate }}</div>
                        <div>夕飯付きフラグ：{{ $roomBasicInfo->withDinnerFlag }}</div>
                        <div>夕飯選択フラグ：{{ $roomBasicInfo->dinnerSelectFlag }}</div>
                        <div>朝食付きフラグ：{{ $roomBasicInfo->withBreakfastFlag }}</div>
                        <div>朝食選択フラグ：{{ $roomBasicInfo->breakfastSelectFlag }}</div>
                        <div>支払い：{{ $roomBasicInfo->payment }}</div>
                        <div>予約URL：{{ $roomBasicInfo->reserveUrl }}</div>
                        <div>営業フォームURL：{{ $roomBasicInfo->salesformFlag }}</div>

                        <div>滞在日：{{ $dailyCharge->stayDate }}</div>
                        <div>楽天チャージ：{{ $dailyCharge->stayDate }}</div>
                        <div>合計：{{ $dailyCharge->total }}</div>
                        <div>チャージフラグ：{{ $dailyCharge->chargeFlag }}</div>

                    </div>
                @endif
            @endforeach

        </div>
    </div>
@endsection
