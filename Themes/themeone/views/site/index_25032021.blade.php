@extends('layouts.sitelayout')
@section('content')
<!--Section-->
<section class="sptb">
    <div class="container">
        <div class="section-title center-block text-center">
            <h2 style="color: #ca282d;">
                KHÓA HỌC ONLINE
            </h2>
            <span class="sectiontitle-design">
                <span class="icons">
                </span>
            </span>
            <p>
                Không khoảng cách, không giới hạn thời gian, tính ứng dụng cao, không những vậy khoá học trực tuyến còn giúp bạn tăng tính độc lập trong việc học. Hikari Academy cung cấp cho bạn một khóa học với các trình độ và kĩ năng, đáp ứng nhu cầu của mọi người.
            </p>
        </div>
        <div class="panel panel-primary">

            


            <div class="">
                <div class="tabs-menu ">
                    <!-- Tabs -->
                    <ul class="nav panel-tabs eductaional-tabs mb-6">
                        <li class="">
                            <a class="active show" data-toggle="tab" href="#tabel1">
                                Tất cả
                            </a>
                        </li>
                        <li>
                            <a class="" data-toggle="tab" href="#tabel2">
                                Khóa học N5
                            </a>
                        </li>
                        <li>
                            <a class="" data-toggle="tab" href="#tabel3">
                                Khóa học N4
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active show" id="tabel1">
                        <div class="row">
                            @if(count($series_el) > 0)
                                @foreach($series_el as $r)
                                <div class="col-xl-4 col-md-6">
                                    <div class="card overflow-hidden">
                                        <div class="ribbon ribbon-top-left text-danger">
                                            <span class="bg-danger">
                                                @if($r->cost == 0) Miễn phí @else Khuyến mại @endif
                                            </span>
                                        </div>
                                        <div class="item-card7-img">
                                            <div class="item-card7-imgs">
                                                @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                            @if($r->total_items == 1)
                                                <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                                </a>
                                                @else
                                                <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                </a>
                                                @endif
                                                        @elseif($r->try_lmscontents > 0)
                                                            @if($r->total_items == 1)
                                                <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                                </a>
                                                @else
                                                <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                </a>
                                                @endif
                                                        @else
                                                <a href="/payments/lms/{{$r->slug}}">
                                                </a>
                                                @endif
                                                <img alt="img" class="cover-image" src="{{ '/public/uploads/lms/combo/'.$r->image}}">
                                                </img>
                                            </div>
                                            <div class="item-card7-overlaytext">
                                                <a class="text-white" href="#">
                                                    #{{$r->code}}
                                                </a>
                                                <h4 class="mb-0 font-weight-semibold fs-16">
                                                    {{ number_format($r->cost, 0, 0, '.')}}đ
                                                    <del class="h4 text-muted ml-2 fs-12">
                                                        {{ number_format($r->selloff, 0, 0, '.')}}đ
                                                    </del>
                                                </h4>
                                            </div>
                                        </div>
                                    <div class="card-body">
                                        <div class="item-card7-desc">
                                            <div class="item-card7-text" style="max-height: 7vh;height: 5vh;">
                                                @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                            @if($r->total_items == 1)
                                                <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug_lmscontents}}">
                                                    @else
                                                    <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                        @endif
                                                        @elseif($r->try_lmscontents > 0)
                                                        @if($r->total_items == 1)
                                                        <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                                            @else
                                                            <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                                @endif
                                                                                                @else
                                                                <a class="text-dark" href="/payments/lms/{{$r->slug}}">
                                                                    @endif
                                                                    <h3 class="font-weight-semibold">
                                                                        {{ $r->title }}
                                                                    </h3>
                                                                </a>
                                                            </a>
                                                        </a>
                                                    </a>
                                                </a>
                                            </div>
                                            {!! limit_words($r->short_description,26) !!}
                                        </div>
                                    </div>
                                    <div class="card-body p-4 pl-5">
                                        <?php $time_options = array(0 =>
                                        '3 tháng', 1 => '6 tháng' , 2 => '12 tháng');  ?>
                                        <a class="mr-4">
                                            <span class="font-weight-bold">
                                                Thời gian:
                                            </span>
                                            <span class="text-muted">
                                                {{$time_options[$r->time]}}
                                            </span>
                                        </a>
                                        <a class="mr-4 float-right">
                                            <span class="font-weight-bold">
                                                Bài học:
                                            </span>
                                            <span class="font-weight-bold text-muted text-danger">
                                                {{$r->lmscontents}}
                                            </span>
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                    @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học ngay
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                                @elseif($r->try_lmscontents > 0)
                                                    @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học thử
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                                @else
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                                {{--
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua khóa luyện thi
                                        </a>
                                        --}}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <h3 class="text-primary">
                                Chưa có khóa học
                            </h3>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane" id="tabel2">
                        <div class="row">
                            @if(count($series_el5) > 0)
                                @foreach($series_el5 as $r)
                            <div class="col-xl-4 col-md-6">
                                <div class="card overflow-hidden">
                                    <div class="ribbon ribbon-top-left text-danger">
                                        <span class="bg-danger">
                                            @if($r->cost == 0) miễn phí @else Khuyến mại @endif
                                        </span>
                                    </div>
                                    <div class="item-card7-img">
                                        <div class="item-card7-imgs">
                                            @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                        @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                                    @elseif($r->try_lmscontents > 0)
                                                        @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                                    @else
                                            <a href="/payments/lms/{{$r->slug}}">
                                            </a>
                                            @endif
                                            <img alt="img" class="cover-image" src="{{ '/public/uploads/lms/combo/'.$r->image}}">
                                            </img>
                                        </div>
                                        <div class="item-card7-overlaytext">
                                            <a class="text-white" href="#">
                                                #{{$r->code}}
                                            </a>
                                            <h4 class="mb-0 font-weight-semibold fs-16">
                                                {{ number_format($r->cost, 0, 0, '.')}}đ
                                                <del class="h4 text-muted ml-2 fs-12">
                                                    {{ number_format($r->selloff, 0, 0, '.')}}đ
                                                </del>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="item-card7-desc">
                                            <div class="item-card7-text" style="max-height: 7vh;height: 5vh;">
                                                @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                            @if($r->total_items == 1)
                                                <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug_lmscontents}}">
                                                    @else
                                                    <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                        @endif
                                                                            @elseif($r->try_lmscontents > 0)
                                                                                @if($r->total_items == 1)
                                                        <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                                            @else
                                                            <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                                @endif
                                                                                                @else
                                                                <a class="text-dark" href="/payments/lms/{{$r->slug}}">
                                                                    @endif
                                                                    <h3 class="font-weight-semibold">
                                                                        {{ $r->title }}
                                                                    </h3>
                                                                </a>
                                                            </a>
                                                        </a>
                                                    </a>
                                                </a>
                                            </div>
                                            {!! limit_words($r->short_description,26) !!}
                                        </div>
                                    </div>
                                    <div class="card-body p-4 pl-5">
                                        <?php $time_options = array(0 =>
                                        '3 tháng', 1 => '6 tháng' , 2 => '12 tháng');  ?>
                                        <a class="mr-4">
                                            <span class="font-weight-bold">
                                                Thời gian:
                                            </span>
                                            <span class="text-muted">
                                                {{$time_options[$r->time]}}
                                            </span>
                                        </a>
                                        <a class="mr-4 float-right">
                                            <span class="font-weight-bold">
                                                Bài học:
                                            </span>
                                            <span class="font-weight-bold text-muted text-danger">
                                                {{$r->lmscontents}}
                                            </span>
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                    @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học ngay
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                                @elseif($r->try_lmscontents > 0)
                                                    @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học thử
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                                @else
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                                {{--
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua khóa luyện thi
                                        </a>
                                        --}}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <h3 class="text-primary">
                                Chưa có khóa học N5
                            </h3>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane" id="tabel3">
                        <div class="row">
                            @if(count($series_el4) > 0)
                                @foreach($series_el4 as $r)
                            <div class="col-xl-4 col-md-6">
                                <div class="card overflow-hidden">
                                    <div class="ribbon ribbon-top-left text-danger">
                                        <span class="bg-danger">
                                            @if($r->cost == 0) miễn phí @else Khuyến mại @endif
                                        </span>
                                    </div>
                                    <div class="item-card7-img">
                                        <div class="item-card7-imgs">
                                            @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                        @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                                    @elseif($r->try_lmscontents > 0)
                                                        @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                                    @else
                                            <a href="/payments/lms/{{$r->slug}}">
                                            </a>
                                            @endif
                                            <img alt="img" class="cover-image" src="{{ '/public/uploads/lms/combo/'.$r->image}}">
                                            </img>
                                        </div>
                                        <div class="item-card7-overlaytext">
                                            <a class="text-white" href="#">
                                                #{{$r->code}}
                                            </a>
                                            <h4 class="mb-0 font-weight-semibold fs-16">
                                                {{ number_format($r->cost, 0, 0, '.')}}đ
                                                <del class="h4 text-muted ml-2 fs-12">
                                                    {{ number_format($r->selloff, 0, 0, '.')}}đ
                                                </del>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="item-card7-desc">
                                            <div class="item-card7-text" style="max-height: 7vh;height: 5vh;">
                                                @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                            @if($r->total_items == 1)
                                                <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug_lmscontents}}">
                                                    @else
                                                    <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                        @endif
                                                                            @elseif($r->try_lmscontents > 0)
                                                                                @if($r->total_items == 1)
                                                        <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                                            @else
                                                            <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                                @endif
                                                                                                @else
                                                                <a class="text-dark" href="/payments/lms/{{$r->slug}}">
                                                                    @endif
                                                                    <h3 class="font-weight-semibold">
                                                                        {{ $r->title }}
                                                                    </h3>
                                                                </a>
                                                            </a>
                                                        </a>
                                                    </a>
                                                </a>
                                            </div>
                                            {!! limit_words($r->short_description,26) !!}
                                        </div>
                                    </div>
                                    <div class="card-body p-4 pl-5">
                                        <?php $time_options = array(0 =>
                                        '3 tháng', 1 => '6 tháng' , 2 => '12 tháng');  ?>
                                        <a class="mr-4">
                                            <span class="font-weight-bold">
                                                Thời gian:
                                            </span>
                                            <span class="text-muted">
                                                {{$time_options[$r->time]}}
                                            </span>
                                        </a>
                                        <a class="mr-4 float-right">
                                            <span class="font-weight-bold">
                                                Bài học:
                                            </span>
                                            <span class="font-weight-bold text-muted text-danger">
                                                {{$r->lmscontents}}
                                            </span>
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                    @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học ngay
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                                @elseif($r->try_lmscontents > 0)
                                                    @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học thử
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                                @else
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                                {{--
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua khóa luyện thi
                                        </a>
                                        --}}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <h3 class="text-primary">
                                Chưa có khóa học N4
                            </h3>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane" id="tabel4">
                        <div class="row">
                            @if(count($series_el3) > 0)
                                @foreach($series_el3 as $r)
                            <div class="col-xl-4 col-md-6">
                                <div class="card overflow-hidden">
                                    <div class="ribbon ribbon-top-left text-danger">
                                        <span class="bg-danger">
                                            @if($r->cost == 0) miễn phí @else Khuyến mại @endif
                                        </span>
                                    </div>
                                    <div class="item-card7-img">
                                        <div class="item-card7-imgs">
                                            @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                        @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                                    @elseif($r->try_lmscontents > 0)
                                                        @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                                    @else
                                            <a href="/payments/lms/{{$r->slug}}">
                                            </a>
                                            @endif
                                            <img alt="img" class="cover-image" src="{{ '/public/uploads/lms/combo/'.$r->image}}">
                                            </img>
                                        </div>
                                        <div class="item-card7-overlaytext">
                                            <a class="text-white" href="#">
                                                #{{$r->code}}
                                            </a>
                                            <h4 class="mb-0 font-weight-semibold fs-16">
                                                {{ number_format($r->cost, 0, 0, '.')}}đ
                                                <del class="h4 text-muted ml-2 fs-12">
                                                    {{ number_format($r->selloff, 0, 0, '.')}}đ
                                                </del>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="item-card7-desc">
                                            <div class="item-card7-text" style="max-height: 7vh;height: 5vh;">
                                                @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                            @if($r->total_items == 1)
                                                <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug_lmscontents}}">
                                                    @else
                                                    <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                        @endif
                                                                            @elseif($r->try_lmscontents > 0)
                                                                                @if($r->total_items == 1)
                                                        <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                                            @else
                                                            <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                                @endif
                                                                                                @else
                                                                <a class="text-dark" href="/payments/lms/{{$r->slug}}">
                                                                    @endif
                                                                    <h3 class="font-weight-semibold">
                                                                        {{ $r->title }}
                                                                    </h3>
                                                                </a>
                                                            </a>
                                                        </a>
                                                    </a>
                                                </a>
                                            </div>
                                            {!! limit_words($r->short_description,26) !!}
                                        </div>
                                    </div>
                                    <div class="card-body p-4 pl-5">
                                        <?php $time_options = array(0 =>
                                        '3 tháng', 1 => '6 tháng' , 2 => '12 tháng');  ?>
                                        <a class="mr-4">
                                            <span class="font-weight-bold">
                                                Thời gian:
                                            </span>
                                            <span class="text-muted">
                                                {{$time_options[$r->time]}}
                                            </span>
                                        </a>
                                        <a class="mr-4 float-right">
                                            <span class="font-weight-bold">
                                                Bài học:
                                            </span>
                                            <span class="font-weight-bold text-muted text-danger">
                                                {{$r->lmscontents}}
                                            </span>
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                    @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học ngay
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                                @elseif($r->try_lmscontents > 0)
                                                    @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học thử
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                                @else
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                                {{--
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua khóa luyện thi
                                        </a>
                                        --}}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <h3 class="text-primary">
                                Chưa có khóa học N3
                            </h3>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="sptb">
    <div class="container">
        <div class="section-title center-block text-center">
            <h2 style="color: #ca282d;">
                KHÓA LUYỆN THI ONLINE
            </h2>
            <span class="sectiontitle-design">
                <span class="icons">
                </span>
            </span>
            <p>
                Không khoảng cách, không giới hạn thời gian, tính ứng dụng cao, không những vậy khoá học trực tuyến còn giúp bạn tăng tính độc lập trong việc học. Hikari Academy cung cấp cho bạn một khóa học với các trình độ và kĩ năng, đáp ứng nhu cầu của mọi người.
            </p>
        </div>
        <div class="panel panel-primary">
            <div class="">
                <div class="tabs-menu ">
                    <!-- Tabs -->
                    <ul class="nav panel-tabs eductaional-tabs mb-6">
                        <li class="">
                            <a class="active show" data-toggle="tab" href="#tab1">
                                Tất cả
                            </a>
                        </li>
                        {{--
                        <li>
                            <a class="" data-toggle="tab" href="#tab2">
                                Khóa luyện thi N5
                            </a>
                        </li>
                        --}}
                        <li>
                            <a class="" data-toggle="tab" href="#tab3">
                                Khóa luyện thi N4
                            </a>
                        </li>
                        <li>
                            <a class="" data-toggle="tab" href="#tab4">
                                Khóa luyện thi N3
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active show" id="tab1">
                        <div class="row">
                            @if(count($series) > 0)
                        @foreach($series as $r)
                            <div class="col-xl-4 col-md-6">
                                <div class="card overflow-hidden">
                                    <div class="ribbon ribbon-top-left text-danger">
                                        <span class="bg-danger">
                                            @if($r->cost == 0) miễn phí @else Khuyến mại @endif
                                        </span>
                                    </div>
                                    <div class="item-card7-img">
                                        <div class="item-card7-imgs">
                                            @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                            @elseif($r->try_lmscontents > 0)
                                                @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                            @else
                                            <a href="/payments/lms/{{$r->slug}}">
                                            </a>
                                            @endif
                                            <img alt="img" class="cover-image" src="{{ '/public/uploads/lms/combo/'.$r->image}}">
                                            </img>
                                        </div>
                                        <div class="item-card7-overlaytext">
                                            <a class="text-white" href="#">
                                                #{{$r->code}}
                                            </a>
                                            <h4 class="mb-0 font-weight-semibold fs-16">
                                                {{ number_format($r->cost, 0, 0, '.')}}đ
                                                <del class="h4 text-muted ml-2 fs-12">
                                                    {{ number_format($r->selloff, 0, 0, '.')}}đ
                                                </del>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="item-card7-desc">
                                            <div class="item-card7-text" style="max-height: 7vh;height: 5vh;">
                                                @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                    @if($r->total_items == 1)
                                                <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug_lmscontents}}">
                                                    @else
                                                    <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                        @endif
                                                                    @elseif($r->try_lmscontents > 0)
                                                                        @if($r->total_items == 1)
                                                        <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                                            @else
                                                            <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                                @endif
                                                                                        @else
                                                                <a class="text-dark" href="/payments/lms/{{$r->slug}}">
                                                                    @endif
                                                                    <h3 class="font-weight-semibold">
                                                                        {{ $r->title }}
                                                                    </h3>
                                                                </a>
                                                            </a>
                                                        </a>
                                                    </a>
                                                </a>
                                            </div>
                                            {!! limit_words($r->short_description,26) !!}
                                        </div>
                                    </div>
                                    <div class="card-body p-4 pl-5">
                                        <?php $time_options = array(0 =>
                                        '3 tháng', 1 => '6 tháng' , 2 => '12 tháng');  ?>
                                        <a class="mr-4">
                                            <span class="font-weight-bold">
                                                Thời gian:
                                            </span>
                                            <span class="text-muted">
                                                {{$time_options[$r->time]}}
                                            </span>
                                        </a>
                                        <a class="mr-4 float-right">
                                            <span class="font-weight-bold">
                                                Bài học:
                                            </span>
                                            <span class="font-weight-bold text-muted text-danger">
                                                {{$r->lmscontents}}
                                            </span>
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                            @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học ngay
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                        @elseif($r->try_lmscontents > 0)
                                            @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học thử
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                        @else
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                        {{--
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua khóa luyện thi
                                        </a>
                                        --}}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                  @else
                            <h3 class="text-primary">
                                Chưa có khóa luyện thi
                            </h3>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane" id="tab2">
                        <div class="row">
                            @if(count($series_5) > 0)
                        @foreach($series_5 as $r)
                            <div class="col-xl-4 col-md-6">
                                <div class="card overflow-hidden">
                                    <div class="ribbon ribbon-top-left text-danger">
                                        <span class="bg-danger">
                                            @if($r->cost == 0) miễn phí @else Khuyến mại @endif
                                        </span>
                                    </div>
                                    <div class="item-card7-img">
                                        <div class="item-card7-imgs">
                                            @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                            @elseif($r->try_lmscontents > 0)
                                                @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                            @else
                                            <a href="/payments/lms/{{$r->slug}}">
                                            </a>
                                            @endif
                                            <img alt="img" class="cover-image" src="{{ '/public/uploads/lms/combo/'.$r->image}}">
                                            </img>
                                        </div>
                                        <div class="item-card7-overlaytext">
                                            <a class="text-white" href="#">
                                                #{{$r->code}}
                                            </a>
                                            <h4 class="mb-0 font-weight-semibold fs-16">
                                                {{ number_format($r->cost, 0, 0, '.')}}đ
                                                <del class="h4 text-muted ml-2 fs-12">
                                                    {{ number_format($r->selloff, 0, 0, '.')}}đ
                                                </del>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="item-card7-desc">
                                            <div class="item-card7-text" style="max-height: 7vh;height: 5vh;">
                                                @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                    @if($r->total_items == 1)
                                                <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug_lmscontents}}">
                                                    @else
                                                    <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                        @endif
                                                                    @elseif($r->try_lmscontents > 0)
                                                                        @if($r->total_items == 1)
                                                        <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                                            @else
                                                            <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                                @endif
                                                                                        @else
                                                                <a class="text-dark" href="/payments/lms/{{$r->slug}}">
                                                                    @endif
                                                                    <h3 class="font-weight-semibold">
                                                                        {{ $r->title }}
                                                                    </h3>
                                                                </a>
                                                            </a>
                                                        </a>
                                                    </a>
                                                </a>
                                            </div>
                                            {!! limit_words($r->short_description,26) !!}
                                        </div>
                                    </div>
                                    <div class="card-body p-4 pl-5">
                                        <?php $time_options = array(0 =>
                                        '3 tháng', 1 => '6 tháng' , 2 => '12 tháng');  ?>
                                        <a class="mr-4">
                                            <span class="font-weight-bold">
                                                Thời gian:
                                            </span>
                                            <span class="text-muted">
                                                {{$time_options[$r->time]}}
                                            </span>
                                        </a>
                                        <a class="mr-4 float-right">
                                            <span class="font-weight-bold">
                                                Bài học:
                                            </span>
                                            <span class="font-weight-bold text-muted text-danger">
                                                {{$r->lmscontents}}
                                            </span>
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                            @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học ngay
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                        @elseif($r->try_lmscontents > 0)
                                            @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học thử
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                        @else
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                        {{--
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua khóa luyện thi
                                        </a>
                                        --}}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                    @else
                            <h3 class="text-primary">
                                Chưa có khóa luyện thi N5
                            </h3>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane" id="tab3">
                        <div class="row">
                            @if(count($series_4) > 0)
                        @foreach($series_4 as $r)
                            <div class="col-xl-4 col-md-6">
                                <div class="card overflow-hidden">
                                    <div class="ribbon ribbon-top-left text-danger">
                                        <span class="bg-danger">
                                            @if($r->cost == 0) miễn phí @else Khuyến mại @endif
                                        </span>
                                    </div>
                                    <div class="item-card7-img">
                                        <div class="item-card7-imgs">
                                            @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                            @elseif($r->try_lmscontents > 0)
                                                @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                            @else
                                            <a href="/payments/lms/{{$r->slug}}">
                                            </a>
                                            @endif
                                            <img alt="img" class="cover-image" src="{{ '/public/uploads/lms/combo/'.$r->image}}">
                                            </img>
                                        </div>
                                        <div class="item-card7-overlaytext">
                                            <a class="text-white" href="#">
                                                #{{$r->code}}
                                            </a>
                                            <h4 class="mb-0 font-weight-semibold fs-16">
                                                {{ number_format($r->cost, 0, 0, '.')}}đ
                                                <del class="h4 text-muted ml-2 fs-12">
                                                    {{ number_format($r->selloff, 0, 0, '.')}}đ
                                                </del>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="item-card7-desc">
                                            <div class="item-card7-text" style="max-height: 7vh;height: 5vh;">
                                                @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                    @if($r->total_items == 1)
                                                <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug_lmscontents}}">
                                                    @else
                                                    <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                        @endif
                                                                    @elseif($r->try_lmscontents > 0)
                                                                        @if($r->total_items == 1)
                                                        <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                                            @else
                                                            <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                                @endif
                                                                                        @else
                                                                <a class="text-dark" href="/payments/lms/{{$r->slug}}">
                                                                    @endif
                                                                    <h3 class="font-weight-semibold">
                                                                        {{ $r->title }}
                                                                    </h3>
                                                                </a>
                                                            </a>
                                                        </a>
                                                    </a>
                                                </a>
                                            </div>
                                            {!! limit_words($r->short_description,26) !!}
                                        </div>
                                    </div>
                                    <div class="card-body p-4 pl-5">
                                        <?php $time_options = array(0 =>
                                        '3 tháng', 1 => '6 tháng' , 2 => '12 tháng');  ?>
                                        <a class="mr-4">
                                            <span class="font-weight-bold">
                                                Thời gian:
                                            </span>
                                            <span class="text-muted">
                                                {{$time_options[$r->time]}}
                                            </span>
                                        </a>
                                        <a class="mr-4 float-right">
                                            <span class="font-weight-bold">
                                                Bài học:
                                            </span>
                                            <span class="font-weight-bold text-muted text-danger">
                                                {{$r->lmscontents}}
                                            </span>
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                            @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học ngay
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                        @elseif($r->try_lmscontents > 0)
                                            @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học thử
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                        @else
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                        {{--
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua khóa luyện thi
                                        </a>
                                        --}}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                    @else
                            <h3 class="text-primary">
                                Chưa có khóa luyện thi N4
                            </h3>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane" id="tab4">
                        <div class="row">
                            @if(count($series_3) > 0)
                        @foreach($series_3 as $r)
                            <div class="col-xl-4 col-md-6">
                                <div class="card overflow-hidden">
                                    <div class="ribbon ribbon-top-left text-danger">
                                        <span class="bg-danger">
                                            @if($r->cost == 0) miễn phí @else Khuyến mại @endif
                                        </span>
                                    </div>
                                    <div class="item-card7-img">
                                        <div class="item-card7-imgs">
                                            @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                            @elseif($r->try_lmscontents > 0)
                                                @if($r->total_items == 1)
                                            <a href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            </a>
                                            @else
                                            <a href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            </a>
                                            @endif
                                            @else
                                            <a href="/payments/lms/{{$r->slug}}">
                                            </a>
                                            @endif
                                            <img alt="img" class="cover-image" src="{{ '/public/uploads/lms/combo/'.$r->image}}">
                                            </img>
                                        </div>
                                        <div class="item-card7-overlaytext">
                                            <a class="text-white" href="#">
                                                #{{$r->code}}
                                            </a>
                                            <h4 class="mb-0 font-weight-semibold fs-16">
                                                {{ number_format($r->cost, 0, 0, '.')}}đ
                                                <del class="h4 text-muted ml-2 fs-12">
                                                    {{ number_format($r->selloff, 0, 0, '.')}}đ
                                                </del>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="item-card7-desc">
                                            <div class="item-card7-text" style="max-height: 7vh;height: 5vh;">
                                                @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                                    @if($r->total_items == 1)
                                                <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug_lmscontents}}">
                                                    @else
                                                    <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                        @endif
                                                                    @elseif($r->try_lmscontents > 0)
                                                                        @if($r->total_items == 1)
                                                        <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                                            @else
                                                            <a class="text-dark" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                                                @endif
                                                                                        @else
                                                                <a class="text-dark" href="/payments/lms/{{$r->slug}}">
                                                                    @endif
                                                                    <h3 class="font-weight-semibold">
                                                                        {{ $r->title }}
                                                                    </h3>
                                                                </a>
                                                            </a>
                                                        </a>
                                                    </a>
                                                </a>
                                            </div>
                                            {!! limit_words($r->short_description,26) !!}
                                        </div>
                                    </div>
                                    <div class="card-body p-4 pl-5">
                                        <?php $time_options = array(0 =>
                                        '3 tháng', 1 => '6 tháng' , 2 => '12 tháng');  ?>
                                        <a class="mr-4">
                                            <span class="font-weight-bold">
                                                Thời gian:
                                            </span>
                                            <span class="text-muted">
                                                {{$time_options[$r->time]}}
                                            </span>
                                        </a>
                                        <a class="mr-4 float-right">
                                            <span class="font-weight-bold">
                                                Bài học:
                                            </span>
                                            <span class="font-weight-bold text-muted text-danger">
                                                {{$r->lmscontents}}
                                            </span>
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        @if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                                            @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học ngay
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                        @elseif($r->try_lmscontents > 0)
                                            @if($r->total_items == 1)
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents}}">
                                            Học thử
                                        </a>
                                        @else
                                        <a class="btn btn-primary btn-block" href="{{PREFIX.'learning-management/lesson/combo/'.$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                        @else
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua ngay
                                        </a>
                                        @endif
                                        {{--
                                        <a class="btn btn-primary btn-block" href="/payments/lms/{{$r->slug}}">
                                            Mua khóa luyện thi
                                        </a>
                                        --}}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                    @else
                            <h3 class="text-primary">
                                Chưa có khóa luyện thi N3
                            </h3>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/Section-->
