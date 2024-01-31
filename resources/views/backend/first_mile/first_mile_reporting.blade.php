<?php

$user = Auth::user();
if ($user->email != "admin@gmail.com") {

    $data = explode(',', $user['rights']);
    $dataPermission = explode(',', $user['permissions']);
} else {
    $data = [];
    $dataPermission = [];
}

?>
@extends( 'backend.layouts.app' )

@section('title', 'First Mile Summary')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <!-- Image Viewer CSS -->
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <!-- Custom Light Box Css -->
    <link href="{{ backend_asset('css/custom_lightbox.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css" integrity="sha256-b5ZKCi55IX+24Jqn638cP/q3Nb2nlx+MH/vMMqrId6k=" crossorigin="anonymous" />
<style>
    .head-text {
        margin: 610px 0px 0px 0px;
    }
</style>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">
        <!-- Datatable -->
        $(document).ready(function () {

            $('#datatable').DataTable({
                "lengthMenu": [250, 500, 750, 1000]
            });

            $(".group1").colorbox({height: "50%", width: "50%"});

            $(document).on('click', '.status_change', function (e) {
                var Uid = $(this).data('id');

                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to change user status??',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    buttons: {
                        'confirm': {
                            text: 'Proceed',
                            btnClass: 'btn-info',
                            action: function () {

                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo URL::to('/'); ?>/api/changeUserStatus/" + Uid,
                                    data: {},
                                    success: function (data) {
                                        if (data == '0' || data == 0) {
                                            var DataToset = '<button type="button" class="btn btn-warning btn-xs status_change" data-toggle="modal" data-id="' + Uid + '" data-target=".bs-example-modal-sm">Blocked</button>';
                                            $('#CurerntStatusDiv' + Uid).html(DataToset);
                                        } else {
                                            var DataToset = '<button type="button" class="btn btn-success btn-xs status_change" data-toggle="modal" data-id="' + Uid + '" data-target=".bs-example-modal-sm">Active</button>'
                                            $('#CurerntStatusDiv' + Uid).html(DataToset);
                                        }
                                    }
                                });

                            }
                        },
                        cancel: function () {
                            //$.alert('you clicked on <strong>cancel</strong>');
                        }
                    }
                });
            });

            $(document).on('click', '.form-delete', function (e) {

                var $form = $(this);
                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to delete user ??',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    buttons: {
                        'confirm': {
                            text: 'Proceed',
                            btnClass: 'btn-info',
                            action: function () {
                                $form.submit();
                            }
                        },
                        cancel: function () {
                            //$.alert('you clicked on <strong>cancel</strong>');
                        }
                    }
                });
            });
        });

    </script>

    <script type="text/javascript">

        $('.cards-details-datatable-show').on('click', function (e) {

            // show loader
            showLoader();
            $("#excel").show();
            $(".total-orders").show();
            $(".head-text").show();
            let data_for = $(this).attr('data-type');
            let date_date = $(this).attr('date-date');
            let start_date = $(this).attr('date-start-date');
            let end_date = $(this).attr('date-end-date');
            let sprint_id_data = $(this).attr('sprint_id-data');

            $("#select_data_type").val($(this).attr('data-type'));
            var title_page = '';
            switch (data_for) {
                case "total_orders":
                    title_page = "Total Orders";
                    break;
                case "picked-up":
                    title_page = " Picked Up From Store";
                    break;
                case "at-hub":
                    title_page = " At Hub";
                    break;
                case "at-store":
                    title_page = "At Store Orders";
                    break;
                case "sorted-order":
                    title_page = "Sorted Orders";
                    break;

                case "out-for-delivery":
                    title_page = "Out For Delivery ";
                    break;
                case "delivered-order":
                    title_page = "Delivered Orders";
                    break;
                case "returned":
                    title_page = "Returned Orders";
                    break;
                case "returned-to-merchant":
                    title_page = "Returned To Merchant ";
            }
            title_page = title_page+' ('+date_date+')';
            $(".title-page").text(title_page);


            //let targeted_table = 'ctc-total-orders';
            var table = $('.ctc-total-orders').DataTable({

                lengthMenu: [[250, 500, 750, 1000], [250, 500, 750, 1000]],
				searching: false,
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('first_mile_reporting_data.data') }}",
                    data: function (req_data) {
               console.log(date_date,data_for,sprint_id_data)
                        req_data.from_date = start_date;
                        req_data.to_date = end_date;
                        req_data.data_for = data_for;
                        req_data.sprint_id_data = sprint_id_data;
                        console.log(req_data);
                    },
                    dataSrc: function ( json ) {
                        // hide loader
                        hideLoader();
                        return json.data;
                    }
                },
                columns: [
                    {data: 'sprint_id', orderable: false, searchable: false},
                    {data: 'store_name', orderable: false, searchable: false},
                    {data: 'tracking_id', orderable: false, searchable: false},
                    {data: 'status_id', orderable: false, searchable: false},
                    {data: 'created_at', orderable: false, searchable: false},
                    {data: 'joey_name', orderable: false, searchable: false},
                    {data: 'picked_up_at', orderable: false, searchable: false},
                    {data: 'delivered_at', orderable: false, searchable: false}
                ],
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#birthday').daterangepicker({
                singleDatePicker: true,
                calender_style: "picker_4"
            }, function (start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
            });
        });
    </script>

    <script>
        $('.buttons-excel').on('click', function (event) {
            event.preventDefault();
            let href = $(this).attr('href');
            let data_for = $('#select_data_type').val();
            // alert(vendor);
            let from_date = $('#fromdatepicker').val();
            let to_date = $('#todatepicker').val();
            //alert(href);
            window.location.href = href + '/' + data_for + '/' + from_date + '/' + to_date;
        });

        $("#fromdatepicker").datetimepicker
        ({
            format :'YYYY-MM-DD',

        });

        $("#todatepicker").datetimepicker({
            format :'YYYY-MM-DD',

        });

        $("#fromdatepicker").on("dp.change", function (e) {
            $('#todatepicker').data("DateTimePicker").minDate(e.date);
        });
        $("#todatepicker").on("dp.change", function (e) {
            $('#fromdatepicker').data("DateTimePicker").maxDate(e.date);
        });

    </script>
