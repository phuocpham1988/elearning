<?php
    $home_bg = 2;
    $image_bg = '';
    switch ($active_class) {
        case 'home':
            // trang chu
            $home_bg = 2;
            $image_bg = '';
            $class_page = 'cover-image banner1 banner-top';
            break;
        case 'contact':
            $home_bg = 3;
            $image_bg = PREFIX . 'public/assets/images/banners/banner5.jpg';
            $class_page = 'cover-image bg-background3';
            break;
        default:
            $home_bg = 3;
            $image_bg = PREFIX . 'public/assets/images/banners/banner4.jpg';
            $class_page = 'cover-image bg-background3';
            break;
    }
?>
<div class="<?php echo e($class_page); ?>" data-image-src="<?php echo e($image_bg); ?>">
    <!--Topbar-->
    <div class="header-main">
        <div class="top-bar">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 col-lg-8 col-sm-4 col-7">
                        <div class="top-bar-left d-flex">
                            <div class="clearfix">
                                <ul class="socials">
                                    <li>
                                        <a class="social-icon text-primary" href="tel:02838497875">
                                            <i class="fa fa-phone">
                                            </i>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="social-icon text-primary" href="#">
                                            <i class="fa fa-envelope">
                                            </i>
                                        </a>
                                    </li>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-sm-8 col-5">
                        <div class="top-bar-right">
                            <ul class="custom">
                                <?php if(Auth::check()): ?>
                                <li class="dropdown">
                                    <a class="header-icons-link1" href="<?php echo e(PREFIX.'lms/exam-categories/comments'); ?>">
                                        <i class="fa fa-bell">
                                        </i>
                                        <span class="main-badge1 badge badge-danger badge-pill">
                                            0
                                        </span>
                                    </a>
                                </li>
                                <li class="dropdown">
                                    <a class="text-dark" data-toggle="dropdown" href="#">
                                        <i class="fa fa-user mr-1">
                                        </i>
                                        <span>
                                            <?php echo e(ucwords(Auth::user()->name)); ?>

                                            <i class="fa fa-caret-down text-white ml-1">
                                            </i>
                                        </span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                        <a class="dropdown-item" href="/users/profile/<?php echo e(Auth::user()->slug); ?>">
                                            <i class="dropdown-icon icon icon-user">
                                            </i>
                                            Trang cá nhân
                                        </a>
                                        <a class="dropdown-item" href="<?php echo e(PREFIX.'lms/exam-categories/list'); ?>">
                                            <i class="dropdown-icon fa fa-graduation-cap">
                                            </i>
                                            Khóa học của bạn
                                        </a>
                                        <a class="dropdown-item" href="<?php echo e(PREFIX.'lms/exam-categories/study'); ?>">
                                            <i class="dropdown-icon icon icon-diamond">
                                            </i>
                                            Khóa luyện thi của bạn
                                        </a>
                                        <a class="dropdown-item" href="<?php echo e(PREFIX.'exams/student-exam-series/list'); ?>">
                                            <i class="dropdown-icon icon icon-heart">
                                            </i>
                                            Phòng thi của bạn
                                        </a>
                                        <a class="dropdown-item" href="<?php echo e(URL_USERS_CHANGE_PASSWORD.Auth::user()->slug); ?>">
                                            <i class="dropdown-icon icon icon-settings">
                                            </i>
                                            Đổi mật khẩu
                                        </a>
                                        <a class="dropdown-item" href="<?php echo e(URL_USERS_LOGOUT); ?>">
                                            <i class="dropdown-icon icon icon-power">
                                            </i>
                                            Thoát
                                        </a>
                                    </div>
                                </li>
                                <?php else: ?>
                                <li>
                                    <a class="text-dark" href="<?php echo e(URL_USERS_REGISTER); ?>">
                                        <i class="fa fa-user mr-1">
                                        </i>
                                        <span>
                                            Đăng ký
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a class="text-dark" href="<?php echo e(URL_USERS_LOGIN); ?>">
                                        <i class="fa fa-sign-in mr-1">
                                        </i>
                                        <span>
                                            Đăng nhập
                                        </span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/Topbar-->
        <!-- Mobile Header -->
        <div class="sticky">
            <div class="horizontal-header clearfix ">
                <div class="container">
                    <a class="animated-arrow" id="horizontal-navtoggle">
                        <span>
                        </span>
                    </a>
                    <span class="smllogo">
                        <img alt="img" src="<?php echo e(PREFIX); ?>public/uploads/settings/logo-elearning.png" width="120"/>
                    </span>
                    <span class="smllogo-white">
                        <img alt="img" src="<?php echo e(PREFIX); ?>public/uploads/settings/logo-elearning.png" width="120"/>
                    </span>
                    <a class="callusbtn" href="tel:02838497875">
                        <i aria-hidden="true" class="fa fa-phone">
                        </i>
                    </a>
                </div>
            </div>
        </div>
        <!-- /Mobile Header -->
        <!--Horizontal-main -->
        <div class="horizontal-main header-style1 bg-dark-transparent clearfix">
            <div class="horizontal-mainwrapper container clearfix">
                <div class="desktoplogo">
                    <a href="/home">
                        <img alt="img" src="<?php echo e(PREFIX); ?>public/uploads/settings/logo-elearning.png" style="width: 120px">
                        </img>
                    </a>
                </div>
                <div class="desktoplogo-1">
                    <a href="/home">
                        <img alt="img" src="<?php echo e(PREFIX); ?>public/uploads/settings/logo-elearning.png" style="width: 120px">
                        </img>
                    </a>
                </div>
                <nav class="horizontalMenu clearfix d-md-flex">
                    <ul class="horizontalMenu-list">
                        <li aria-haspopup="true" class="active">
                            <a href="/home">
                                Trang Chủ
                            </a>
                        </li>
                        <li aria-haspopup="true">
                            <a href="/site/courses">
                                Khóa Học
                            </a>
                        </li>
                        <li aria-haspopup="true">
                            <a href="/site/study">
                                Khóa Luyện Thi
                            </a>
                        </li>
                        <li aria-haspopup="true">
                            <a href="/site/contact">
                                Liên Hệ
                            </a>
                        </li>
                        <?php if(!Auth::check()): ?>
                        <li aria-haspopup="true" class="d-lg-none mt-5 pb-5 mt-lg-0">
                            <span>
                                <a class="btn btn-success" href="/login">
                                    Đăng nhập ~
                                </a>
                            </span>
                        </li>
                        <li aria-haspopup="true" class="d-lg-none pb-5 mt-lg-0">
                            <span>
                                <a class="btn btn-info" href="<?php echo e(URL_USERS_REGISTER); ?>">
                                    Đăng ký
                                </a>
                            </span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!--/Horizontal-main -->
    <?php if($home_bg == 3): ?>
    <div>
        <div class="bannerimg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white ">
                        <h1 class="">
                            <?php echo e($title); ?>

                        </h1>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if($home_bg == 2): ?>
    <div class="owl-carousel bannner-owl-carousel1 slider slider-header banner-height slide-banner slider-images">
        <div class="item cover-image">
            <div class="slider-img" style="background-image: url(<?php echo e(PREFIX); ?>public/assets/images/banners/banner1.jpg);">
            </div>
            <div class="banner-text mb-0">
                <div class="container">
                    <div class="col-md-12">
                        <h1 class="banner-title">
                            TIẾNG NHẬT KHÓ ĐÃ CÓ HIKARI ACADEMY
                        </h1>
                        <h4 class="banner-subtitle">
                            Cung cấp đầy đủ mọi thông tin, kiến thức và kỹ năng
                        </h4>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="item">
            <div class="slider-img" style="background-image: url(<?php echo e(PREFIX); ?>public/assets/images/banners/banner2.jpg);">
            </div>
            <div class="banner-text mb-0">
                <div class="container">
                    <div class="col-md-12">
                        <h1 class="banner-title">
                            KHÓA HỌC ONLINE HÀNG ĐẦU VIỆT NAM
                        </h1>
                        <h4 class="banner-subtitle">
                            Không khoảng cách, không giới hạn thời gian, tính ứng dụng cao
                        </h4>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="item">
            <div class="slider-img" style="background-image: url(<?php echo e(PREFIX); ?>public/assets/images/banners/banner3.jpg);">
            </div>
            <div class="banner-text banner-search mb-0">
                <div class="container">
                    <div class="col-md-12">
                        <h1 class="banner-title">
                            XÂY DỰNG KỸ NĂNG TOÀN DIỆN
                        </h1>
                        <h4 class="banner-subtitle">
                            Từ vựng, ngữ pháp, đọc hiểu, nghe, hội thoại, luyện tập
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!--/Section-->
</div>
<style type="text/css">
    .header-main .header-icons-link1 .main-badge1 {
        position: absolute;
        top: -8px;
        right: -10px;
        text-align: center;
        font-size: 10px;
    }
</style>