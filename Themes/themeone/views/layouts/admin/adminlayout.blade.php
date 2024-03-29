<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="google" content="notranslate"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="{{getSetting('meta_description', 'seo_settings')}}">
	<meta name="keywords" content="{{getSetting('meta_keywords', 'seo_settings')}}">
	<meta name="csrf_token" content="{{ csrf_token() }}">
	<link rel="icon" href="/public/uploads/settings/favicon.png" type="image/x-icon" />
	<title>@yield('title') {{ isset($title) ? $title : getSetting('site_title','site_settings') }}</title>
	<!-- Bootstrap Core CSS -->
	@yield('header_scripts')
	<link href="{{themes('css/bootstrap.min.css')}}" rel="stylesheet">
	<link href="{{themes('css/sweetalert.css')}}" rel="stylesheet">
	<link href="{{themes('css/metisMenu.min.css')}}" rel="stylesheet">
	<link href="{{themes('css/custom-fonts.css')}}" rel="stylesheet">
	<link href="{{themes('css/materialdesignicons.css')}}" rel="stylesheet">
	<link href="{{themes('font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
	<link href="{{themes('css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
	<!-- Morris Charts CSS -->
	{{-- <link href="{{CSS}}plugins/morris.css" rel="stylesheet"> --}}
	<link href="{{themes('css/plugins/morris.css')}}" rel="stylesheet">
	<link href="{{themes('css/sb-admin.css')}}" rel="stylesheet">
	{{-- <link href="{{themes('css/themeone-blue.css')}}" rel="stylesheet"> --}}
	<?php
	$theme_color  = getThemeColor();
    // dd($theme_color);
	?>
	@if($theme_color == 'blueheader')
	<link href="{{themes('css/theme-colors/header-blue.css')}}" rel="stylesheet">
	@elseif($theme_color == 'bluenavbar')
	<link href="{{themes('css/theme-colors/blue-sidebar.css')}}" rel="stylesheet">
	@elseif($theme_color == 'darkheader')
	<link href="{{themes('css/theme-colors/dark-header.css')}}" rel="stylesheet">
	@elseif($theme_color == 'darktheme')
	<link href="{{themes('css/theme-colors/dark-theme.css')}}" rel="stylesheet">
	@elseif($theme_color == 'whitecolor')
	<link href="{{themes('css/theme-colors/white-theme.css')}}" rel="stylesheet">]
	@endif