<!--/Section-->
<!--Section-->
<section>
    <div class="cover-image sptb bg-background-color text-white" data-image-src="/public/assets/images/banners/banner3.jpg">
        <div class="content-text mb-0">
            <div class="container">
                <div class="section-title center-block text-center">
                    <h2>
                        TIẾNG NHẬT KHÓ ĐÃ CÓ HIKARI ACADEMY
                    </h2>
                    <span class="sectiontitle-design">
                        <span class="icons">
                        </span>
                    </span>
                    <p class="text-white-50">
                        Cung cấp cho bạn một hệ thống kiến thức tiếng Nhật đầy đủ, phong phú và toàn diện nhất
                    </p>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="widgets-cards mb-5">
                                    <div class="d-flex">
                                        <div class="widgets-cards-icons">
                                            <div class="wrp counter-icon1 mr-5">
                                                <i class="fe fe-wifi">
                                                </i>
                                            </div>
                                        </div>
                                        <div class="widgets-cards-data">
                                            <div class="text-wrapper">
                                                <h4>
                                                    <a class="text-white fs-25" href="#">
                                                        Đào tạo khoa học
                                                    </a>
                                                </h4>
                                                <p class="text-white mt-2 mb-0">
                                                    Cùng lộ trình giảng dạy bài bản, chuyên sâu xây dựng bởi đội ngũ giảng viên tiếng Nhật giàu kinh nghiệm
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="widgets-cards mb-5">
                                    <div class="d-flex">
                                        <div class="widgets-cards-icons">
                                            <div class="wrp counter-icon1 mr-5">
                                                <i class="fe fe-wifi-off">
                                                </i>
                                            </div>
                                        </div>
                                        <div class="widgets-cards-data">
                                            <div class="text-wrapper">
                                                <h4>
                                                    <a class="text-white fs-25" href="#">
                                                        Mô phỏng thực tế
                                                    </a>
                                                </h4>
                                                <p class="text-white mt-2 mb-0">
                                                    Thời gian thực làm quen cùng kỳ thi JLPT cùng kho tàng đề thi đa dạng - đầy đủ nhất Việt Nam
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="widgets-cards">
                                    <div class="d-flex">
                                        <div class="widgets-cards-icons">
                                            <div class="wrp counter-icon1 mr-5">
                                                <i class="fe fe-book-open">
                                                </i>
                                            </div>
                                        </div>
                                        <div class="widgets-cards-data">
                                            <div class="text-wrapper">
                                                <h4>
                                                    <a class="text-white fs-25" href="#">
                                                        Cơ hội việc làm
                                                    </a>
                                                </h4>
                                                <p class="text-white mt-2 mb-0">
                                                    Cung cấp cơ hội việc làm và du học tại Nhật Bản với tính năng tìm việc cùng thông tin du học đa dạng.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mrt-sm-2">
                        <div class="clients-img ">
                            <img alt="img" class="bg-white br-3 p-1" src="https://hikariacademy.edu.vn/wp-content/uploads/2019/07/dm-tieng-nhat-cap-toc.png">
                                <img alt="img" class="bg-white br-3 p-1" src="https://hikariacademy.edu.vn/wp-content/uploads/2019/07/dm-07.png">
                                    <img alt="img" class="bg-white br-3 p-1" src="https://hikariacademy.edu.vn/wp-content/uploads/2019/07/dm-04-300x191.png">
                                    </img>
                                </img>
                            </img>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/Section-->
