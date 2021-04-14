<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>@yield('title') {{ isset($title) ? $title : getSetting('site_title','site_settings') }}</title>
  <meta name="csrf_token" content="{{ csrf_token() }}">
  @yield('header_scripts')
  <!-- plugins:css -->
  <link rel="stylesheet" href="/Themes/themeone/assets/student/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="/Themes/themeone/assets/student/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="/Themes/themeone/assets/student/vendors/owl-carousel-2/owl.carousel.min.css">
  <link rel="stylesheet" href="/Themes/themeone/assets/student/vendors/owl-carousel-2/owl.theme.default.min.css">
  <link href="{{themes('css/ihover.min.css')}}" rel="stylesheet">
  <link href="{{themes('css/sweetalert.css')}}" rel="stylesheet">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link href="{{themes('font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="/Themes/themeone/assets/student/css/horizontal-layout/style.css">
  <link href="{{themes('css/sb-admin-site.css')}}" rel="stylesheet">
  <!-- <link href="{{themes('site/css/main.css')}}" rel="stylesheet"> -->
  <!-- endinject -->
  <link rel="shortcut icon" href="/public/uploads/settings/favicon.png" />
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-149456320-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-149456320-1');
  </script>
</head>
<body>
  <div class="container-scroller">
    @include('site.header')
    <div class="container-fluid page-body-wrapper">
      <div class="main-panel">
        @yield('content')
        
      </div>
      @include('site.footer')
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <script src="{{themes('js/jquery-1.12.1.min.js')}}"></script>
  <script src="/Themes/themeone/assets/student/vendors/js/vendor.bundle.base.js"></script>
  <script src="/Themes/themeone/assets/student/js/template.js"></script>
  <script src="/Themes/themeone/assets/student/js/dashboard.js"></script>
  <script src="/Themes/themeone/assets/student/vendors/owl-carousel-2/owl.carousel.min.js"></script>
  <script src="/Themes/themeone/assets/student/js/owl-carousel.js"></script>
  <script src="{{themes('js/main.js')}}"></script>
  <script src="{{themes('site/js/wow.min.js')}}"></script>
  <script src="{{themes('js/sweetalert-dev.js')}}"></script>
  <script>
    wow = new WOW(
      {
        animateClass: 'animated',
        offset:       100,
        callback:     function(box) {
          console.log("WOW: animating <" + box.tagName.toLowerCase() + ">")
        }
      }
    );
    wow.init();
    document.getElementById('moar').onclick = function() {
      var section = document.createElement('section');
      section.className = 'section--purple wow fadeInDown';
      this.parentNode.insertBefore(section, this);
    };
  </script>
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
  @include('common.alertify')
  @yield('footer_scripts')
  @include('errors.formMessages')
  @yield('custom_div_end')
</body>
</html>