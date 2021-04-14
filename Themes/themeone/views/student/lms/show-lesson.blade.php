<?php //dump($hi_koi); ?>
@extends('layouts.sitelayout')
@section('header_scripts')
<link href="{{themes('css/file/application.css')}}" rel="stylesheet">
    <style>
        .vjs-current-timel {
            display: block!important;
        }
        .vjs-duration {
            display: block!important;
        }

         .video-js{

             height: 410px!important;
             width: 100%!important;

         }
        @media (max-width: 768px){
            .video-js{

                height: 193px!important;
               /* width: 100%!important;*/
            }
        }

        .none-after{
            content: none !important;
        }

        #accordian h3 a.none-after:after{
            content: none;
        }
        #accordian .active> h3 a.none-after:after{
            content: none;
        }
        #accordian h3 a {
            padding: 0 10px;
            font-size: 15px;
            line-height: 34px;
            display: block;
            color: #232731;
            text-decoration: none;
            position: relative;
            text-align: start;
        }

        #accordian h3:hover {
            text-shadow: 0 0 1px rgba(255, 255, 255, 0.7);
        }

        i {
            margin-right: 10px;
        }
        #accordian ul{
            padding-left: 5px;
        }
        #accordian li {
            list-style-type: none;
        }

        #accordian ul ul li a,
        #accordian h4 {
            color: #232731;
            text-decoration: none;
            font-size: 13px;
            line-height: 27px;
            display: block;
            padding: 0 15px;
            transition: all 0.15s;
            position: relative;
            font-weight: 500;
            text-align: start;
        }

        #accordian ul ul li a:hover {
            background: #185384;
            border-left: 3px solid #f48133;
            color: #fafaf4!important;
        }

        #accordian ul ul {
            display: none;
        }

        #accordian li.active>ul {
            display: block;
        }

        #accordian ul ul ul {
            margin-left: 15px;
            border-left: 1px dotted rgba(0, 0, 0, 0.5);
        }

        #accordian ul li ul {
            margin-left: 15px;
            border-left: 1px dotted rgba(0, 0, 0, 0.5);
        }
        #accordian a:not(:only-child):after {
            content: "\f104";
            font-family: fontawesome;
            position: absolute;
            right: 15px;
            top: 0;
            font-size: 14px;
            /*color: #232731;*/
        }

        #accordian .active>a:not(:only-child):after {
            content: "\f107";
        }

        #accordian h3 a:after {
            content: "\f104";
            font-family: fontawesome;
            position: absolute;
            right: 15px;
            font-size: 14px;
        }
        #accordian .active> h3 a:after {
            content: "\f107";
        }
        #accordian ul li a i {
            color: #2196f3;
        }
    </style>
    @stop
