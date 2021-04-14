@extends($layout)
@section('header_scripts')
<link href="//cdn.datatables.net/1.10.10/css/jquery.dataTables.min.css" rel="stylesheet">
@stop
@section('content')

<div id="page-wrapper">
			<div class="container-fluid">
				<!-- Page Heading -->
				<div class="row">
					<div class="col-lg-12">
						<ol class="breadcrumb">
							<li><a href="{{url('/')}}"><i class="mdi mdi-home"></i></a> </li>
							<li>{{ $title }}</li>
						</ol>
					</div>
				</div>
								
				<!-- /.row -->
				<div class="panel panel-custom">
					<div class="panel-heading">
						
						<div class="pull-right messages-buttons">
							 
							<a href="/parent/class" class="btn  btn-primary button" >Danh sách lớp</a>
						</div>
						<h1>{{ $title }}</h1>
					</div>
					<div class="panel-body packages">
						<div > 
						<table class="table table-striped table-bordered datatable" cellspacing="0" width="100%">
							<thead>
								<tr>
								 	<th width="30%">Họ Tên </th>
									<th width="20%">Điểm thi</th>
									<th width="10%">Tổng điểm</th>
									<th width="10%">Kết quả</th>
									<!-- <th width="5%">{{ getPhrase('action')}}</th> -->
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
 
 <?php $url = URL_PARENT_CLASSMARK_GETLIST.$slug.'/'.$slug_exam.'/'.$slug_category;
 
  ?>
@section('footer_scripts')
  @include('common.datatables', array('route'=>$url, 'route_as_url' => TRUE, 'pdf'=>'0,1,2,3' ))
@stop
