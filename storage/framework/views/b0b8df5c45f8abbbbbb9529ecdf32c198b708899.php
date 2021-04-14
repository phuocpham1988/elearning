<?php $__env->startSection('content'); ?>
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
            
        </div>
        <div class="panel panel-primary">
            <div class="owl-carousel owl-carousel-icons6" id="myCarousel1">
                <?php if(count($series_el) > 0): ?>
                    <?php $__currentLoopData = $series_el; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="item">
                    <div class="card overflow-hidden">
                        <?php if( (int)$r->selloff > (int)$r->cost ): ?>
                            <div class="ribbon ribbon-top-left text-danger">
                                <span class="bg-danger">
                                    Khuyến mại
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if( (int)$r->cost  == 0): ?>
                            <div class="ribbon ribbon-top-left text-success">
                                <span class="bg-success">
                                    Miễn phí
                                </span>
                            </div>
                        <?php endif; ?>
                        <div class="item-card7-img">
                            <div class="item-card7-imgs">
                                <?php if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6)): ?>
                                    <?php if($r->total_items == 1): ?>
                                        <a href="<?php echo e(PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents); ?>">
                                    </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(PREFIX.'learning-management/lesson/combo/'.$r->slug); ?>">
                                        </a>
                                    <?php endif; ?>
                                <?php elseif($r->try_lmscontents > 0): ?>
                                    <?php if($r->total_items == 1): ?>
                                        <a href="<?php echo e(PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents); ?>">
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(PREFIX.'learning-management/lesson/combo/'.$r->slug); ?>">
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                        <a href="/payments/lms/<?php echo e($r->slug); ?>"></a>
                                <?php endif; ?>
                                <img alt="img" class="cover-image" src="<?php echo e('/public/uploads/lms/combo/'.$r->image); ?>"/>
                            </div>
                            <div class="item-card7-overlaytext">
                                <a class="text-white" href="#">
                                    #<?php echo e($r->code); ?>

                                </a>
                                <h4 class="mb-0 font-weight-semibold fs-16">
                                    <?php if((int)$r->cost > 0 ): ?>
                                        <?php echo e(number_format($r->cost, 0, 0, '.')); ?>đ
                                        <?php if( (int)$r->selloff > (int)$r->cost ): ?>
                                            <del class="h4 text-muted ml-2 fs-12">
                                                <?php echo e(number_format($r->selloff, 0, 0, '.')); ?>đ
                                            </del>
                                        <?php endif; ?>
                                    <?php else: ?> 
                                        Miễn phí
                                    <?php endif; ?>    
                                </h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="item-card7-desc">
                                <div class="item-card7-text" style="max-height: 7vh;height: 5vh;">
                                    <?php if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6)): ?>
                                                    <?php if($r->total_items == 1): ?>
                                    <a class="text-dark" href="<?php echo e(PREFIX.'learning-management/lesson/show/'.$r->slug_lmscontents); ?>">
                                        <?php else: ?>
                                        <a class="text-dark" href="<?php echo e(PREFIX.'learning-management/lesson/combo/'.$r->slug); ?>">
                                            <?php endif; ?>
                                                <?php elseif($r->try_lmscontents > 0): ?>
                                                <?php if($r->total_items == 1): ?>
                                            <a class="text-dark" href="<?php echo e(PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents); ?>">
                                                <?php else: ?>
                                                <a class="text-dark" href="<?php echo e(PREFIX.'learning-management/lesson/combo/'.$r->slug); ?>">
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <a class="text-dark" href="/payments/lms/<?php echo e($r->slug); ?>">
                                                        <?php endif; ?>
                                                        <h3 class="font-weight-semibold">
                                                            <?php echo e($r->title); ?>

                                                        </h3>
                                                    </a>
                                                </a>
                                            </a>
                                        </a>
                                    </a>
                                </div>
                                <?php echo limit_words($r->short_description,26); ?>

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
                                    <?php echo e($time_options[$r->time]); ?>

                                </span>
                            </a>
                            <a class="mr-4 float-right">
                                <span class="font-weight-bold">
                                    Bài học:
                                </span>
                                <span class="font-weight-bold text-muted text-danger">
                                    <?php echo e($r->lmscontents); ?>

                                </span>
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6)): ?>
                                            <?php if($r->total_items == 1): ?>
                            <a class="btn btn-primary btn-block" href="<?php echo e(PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents); ?>">
                                Học ngay
                            </a>
                            <?php else: ?>
                            <a class="btn btn-primary btn-block" href="<?php echo e(PREFIX.'learning-management/lesson/combo/'.$r->slug); ?>">
                                Mua ngay
                            </a>
                            <?php endif; ?>
                                        <?php elseif($r->try_lmscontents > 0): ?>
                                            <?php if($r->total_items == 1): ?>
                            <a class="btn btn-primary btn-block" href="<?php echo e(PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents); ?>">
                                Học thử
                            </a>
                            <?php else: ?>
                            <a class="btn btn-primary btn-block" href="<?php echo e(PREFIX.'learning-management/lesson/combo/'.$r->slug); ?>">
                                Mua ngay
                            </a>
                            <?php endif; ?>
                                        <?php else: ?>
                            <a class="btn btn-primary btn-block" href="/payments/lms/<?php echo e($r->slug); ?>">
                                Mua ngay
                            </a>
                            <?php endif; ?>
                                        
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                <h3 class="text-primary">
                    Chưa có khóa học
                </h3>
                <?php endif; ?>
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
            
        </div>
        <div class="panel panel-primary">
            <div class="owl-carousel owl-carousel-icons6" id="myCarousel1">
                <?php if(count($series) > 0): ?>
                    <?php $__currentLoopData = $series; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="item">
                    <div class="card overflow-hidden">
                        <?php if( (int)$r->selloff > (int)$r->cost ): ?>
                            <div class="ribbon ribbon-top-left text-danger">
                                <span class="bg-danger">
                                    Khuyến mại
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if( (int)$r->cost  == 0): ?>
                            <div class="ribbon ribbon-top-left text-success">
                                <span class="bg-success">
                                    Miễn phí
                                </span>
                            </div>
                        <?php endif; ?>
                        <div class="item-card7-img">
                            <div class="item-card7-imgs">
                                <?php if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6)): ?>
                                              <?php if($r->total_items == 1): ?>
                                <a href="<?php echo e(PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents); ?>">
                                </a>
                                <?php else: ?>
                                <a href="<?php echo e(PREFIX.'learning-management/lesson/combo/'.$r->slug); ?>">
                                </a>
                                <?php endif; ?>
                                          <?php elseif($r->try_lmscontents > 0): ?>
                                              <?php if($r->total_items == 1): ?>
                                <a href="<?php echo e(PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents); ?>">
                                </a>
                                <?php else: ?>
                                <a href="<?php echo e(PREFIX.'learning-management/lesson/combo/'.$r->slug); ?>">
                                </a>
                                <?php endif; ?>
                                          <?php else: ?>
                                <a href="/payments/lms/<?php echo e($r->slug); ?>">
                                </a>
                                <?php endif; ?>
                                <img alt="img" class="cover-image" src="<?php echo e('/public/uploads/lms/combo/'.$r->image); ?>">
                                </img>
                            </div>
                            <div class="item-card7-overlaytext">
                                <a class="text-white" href="#">
                                    #<?php echo e($r->code); ?>

                                </a>
                                <h4 class="mb-0 font-weight-semibold fs-16">
                                    <?php if((int)$r->cost > 0 ): ?>
                                        <?php echo e(number_format($r->cost, 0, 0, '.')); ?>đ
                                        <?php if( (int)$r->selloff > (int)$r->cost ): ?>
                                            <del class="h4 text-muted ml-2 fs-12">
                                                <?php echo e(number_format($r->selloff, 0, 0, '.')); ?>đ
                                            </del>
                                        <?php endif; ?>
                                    <?php else: ?> 
                                        Miễn phí
                                    <?php endif; ?>    
                                </h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="item-card7-desc">
                                <div class="item-card7-text" style="max-height: 7vh;height: 5vh;">
                                    <?php if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6)): ?>
                                                  <?php if($r->total_items == 1): ?>
                                    <a class="text-dark" href="<?php echo e(PREFIX.'learning-management/lesson/show/'.$r->slug_lmscontents); ?>">
                                        <?php else: ?>
                                        <a class="text-dark" href="<?php echo e(PREFIX.'learning-management/lesson/combo/'.$r->slug); ?>">
                                            <?php endif; ?>
                                                                  <?php elseif($r->try_lmscontents > 0): ?>
                                                                      <?php if($r->total_items == 1): ?>
                                            <a class="text-dark" href="<?php echo e(PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents); ?>">
                                                <?php else: ?>
                                                <a class="text-dark" href="<?php echo e(PREFIX.'learning-management/lesson/combo/'.$r->slug); ?>">
                                                    <?php endif; ?>
                                                                                      <?php else: ?>
                                                    <a class="text-dark" href="/payments/lms/<?php echo e($r->slug); ?>">
                                                        <?php endif; ?>
                                                        <h3 class="font-weight-semibold">
                                                            <?php echo e($r->title); ?>

                                                        </h3>
                                                    </a>
                                                </a>
                                            </a>
                                        </a>
                                    </a>
                                </div>
                                <?php echo limit_words($r->short_description,26); ?>

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
                                    <?php echo e($time_options[$r->time]); ?>

                                </span>
                            </a>
                            <a class="mr-4 float-right">
                                <span class="font-weight-bold">
                                    Bài học:
                                </span>
                                <span class="font-weight-bold text-muted text-danger">
                                    <?php echo e($r->lmscontents); ?>

                                </span>
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if((isset($r->payment) && $r->payment > 0) || (Auth::check() && Auth::user()->role_id == 6)): ?>
                                          <?php if($r->total_items == 1): ?>
                            <a class="btn btn-primary btn-block" href="<?php echo e(PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents); ?>">
                                Học ngay
                            </a>
                            <?php else: ?>
                            <a class="btn btn-primary btn-block" href="<?php echo e(PREFIX.'learning-management/lesson/combo/'.$r->slug); ?>">
                                Mua ngay
                            </a>
                            <?php endif; ?>
                                      <?php elseif($r->try_lmscontents > 0): ?>
                                          <?php if($r->total_items == 1): ?>
                            <a class="btn btn-primary btn-block" href="<?php echo e(PREFIX.'learning-management/lesson/show/'.$r->slug.'/'.$r->slug_lmscontents); ?>">
                                Học thử
                            </a>
                            <?php else: ?>
                            <a class="btn btn-primary btn-block" href="<?php echo e(PREFIX.'learning-management/lesson/combo/'.$r->slug); ?>">
                                Mua ngay
                            </a>
                            <?php endif; ?>
                                      <?php else: ?>
                            <a class="btn btn-primary btn-block" href="/payments/lms/<?php echo e($r->slug); ?>">
                                Mua ngay
                            </a>
                            <?php endif; ?>
                                      
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                <h3 class="text-primary">
                    Chưa có khóa luyện thi
                </h3>
                <?php endif; ?>
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
<?php $__env->stopSection(); ?>
<style type="text/css">
    .owl-carousel .owl-nav.disabled, .owl-carousel .owl-dots.disabled {
        display: block !important;
    }
</style>
<?php echo $__env->make('layouts.sitelayout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>