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

@section('title', 'Client Dashboard')

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

        $(function () {
            {{--//appConfig.set('yajrabox.ajax', '{{ route('new-ctc-dashboard.data') }}');--}}
            appConfig.set('yajrabox.ajax', '{{ route('last-mile-dashboard.data') }}');
            appConfig.set('dt.order', [0, 'desc']);
            //appConfig.set('yajrabox.scrollx_responsive',true);
            appConfig.set('yajrabox.scrollx_responsive', true);
            appConfig.set('yajrabox.autoWidth', false);
            appConfig.set('dt.searching', false);
            appConfig.set('yajrabox.ajax.data', function (data) {
                data.datepicker = jQuery('[name=datepicker]').val();
                data.tracking_id = jQuery('[name=tracking_id]').val();
                data.route_id = jQuery('[name=route_id]').val();
                data.city = jQuery('select[name=city]').val();
                data.status = jQuery('select[name=status]').val();
            });

            appConfig.set('yajrabox.columns', [
                {data: 'sprint_id', orderable: false, searchable: false},
                {data: 'route_id', orderable: false, searchable: false},
                {data: 'joey_name', orderable: false, searchable: false},
                {data: 'tracking_id', orderable: false, searchable: false, className: 'text-center'},
                {data: 'eta_time', orderable: false, searchable: false, className: 'text-center'},
                {data: 'task_status_id', orderable: false, searchable: false, className: 'text-center'},
                {data: 'store_name', orderable: false, searchable: false},
                {data: 'customer_name', orderable: false, searchable: false},
                {data: 'weight', orderable: false, searchable: false, className: 'text-center'},
                {data: 'address_line_2', orderable: false, searchable: false, className: 'text-center'},
                {data: 'action', orderable: false, searchable: false, className: 'text-center'}
            ]);
        })

        //pageRefresh();
        /* $(document).ready(function () {
             setInterval(function () {
                 $("#ctcCards").load(window.location.href + " #ctc-dashbord-tiles-id");
                 var ref = $('#yajra-reload').DataTable();
                 ref.ajax.reload();
             }, 50000);
         });*/

        $('.buttons-excel').on('click', function (event) {
            event.preventDefault();
            let href = $(this).attr('href');
            let selected_date = $('.data-selector').val();
            window.location.href = href + '/' + selected_date;
        });
    </script>
@endsection

@section('content')

    <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left amazon-text">
                    {{--<h3 class="text-center"> New Client Dashboard<small></small></h3>--}}
                    <h3 class=""> Client Dashboard
                        <small></small>
                    </h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <!--Count Div Row Open-->
        {{-- @include('backend.ctc.ctc_new_cards')--}}
        <!--Count Div Row Close-->
            {{--@include('backend.layouts.modal')
            @include( 'backend.layouts.popups')--}}
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">


                            {{-- <div class="excel-btn" style="float: right">
                                 <a  href="{{ route('export_ctc_new_dashboard.excel') }}"
                                    class="btn buttons-excel buttons-html5 btn-sm btn-primary excelstyleclass" target="_blank">
                                     Export to Excel
                                 </a>
                             </div>--}}
                            {{--@if(can_access_route('export_ctc_new_dashboard_test.excel',$userPermissoins))--}}
                            {{--<div class="excel-btn" style="float: right">--}}
                            {{--<a  href="{{ route('new-ctc-dashboard-export.excel') }}"--}}
                            {{--class="btn buttons-excel buttons-html5 btn-sm btn-primary excelstyleclass" target="_blank">--}}
                            {{--Export to Excel--}}
                            {{--</a>--}}
                            {{--</div>--}}

                            <div class="excel-btn" style="float: right">
                                <a href="{{ route('export_ctc_new_dashboard.excel') }}"
                                   class="btn buttons-excel buttons-html5 btn-sm btn-primary excelstyleclass"
                                   target="_blank">
                                    Export to Excel
                                </a>
                            </div>


                            {{--@if(can_access_route('new-ctc-dashboard-export-otd-report.excel',$userPermissoins))--}}
                            {{--<div class="excel-btn" style="float: right">--}}
                            {{--<a  href="{{ route('new-ctc-dashboard-export-otd-report.excel') }}"--}}
                            {{--class="btn buttons-excel buttons-html5 btn-sm btn-primary excelstyleclass" target="_blank">--}}
                            {{--OTD Report--}}
                            {{--</a>--}}
                            {{--</div>--}}
                            {{--@endif--}}
                            {{--@if(can_access_route('new-ctc-missing-id-export.excel',$userPermissoins))--}}
                            {{--<div class="excel-btn" style="float: right">--}}
                            {{--<a  href="{{ route('new-ctc-missing-id-export.excel') }}"--}}
                            {{--class="btn buttons-excel buttons-html5 btn-sm btn-primary excelstyleclass" target="_blank">--}}
                            {{--Missing Report--}}
                            {{--</a>--}}
                            {{--</div>--}}
                            {{--@endif--}}


                            @if(can_access_route('export_ctc_new_dashboard_otd_report.excel',$userPermissoins))
                                <div class="excel-btn" style="float: right">
                                    <a href="{{ route('export_ctc_new_dashboard_otd_report.excel') }}"
                                       class="btn buttons-excel buttons-html5 btn-sm btn-primary excelstyleclass"
                                       target="_blank">
                                        OTD Report
                                    </a>
                                </div>
                            @endif
                            @if(can_access_route('export_ctc_missing_id.excel',$userPermissoins))
                                <div class="excel-btn" style="float: right">
                                    <a href="{{ route('export_ctc_missing_id.excel') }}"
                                       class="btn buttons-excel buttons-html5 btn-sm btn-primary excelstyleclass"
                                       target="_blank">
                                        Missing Report
                                    </a>
                                </div>
                            @endif

                            <div class="clearfix"></div>
                        </div>

                        <div class="x_title">
                            <form method="get" action="">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Search By Date:</label>
                                            <input type="date" name="datepicker" class="data-selector form-control"
                                                   required=""
                                                   value="{{ isset($_GET['datepicker'])?$_GET['datepicker']: date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Search By Tracking ID:</label>
                                            <input type="text" name="tracking_id" class="form-control"
                                                   value="{{ isset($_GET['tracking_id'])?$_GET['tracking_id']: "" }}"
                                                   placeholder="Tracking Id">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Search By Route ID:</label>
                                            <input type="text" name="route_id" class="form-control"
                                                   value="{{ isset($_GET['route_id'])?$_GET['route_id']: "" }}"
                                                   placeholder="Route Id">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>City:</label>
                                            <select class="form-control" name="city" id="city">
                                                <option value="all" {{( $city == 'all') ? 'Selected' : ''}}>All</option>
                                                <option value="ottawa" {{( $city == 'ottawa') ? 'Selected' : ''}}>
                                                    Ottawa
                                                </option>
                                                <option value="toronto" {{( $city == 'toronto') ? 'Selected' : ''}}>
                                                    Toronto
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Select Status:</label>
                                            {!! Form::select('status', [null=>'Please Select'] + $status_code, null, ['class' => 'js-example-basic-multiple form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <button class="btn btn-primary" type="submit" style="       margin-top: 24px;
    padding-bottom: 9px;">
                                                Go</a> </button>
                                        </div>
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
                                    <th class="text-center ">JoeyCo Order#</th>
                                    <th class="text-center ">Route#</th>
                                    <th class="text-center ">Joey</th>
                                    <th class="text-center ">Shipment Tracking#</th>
                                    <th class="text-center ">Estimated Customer Delivery Time</th>
                                    <th class="text-center ">Shipment Delivery Status</th>
                                    <th class="text-center ">Store Name</th>
                                    <th class="text-center ">Customer Name</th>
                                    <th class="text-center ">Weight</th>
                                    <th class="text-center ">Customer Address</th>
                                    <th class="text-center ">Action</th>


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

    <!-- <script src="{{ backend_asset('js/jquery-1.12.4.js') }}"></script>
<script src="{{ backend_asset('js/jquery-ui.js') }}"></script> -->

    <!-- <script src="{{ backend_asset('js/gm-date-selector.css') }}"></script>
<script src="{{ backend_asset('css/bootstrap.css') }}"></script>
<script src="{{ backend_asset('css/bootstrap.js') }}"></script>
 -->

    <!-- <script src="{{ backend_asset('js/bootstrap.js') }}"></script> -->



@endsection
