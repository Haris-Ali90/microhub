<?php
$statusId = array(
    "136" => "Client requested to cancel the order",
    "137" => "Delay in delivery due to weather or natural disaster",
    "118" => "left at back door",
    "117" => "left with concierge",
    "135" => "Customer refused delivery",
    "108" => "Customer unavailable-Incorrect address",
    "106" => "Customer unavailable - delivery returned",
    "107" => "Customer unavailable - Left voice mail - order returned",
    "109" => "Customer unavailable - Incorrect phone number",
    "142" => "Damaged at hub (before going OFD)",
    "143" => "Damaged on road - undeliverable",
    "144" => "Delivery to mailroom",
    "103" => "Delay at pickup",
    "139" => "Delivery left on front porch",
    "138" => "Delivery left in the garage",
    "114" => "Successful delivery at door",
    "113" => "Successfully hand delivered",
    "120" => "Delivery at Hub",
    "110" => "Delivery to hub for re-delivery",
    "111" => "Delivery to hub for return to merchant",
    "121" => "Pickup from Hub",
    "102" => "Joey Incident",
    "104" => "Damaged on road - delivery will be attempted",
    "105" => "Item damaged - returned to merchant",
    "129" => "Joey at hub",
    "128" => "Package on the way to hub",
    "140" => "Delivery missorted, may cause delay",
    "116" => "Successful delivery to neighbour",
    "132" => "Office closed - safe dropped",
    "101" => "Joey on the way to pickup",
    "32" => "Order accepted by Joey",
    "14" => "Merchant accepted",
    "36" => "Cancelled by JoeyCo",
    "124" => "At hub - processing",
    "38" => "Draft",
    "18" => "Delivery failed",
    "56" => "Partially delivered",
    "17" => "Delivery success",
    "68" => "Joey is at dropoff location",
    "67" => "Joey is at pickup location",
    "13" => "At hub - processing",
    "16" => "Joey failed to pickup order",
    "57" => "Not all orders were picked up",
    "15" => "Order is with Joey",
    "112" => "To be re-attempted",
    "131" => "Office closed - returned to hub",
    "125" => "Pickup at store - confirmed",
    "61" => "Scheduled order",
    "37" => "Customer cancelled the order",
    "34" => "Customer is editting the order",
    "35" => "Merchant cancelled the order",
    "42" => "Merchant completed the order",
    "54" => "Merchant declined the order",
    "33" => "Merchant is editting the order",
    "29" => "Merchant is unavailable",
    "24" => "Looking for a Joey",
    "23" => "Waiting for merchant(s) to accept",
    "28" => "Order is with Joey",
    "133" => "Packages sorted",
    "55" => "ONLINE PAYMENT EXPIRED",
    "12" => "ONLINE PAYMENT FAILED",
    "53" => "Waiting for customer to pay",
    "141" => "Lost package",
    "60" => "Task failure",
    "145" => "Returned To Merchant",
    "146" => "Delivery Missorted, Incorrect Address",
    '153' => 'Miss sorted to be reattempt',
    '154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow'
);
$statuses = array(
    "136" => "Client requested to cancel the order",
    "135" => "Customer refused delivery",
    "108" => "Customer unavailable-Incorrect address",
    "106" => "Customer unavailable - delivery returned",
    "107" => "Customer unavailable - Left voice mail - order returned",
    "109" => "Customer unavailable - Incorrect phone number",
    "142" => "Damaged at hub (before going OFD)",
    "143" => "Damaged on road - undeliverable",
    "110" => "Delivery to hub for re-delivery",
    "111" => "Delivery to hub for return to merchant",
    "102" => "Joey Incident",
    "104" => "Damaged on road - delivery will be attempted",
    "105" => "Item damaged - returned to merchant",
    "101" => "Joey on the way to pickup",
    "112" => "To be re-attempted",
    "131" => "Office closed - returned to hub",
    "145" => "Returned To Merchant",
    "146" => "Delivery Missorted, Incorrect Address"v,
'153' => 'Miss sorted to be reattempt',
'154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow'
);
?>

@extends( 'backend.layouts.app' )

