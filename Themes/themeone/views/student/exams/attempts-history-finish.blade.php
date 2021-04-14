@extends($layout)
@section('header_scripts')
<link href="{{CSS}}ajax-datatables.css" rel="stylesheet">
	<style>
		table tr td{
			padding:20px 10px!important;
			font-size: 14px!important;
		}
		table thead tr th:before,table thead tr th:after{
			content: none!important;
		}
		table tbody tr td:nth-child(3),table tbody tr td:nth-child(4),table thead tr th{
			text-align: center!important;
		}
		.dataTables_wrapper .dataTables_paginate .paginate_button:hover{

			background: transparent;
		}
		.pagination>li:first-child>a{
			border-top-left-radius: 0.25rem!important;
			border-bottom-left-radius: 0.25rem!important;
		}
		.pagination>li:last-child>a{
			border-top-right-radius: 0.25rem!important;
			border-bottom-right-radius: 0.25rem!important;
		}
		.pagination>li>a{
			padding: 0.5rem 1rem!important;
		}
		.pagination>.active>a{
			line-height:  41px!important;
			padding: 0.5rem 1rem!important;
		}
		.dataTables_wrapper .dataTables_paginate .paginate_button:hover  a{
			color: white !important;
			background: #438afe;
		}

		.dataTables_filter, .dataTables_info, .dataTables_length,.dataTables_paginate { display: none; }
		table.dataTable thead .sorting{
			background: none;
		}
		.table-responsive{
			overflow-x: auto!important;
		}
</style>
@stop
@section('content')

	<div class="container" style="background-color: #fff;">

	    <div class="row">

	        <div class="col-sm-12">

				 <nav aria-label="breadcrumb">

				   <ol class="breadcrumb breadcrumb-custom bg-inverse-info">

				     <li class="breadcrumb-item"><a href="/home"><i class="mdi mdi-home menu-icon"></i></a></li>

				     <li class="breadcrumb-item"><a href="/home">Phòng thi</a></li>

				     <li class="breadcrumb-item active" aria-current="page"><span>{{ ucfirst($title) }}</span></li>

				   </ol>

				 </nav>
				
				 <div class="ed_heading_top col-sm-12">

				  <h3 class="tilte-h3 wow fadeInDown text-danger animated text-uppercase" style="visibility: visible;">{{ $title }}</h3>

				</div>
				
				<div class="table-responsive">
					<table class="table table-striped table-bordered datatable" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>Bộ đề thi</th>
								<!-- <th>Thời gian thi</th> -->
								<th>Điểm từng phần thi</th>
								<th style="text-align: center;">Tổng điểm <span style="font-size: 10px"><br/>(đã nhân hệ số)/180</span></th>
								<th>Kết quả</th>
								<th>Xem đáp án</th>
							</tr>
						</thead>
					</table>
				</div>


	</div>
	</div>
	</div>
	
	<!-- /.row -->
	

@endsection
@section('footer_scripts')
@if(!$exam_record)
@include('common.datatables', array('route'=>URL_STUDENT_EXAM_GETATTEMPTS_FINISH.$user->slug, 'route_as_url' => 'TRUE'))
@else
@include('common.datatables', array('route'=>URL_STUDENT_EXAM_GETATTEMPTS_FINISH.$user->slug.'/'.$exam_record->slug, 'route_as_url' => 'TRUE'))
@endif
@stop