@endsection

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left amazon-text">
                    <h3 class="text-center">  First Mile Summary
                        <small></small>
                    </h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">

                            <div class="clearfix"></div>

                        </div>
                        @include( 'backend.layouts.notification_message' )
                        <div class="x_title">
                            {{-- <form style="margin-left: 115px" method="get" action="">\ --}}
                            <form style="" method="get" action="">
                                <div class="row">
                                    <div class="12">
                                        <div class="filter_form">
                                            <div class="col-lg-3">
                                                <select name="days" id="days" class="form-control" style="">
                                                    <option value="1days" {{ ('1days' ==  $selectDateFilter)?'selected': '' }} name="1days">Day</option>
                                                    <option value="lastweek" {{ ('lastweek' ==  $selectDateFilter)?'selected': '' }} name="lastweek">Last week</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </form>
                        <div class="clearfix"></div>
                    </div>
                    <!--Count Div Row Open-->
                @include('backend.first_mile.action_file.first_mile_reporting_cards')
                <!--Count Div Row Close-->


                    <div class="x_content">
                        <div class="clearfix"></div>



                    <!--total-orders-table-open-->

                        <div class="table-responsive " >
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 20%">Store Name</th>
                                <th style="width: 10%">Total Orders</th>
                                <th style="width: 10%"> Picked Up At Store</th>
                                <th style="width: 10%"> At Hub</th>
                                <th style="width: 10%">At Store Orders</th>
                                <th style="width: 10%">Who Scanned / Who Recieved</th>

                            </tr>
                            </thead>
                            <tbody>

                            @foreach($ctc_range_count as $key=> $value)

                                <tr>
                                    <td >{{$key}}</td>
                                    <td class="cards-details-datatable-show" date-start-date="{{$start}}" date-end-date="{{$end}}" date-date="{{$key}}" sprint_id-data="{{$value['sprint_id']}}" data-type="total_orders" data-target="total-orders" data-ajax-status="false" id="total-order"><button style="font-weight: bold;" class="btn btn-link">{{ $value['total'] }}</button></td>
                                    <td class="cards-details-datatable-show" date-start-date="{{$start}}" date-end-date="{{$end}}" date-date="{{$key}}" sprint_id-data="{{$value['sprint_id']}}" data-type="picked-up" data-target="picked-up" data-ajax-status="false" id="picked-up"><button style="font-weight: bold;" class="btn btn-link">{{$value['picked-up'] }}</button></td>
                                    <td class="cards-details-datatable-show" date-start-date="{{$start}}" date-end-date="{{$end}}" date-date="{{$key}}" sprint_id-data="{{$value['sprint_id']}}" data-type="at-hub" data-target="at-hub" data-ajax-status="false" id="at-hub"><button style="font-weight: bold;" class="btn btn-link">{{$value['at-hub'] }}</button></td>
                                    <td class="cards-details-datatable-show" date-start-date="{{$start}}" date-end-date="{{$end}}" date-date="{{$key}}" sprint_id-data="{{$value['sprint_id']}}" data-type="at-store" data-target="at-store" data-ajax-status="false" id="at-store"><button style="font-weight: bold;" class="btn btn-link">{{$value['at-store'] }}</button></td>
                                    <td class="cards-details-datatable-show"></td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <br>

                        <div class="clearfix"></div>
                        <div style="background: #3E3E3E;display: none; color: white; height: 44px; margin: 0; float: left; width: 100%;" class="head-text">
                            <h3 style="margin-left: 12px;">
                                <label class="title-page">

                                </label>
                            </h3>
                        </div>
                        <br>
                        <div class="table-responsive  total-orders" style="display: none">
                            <table class="table table table-striped table-bordered ctc-total-orders">
                                <thead>
                                <tr>
                                    <th>Sprint ID</th>
                                    <th>Store Name</th>
                                    <th>Tracking ID</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Joey</th>
                                    <th>Picked At Store Time</th>
                                    <th>Delivered At Hub Time</th>
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
    </div>
    <!-- /#page-wrapper -->
@endsection