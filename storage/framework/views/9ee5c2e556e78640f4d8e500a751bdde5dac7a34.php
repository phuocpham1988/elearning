
<div class="row">
	<fieldset class="form-group col-md-12">
		<?php echo e(Form::label('title', getphrase('Tiêu đề'))); ?>


		<span class="text-red">*</span>

		<?php echo e(Form::text('title', $value = null , $attributes = array('class'=>'form-control', 'placeholder' => getPhrase('series_title'),

		'ng-model'=>'title',

		'ng-pattern'=>'',

		'required'=> 'true',

		'ng-class'=>'{"has-error": formLms.title.$touched && formLms.title.$invalid}',

		'ng-minlength' => '2',

		'ng-maxlength' => '240',

		))); ?>


		<div class="validation-error" ng-messages="formLms.title.$error" >

			<?php echo getValidationMessage(); ?>


			<?php echo getValidationMessage('pattern'); ?>


			<?php echo getValidationMessage('minlength'); ?>


			<?php echo getValidationMessage('maxlength'); ?>


		</div>

	</fieldset>

</div>


<div class="row">

	<?php $category_options = array(0 => 'Khóa học', 1 => 'Khóa luyện thi');?>

	<fieldset class="form-group col-md-4" >

			<?php echo e(Form::label('type', 'Loại khóa')); ?>


			<span class="text-red">*</span>

			<?php echo e(Form::select('type', $category_options, $value, ['class'=>'form-control',

            'ng-model'=>'type',

            'required'=> 'true',

            'ng-pattern' => getRegexPattern("name"),

            'ng-minlength' => '2',

            'ng-maxlength' => '20',

            'ng-class'=>'{"has-error": formLms.type.$touched && formLms.type.$invalid}',



            ])); ?>


			<div class="validation-error" ng-messages="formLms.type.$error" >

				<?php echo getValidationMessage(); ?>


			</div>





		</fieldset>


	<fieldset class="form-group col-md-6" >

		<?php echo e(Form::label('image', getphrase('image'))); ?>


		<input type="file" class="form-control" name="image"
			   accept=".png,.jpg,.jpeg" id="image_input">



		<div class="validation-error" ng-messages="formCategories.image.$error" >

			<?php echo getValidationMessage('image'); ?>




		</div>

	</fieldset>



	<fieldset class="form-group col-md-2" >

		<?php if($record): ?>

			<?php if($record->image): ?>

				<?php $examSettings = getExamSettings(); ?>

				<img src="<?php echo e('/public/uploads/lms/combo/'.$record->image); ?>" height="auto" width="100" >



			<?php endif; ?>

		<?php endif; ?>

	</fieldset>

</div>


<div class="row" ng-if="type == 0">

	<fieldset class="form-group col-md-6">
		<?php echo e(Form::label('n1', 'Khóa học N1')); ?>

		<span class="text-red">*</span>
		<?php echo e(Form::select('n1', $n1, $value, ['class'=>'form-control'])); ?>

	</fieldset>
	<fieldset class="form-group col-md-6">
		<?php echo e(Form::label('n2', 'Khóa học N2')); ?>

		<span class="text-red">*</span>
		<?php echo e(Form::select('n2', $n2, $value, ['class'=>'form-control'])); ?>

	</fieldset>
	<fieldset class="form-group col-md-6">
		<?php echo e(Form::label('n3', 'Khóa học N3')); ?>

		<span class="text-red">*</span>
		<?php echo e(Form::select('n3', $n3, $value, ['class'=>'form-control'])); ?>

	</fieldset>
	<fieldset class="form-group col-md-6">
		<?php echo e(Form::label('n4', 'Khóa học N4')); ?>

		<span class="text-red">*</span>
		<?php echo e(Form::select('n4', $n4, $value, ['class'=>'form-control'])); ?>

	</fieldset>
	<fieldset class="form-group col-md-6">
		<?php echo e(Form::label('n5', 'Khóa học N5')); ?>

		<span class="text-red">*</span>
		<?php echo e(Form::select('n5', $n5, $value, ['class'=>'form-control'])); ?>

	</fieldset>
	