</head>
<body ng-app="academia">
	@yield('custom_div')
	<?php
	$class = '';
	if(!isset($right_bar))
		$class = 'no-right-sidebar';
	?>
	<div id="wrapper" class="{{$class}}">
		<!-- Navigation -->
		<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
				<a class="navbar-brand" href="{{ URL_HOME }}" target="_blank"><img src="/public/uploads/settings/logo-elearning.png" style="width: 190px;"></a>
			</div>
			<!-- Top Menu Items -->
			<?php $newUsers = (new App\User())->getLatestUsers(); ?>
			<ul class="nav navbar-right top-nav">
				<!-- <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<i class="icon-topbar-event"></i> {{ getPhrase('latest_users') }}  </a>
					<div class="dropdown-menu dropdown-menu-right dropdown-menu-notif" aria-labelledby="dd-notification">
						<div class="dropdown-menu-notif-list" id="latestUsers">
							@foreach($newUsers as $user)
							<div class="dropdown-menu-notif-item">
								<div class="photo">
									<img src="{{ getProfilePath($user->image)}}" alt="">
								</div>
								<a href="{{URL_USER_DETAILS.$user->slug}}">{{ucfirst($user->name)}}</a>  {{ getPhrase('was_joined_as').' '. getRoleData($user->role_id)}}
								<div class="color-blue-grey-lighter"></div>
							</div>
							@endforeach
						</div>
						<div class="dropdown-menu-notif-more">
							<a href="{{URL_USERS}}">{{ getPhrase('see_more') }}</a>
						</div>
					</div>
				</li> -->
				<li class="dropdown profile-menu">
					<div class="dropdown-toggle top-profile-menu" data-toggle="dropdown">
						@if(Auth::check())
						<div class="username">
							<h2>{{Auth::user()->name}}</h2>
						</div>
						@endif
						<div class="profile-img"> <img src="{{ getProfilePath(Auth::user()->image, 'thumb') }}" alt=""> </div>
						<div class="mdi mdi-menu-down"></div>
					</div>
					<ul class="dropdown-menu">
						<li>
							<a href="{{URL_USERS_EDIT}}{{Auth::user()->slug}}">
								<sapn>My Hikari</sapn>
							</a>
						</li>
						<li>
							<a href="{{URL_USERS_CHANGE_PASSWORD}}{{Auth::user()->slug}}">
								<sapn>Đổi mật khẩu</sapn>
							</a>
						</li>
						<li>
							<a href="{{URL_USERS_LOGOUT}}">
								<sapn>Đăng xuất</sapn>
							</a>
						</li>
					</ul>
				</li>
			</ul>
			<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
			<!-- /.navbar-collapse -->
		</nav>
  		<aside class="left-sidebar">
  			<div class="collapse navbar-collapse navbar-ex1-collapse">
  				<ul class="nav navbar-nav side-nav">
  					<li {{ isActive($active_class, 'dashboard') }}>
  			<a href="{{PREFIX}}">
  				<i class="fa fa-fw fa-window-maximize"></i> {{ getPhrase('dashboard') }}
  			</a>
  		</li>
					<!-- <li {{ isActive($active_class, 'languages') }}> <a href="{{URL_LANGUAGES_LIST}}">
						<i class="fa fa-fw fa-language" aria-hidden="true"></i> {{ getPhrase('languages') }} </a> </li> -->
						<li {{ isActive($active_class, 'exams') }} >
							<a data-toggle="collapse" data-target="#exams"><i class="fa fa-fw fa-desktop" ></i>
							Đề thi </a>
							<ul id="exams" class="collapse sidemenu-dropdown">
								<li><a href="{{URL_QUIZ_QUESTIONBANK}}"> <i class="fa fa-fw fa-fw fa-question"></i>Ngân hàng câu hỏi</a></li>
								<li><a href="{{URL_QUIZZES}}"> <i class="fa fa-fw fa-list-ol"></i> Đề thi</a></li>
								<!-- <li><a href="{{URL_EXAM_TYPES}}"> <i class="fa fa-fw fa-list"></i> {{ getPhrase('exam_types')}}</a></li> -->
								<li><a href="{{URL_EXAM_SERIES}}"> <i class="fa fa-fw fa-list-ol"></i> Bộ đề thi</a></li>
								<li><a href="{{URL_MASTERSETTINGS_SUBJECTS}}"> <i class="icon-books"></i> Mondai</a></li>
								<li><a href="{{URL_MASTERSETTINGS_TOPICS}}"> <i class="fa fa-fw fa-database"></i> Câu hỏi mondai</a></li>
								<li><a href="{{URL_BOOKS}}"> <i class="fa fa-fw fa-book"></i> Sách</a></li>
								<li><a href="{{URL_QUIZ_CATEGORIES}}"> <i class="fa fa-fw fa-fw fa-random"></i>Danh mục đề</a></li>
								<li><a href="{{URL_INSTRUCTIONS}}"> <i class="fa fa-fw fa-hand-o-right"></i> Hướng dẫn</a></li>
							</ul>
						</li>
						<li {{ isActive($active_class, 'lms') }} >
							<a data-toggle="collapse" data-target="#lms"><i class="fa fa-fw fa-book" ></i>
							Đợt thi </a>
							<ul id="lms" class="collapse sidemenu-dropdown">
								<li><a href="{{URL_EXAM_SERIES_FREE_ADD}}"> <i class="fa fa-fw fa-random"></i>Thêm đợt thi</a></li>
								<li><a href="{{URL_EXAM_SERIES_FREE}}"> <i class="icon-books"></i>Danh sách</a></li>
							</ul>
						</li>
						<li {{ isActive($active_class, 'lms') }} >
							<a data-toggle="collapse" data-target="#lms"><i class="fa fa-fw fa-tv" ></i>
							Khóa học </a>
							<ul id="lms" class="collapse sidemenu-dropdown">
								<li><a href="{{ URL_LMS_SERIES }}"> <i class="fa fa-fw fa-random"></i>Danh sách khóa học</a></li>
								<li><a href="{{ URL_LMS_CLASS }}"> <i class="icon-books"></i>Thêm lớp</a></li>
								<li><a href="{{PREFIX.'lms/class-content/'}}"> <i class="icon-books"></i>Lớp học chỉ định</a></li>
							</ul>
						</li>
						<li {{ isActive($active_class, 'lms') }} >
							<a data-toggle="collapse" data-target="#lms"><i class="fa fa-fw fa-certificate" ></i>
							Khóa luyện thi </a>
							<ul id="lms" class="collapse sidemenu-dropdown">
								<li><a href="{{ URL_LMS_SERIES_EXAM }}"> <i class="fa fa-fw fa-random"></i>Danh sách</a></li>
								<li><a href="#"> <i class="fa fa-fw fa-random"></i>Thêm mới</a></li>
							</ul>
						</li>
						<li {{ isActive($active_class, 'lms') }} >
							<a data-toggle="collapse" data-target="#lms"><i class="fa fa-fw fa-shopping-cart" ></i>
								Chi phí khóa học </a>
							<ul id="lms" class="collapse sidemenu-dropdown">
								<li><a href="{{PREFIX.'lms/seriescombo'}}"> <i class="icon-books"></i>Danh sách</a></li>
								<li><a href="{{PREFIX.'lms/seriescombo/add'}}"> <i class="fa fa-fw fa-random"></i>Thêm khóa</a></li>

							</ul>
						</li>
						<li {{ isActive($active_class, 'coupons') }} >
							<a data-toggle="collapse" data-target="#coupons"><i class="fa fa-fw fa-tags"></i>
							{{ getPhrase('coupons') }} </a>
							<ul id="coupons" class="collapse sidemenu-dropdown">
								<li><a href="#"> <i class="fa fa-fw fa-list"></i>Danh sách</a></li>
								<li><a href="#"> <i class="fa fa-fw fa-plus"></i>Thêm mã</a></li>
							</ul>
						</li>
						<li {{ isActive($active_class, 'reports') }} >
							<a data-toggle="collapse" data-target="#reports"><i class="fa fa-fw fa-credit-card" ></i>
							Báo cáo thanh toán </a>
							<ul id="reports" class="collapse sidemenu-dropdown">
								<li><a href="{{PREFIX.'payments-order'}}"> <i class="fa fa-fw fa-link"></i>Order</a></li>
								<li><a href="{{URL_ONLINE_PAYMENT_REPORTS}}"> <i class="fa fa-fw fa-link"></i>Online</a></li>
								<li><a href="{{URL_OFFLINE_PAYMENT_REPORTS}}"> <i class="fa fa-fw fa-chain-broken"></i>Offline</a></li>
								<li><a href="{{URL_PAYMENT_REPORT_EXPORT}}"> <i class="fa fa-fw fa-file-excel-o"></i>Xuất báo cáo</a></li>
							</ul>
						</li>
						<li {{ isActive($active_class, 'notifications') }} >
							<a href="{{URL_ADMIN_NOTIFICATIONS}}" ><i class="fa fa-fw fa-bell" aria-hidden="true"></i>
							Thông báo </a>
						</li>

				<li {{ isActive($active_class, 'messages') }} >
					<a  href="{{PREFIX."comments/index"}}"> <i class="fa fa-fw fa-comments" aria-hidden="true"> </i>
						{{ getPhrase('messages')}} <small class="msg">{{$count = Auth::user()->newThreadsCount()}} </small></a>
					</li>
					<li {{ isActive($active_class, 'feedback') }} >
						<a href="{{URL_FEEDBACKS}}" ><i class="fa fa-fw fa-commenting" ></i>
						{{ getPhrase('feedback') }} </a>
					</li>
					<li {{ isActive($active_class, 'class') }}> <a href="{{URL_CLASS}}"><i class="fa fa-fw fa-users"></i> Lớp học </a> </li>
					<li {{ isActive($active_class, 'users') }}> <a href="{{URL_USERS}}"><i class="fa fa-fw fa-user-circle"></i> {{ getPhrase('users') }} </a> </li>
					<li {{ isActive($active_class, 'master_settings') }} >
						<a data-toggle="collapse" data-target="#master_settings" href="#"><i class="fa fa-fw fa-cog" ></i>
						Cài đặt </a>
						<ul id="master_settings" class="collapse sidemenu-dropdown">
							{{-- <li><a href="#"> <i class="icon-history"></i> {{ getPhrase('email_templates') }}</a></li> --}}
							@if(checkRole(getUserGrade(1)))
							<li><a href="/mastersettings/settings/view/email-settings"> <i class="icon-settings"></i> Cấu hình Mail</a></li>
							<li><a href="/email/templates"> <i class="icon-settings"></i> Mail template</a></li>
							<li><a href="/languages/list"> <i class="icon-settings"></i> Ngôn ngữ</a></li>
							@endif
						</ul>
					</li>

			</ul>
		</div>
	</aside>
	@if(isset($right_bar))
	<aside class="right-sidebar" id="rightSidebar">
		<button class="sidebat-toggle" id="sidebarToggle" href='javascript:'><i class="mdi mdi-menu"></i></button>
		<div class="panel panel-right-sidebar">
			<?php $data = '';
			if(isset($right_bar_data))
				$data = $right_bar_data;
			?>
			@include($right_bar_path, array('data' => $data))
		</div>
	</aside>
	@endif
	@yield('content')
</div>
<!-- /#wrapper -->
<!-- jQuery -->
{{-- <script>
	var csrfToken = $('[name="csrf_token"]').attr('content');
            setInterval(refreshToken, 600000); // 1 hour
            function refreshToken(){
            	$.get('refresh-csrf').done(function(data){
                    csrfToken = data; // the new token
                });
            }
            setInterval(refreshToken, 600000); // 1 hour
        </script> --}}
        <!-- Bootstrap Core JavaScript -->
        <script src="{{themes('js/jquery-1.12.1.min.js')}}"></script>
        <script src="{{themes('js/bootstrap.min.js')}}"></script>
        <script src="{{themes('js/main.js')}}"></script>
        <script src="{{themes('js/metisMenu.min.js')}}"></script>
        <script src="{{themes('js/sweetalert-dev.js')}}"></script>
        <script >
        	/*Sidebar Menu*/
        	$("#ag-menu").metisMenu();
        </script>
        @yield('footer_scripts')
        @include('errors.formMessages')
        @yield('custom_div_end')
        <div class="ajax-loader" style="display:none;" id="ajax_loader"><img src="{{AJAXLOADER}}"> {{getPhrase('please_wait')}}...</div>
    </body>
    </html>