@section('content')
    <!--Breadcrumb-->
    <div class="bg-white border-bottom">
        <div class="container">
            <div class="page-header">
                <h3 class=" text-primary ">
                    {{ $current_series->title }}
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/home">
                            Trang chủ
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{PREFIX.'lms/exam-categories/list'}}">
                            Khóa học
                        </a>
                    </li>
                    {{--
                    <li class="breadcrumb-item">
                        <a href="{{PREFIX.'learning-management/lesson/combo/'.$hi_combo->slug}}">
                            {{$hi_combo->title}}
                        </a>
                    </li>
                    --}}
                    <li aria-current="page" class="breadcrumb-item active">
                        {{ $current_series->title }}
                    </li>
                </ol>
            </div>
        </div>
    </div>
    <!--/Breadcrumb-->
    <!--/Section-->
    <section class="sptb">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 col-lg-8 col-md-12">
                    <!--Coursed Description-->
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="item-det mb-4">
                                <a class="text-dark text-primary" href="#">
                                    <h3 class="font-weight-semibold">
                                        {{$current_lesson}}
                                    </h3>
                                </a>
                            </div>
                            <div class="product-slider" id="edm_player_zone">
                                <div class="row lecture-player-main no-margin ">
                                    <video class="video-js vjs-theme-fantasy" id="video-js">
                                        <source label="1080" selected="true" src="{{$current_video}}" type="application/x-mpegURL">
                                        </source>
                                        <source label="720" src="{{$current_video}}" type="application/x-mpegURL">
                                        </source>
                                        <source label="480" src="{{$current_video}}" type="application/x-mpegURL">
                                        </source>
                                        <source label="240" src="{{$current_video}}" type="application/x-mpegURL">
                                        </source>
                                        <p class="vjs-no-js">
                                            To view this video please enable JavaScript, and consider upgrading to a
                                            web browser that
                                            <a href="https://videojs.com/html5-video-support/" target="_blank">
                                                supports HTML5 video
                                            </a>
                                        </p>
                                    </video>
                                </div>
                            </div>
                        </div>
                    </div>
                   {{-- <div class="card">
                        <div class="card-body p-6">

                        </div>
                    </div>--}}
                    <div class="card panel panel-primary">
                        <div class="tab-menu-heading">
                            <div class="tabs-menu ">
                                <!-- Tabs -->
                                <ul class="nav panel-tabs">
                                    <li class="">
                                        <a class="active font-weight-bold  fs-18" data-toggle="tab" href="#tab1">
                                            Mô tả khóa học
                                        </a>
                                    </li>
                                    @if(Auth::check() && ((isset($checkpay->payment) && $checkpay->payment > 0) ||Auth::user()->role_id == 6 ))
                                        <li>
                                            <a class="font-weight-bold  fs-18" data-toggle="tab" href="#tab2">
                                                Đặt câu hỏi
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body tabs-menu-body">
                            <div class="tab-content">
                                <div class="tab-pane active " id="tab1">
                                    <p>
                                        {!!  change_furigana_title($hi_koi->description) !!}
                                    </p>
                                </div>
                                <div class="tab-pane " id="tab2">
                                    @if(Auth::check() && ((isset($checkpay->payment) && $checkpay->payment > 0) ||Auth::user()->role_id == 6 ))


                                        <div class="">
                                            <div class="card-header">
                                                <h3 class="card-title">Câu hỏi của bạn</h3>
                                            </div>

                                            <div class="card-body p-0" id="comment_boby">
                                                @if(count($comment) > 0)
                                                    @foreach($comment as $r)

                                                        <?php

                                                        $name = $r->user_name;
                                                        if ($r ->admin_id !== null ){
                                                            $name = DB::table('users')
                                                                ->select('name')
                                                                ->where('id' ,$r ->admin_id)
                                                                ->first()->name;
                                                        }
                                                        ?>
                                                        <div class="media mt-0 p-5 border-top">
                                                            <div class="media-body">
                                                                <h4 class="mt-0 mb-1 font-weight-bold">
                                                                    {{$name}}
                                                                    <span class="fs-14 ml-0" data-toggle="tooltip" data-placement="top" title="" data-original-title="verified"><i class="fa fa-check-circle-o text-success"></i></span>
                                                                </h4>
                                                                <small class="text-muted">
                                                                    <i class="fa fa-calendar"></i>
                                                                    {{date_format(date_create($r->created_at),"d-m-Y H:m:i")}}
                                                                </small>
                                                                <p class="font-13  mb-2 mt-2">
                                                                    {{$r->body}}
                                                                </p>

                                                                <a href="javascript:void(0)" class="mr-2" onclick="myModal({{$r->id}})"><span class="badge badge-default">Comment</span></a>
                                                                @if(count($comment_child) > 0)
                                                                    @foreach($comment_child as $cr)
                                                                        @if($cr->parent_id == $r->id)
                                                                            <?php
                                                                            $cname = $cr->user_name;
                                                                            if ($cr ->admin_id !== null ){
                                                                                $cname = DB::table('users')
                                                                                    ->select('name')
                                                                                    ->where('id' ,$cr ->admin_id)
                                                                                    ->first()->name;
                                                                            }
                                                                            ?>
                                                                            <div class="media mt-5">
                                                                                <div class="d-flex mr-5">
                                                                                </div>
                                                                                <div class="media-body">
                                                                                    <h4 class="mt-0 mb-1 font-weight-bold">{{$cname}} <span class="fs-14 ml-0" data-toggle="tooltip" data-placement="top" title="" data-original-title="verified"><i class="fa fa-check-circle-o text-success"></i></span></h4>
                                                                                    <small class="text-muted">
                                                                                        <i class="fa fa-calendar"></i> {{date_format(date_create($r->created_at),"d-m-Y H:m:i")}}
                                                                                    </small>
                                                                                    <p class="font-13  mb-2 mt-2">
                                                                                        {{$cr->body}}
                                                                                    </p>
                                                                                    <a href="javascript:void(0)" onclick="myModal({{$r->id}})"><span class="badge badge-default">Comment</span></a>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    @endforeach
                                                                @endif

                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="media mt-0 p-5 border-top">
                                                        <div class="media-body">
                                                            <p class="mt-0 ml-5 mb-1 ">
                                                                Bạn chưa có câu hỏi
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <p></p>
                                                @endif
                                            </div>
                                        </div>


                                        <div class=" mb-lg-0">
                                            <div class="card-header">
                                                <h3 class="card-title">Đặt câu hỏi</h3>
                                            </div>
                                            <div class="card-body">
                                                {{ Form::model(null,array('url' => url('comments/add'),'method'=>'post', 'files' => false, 'name'=>'formLms', 'novalidate'=>'')) }}
                                                <input hidden name="user_id" value="{{Auth::id()}}">
                                                <input hidden name="lmsseries_slug" value="{{$series}}">
                                                <input hidden name="lmscombo_slug" value="{{$combo_slug}}">
                                                <input hidden name="lmscontent_id" value="{{$slug}}">
                                                <input hidden name="parent_id" value="0">
                                                <div class="form-group">

                                                    {{ Form::textarea('body', $value = null , $attributes = array('class'=>'form-control','required'=> 'true', 'rows'=>'5')) }}
                                                </div>

                                                <div class="text-right">
                                                    <button onclick="onComment(event)" class="btn btn-primary ">Gửi</button>
                                                </div>

                                                {!! Form::close() !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!--Right Side Content-->
                <div class="col-xl-4 col-lg-4 col-md-12">
                    @if((isset($checkpay->payment) && $checkpay->payment > 0) || (Auth::check() && Auth::user()->role_id == 6))
                    <div class="card overflow-hidden">
                        <div class="ribbon ribbon-top-right text-danger">
                            <span class="bg-danger">
                                Đã mua
                            </span>
                        </div>
                        <div class="card-header">
                            <h3 class="card-title">
                                Bài học
                            </h3>
                        </div>
                        <div class="card-body item-user" style="padding: 1.5rem 0.75rem">
                            <div class="profile-pic mb-0">
                                <div class="container-fluid" id="wrapper_new_lecture">
                                    <div class="content_lecture">
                                        <div class="show_lecture_course ">
                                            <div aria-expanded="true" class="content_show_lecture" style="display: block;">
                                                <div id="accordian">
                                                    <ul>
                                                        {!!  $lesson_menu !!}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                Tiến độ khóa học
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <p class="mb-2">
                                            <span class="fs-14 ml-2">
                                                <i class="fa fa-star text-yellow mr-2">
                                                </i>
                                                Hoàn thành: {{$current_course}}/{{$total_course}} bài học
                                            </span>
                                        </p>
                                        <div class="progress position-relative">
                                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?php echo (int)ceil((($current_course/$total_course)*100))?>" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo ceil((($current_course/$total_course)*100))?>%">
                                            </div>
                                            <small class="justify-content-center d-flex position-absolute w-100">
                                                <?php echo (int)ceil((($current_course/$total_course)*100))?>
                                                %
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="card overflow-hidden">
                        <div class="ribbon ribbon-top-left text-danger">
                            <span class="bg-danger">
                                Khuyến mại
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="mb-5">
                                <div class="text-dark mb-2">
                                    <img alt="{{$hi_combo->title}}" src="{{ '/public/uploads/lms/combo/'.$hi_combo->image}}">
                                    </img>
                                </div>
                                <div class="text-dark mb-2 text-right">
                                    <span class="text-primary font-weight-semibold h3">
                                        {{ number_format($hi_combo->cost, 0, 0, '.')}}đ
                                    </span>
                                    <span class="text-muted h4 font-weight-normal ml-1">
                                        <span class="strike-text">
                                            {{ number_format($hi_combo->selloff, 0, 0, '.')}}đ
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="">
                                @if($hi_combo->cost > 0)
                                        @if(Auth::check())
                                <a class="btn btn-primary btn-lg btn-block" href="/payments/lms/{{$hi_combo->slug}}">
                                    Mua ngay
                                </a>
                                @else
                                <a class="btn btn-primary btn-lg btn-block" href="/login">
                                    Mua ngay
                                </a>
                                @endif
                                    @else
                                <a class="btn btn-primary btn-lg btn-block" href="javascript:void(0)">
                                    Miễn phí
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card overflow-hidden">
                        <div class="card-header">
                            <h3 class="card-title">
                                Bài học
                            </h3>
                        </div>
                        <div class="card-body item-user" style="padding: 1.5rem 0.75rem">
                            <div class="profile-pic mb-0">
                                <div class="container-fluid" id="wrapper_new_lecture">
                                    <div class="content_lecture">
                                        <div class="show_lecture_course ">
                                            <div aria-expanded="true" class="content_show_lecture" style="display: block;">
                                                <div id="accordian">
                                                    <ul>
                                                        {!!  $lesson_menu !!}
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!--Right Side Content-->
                </div>
            </div>
        </div>
    </section>
    <!--/Section-->
    <!--Comment Modal -->
    @if(Auth::check() && ((isset($checkpay->payment) && $checkpay->payment > 0) ||Auth::user()->role_id == 6 ))
    <div aria-hidden="true" class="modal fade" id="Comment" role="dialog" style="display: none;" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleCommentLongTitle">
                        Đặt câu hỏi
                    </h5>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ Form::model(null,array('url' => url('comments/add'),'method'=>'post', 'files' => false, 'name'=>'formComments', 'novalidate'=>'')) }}
                         <input hidden="" name="user_id" value="{{Auth::id()}}">
                        <input hidden="" name="parent_id" value="">
                            <input hidden="" name="lmsseries_slug" value="{{$series}}">
                                <input hidden="" name="lmscombo_slug" value="{{$combo_slug}}">
                                    <input hidden="" name="lmscontent_id" value="{{$slug}}">
                                        <div class="form-group">
                                            {{ Form::textarea('body', $value = null , $attributes = array('class'=>'form-control','required'=> 'true', 'rows'=>'5', 'placeholder' => getPhrase('Comment'))) }}
                                        </div>
                                        {!! Form::close() !!}
                                    </input>
                                </input>
                            </input>
                        </input>
                    </input>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-dismiss="modal" type="button">
                        Hủy
                    </button>
                    <button class="btn btn-success" onclick="upComment(event)" type="button">
                        Gửi
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- /Comment Modal -->
    @stop

