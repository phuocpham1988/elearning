<!doctype html>

<html lang="en">

  <head>

    <!-- Meta data -->

    <meta charset="UTF-8">

    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta content="" name="description">

    <meta content="" name="author">

    <meta name="keywords" content=""/>

     <meta name="csrf_token" content="<?php echo e(csrf_token()); ?>">

    <!-- Favicon -->

    <link rel="icon" href="/public/assets/images/brand/favicon.ico" type="image/x-icon"/>

    <link rel="shortcut icon" type="image/x-icon" href="/public/uploads/settings/favicon.png" />

    <!-- Title -->

    <title><?php echo $__env->yieldContent('title'); ?> <?php echo e(isset($title) ? $title : getSetting('site_title','site_settings')); ?></title>

    <!-- Bootstrap css -->

    <link href="/public/assets/plugins/bootstrap-4.3.1/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Style css -->

    <link href="/public/assets/css/style.css" rel="stylesheet" />

    <link href="/public/assets/css/skin-modes.css" rel="stylesheet" />

    <!-- Font-awesome  css -->

    <link href="/public/assets/css/icons.css" rel="stylesheet"/>

    <!--Horizontal Menu css-->

    <link href="/public/assets/plugins/horizontal-menu/horizontal-menu.css" rel="stylesheet" />

    <!--Select2 css -->

    <link href="/public/assets/plugins/select2/select2.min.css" rel="stylesheet" />

    <!-- Cookie css -->

    

    <!-- Owl Theme css-->

    <link href="/public/assets/plugins/owl-carousel/owl.carousel.css" rel="stylesheet" />

    <!-- Custom scroll bar css-->

    <link href="/public/assets/plugins/scroll-bar/jquery.mCustomScrollbar.css" rel="stylesheet" />

    <!-- Pscroll bar css-->

    <link href="/public/assets/plugins/pscrollbar/pscrollbar.css" rel="stylesheet" />

    <!-- Switcher css -->

    <link  href="/public/assets/switcher/css/switcher.css" rel="stylesheet" id="switcher-css" type="text/css" media="all"/>

    <!-- Color Skin css -->

    <link id="theme" rel="stylesheet" type="text/css" media="all" href="/public/assets/color-skins/color6.css" />

    <?php echo $__env->yieldContent('header_scripts'); ?>

    <link href="<?php echo e(themes('css/sweetalert.css')); ?>" rel="stylesheet">

  </head>

  <body ng-app="academia">

    <!--Loader-->

    <!--/Loader-->

    <?php echo $__env->make('site.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo $__env->yieldContent('content'); ?>

    <?php echo $__env->make('site.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 

    <script>

    var csrfToken = $('[name="csrf_token"]').attr('content');

      setInterval(refreshToken, 600000); // 1 hour 

      function refreshToken(){

        $.get('refresh-csrf').done(function(data){

              csrfToken = data; // the new token

          });

      }

      setInterval(refreshToken, 600000); // 1 hour 

    </script>

    <script src="<?php echo e(themes('js/main.js')); ?>"></script>

    <script src="<?php echo e(themes('js/sweetalert-dev.js')); ?>"></script>

    <?php echo $__env->make('common.alertify', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo $__env->yieldContent('footer_scripts'); ?>

    <?php echo $__env->make('errors.formMessages', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo $__env->yieldContent('custom_div_end'); ?>

  </body>





</html>