<?php

$dr_trinhdo = array(

		1 =>
'N1',

		2=> 'N2',

		3=> 'N3',

		4=> 'N4',

		5=> 'N5',

		6=> 'Chữ cái',



)

?>

@extends($layout)

@section('content')
<div class="card mb-0">
    <div class="card-header">
        <h3 class="card-title">
            {{$title}}
        </h3>
    </div>
    <div class="card-body">
        <div class="manged-ad table-responsive border-top userprof-tab">
            <table class="table table-bordered table-hover mb-0 text-nowrap">
                <thead>
                    <tr>
                        <th class="text-center align-middle" style="width: 5%;">
                            STT
                        </th>
                        <th>
                            {{$title}} của bạn
                        </th>
                        <th class="text-center align-middle">
                            Trình độ
                        </th>
                        <th>
                        </th>
                        {{--
                        <th>
                            Giá
                        </th>
                        <th>
                            Trạng thái
                        </th>
                        --}}
                    </tr>
                </thead>
                <tbody>
                    @if(count($series) > 0)

												@foreach($series as $r)
                    <tr>
                        <td class="text-center align-middle">
                            {{$loop->index+1}}
                        </td>
                        <td>
                            <div class="media mt-0 mb-0">
                                <div class="card-aside-img">
                                    <a href="{{PREFIX.'learning-management/lesson/show/'.$r->combo_slug.'/'.$r->slug}}">
                                    </a>
                                    <img alt="{{$r->title}}" src="{{ IMAGE_PATH_UPLOAD_LMS_SERIES.$r->image}}" style="height: auto;">
                                    </img>
                                </div>
                                <div class="media-body">
                                    <div class="card-item-desc ml-4 p-0 mt-2">
                                        <?php $dr_time  = array(0 =>
                                        '3 tháng' , 1 =>'6 tháng' , 2 => '12 tháng')?>
                                        <a class="text-dark" href="{{PREFIX.'learning-management/lesson/show/'.$r->slug}}">
                                            <h4 class="font-weight-semibold">
                                                {{$r->title}} ({{$dr_time[$r->time]}})
                                            </h4>
                                        </a>
                                        <a href="#">
                                            Ngày mua: {{date_format(date_create($r->created_at),"d-m-Y")}}
                                        </a>
                                        <br>
                                            <?php $dr_time  = array(0 =>
                                            90 , 1 =>180 , 2 => 365)?>
                                            <a href="#">
                                                Ngày hết hạn: {{date_format(date_add(date_create($r->created_at),date_interval_create_from_date_string($dr_time[$r->time]." days")),"d-m-Y")}}
                                            </a>
                                        </br>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center align-middle">
                            <div class="media mt-0 mb-0">
                                {{--
                                <div class="media-body">
                                    {{ $dr_trinhdo[$r->lms_category_id] }}
                                </div>
                                --}}
                                <div class="media-body">
                                    <div class="card-item-desc ">
                                        <p class="mb-2">
                                            <span class="fs-14 ml-2">
                                                <i class="fa fa-star text-yellow mr-2">
                                                </i>
                                                Hoàn thành: {{$r->current_course}}/{{$r->total_course}} bài học
                                            </span>
                                        </p>
                                        <div class="progress position-relative">
										    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo ceil((($r->current_course/$r->total_course)*100))?>%" aria-valuenow="<?php echo (int)ceil((($r->
                                                current_course/$r->total_course)*100))?>" aria-valuemin="0" aria-valuemax="100"></div>
										    <small class="justify-content-center d-flex position-absolute w-100"><?php echo (int)ceil((($r->
                                                current_course/$r->total_course)*100))?>%</small>
										</div>
                                        
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center align-middle" style="width: 10%;">
                            <a class="btn btn-primary mb-3 mb-xl-0" href="{{PREFIX.'learning-management/lesson/show/'.$r->combo_slug.'/'.$r->slug}}">
                                <i class="fa fa-leanpub mr-1">
                                </i>
                                Học ngay
                            </a>
                        </td>
                        {{--
                        <td class="font-weight-semibold fs-16">
                            {{ number_format($r->cost, 0, 0, '.')}}đ
                        </td>
                        <td>
                            <a class="badge badge-warning" href="#">
                                Đã mua
                            </a>
                        </td>
                        --}}
                    </tr>
                    @endforeach

												@else
                    <tr>
                        <td colspan="5">
                            <h5 style="color: #ee2833!important">
                                Bạn chưa có {{$title}}
                            </h5>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