</div>
<div class="row" ng-if="type == 1">

	<fieldset class="form-group col-md-6">
		<?php echo e(Form::label('n1', 'Khóa luyện thi N1')); ?>

		<span class="text-red">*</span>
		<?php echo e(Form::select('n1', $en1, $value, ['class'=>'form-control'])); ?>

	</fieldset>
	<fieldset class="form-group col-md-6">
		<?php echo e(Form::label('n2', 'Khóa luyện thi N2')); ?>

		<span class="text-red">*</span>
		<?php echo e(Form::select('n2', $en2, $value, ['class'=>'form-control'])); ?>

	</fieldset>
	<fieldset class="form-group col-md-6">
		<?php echo e(Form::label('n3', 'Khóa luyện thi N3')); ?>

		<span class="text-red">*</span>
		<?php echo e(Form::select('n3', $en3, $value, ['class'=>'form-control'])); ?>

	</fieldset>
	<fieldset class="form-group col-md-6">
		<?php echo e(Form::label('n4', 'Khóa luyện thi N4')); ?>

		<span class="text-red">*</span>
		<?php echo e(Form::select('n4', $en4, $value, ['class'=>'form-control'])); ?>

	</fieldset>
	<fieldset class="form-group col-md-6">
		<?php echo e(Form::label('n5', 'Khóa luyện thi N5')); ?>

		<span class="text-red">*</span>
		<?php echo e(Form::select('n5', $en5, $value, ['class'=>'form-control'])); ?>

	</fieldset>
	
</div>

<div class="row">

	<fieldset class="form-group col-md-4">



		<?php echo e(Form::label('cost', getphrase('cost'))); ?>


		<span class="text-red">*</span>

		<?php echo e(Form::number('cost', $value = null , $attributes = array('class'=>'form-control', 'placeholder' => '40',

        'min'=>'0',



        'ng-model'=>'cost',

        'required'=> 'true',
        'string-to-number'=>'true',
        'ng-class'=>'{"has-error": formLms.cost.$touched && formLms.cost.$invalid}',



        ))); ?>


		<div class="validation-error" ng-messages="formLms.cost.$error" >

			<?php echo getValidationMessage(); ?>


			<?php echo getValidationMessage('number'); ?>


		</div>

	</fieldset>
	<fieldset class="form-group col-md-4">



		<?php echo e(Form::label('selloff', getphrase('selloff'))); ?>


		<span class="text-red">*</span>

		<?php echo e(Form::number('selloff', $value = null , $attributes = array('class'=>'form-control', 'placeholder' => '40',

        'min'=>'0',



        'ng-model'=>'selloff',

        'required'=> 'true',
        'string-to-number'=>'true',
        'ng-class'=>'{"has-error": formLms.selloff.$touched && formLms.selloff.$invalid}',



        ))); ?>


		<div class="validation-error" ng-messages="formLms.selloff.$error" >

			<?php echo getValidationMessage(); ?>


			<?php echo getValidationMessage('number'); ?>


		</div>

	</fieldset>

	<?php $time_options = array(0 => '3 tháng', 1 => '6 tháng' , 2 => '12 tháng');?>

	<fieldset class="form-group col-md-4" >

		<?php echo e(Form::label('time', 'Thời gian')); ?>


		<span class="text-red">*</span>

		<?php echo e(Form::select('time', $time_options, $value, ['placeholder' => getPhrase('select'),'class'=>'form-control',

        'ng-model'=>'time',

        'required'=> 'true',

        'ng-pattern' => getRegexPattern("name"),

        'ng-minlength' => '2',

        'ng-maxlength' => '20',

        'ng-class'=>'{"has-error": formLms.time.$touched && formLms.time.$invalid}',



        ])); ?>


		<div class="validation-error" ng-messages="formLms.time.$error" >

			<?php echo getValidationMessage(); ?>


		</div>





	</fieldset>

</div>
<div class="row">

	<fieldset class="form-group  col-md-12">



		<?php echo e(Form::label('short_description', getphrase('short_description'))); ?>




		<?php echo e(Form::textarea('short_description', $value = null , $attributes = array('class'=>'form-control ckeditor', 'rows'=>'5', 'placeholder' => getPhrase('short_description')))); ?>


	</fieldset>

	<fieldset class="form-group  col-md-12">



		<?php echo e(Form::label('description', getphrase('description'))); ?>




		<?php echo e(Form::textarea('description', $value = null , $attributes = array('class'=>'form-control ckeditor', 'rows'=>'5', 'placeholder' => getPhrase('description')))); ?>


	</fieldset>



</div>

<div class="buttons text-center">

	<button class="btn btn-lg btn-success button"

	ng-disabled='!formLms.$valid'><?php echo e($button_name); ?></button>

</div>
