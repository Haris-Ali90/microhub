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
    "147" => "Scanned at hub",
    "148" => "Scanned at Hub and labelled",
    "149" => "Bundle Pick From Hub",
    "150" => "Bundle Drop To Hub",
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
    "146" => "Delivery Missorted, Incorrect Address",
    "137" => "Delay in delivery due to weather or natural disaster",
    "140" => "Delivery missorted, may cause delay",
    "147" => "Scanned at hub",
    "148" => "Scanned at Hub and labelled",
    "149" => "Bundle Pick From Hub",
    "150" => "Bundle Drop To Hub",
    '153' => 'Miss sorted to be reattempt',
    '154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow'
);
?>

@extends( 'backend.layouts.app' )

@section('title', 'Reattempt Order History')

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


        .input-error {
            color: red;
            padding: 10px 0px;
        }
        .form-submit-btn {
            margin-top: 26px;
            background-color: #c6dd38;
        }
        .show-notes{
            background-color: #C6DD38;
            border-style:none;
            padding: 6px 9px 6px 9px;
        }
    </style>


@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0&libraries=places" type="text/javascript"></script>
    <!-- Custom JavaScript -->
    <script src="{{ backend_asset('js/joeyco-custom-script.js')}}"></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">
        <!-- Datatable -->
        $(document).ready(function () {

            $('.return-order-datatable').dataTable({
                scrollX: true,   // enables horizontal scrolling,
                scrollCollapse: true,
                /*columnDefs: [
                    { width: '20%', targets: 0 }
                ],*/
                fixedColumns: true,
            });
            // make select box selected for status
            var old_plan_val = "{{($old_request_data && isset($old_request_data['status'])) ? trim($old_request_data['status']): '' }}";

            make_option_selected_trigger('.status-select',old_plan_val);
        });


    </script>
@endsection

@section('content')
    @include( 'backend.layouts.loader' )
    <!--Open Main Div-->
    <div class="right_col" role="main">

        <!--Open Heading Div-->
        <div class="page-title">
            <div class="title_left">
                <h3>Reattempt Order History
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

                        <!--table-top-form-from-open-->
                        <form class="form-horizontal table-top-form-from">
                            <!--table-top-form-row-open-->
                            <div class="row table-top-form-row d-flex align-item-end">
                                <!--table-top-form-col-warp-open-->
                                <div class="col-sm-3 col-md-3 table-top-form-col-warp">
                                    <label class="control-label">Start Date</label>
                                    <input name="start_date" max='{{date("Y-m-d")}}' value="@if($old_request_data){{trim($old_request_data['start_date'])}}@endif"  type="date" class="form-control">
                                </div>
                                <!--table-top-form-col-warp-close-->

                                <!--table-top-form-col-warp-open-->
                                <div class="col-sm-3 col-md-3 table-top-form-col-warp">
                                    <label class="control-label">End date</label>
                                    <input name="end_date" max='{{date("Y-m-d")}}'  value="@if($old_request_data){{trim($old_request_data['end_date'])}}@endif"  type="date" class="form-control">
                                </div>
                                <!--table-top-form-col-warp-close-->

                                <!--table-top-form-col-warp-open-->
                                <div class="col-sm-3 col-md-4 table-top-form-col-warp">
                                    <label class="control-label">Status</label>
                                    <select name="status"   class="form-control status-select">
                                        <option value="">Select an option</option>
                                        @foreach($statuses as $key => $status)
                                            <option value="{{$key}}">{{$status}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!--table-top-form-col-warp-close-->

                                <!--table-top-form-col-warp-open-->
                                <div class="col-sm-3 col-md-2 table-top-form-col-warp">
                                    <button class="btn orange form-submit-btn"  type="submit"> Filter </button>
                                </div>
                                <!--table-top-form-col-warp-close-->

                            </div>
                            <!--table-top-form-row-close-->
                        </form>
                        <!--table-top-form-from-open-->
                        @include( 'backend.layouts.notification_message' )
                        <h2>Reattempt Order History</h2>
                        <div class="clearfix"></div>
                    </div>
                    <!--Close x_title Div-->

                    <!--Open X_Content Div Of Table-->
                    <div class="x_content">
                        <!--Open Table Responce Div-->
                        <!-- <div class="table-responsive"> -->

                        <!--Open Table Tracking Order List-->
                        <table class="table table-striped table-bordered return-order-datatable" data-form="deleteForm">
                            <thead>
                            <tr>
                                <th>Order Id</th>
                                <th>Tracking Id</th>
                                <th>Route Number</th>
                                <th style="width: 29%">Customer Address</th>
                                <th>Customer Phone</th>
                                <th>Status</th>
                                <th>Scan At</th>
                                <th>Process At</th>
                                <th>Scan By</th>
                                <th>Verified By</th>
                                <th>Verified At</th>
                                <th>Count Of Reattempts Left</th>
                                <th>Verify Notes</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($return_reattempt_history as $history)
                                <tr>
                                    <td>{{$history->sprint_id}}</td>
                                    <td>{{$history->tracking_id}}</td>
                                    <td>{{$history->route_id}}</td>
                                    <td>{{$history->customer_address.', '.$history->postal_code}}</td>
                                    <td>{{$history->customer_phone}}</td>
                                    <td> @if (isset($statusId[$history->status_id])) {{ $statusId[$history->status_id]}} @endif</td>
                                    <td>{{ConvertTimeZone($history->created_at,'UTC','America/Toronto')}}</td>
                                    <td>{{ConvertTimeZone($history->proceed_at,'UTC','America/Toronto')}}</td>
                                    <td>{{ (isset($history->ScanByUser->full_name)) ? $history->ScanByUser->full_name : 'User record not found'}}</td>
                                    <td> @if (isset($history->VerifiedByUser)) {{$history->VerifiedByUser->full_name}}@endif</td>
                                    <td>{{ConvertTimeZone($history->verified_at,'UTC','America/Toronto')}}</td>
                                    <td>{{$history->reattempt_left}}</td>
                                    <td class="notes-td">
                                        {{$history->varify_note}}
                                        <br>
                                        <a href="{{backend_url('notes/'.$history->id)}}" target="_blank" title="Detail" class="btn btn-success btn-sm show-notes"><i class="fa fa-tags notes-icon"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <!--Close Table Tracking Order List-->
                        <!-- </div> -->
                        <!--Close Table Responce Div-->
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