@section('footer_scripts')
    <script src="{{themes('js/videojs/video.js')}}">
    </script>
    <link href="{{themes('css/videojs/video-js.css')}}" rel="stylesheet">
        <link href="{{themes('css/videojs/quality-selector.css')}}" rel="stylesheet">
            <link href="{{themes('css/videojs/index.css')}}" rel="stylesheet">
                <script src="{{themes('js/videojs/silvermine-videojs-quality-selector.min.js')}}">
                </script>
                <script>
                    $(document).ready(function (){
            var options, myPlayer;

            options = {
                // poster: "{{$current_poster}}",
                controls: true,
                preload: 'auto',
                autoplay: true,
                controlBar: {
                    children: [
                        'playToggle',
                        'currentTimeDisplay',
                        'progressControl',
                        'volumePanel',
                        'durationDisplay',
                        'fullscreenToggle',
                    ],
                },
            };

            myPlayer = videojs('video-js', options);
            $('.vjs-current-time').css('display','block');
            var previousTime = 0;
            @if(isset($current_time))
            //myPlayer.currentTime({{(int)$current_time}})
            myPlayer.currentTime(0)
            previousTime = {{(int)$current_time}};
            @endif

            var time = 0;
            myPlayer.on('timeupdate',  function() {
                if (!this.seeking()){
                    previousTime =  Math.max(previousTime, myPlayer.currentTime());
                    let temp = this.currentTime();
                    let temp2 = time+10;
                    if(temp > temp2){
                        time = temp;
                        @if(Auth::check() && (isset($checkpay->payment) && $checkpay->payment > 0))
                        callAjax("{{$contentslug}}",time);
                        @endif
                    }
                }
            });

// $('#exercise').append("<a target='_blank' href='{{$url_excer}}' class='btn-rounded btn btn-info button'>Bài tập</a>");

            @if(Auth::check())
                 @if((isset($checkpay->payment) && $checkpay->payment > 0) || Auth::user()->role_id == 6)
                    @if(!isset($viewed_video))
                    myPlayer.on('seeking', function() {
                        if (this.currentTime() > previousTime) {
                            this.currentTime(previousTime);
                        }
                    });
                    @endif
                @endif
            @endif

            myPlayer.on('ended', async function() {
                myPlayer.pause();
                await swal({
                    title: "",
                    text: "Hoàn thành bài học",
                    type: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
                @if(Auth::check() && (isset($checkpay->payment) && $checkpay->payment > 0))
                    await finishTimeVideo('{{$contentslug}}');
                @endif

                    await netxUrl('{{$slug}}','{{$series}}','{{$combo_slug}}');
                    //await location.reload();
                // $('#exercise').empty();
                // $('#exercise').append("<a target='_blank' href='{{$url_excer}}' class='btn-rounded btn btn-info button'>Bài tập</a>");
            })
        })


        @if(Auth::check() && (isset($checkpay->payment) && $checkpay->payment > 0))

        function callAjax(slug,currentTime){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN':'{{csrf_token()}}'
                },
                url : '{{route('updateTimeVideo')}}',
                type : "post",
                data: {slug : slug, currentTime : currentTime},
            });
        }

        function finishTimeVideo(slug){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN':'{{csrf_token()}}'
                },
                url : '{{route('finishTimeVideo')}}',
                type : "post",
                data: {slug : slug},
            });
        }
        @endif


        function netxUrl(slug,series,combo){

            $.ajax({

                headers: {

                    'X-CSRF-TOKEN':'{{csrf_token()}}'
                },

                url : '{{route('nextUrl')}}',

                type : "post",

                data: {slug : slug,series: series,combo: combo},


            }).done(function(data) {

                if (data.status === 1){

                    //$('.btn-end-exercise').attr("href", data.url);

                    console.log(data.url)
                    window.location.href = data.url;

                }

            });

        }

        $(document).ready(function() {
            $("#accordian a").click(function() {
                let link = $(this);
                let closest_ul = link.closest("ul");
                let parallel_active_links = closest_ul.find(".active")
                let closest_li = link.closest("li");
                let link_status = closest_li.hasClass("active");
                let count = 0;

                closest_ul.find("ul").slideUp(function() {
                    if (++count === closest_ul.find("ul").length)
                        parallel_active_links.removeClass("active");
                });

                if (!link_status) {
                    closest_li.children("ul").slideDown();
                    closest_li.addClass("active");
                }
            })


            let _elmentActive = $('li.lesson_active');


            if (_elmentActive.find('ul').length === 0){
                _elmentActive.children().css('color','#e62020');
                _elmentActive.children().children().css('color','#e62020');
                _elmentActive.parent().parent().children().css('color','#e62020');
                _elmentActive.parent().parent().children().children().css('color','#e62020');
                _elmentActive.parent().parent().parent().parent().children().children().css('color','#e62020');
                _elmentActive.parent().parent().parent().parent().children().children().children(":first").css('color','#e62020');
                _elmentActive.parent().parent().addClass("active");
                _elmentActive.parent().parent().parent().parent().addClass("active");
                _elmentActive.parent().slideDown();
            }


            let _elmentActivea = $('a.lesson_active');
            if (_elmentActivea.length > 0){
                _elmentActivea.css('color','#e62020');
                _elmentActivea.children().css('color','#e62020');
            }
        })

        function showpayment(){
            swal({

                title: "Yêu cầu sở hữu khóa học",

                text: "Vui lòng sở hữu khóa học để xem nội này",

                type: "warning",

                showCancelButton: false,
                confirmButtonColor: '#8CD4F5',
                /* confirmButtonClass: "btn-danger",*/

                confirmButtonText: "Đồng ý",

                /*  cancelButtonText: "Hủy bỏ",*/

                closeOnConfirm: false,

                closeOnCancel: true

            });
        }

        @if(Auth::check() && ((isset($checkpay->payment) && $checkpay->payment > 0) || Auth::user()->role_id == 6))
        function onComment(e){
            e.preventDefault();
            let form = $('form[name="formLms"]');

            if (form.find('textarea').val().length == 0){
                swal({
                    title: "Thông báo",
                    text: "Vui lòng nhập thông tin phản hồi",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: '#8CD4F5',
                    confirmButtonText: "Đồng ý",
                    closeOnConfirm: false,
                    closeOnCancel: true

                });
                return;
            }


            let route = form.attr('action');
            let data = form.serialize();


            $.ajax({
                headers: {

                    'X-CSRF-TOKEN':'{{csrf_token()}}'
                },
                url:route,
                type: 'post',
                dataType: "json",
                data: data,
                beforeSend: function() {
                    // setting a timeout
                    swal({
                        html:true,
                        title: 'Đang xử lý vui lòng chờ',
                        text: '<img style="position: relative;" src="/public/assets/images/loader.svg" class="loader-img" alt="img">',
                        type: '',
                        showConfirmButton: false,
                        showCancelButton: false,

                    });
                },
                success:function(data){

                    //console.log(data)
                    if(data.error === 1){
                        $('textarea[name="body"]').val('');

                        $.ajax({
                            headers: {

                                'X-CSRF-TOKEN':'{{csrf_token()}}'
                            },
                            url: '{{url('comments/index')}}',
                            type: 'post',
                            dataType: "json",
                            data: {
                                slug : '{{$series}}',
                                combo_slug : '{{$combo_slug}}',
                                id : '{{$slug}}',
                            },
                            success:function(data){
                                console.log(data)
                                if(data.error === 1) {
                                    $('#comment_boby').empty();
                                    $('#comment_boby').html(data.message)
                                }
                            }
                        })
                        swal({
                            title: 'Thông báo',
                            text: data.message,
                            type: 'success',
                            showConfirmButton: false,
                            showCancelButton: false,
                            timer: 3000,
                        });
                    }else {
                        swal({
                            title: 'Thông báo',
                            text: data.message,
                            type: 'warning',
                            showConfirmButton: false,
                            showCancelButton: false,
                            timer: 3000,
                        });
                    }

                }
            })

        }


        function myModal(id){

            let form = $('form[name="formComments"]');
           /* $('input[name="parent_id"]').val(id);*/

            form.find('input[name="parent_id"]').val(id)
            $('#Comment').modal('show')
        }

        function upComment(e){
            e.preventDefault();




            let form = $('form[name="formComments"]');

            if (form.find('textarea').val().length == 0){
                swal({
                    title: "Thông báo",
                    text: "Vui lòng nhập thông tin phản hồi",
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: '#8CD4F5',
                    confirmButtonText: "Đồng ý",
                    closeOnConfirm: false,
                    closeOnCancel: true

                });
                return;
            }


            let route = form.attr('action');
            let data = form.serialize();


            $.ajax({
                headers: {

                    'X-CSRF-TOKEN':'{{csrf_token()}}'
                },
                url:route,
                type: 'post',
                dataType: "json",
                data: data,
                beforeSend: function() {
                    // setting a timeout
                    swal({
                        html:true,
                        title: 'Đang xử lý vui lòng chờ',
                        text: '<img style="position: relative;" src="/public/assets/images/loader.svg" class="loader-img" alt="img">',
                        type: '',
                        showConfirmButton: false,
                        showCancelButton: false,

                    });
                },
                success: function(data){

                    //console.log(data)
                    if(data.error === 1){
                        $('textarea[name="body"]').val('');

                        $.ajax({
                            headers: {

                                'X-CSRF-TOKEN':'{{csrf_token()}}'
                            },
                            url: '{{url('comments/index')}}',
                            type: 'post',
                            dataType: "json",
                            data: {
                                slug : '{{$series}}',
                                combo_slug : '{{$combo_slug}}',
                                id : '{{$slug}}',
                            },
                            success:function(data){
                                //console.log(data)
                                if(data.error === 1) {
                                    $('#comment_boby').empty();
                                    $('#comment_boby').html(data.message)
                                }
                            }
                        })


                        swal({
                            title: 'Thông báo',
                            text: data.message,
                            type: 'success',
                            showConfirmButton: false,
                            showCancelButton: false,
                            timer: 3000,
                        });
                    }else {
                        swal({
                            title: 'Thông báo',
                            text: data.message,
                            type: 'warning',
                            showConfirmButton: false,
                            showCancelButton: false,
                            timer: 3000,
                        });
                    }
                    $('#Comment').modal('hide');
                }


            })

        }
        @endif
                </script>
                @stop
            </link>
        </link>
    </link>
</link>