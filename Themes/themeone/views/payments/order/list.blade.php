@extends($layout)

@section('header_scripts')

    <link href="{{CSS}}ajax-datatables.css" rel="stylesheet">

@stop

@section('content')





    <div id="page-wrapper" ng-controller="payments_report">

        <div class="container-fluid">

            <!-- Page Heading -->

            <div class="row">

                <div class="col-lg-12">

                    <ol class="breadcrumb">

                        <li><a href="{{PREFIX}}"><i class="mdi mdi-home"></i></a> </li>




                        <li>{{ $title }}</li>

                    </ol>

                </div>

            </div>



            <!-- /.row -->

            <div class="panel panel-custom">

                <div class="panel-heading">

                    <h1>{{ $title }}</h1>

                </div>

                <div class="panel-body packages">

                    <div class="table-responsive">

                        <table class="table table-striped table-bordered datatable" cellspacing="0" width="100%">

                            <thead>

                                <tr>
                                    <th>STT</th>
                                    <th>{{ getPhrase('user_name')}}</th>
                                    <th>{{ getPhrase('email')}}</th>
                                    <th>Tên đơn hàng</th>
                                    <th>Loại</th>
                                    <th>Giá</th>
                                    <th>Thời gian đặt hàng</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>

                            </thead>



                        </table>

                    </div>



                </div>

            </div>

        </div>

        <!-- /.container-fluid -->

        <!-- Modal -->

    </div>











@endsection





@section('footer_scripts')



    @include('common.datatables', array('route'=>url('payments-order/getList'), 'route_as_url' => TRUE))

   @include('payments.scripts.js-scripts');

    {{-- @include('common.deletescript', array('route'=>URL_QUIZ_DELETE)) --}}

    {{--<script>

        function viewDetails(record_id)

        {

            angular.element('#page-wrapper').scope().setDetails(record_id);

            angular.element('#page-wrapper').scope().$apply()

            $('#myModal').modal('show');

        }

    </script>--}}

    <script>
            function successOrder(slug){
                var slug  =  slug;
                swal({

                        title: "Xác nhận đã thanh toán",

                        text: "",

                        type: "warning",

                        showCancelButton: true,
                        confirmButtonColor: '#8CD4F5',
                        /* confirmButtonClass: "btn-danger",*/

                        confirmButtonText: "Đồng ý",

                        cancelButtonText: "Hủy bỏ",

                        closeOnConfirm: false,

                        closeOnCancel: false

                    },

                    function(isConfirm) {

                        if (isConfirm) {

                            let route = '{{url('payments-order/success')}}';
                            let token = '{{csrf_token()}}';


                            $.ajax({

                                url:route,

                                type: 'post',
                                dataType: "json",
                                data: {
                                    _method: 'post',
                                    _token :token,
                                    slug : slug,
                                },
                                success:function(data){


                                    if(data.error === 1){
                                        swal({
                                            title: 'success'+"!",
                                            text: data.message,
                                            type: 'success',
                                            showConfirmButton: false,
                                            showCancelButton: false,
                                            timer: 1000,
                                        });
                                    }else {
                                        swal({
                                            title: 'warning'+"!",
                                            text: data.message,
                                            type: 'warning',
                                            showConfirmButton: false,
                                            showCancelButton: false,
                                            timer: 1000,
                                        });
                                    }
                                }
                            })

                            $('.datatable').DataTable().ajax.reload();
                        } else {

                            swal("cancelled", "Your Record Is Safe :)", "error");

                        }

                    });
            }

    </script>
@stop

