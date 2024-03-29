				    <?php 
				    $exam_difficulty_levels = array(1=>'Chữ Hán', 2=>'Từ vựng', 3=>'Ngữ pháp', 4=>'Đọc hiểu',5=>'Nghe hiểu');
				    ?>
				    <fieldset class="form-group " style="display: none">
					{{ Form::label('difficulty_level', 'Bài thi') }}
					<span class="text-red">*</span>
					{{Form::select('difficulty_level',$exam_difficulty_levels , 'medium', ['class'=>'form-control', "id"=>"difficulty_level" ])}}
					</fieldset>
				    <fieldset class="form-group">
						{{ Form::label('subject_title', 'Mondai') }}
						<span class="text-red">*</span>
						{{ Form::text('subject_title', $value = null , $attributes = array('class'=>'form-control', 'placeholder' => '',
							'ng-model'=>'subject_title', 
							'ng-pattern' => '',
							'required'=> 'true', 
							'ng-class'=>'{"has-error": formSubjects.subject_title.$touched && formSubjects.subject_title.$invalid}',
							'ng-minlength' => '2',
							'ng-maxlength' => '40',
						)) }}
						<div class="validation-error" ng-messages="formSubjects.subject_title.$error" >
	    					{!! getValidationMessage()!!}
	    					{!! getValidationMessage('pattern')!!}
	    					{!! getValidationMessage('minlength')!!}
	    					{!! getValidationMessage('maxlength')!!}
						</div>
					</fieldset>
					<fieldset class="form-group">
						{{ Form::label('subject_code', 'Mô tả') }}
						<span class="text-red">*</span>
						{{ Form::textarea('subject_code', $value = null , $attributes = array('class'=>'form-control', 'placeholder' => '',
							'ng-class'=>'',
						)) }}
						<div class="validation-error" ng-messages="formSubjects.subject_code.$error" >
	    					{!! getValidationMessage()!!}
						</div>
					</fieldset>
					{{-- <div class="row">
					<fieldset class='form-group col-md-6'>
						{{ Form::label('is_lab', getphrase('is_lab')) }}
						<div class="form-group row">
							<div class="col-md-6">
							{{ Form::radio('is_lab', 0, true, array('id'=>'free', 'name'=>'is_lab')) }}
								<label for="free"> <span class="fa-stack radio-button"> <i class="mdi mdi-check active"></i> </span> {{getPhrase('No')}}</label> 
							</div>
							<div class="col-md-6">
							{{ Form::radio('is_lab', 1, false, array('id'=>'paid', 'name'=>'is_lab')) }}
								<label for="paid"> <span class="fa-stack radio-button"> <i class="mdi mdi-check active"></i> </span> {{getPhrase('Yes')}} </label>
							</div>
						</div>
					</fieldset>
 					 <fieldset class='form-group col-md-6'>
						{{ Form::label('is_elective_type', getphrase('is_elective')) }}
						<div class="form-group row">
							<div class="col-md-6">
							{{ Form::radio('is_elective_type', 0, true, array('id'=>'free1', 'name'=>'is_elective_type')) }}
								<label for="free1"> <span class="fa-stack radio-button"> <i class="mdi mdi-check active"></i> </span> {{getPhrase('No')}}</label> 
							</div>
							<div class="col-md-6">
							{{ Form::radio('is_elective_type', 1, false, array('id'=>'paid1', 'name'=>'is_elective_type')) }}
								<label for="paid1"> <span class="fa-stack radio-button"> <i class="mdi mdi-check active"></i> </span> {{getPhrase('Yes')}} </label>
							</div>
						</div>
					</fieldset>
					</div> --}}	
					{{-- <div class="row">
					 <fieldset class="form-group col-md-6">
						{{ Form::label('maximum_marks', getphrase('maximum_marks')) }}
						<span class="text-red">*</span>
						{{ Form::number('maximum_marks', $value = null , $attributes = array('class'=>'form-control', 'placeholder' => '100', 'id'=>'maximum_marks', 'min'=>'1', 'onblur'=>'validateMarks();',
							'ng-model'=>'maximum_marks', 
							'ng-number' => 'true',
							'required'=> 'true', 
							'ng-class'=>'{"has-error": formSubjects.maximum_marks.$touched && formSubjects.maximum_marks.$invalid}'
						)) }}
						<div class="validation-error" ng-messages="formSubjects.maximum_marks.$error" >
	    					{!! getValidationMessage()!!}
	    					{!! getValidationMessage('number')!!}
						</div>
					</fieldset>
					<fieldset class="form-group col-md-6">
						{{ Form::label('pass_marks', getphrase('pass_marks')) }}
						<span class="text-red">*</span>
						{{ Form::number('pass_marks', $value = null , $attributes = array('class'=>'form-control', 'placeholder' => '40', 'id'=>'pass_marks','min'=>'1', 'onblur'=>'validateMarks();',
							'ng-model'=>'pass_marks', 
							'required'=> 'true', 
							'ng-class'=>'{"has-error": formSubjects.pass_marks.$touched && formSubjects.pass_marks.$invalid}'
						)) }}
						<div class="validation-error" ng-messages="formSubjects.pass_marks.$error" >
	    					{!! getValidationMessage()!!}
	    					{!! getValidationMessage('number')!!}
						</div>
					</fieldset>
					</div> --}}
					</fieldset>
						<div class="buttons text-center">
							<button class="btn btn-lg btn-success button" 
							ng-disabled='!formSubjects.$valid'>{{ $button_name }}</button>
						</div>
		 