@section('title', 'Micro Hub Bundle Scanning')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <!-- Image Viewer CSS -->
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <!--joey-custom-css-->
    <link href="{{ backend_asset('css/joeyco-custom.css') }}" rel="stylesheet">
    <style>


        .search-order {
            background-color: #c6dd38;
        }
        .search-order {
            margin-top: 0;
        }

        .tracking_id {
            margin-top: 26px;
        }

        .input-error {
            color: red;
            padding: 10px 0px;
        }

        .form-submit-btn {
            margin-top: 26px;
            width: 100%;
            background-color: #c6dd38;
        }

        .filter-out-button .filter-out {
            margin-top: 26px;
            text-align: center;
            background-color: #c6dd38;
        }

        .show-notes {
            background-color: #C6DD38;
            border-style: none;
            padding: 6px 9px 6px 9px;
        }

        .grid_overlay small {
            font-size: 16px;
            font-weight: 600;
            color: #bcd82b;
        }

        .grid_overlay-cancel-order {
            float: left;
            width: 100%;
            background-color: #ffffff;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            border: 2px solid #f6f2ef;
            box-shadow: 0 0 13px 1px #f6f2ef;
            overflow: hidden;
            position: relative;
        }

        .grid_overlay-cancel-order:after {
            content: "";
            position: absolute;
            left: -20px;
            bottom: -20px;
            transform: rotate(19deg);
            background-color: #bcd82b;
            height: 100px;
            width: 100px;
            z-index: 0;
        }

        #cancelled-head {
            width: 109% !important;
        }

        .div-space {
            height: 42px;
        }
        .alert {
            margin-top: 13px !important;
        }
        @media (max-width: 768px) {
            .search-order {
                margin-top: 0px;
            }
            .addGap {
                float: left;
                width: 100%;
                margin-bottom: 10px;
            }
            .removeGap {
                padding: 0;
            }
            .removeGap .search-order {
                margin-top: 5px;
            }
        }
    </style>


@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0&libraries=places"
            type="text/javascript"></script>
    <!-- Custom JavaScript -->
    <script src="{{ backend_asset('js/joeyco-custom-script.js')}}"></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">
        <!-- Datatable -->
        $(document).ready(function () {

            //Set Columns In Table After Scan
            let tracking_datatable = $('.return-order-datatable').DataTable({
                "columns": [
                    {"data": "bundle_id"},
                    {"data": "hub_id"},
                    {"data": "own_hub"},
                    {"data": "created_at"},
                    {"data": "action"}
                ],
                scrollX: true,   // enables horizontal scrolling,
                scrollCollapse: true,
                "lengthMenu": [250, 500, 750, 1000],
                /*columnDefs: [
                    { width: '20%', targets: 0 }
                ],*/
                fixedColumns: true,
            });

            // submit tracking form action
            $('.search-tracking-id').submit(function (e) {

                e.preventDefault();
                //let counter = 1;
                let action = $(this).attr('action');
                let method = $(this).attr('method');
                let bundle_id = $(this).find('.bundle_id').val();
                // removing old value for re-scan
                $(this).find('.bundle_id').val('');
                showLoader();
                // calling ajax
                $.ajax({
                    type: method,
                    url: action,
                    data: {"bundle_id": bundle_id},
                    success: function (res) {

                        hideLoader();

                        // status is false
                        if (res.status == false) {
                            ShowSessionAlert('danger', res.message);
                            return false;
                        }
                        window.location.reload();

                        /*
                                                //$(".x_content").find('.alert').remove();
                                                // getting responce body
                                                var responce_data = res.body;
                                                $(".scanning-bundle").css("display", "block");
                                                $(".bundle_id").html(responce_data.bundle_id);
                                                $(".hubName").html(responce_data.hub_name);
                                                $(".trackingID").html(responce_data.tracking_id);
                                                $('.scanning-print-label').attr('href',document.location.origin+'/micro-hub/public/scanning-bundle/label/'+responce_data.bundle_id,document.location);
                                                console.log(responce_data);
                                                //add row for tracking order
                                                tracking_datatable.rows.add([responce_data]).draw(false);*/


                    },
                    error: function (error) {
                        ShowSessionAlert('danger', 'Something critical went wrong');
                        console.log(error);
                        hideLoader();
                    }
                });

            });
        });


    </script>
@endsection

