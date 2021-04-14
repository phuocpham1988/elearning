@extends('layouts.admin.adminlayout')
@section('custom_div')
<div ng-controller="prepareQuestions">
	@stop
	@section('content')
	<div id="page-wrapper">
		<div class="container-fluid" ng-init="recordData({{$record->is_paid}});">
			<!-- Page Heading -->
			<div class="row">
				<div class="col-lg-12">
					<ol class="breadcrumb">
						<li><a href="{{PREFIX}}"><i class="mdi mdi-home"></i></a> </li>
						<li><a href="{{URL_EXAM_SERIES}}">Bộ đề thi</a></li>
						<li class="active">{{isset($title) ? $title : ''}}</li>
					</ol>
				</div>
			</div>
			@include('errors.errors')
			<?php $settings = ($record) ? $settings : ''; ?>
			<div class="panel panel-custom" ng-init="initAngData({{$settings}});" style="width: 730px;">
				<div class="panel-heading">
					<div class="pull-right messages-buttons">
						<a href="{{URL_EXAM_SERIES}}" class="btn  btn-primary button" >{{ getPhrase('list')}}</a>
					</div>
					<h1>{{ $title }}  </h1>
				</div>
				<div class="panel-body" >
					<?php $button_name = getPhrase('create'); ?>
					<div class="row">
						<fieldset class="form-group col-md-6">
							{{ Form::label('exam_categories', 'Danh mục đề thi') }}
							<span class="text-red">*</span>
							{{Form::select('exam_categories', $exam_categories, null, ['class'=>'form-control', 'ng-model' => 'category_id', 
							'placeholder' => '--Chọn danh mục đề thi--', 
							'ng-change'=>'categoryChanged(category_id)' ])}}
						</fieldset>
						<div class="col-md-12">
							<div ng-if="examSeries!=''" class="vertical-scroll" >
								<h4 ng-if="categoryExams.length>0" class="text-success">Số đề thi: @{{ categoryExams.length}} </h4>
								<table  
								class="table table-hover">
								<th>Đề thi</th>
								<th>Thời gian</th>
								<th>Điểm</th>
								<th>Câu hỏi</th>	
								<th>{{getPhrase('action')}}</th>	
								<tr ng-repeat="exam in categoryExams  track by $index">
									<td 
									title="@{{exam.title}}" >
									@{{exam.title}}
								</td>
								<td>@{{exam.dueration}}</td>
								<td>@{{exam.total_marks}}</td>
								<td>@{{exam.total_questions}}</td>
								<td><a 
									ng-click="addQuestion(exam);" class="btn btn-primary" >{{getPhrase('add')}}</a>
								</td>
							</tr>
						</table>
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
@stop
@section('footer_scripts')
@include('exams.examseries.scripts.js-scripts')
@stop
@section('custom_div_end')
</div>
@stop