<?php $__env->startSection('header_scripts'); ?>
<link href="<?php echo e(CSS); ?>ajax-datatables.css" rel="stylesheet">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<div id="page-wrapper">
	<div class="container-fluid">
		<!-- Page Heading -->
		<div class="row">
			<div class="col-lg-12">
				<ol class="breadcrumb">
					<li><a href="<?php echo e(PREFIX); ?>"><i class="mdi mdi-home"></i></a> </li>
					<li><a href="<?php echo e(PREFIX); ?>">Khóa luyện thi</a> </li>
				</ol>
			</div>
		</div>
		<!-- /.row -->
		<div class="panel panel-custom">
			<div class="panel-heading">
				<div class="pull-right messages-buttons">
					<a href="<?php echo e($create_url); ?>" class="btn btn-primary button" ><?php echo e(getPhrase('create')); ?></a>
				</div>
			</div>
			<div class="panel-body packages">
				<div>
					<table class="table table-striped table-bordered datatable" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>khóa luyện thi</th>
								<!-- <th>Loại</th>
								<th>Giá</th>
								<th>Số bài</th> -->
								<th><?php echo e(getPhrase('action')); ?></th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
	<!-- /.container-fluid -->
</div>

<div class="modal fade" id="import-exams" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-2" aria-hidden="true">

	<div class="modal-dialog" role="document">

		<div class="modal-content">

			<div class="modal-header">

				<h5 class="modal-title" id="exampleModalLabel-2">Import excel</h5>

			</div>

			<form action="<?php echo e($URL_IMPORT_CONTENT); ?>" class="forms-sample" method="post" id="form-importExcel"  enctype="multipart/form-data">
				<?php echo e(csrf_field()); ?>


				<div class="modal-body">

					<div class="card-body">
						<label>File (.xlsx)</label>
						<input type="file" name="file" class="form-control">
					</div>

				</div>

				<div class="modal-footer">

					<button type="button" class="btn btn-danger" data-dismiss="modal">Hủy bỏ</button>
					<button type="submit" class="btn btn-success">Tải lên</button>

				</div>

			</form>

		</div>

	</div>

</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footer_scripts'); ?>

<?php echo $__env->make('common.datatables', array('route'=>$datatbl_url, 'route_as_url' => true), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('common.deletescript', array('route'=>URL_LMS_SERIES_DELETE), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.adminlayout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>