@section('content')
    @include( 'backend.layouts.loader' )
    <div id="map"></div>
    <!--Open Main Div-->
    <div class="right_col" role="main">

        <!--Open Heading Div-->
        <div class="page-title">
            <div class="title_left">
                <h3>Micro Hub Bundle Scanning
                    <small></small>
                </h3>
            </div>
        </div>
        <!--Close Heading Div-->
        <!--Open Row Div-->
        <div class="row">
            <!--Open Col Div-->
            <div class="col-md-12 col-sm-12 col-xs-12">
                <!--Open X_Panel Div-->
                <div class="x_panel">
                    <!--Open x_title Div-->
                    <div class="x_title">
                        <div class="clearfix"></div>
                        <!--Open Form For Tracking ID Search-->
                        <form class="search-tracking-id" action="{{ route('bundle-scanning.search') }}" method="get">
                            <!--Open Form Row Div-->
                            <div class="row col-md-12">
                                <!--Open Input Tracking ID Div-->
                                <div class="col-sm-4 col-md-4 removeGap">
                                    <input class="form-control bundle_id" type="text" placeholder="Bundle Id"
                                           required="required"/>
                                </div>
                                <!--Close Input Tracking ID Div-->
                                <!--Open Search Button Div-->
                                <div class="col-sm-4 col-md-2 removeGap">
                                    <button class="form-control search-order">Search</button>
                                </div>
                                <!--Close Search Button Div-->
                            </div>
                            <!--Close Form Row Div-->
                        </form>

                        <div class="clearfix"></div>
                    </div>
                    <form method="get" name="date" action="{{route('bundle-scanning.list')}}">
                        <div class="row">
                            <div class="col-sm-4 col-md-4 addGap">
                                <input type="date" name="to_date_picker"
                                       value="{{ isset($_GET['to_date_picker'])?$_GET['to_date_picker']: date('Y-m-d') }}"
                                       class="form-control" id="datepicker">
                            </div>
                            <div class="col-sm-4 col-md-4 addGap">
                                <input type="date" name="from_date_picker"
                                       value="{{ isset($_GET['from_date_picker'])?$_GET['from_date_picker']: date('Y-m-d') }}"
                                       class="form-control" id="datepicker">
                            </div>
                            <div class="col-sm-4 col-md-2">
                                <button class="form-control search-order">Filter</button>
                            </div>
                        </div>
                    </form>
                @include( 'backend.layouts.notification_message' )
                <!--table-top-form-from-open-->
                    <!--Open X_Content Div Of Table-->
                    <div class="x_content">
                    @include( 'backend.layouts.errors' )
                    <!--Open Table Responce Div-->
                        <!-- <div class="table-responsive"> -->
                        <div class="grid_rows_divider">
                            <?php
                            $i = 1;
                            $date = [ $to_date, $from_date];
                            ?>
                            @foreach ($MicroHubBundleScanned as $ScannedBundle)

                                <div class="grid_col">

                                    <div class="grid_overlay">
                                        <span class="id_counts">{{$i++}}</span>
                                        <small>
                                            @if($ScannedBundle->hub->id == auth()->user()->hub_id)
                                                My Hub Bundles
                                            @else
                                                Other Hub Bundles
                                            @endif
                                        </small>
                                        {{-- <h4><strong>{{$ScannedBundle->bundle_id}}</strong></h4>--}}
                                        <div class="heading_divider">
                                            <h5>{{isset($ScannedBundle->hub->title) ? $ScannedBundle->hub->title : ''}}</h5>
                                            {{--@if($ScannedBundle->hub->id == auth()->user()->hub_id)

                                            @else
                                                <a href="{{backend_url('scanning-bundle/label/'.$ScannedBundle->bundle_id)}}" target="_blank" title="Detail" class="btn btn-primary btn-xs smBtn">
                                                    Print Label
                                                </a>
                                            @endif--}}

                                        </div>
                                        <div class="futherDetail_hubID">
                                            <ul>
                                               {{-- @if (isset($ScannedBundle->hubID($date)))--}}
                                                    @foreach($ScannedBundle->hubID($date) as $bundleId)

                                                        <li>
                                                            {{isset($bundleId->bundle_id) ? $bundleId->bundle_id : ''}}
                                                        </li>
                                                    @endforeach

                                                {{--@endif--}}

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                {{--@else

                                   <script>

                                       var hubId;
                                       var bundle_id;
                                       hubId =  {!! json_encode($ScannedBundle->hub_id) !!};
                                       bundle_id =  {!! json_encode($ScannedBundle->bundle_id) !!};
                                       var app = document.querySelector('#hub');
                                       app.append(bundle_id)

//                                       console.log(div.dataset.id)
                                   </script>--}}


                            @endforeach


                        </div>

                    </div>
                    <!--Close X_Content Div Of Table-->

                </div>
                <!--Open X_Panel Div-->
            </div>
            <!--Close Col Div-->
        </div>
        <!--Close Row Div-->
    </div>
    <!--Close Main Div-->
@endsection