<!--Section-->
<section class="sptb bg-white">
    <div class="container">
        <div class="section-title center-block text-center">
            <h2 style="color: #ca282d;text-transform: uppercase;">
                Đối tác của Hikari Academy
            </h2>
            <span class="sectiontitle-design">
                <span class="icons">
                </span>
            </span>
            {{--
            <p>
                Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua
            </p>
            --}}
        </div>
        <div class="owl-carousel client-carousel" id="small-categories">
            <div class="item">
                <div class="client-img">
                    <img alt="img" src="/public/assets/images/clients/1.jpg">
                    </img>
                </div>
            </div>
            <div class="item">
                <div class="client-img">
                    <img alt="img" src="/public/assets/images/clients/2.jpg">
                    </img>
                </div>
            </div>
            <div class="item">
                <div class="client-img">
                    <img alt="img" src="/public/assets/images/clients/3.jpg">
                    </img>
                </div>
            </div>
            <div class="item">
                <div class="client-img">
                    <img alt="img" src="/public/assets/images/clients/4.jpg">
                    </img>
                </div>
            </div>
            <div class="item">
                <div class="client-img">
                    <img alt="img" src="/public/assets/images/clients/5.jpg">
                    </img>
                </div>
            </div>
            <div class="item">
                <div class="client-img">
                    <img alt="img" src="/public/assets/images/clients/6.jpg">
                    </img>
                </div>
            </div>
            <div class="item">
                <div class="client-img">
                    <img alt="img" src="/public/assets/images/clients/7.jpg">
                    </img>
                </div>
            </div>
            <div class="item">
                <div class="client-img">
                    <img alt="img" src="/public/assets/images/clients/8.jpg">
                    </img>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/Section-->
