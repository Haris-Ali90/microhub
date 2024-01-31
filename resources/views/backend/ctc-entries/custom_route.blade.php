<?php

$user = Auth::user();
if($user->email!="admin@gmail.com")
{

    $data = explode(',', $user['rights']);
    $dataPermission = explode(',', $user['permissions']);
}

else{
    $data = [];
    $dataPermission=[];
}

?>
@extends( 'backend.layouts.app' )

@section('title', 'Last Mile Dashboard')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <!-- Image Viewer CSS -->
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <!-- Custom Light Box Css -->
    <link href="{{ backend_asset('css/custom_lightbox.css') }}" rel="stylesheet">

    <!-- Custom lib Css -->
    <link href="{{ backend_asset('css/min_lib.css') }}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="{{ backend_asset('js/jquery-ui.js') }}"></script>
    <link href="{{ backend_asset('js/jquery-ui.css') }}" rel="stylesheet"> -->
    <!-- Custom Light Box JS -->
    <script src="{{ backend_asset('js/custom_lightbox.js')}}"></script>
@endsection

@section('inlineJS')


    <script>
        $(document).ready(function() {
            $('#birthday').daterangepicker({
                singleDatePicker: true,
                calender_style: "picker_4"
            }, function(start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
            });
        });
    </script>
    <script>

        $(function () {
            appConfig.set('yajrabox.ajax', '{{ route('new-custom-route-ctc.data') }}');
            appConfig.set('dt.order', [0, 'desc']);
            appConfig.set('yajrabox.scrollx_responsive',true);
            appConfig.set('yajrabox.autoWidth', false);
            appConfig.set('yajrabox.ajax.data', function (data) {
                data.datepicker = jQuery('[name=datepicker]').val();
            });

            appConfig.set('yajrabox.columns', [
                {data: 'sprint_id', orderable: true, searchable: true, className: 'text-center'},
                {data: 'route_id', orderable: true, searchable: true, className: 'text-center'},
                {data: 'joey_name', orderable: true, searchable: true,className: 'text-center'},
                {data: 'address_line_1', orderable: true, searchable: true, className: 'text-center'},
                {data: 'picked_up_at', orderable: true, searchable: false, className: 'text-center'},
                {data: 'sorted_at', orderable: true, searchable: false, className: 'text-center'},
                {data: 'delivered_at', orderable: true, searchable: true},
                {data: 'order_image', orderable: false, searchable: false, className: 'text-center'},
                {data: 'tracking_id', orderable: true, searchable: true, className: 'text-center'},
                {data: 'task_status_id', orderable: true, searchable: true, className: 'text-center'},
                {data: 'action', orderable: false, searchable: false, className: 'text-center'},
            ]);
        })






        function getCustomRouteData() {
            let selected_date = $('.data-selector').val();
            // show loader
            $('.custom-route').addClass('show');
            $.ajax({
                type: "GET",
                url: "<?php echo URL::to('/'); ?>/new/last-mile/customroutecards/" + selected_date,
                data: {},
                success: function(data)
                {
                    $('#custom_orders').text(data['custom_route']);
                    // hide loader
                    $('.custom-route').removeClass('show');
                },
                error:function (error) {
                    console.log(error);
                    // hide loader
                    $('.custom-route').removeClass('show');
                }
            });
        }



        setTimeout(function(){
            getCustomRouteData();
        }, 1000);


        $('.buttons-reload').on('click',function(event){
            event.preventDefault();
            // show main loader
            showLoader();

            // update data table data
            var ref = $('#yajra-reload').DataTable();
            ref.ajax.reload(function(){
                // hide loader
                hideLoader()
            });

            // updating cards data
            getCustomRouteData();

        });




        $('.buttons-excel').on('click',function(event){
            event.preventDefault();
            let href = $(this).attr('href');
            let selected_date = $('.data-selector').val();
            window.location.href = href+'/'+selected_date;
        });

    </script>



@endsection

@section('content')


    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3 class="">Last Mile Custom Route Orders<small></small></h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <!--Count Div Row Open-->
        @include('backend.ctc-entries.ctc_cards')
            <!--Count Div Row Close-->

            {{--@include('backend.layouts.modal')
            @include( 'backend.layouts.popups')--}}
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <form method="get" action="">
                                <div class="row d-flex align-item-end">
                                    <div class="col-md-3">
                                        <label>Search By Date:</label>
                                        <input type="date" name="datepicker" class="data-selector form-control" required=""
                                               value="{{ isset($_GET['datepicker'])?$_GET['datepicker']: date('Y-m-d') }}">
                                        <input type="hidden" name="type" value="custom" id="type">
                                    </div>

                                    <div class="col-md-3">
                                        <button class="btn btn-primary" type="submit" style="       margin-top: 25px;">
                                            Go
                                        </button>
                                    </div>
                                    <div class="col-md-6 sm_custm">
                                        <div class="excel-btn" style="float: right">
                                            <a href="{{ route('new-custom-route-ctc-export.excel') }}"
                                               class="btn buttons-excel buttons-html5 btn-sm btn-primary excelstyleclass">
                                                Export to Excel
                                            </a>
                                        </div>

                                        <div class="excel-btn" style="float: right">
                                            <a href="#"
                                               class="btn buttons-reload buttons-html5 btn-sm btn-danger excelstyleclass">
                                                Reload
                                            </a>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </form>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )


                                <table class="table table-striped table-bordered yajrabox" id="yajra-reload">
                    <thead stylesheet="color:black;">
                    <tr>
                        <th>JoeyCo Order#</th>
                        <th>Route Number</th>
                        <th>Joey</th>
                        <th>Customer Address</th>
                        <th>Out For Delivery</th>
                        <th>Sorted Time</th>
                        <th>Actual Arrival @ CX</th>
                        <th>Image</th>
                        <th>Last Mile Tracking#</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                      </thead>
                      <tbody>
                      </tbody>
  </table>

                        </div>
                    </div>
                </div>

            </div>


        </div>
    </div>
    <!-- /#page-wrapper -->

@endsection
