@extends('layouts.'.getRole().'.'.getRole().'layout')
@section('header_scripts')
<link href="{{CSS}}ajax-datatables.css" rel="stylesheet">
@stop
@section('content')
<?php $image_path = PREFIX.(new App\ImageSettings())->getExamImagePath(); ?>
<div id="page-wrapper">
	<div class="container-fluid">
		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<ol class="breadcrumb">
					<li><a href="{{url('/')}}"><i class="mdi mdi-home"></i></a> </li>
					<li><a href="{{URL_QUIZ_QUESTIONBANK}}">Flashcard</a></li>
					<!-- <li><a href="{{URL_QUESTIONBAMK_IMPORT}}">{{ getPhrase('import_questions') }}</a></li> -->
					<li>{{ $title }}</li>
				</ol>
			</div>
		</div>
		<!-- /.row -->
		<div class="panel panel-custom">
			<div class="panel-heading">
				<div class="pull-right messages-buttons">
					<a href="/lms/flashcard-detail/add/{{$flashcard->id}}" class="btn  btn-primary button" >Tạo mới</a>
				</div>
				<h1>{{ $title }}</h1>
			</div>
			<div class="panel-body packages">
				<div class="table-responsive"> 
					<table class="table table-striped table-bordered datatable" id="hikari-table-view" cellspacing="0" width="100%">
						<thead>
							<tr>
								<!-- <th width="20%">Mondai</th> -->
								{{-- <th width="5%">Chủ đề</th> --}}
								<th width="20%">Từ vựng(M1)</th>
								<th width="20%">Ví dụ(M1)</th>
								<th width="10%">Cách đọc</th>
								<th width="10%">Âm Hán Việt</th>
								<th width="10%">Ý nghĩa</th>
								<th width="10%">Ví dụ</th>
								<th width="10%">Stt</th>
								<th width="3%">Mp3</th>
								<th width="3%">{{ getPhrase('action')}}</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
	<!-- /.container-fluid -->
</div>


@endsection
@section('footer_scripts')
<script src="{{themes('js/jquery-1.12.1.min.js')}}"></script>
@include('common.datatables', array('route'=>'/lms/flashcard/show/'.$flashcard->id, 'route_as_url' => 'TRUE'))
@include('common.deletescript', array('route'=>URL_QUESTIONBANK_DELETE))
@stop