<!--Section-->
<section class="sptb">
    <div class="container">
        <div class="section-title center-block text-center">
            <h2 style="color: #ca282d;text-transform: uppercase;">
                Các trung tâm chi nhánh của Hikari Academy
            </h2>
            <span class="sectiontitle-design">
                <span class="icons">
                </span>
            </span>
            {{--
            <p>
                Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua
            </p>
            --}}
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xl-6 col-sm-12">
                <div class="row">
                    <div class="col-lg-12 col-xl-12">
                        <div class="item-card overflow-hidden">
                            <div class="item-card-desc">
                                <div class="card overflow-hidden mb-0">
                                    <div class="card-img">
                                        <img alt="img" class="cover-image" src="/public/assets/images/media/locations/7.jpg" style="height: 200px">
                                        </img>
                                    </div>
                                    {{--
                                    <div class="item-tags">
                                        <div class="bg-primary tag-option">
                                            <i class="fa fa fa-heart-o mr-1">
                                            </i>
                                            10209
                                        </div>
                                    </div>
                                    --}}
                                    <div class="item-card-text">
                                        <h4 class="">
                                            Trung tâm Nhật Ngữ - Lê Quang Định
                                        </h4>
                                        <div>
                                            <i class="fa fa-map-marker text-primary mr-1">
                                            </i>
                                            Bình Thạnh: 310 Lê Quang Định, Phường 11, Quận Bình Thạnh, Tp, HCM
                                        </div>
                                        <div>
                                            <i class="fa fa-phone-square text-primary mr-1">
                                            </i>
                                            Tel:  028 3849 7875 | Hotline: 0902 390 885
                                        </div>
                                        <div>
                                            <i class="fa fa-envelope text-primary mr-1">
                                            </i>
                                            Email:
                                            <a href="mailto:tieptan@hikariacademy.edu.vn" style="position:relative;text-align: center;color: #fff;z-index: 2;">
                                                tieptan@hikariacademy.edu.vn
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-xl-6 col-sm-12">
                <div class="row">
                    <div class="col-lg-12 col-xl-12">
                        <div class="item-card overflow-hidden">
                            <div class="item-card-desc">
                                <div class="card overflow-hidden mb-0">
                                    <div class="card-img">
                                        <img alt="img" class="cover-image" src="/public/assets/images/media/locations/7.jpg" style="height: 200px">
                                        </img>
                                    </div>
                                    {{--
                                    <div class="item-tags">
                                        <div class="bg-primary tag-option">
                                            <i class="fa fa fa-heart-o mr-1">
                                            </i>
                                            4009
                                        </div>
                                    </div>
                                    --}}
                                    <div class="item-card-text">
                                        <h4 class="">
                                            Trung tâm Nhật Ngữ - Quận 12
                                        </h4>
                                        <div>
                                            <i class="fa fa-map-marker text-primary mr-1">
                                            </i>
                                            Quận 12: Tòa nhà JVPE, lô 20, Đường số 2, Công viên phần mềm Quang Trung, P.Tân Chánh Hiệp, Quận 12, TP.HCM
                                        </div>
                                        <div>
                                            <i class="fa fa-phone-square text-primary mr-1">
                                            </i>
                                            Tel: 028 3849 7870 – 028 3849 7875
                                        </div>
                                        <div>
                                            <i class="fa fa-envelope text-primary mr-1">
                                            </i>
                                            Email:
                                            <a href="mailto:info@hikariacademy.edu.vn" style="position:relative;text-align: center;color: #fff;z-index: 2;">
                                                info@hikariacademy.edu.vn
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/Section-->
@stop