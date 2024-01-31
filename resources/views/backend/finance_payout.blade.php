<?php
$user = Auth::user();
if ($user->email != "admin@gmail.com") {

    $data = explode(',', $user['rights']);
    $permissions = explode(',', $user['permissions']);
} else {
    $data = [];
    $permissions = [];
}
?>

@extends( 'backend.layouts.app' )

@section('title', 'Dashboard')

@section('CSSLibraries')
    <style>
        .dashboard-statistics-box {
            min-height: 400px;
            margin: 15px 0px;
            position: relative;
            box-sizing: border-box;
        }

        .dashboard-statistics-box.dashboard-statistics-tbl-show td {
            padding-top: 52px;
            padding-bottom: 52px;
        }
    </style>
@endsection
@section('JSLibraries')
    <script src="{{ backend_asset('libraries/Chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ backend_asset('nprogress/nprogress.js') }}"></script>
    <script src="{{ backend_asset('libraries/gauge.js/dist/gauge.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/skycons/skycons.js') }}"></script>
    <script src="{{ backend_asset('libraries/Chart.js/dist/Chart.min.js') }}"></script>

@endsection


@section('content')
    <!--right_col open-->
    <div class="right_col" role="main">
 @if (Session::has('error'))
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                {!! Session::pull('error') !!}
            </div>
        @endif

    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">

        <!--loader-mian-wrap-open-->
        <div class="row loader-mian-wrap show">

            <!--loader-inner-wrap-open-->
            <div class="col-sm-12 loader-inner-wrap">
                <div class="lds-roller">
                    <div class="dot-1"></div>
                    <div class="dot-2"></div>
                    <div class="dot-3"></div>
                    <div class="dot-4"></div>
                    <div class="dot-5"></div>
                    <div class="dot-6"></div>
                    <div class="dot-7"></div>
                    <div class="dot-8"></div>
                </div>
            </div>
            <!--loader-inner-wrap-close-->

        </div>
        <!--loader-mian-wrap-close-->

        <!--progress-bar-loader-open-->
        <div class="progress-main-wrap">
            <div class="progress">
                <p class="progress-label">Downloading in progress . . . .</p>
                <p class="error-report">Connection lost, trying to reconnect . . .</p>
                <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="0"
                     class="progress-bar progress-bar-striped active" role="progressbar" style="width:0%">Creating file in progress ...
                </div>
            </div>
        </div>
        <!--progress-bar-loader-close-->
        <div class="page-content">
            <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <div aria-hidden="true" aria-labelledby="myModalLabel" class="modal fade" id="confirmDelete" role="dialog"
                 tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-hidden="true" class="close" data-dismiss="modal" type="button"></button>
                            <h4 class="modal-title">Delete Record</h4>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this record?
                        </div>
                        <div class="modal-footer">
                            <button class="btn default" data-dismiss="modal" type="button">No</button>
                            <button class="btn blue" data-dismiss="modal" id="deleteButton" type="button">Yes</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
            <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
            <!-- BEGIN PAGE HEADER-->
            <div class="session-wrapper ">


            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">Joey Payout Report <small></small></h3>
                    <ol class="breadcrumb">

                        <li class="breadcrumb-item"><a href="https://finance.joeyco.com/dashboard">Dashboard</a></li>


                        <li class="breadcrumb-item active">Joey Payout Report</li>

                    </ol>


                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE HEADER-->

            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">

                    <!-- Action buttons Code Start -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Add New Button Code Moved Here -->
                            <div class="table-toolbar pull-right">
                            </div>
                            <!-- Add New Button Code Moved Here -->
                        </div>
                    </div>
                    <!-- Action buttons Code End -->


                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box blue">

                        <div class="portlet-title">
                            <div class="caption">
                                Joey Payout Report ffffff
                            </div>
                        </div>

                        <div class="portlet-body">
                            <!--table-top-form-from-open-->
                            <form action="https://finance.joeyco.com/joey/reports/payout/data" class="form-horizontal table-top-form-from"
                                  method="post">
                                <input name="_token" type="hidden" value="zfA0GffnE1PydHa5UL7yYFzJk1ikx3JHZzjcf14h">
                                <!--table-top-form-row-open-->
                                <div class="row table-top-form-row">

                                    <!--table-top-form-col-warp-open-->
                                    <div class="col-sm-3 col-md-2 table-top-form-col-warp">
                                        <label class="control-label">Joey Name</label>
                                        <input class="form-control" name="joye_name" type="text" value="">
                                    </div>
                                    <!--table-top-form-col-warp-close-->

                                    <!--table-top-form-col-warp-open-->
                                    <div class="col-sm-3 col-md-2 table-top-form-col-warp">
                                        <label class="control-label">Joey ID</label>
                                        <input class="form-control" min="1" name="joye_id" type="number" value="">
                                    </div>
                                    <!--table-top-form-col-warp-close-->

                                    <!--table-top-form-col-warp-open-->
                                    <div class="col-sm-3 col-md-2 table-top-form-col-warp">
                                        <label class="control-label">Vendor ID</label>
                                        <input class="form-control" min="1" name="vendor_id" type="number" value="">
                                    </div>
                                    <!--table-top-form-col-warp-close-->

                                    <!--table-top-form-col-warp-open-->
                                    <div class="col-sm-3 col-md-2 table-top-form-col-warp">
                                        <label class="control-label">Cities</label>
                                        <select class="form-control city-select" name="city">
                                            <option value="">Select an option</option>
                                            <option value="4">Amazon Montreal</option>
                                            <option value="22">Ottawa</option>
                                            <option value="23">Toronto</option>
                                        </select>
                                    </div>
                                    <!--table-top-form-col-warp-close-->

                                    <!--table-top-form-col-warp-open-->
                                    <div class="col-sm-3 col-md-2 table-top-form-col-warp">
                                        <label class="control-label">Plans</label>
                                        <select class="form-control plan-select" name="plan">
                                            <option value="">Select an option</option>
                                            <option value="146">AadilCo</option>
                                            <option value="145">Joey (Ottawa) Hourly Plan</option>
                                            <option value="131">TOR JAN 2022</option>
                                            <option value="127">Default Plan</option>
                                            <option value="113">(A 4.0 - B 4.0 - C4.0 - BG 4.0)</option>
                                            <option value="112">(A 2.10 - B 2.25 - C 3.0 - BG 3.5)</option>
                                            <option value="109">MaskooneeCO Broker - Montreal</option>
                                            <option value="96">Joey Hourly Plan (OTT)</option>
                                            <option value="95">Joey Hourly Plan (MTL)</option>
                                            <option value="94">KevinCO Broker - Montreal</option>
                                            <option value="93">FaiqCO Broker - Toronto</option>
                                            <option value="92">MikeCO Broker - Ottawa</option>
                                            <option value="65">SaifCO - Montreal</option>
                                            <option value="62">Individual Joey - Ottawa</option>
                                            <option value="61">Individual Joey - Montreal</option>
                                            <option value="60">SahibCO Broker - Montreal</option>
                                            <option value="59">ShaibanCO Broker (Inactive) - Montreal</option>
                                            <option value="58">ShaibanCO Broker - Ottawa</option>
                                            <option value="57">SubhiCO Broker - Montreal</option>
                                            <option value="56">SubhiCO Broker - Ottawa</option>
                                            <option value="55">KishanCO Broker - Montreal</option>
                                            <option value="51">MikeCO Broker - Montreal</option>
                                            <option value="50">Toronto Per Drop Per Zone (Broker)</option>
                                            <option value="49">Toronto Per Drop Per Zone (JOEY)</option>
                                            <option value="47">Toronto Joey Duration</option>
                                            <option value="46">EmadCO Broker - Toronto</option>
                                            <option value="45">ChristineCO/Xero Broker - Toronto (INACTIVE)</option>
                                            <option value="44">KheraCO Broker - Toronto</option>
                                            <option value="43">GraceCO Broker - Ottawa (INACTIVE)</option>
                                            <option value="42">VaishakCO Broker - Ottawa</option>
                                            <option value="41">KhalidCO Broker - Ottawa</option>
                                            <option value="40">MaryCO Broker - Ottawa</option>
                                            <option value="39">VannaCO Broker - Ottawa</option>
                                            <option value="38">MathewCO Broker - Ottawa</option>
                                            <option value="37">IhabCO Broker - Ottawa</option>
                                            <option value="36">GabrielCO Broker - Ottawa</option>
                                            <option value="35">KhalilCO Broker - Montreal</option>
                                            <option value="34">TaqiCO Broker - Montreal</option>
                                            <option value="33">JalalCO Broker - Montreal</option>
                                            <option value="32">HaniCO Broker - Montreal</option>
                                            <option value="31">MohamedCO Broker - Montreal</option>
                                            <option value="30">FernandoCO Broker - Montreal</option>
                                            <option value="29">MarioCO Broker - Montreal (INACTIVE)</option>
                                            <option value="28">MatarCO Broker - Montreal</option>
                                            <option value="27">MaanCO Broker - Montreal</option>
                                            <option value="15">Milk-run</option>
                                            <option value="14">No Guarantee 2019</option>
                                            <option value="13">SR 12.00 Guarantee</option>
                                            <option value="12">$17.50 Guarantee</option>
                                            <option value="11">$16.00 Guarantee</option>
                                            <option value="10">High Commission</option>
                                            <option value="8">$6.00 Guarantee</option>
                                            <option value="7">Grocery Gateway</option>
                                            <option value="6">$12.00 Guarantee</option>
                                            <option value="5">Area Manager</option>
                                            <option value="4">Free Joeys</option>
                                            <option value="3">Super Joey</option>
                                            <option value="2">zone manager</option>
                                            <option value="1">Purgatory</option>
                                        </select>
                                    </div>
                                    <!--table-top-form-col-warp-close-->

                                    <!--table-top-form-col-warp-open-->
                                    <div class="col-sm-3 col-md-2 table-top-form-col-warp">
                                        <label class="control-label">Start Date</label>
                                        <input class="form-control" max='2022-03-24' min='2021-03-07' name="start_date" type="date"
                                               value="">
                                    </div>
                                    <!--table-top-form-col-warp-close-->

                                    <!--table-top-form-col-warp-open-->
                                    <div class="col-sm-3 col-md-2 table-top-form-col-warp">
                                        <label class="control-label">End date</label>
                                        <input class="form-control" max='2022-03-24' min='2021-03-07' name="end_date" type="date"
                                               value="">
                                    </div>
                                    <!--table-top-form-col-warp-close-->

                                    <!--table-top-form-col-warp-open-->
                                    <div class="col-sm-3 col-md-2 table-top-form-col-warp">
                                        <button class="btn orange from-submit-btn" type="submit"> Generate</button>
                                    </div>
                                    <!--table-top-form-col-warp-close-->

                                </div>
                                <!--table-top-form-row-close-->
                            </form>
                            <!--apply-system-parameters-sec-wrap-open-->
                            <div class="row apply-system-parameters-sec-wrap ">

                                <div class="col-md-12 apply-system-parameters-heading">
                                    <p>Show / Hide Table Columns </p>
                                </div>

                                <!--check-box-wapper-row-open-->
                                <div class="row check-box-wapper-row">

                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-show-hide-all-handle"
                                                   type="checkbox">
                                            <lable class="control-label">Show / Hide All Columns</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->

                                </div>
                                <!--check-box-wapper-row-close-->


                                <!--check-box-wapper-row-open-->
                                <div class="row check-box-wapper-row">
                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="0" type="checkbox">
                                            <lable class="control-label">#</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="1" type="checkbox">
                                            <lable class="control-label">Route</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="2" type="checkbox">
                                            <lable class="control-label">Joey Id</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="3" type="checkbox">
                                            <lable class="control-label">Joey Name</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="4" type="checkbox">
                                            <lable class="control-label">Broker</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="5" type="checkbox">
                                            <lable class="control-label">Zone</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->

                                </div>
                                <!--check-box-wapper-row-close-->


                                <!--check-box-wapper-row-open-->
                                <div class="row check-box-wapper-row">
                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="6" type="checkbox">
                                            <lable class="control-label">City</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="7" type="checkbox">
                                            <lable class="control-label">Vendor Id</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="8" type="checkbox">
                                            <lable class="control-label">Vendor Name</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="9" type="checkbox">
                                            <lable class="control-label"># Of Drops</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="10" type="checkbox">
                                            <lable class="control-label"># Of Picked</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="11" type="checkbox">
                                            <lable class="control-label"># Of Drops Completed</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->

                                </div>
                                <!--check-box-wapper-row-close-->


                                <!--check-box-wapper-row-open-->
                                <div class="row check-box-wapper-row">
                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="12" type="checkbox">
                                            <lable class="control-label"># Of Returns</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="13" type="checkbox">
                                            <lable class="control-label"># Of Unattempted</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="14" type="checkbox">
                                            <lable class="control-label">Plan Type</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="15" type="checkbox">
                                            <lable class="control-label">Plan Name</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="16" type="checkbox">
                                            <lable class="control-label">First Pickup Scan</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="17" type="checkbox">
                                            <lable class="control-label">First Drop Scan</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->

                                </div>
                                <!--check-box-wapper-row-close-->


                                <!--check-box-wapper-row-open-->
                                <div class="row check-box-wapper-row">
                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="18" type="checkbox">
                                            <lable class="control-label">Last Drop Off Scan</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="19" type="checkbox">
                                            <lable class="control-label">Total KM</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="20" type="checkbox">
                                            <lable class="control-label">Actual KM</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="21" type="checkbox">
                                            <lable class="control-label">Total Duration</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="22" type="checkbox">
                                            <lable class="control-label">Actual Duration</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="23" type="checkbox">
                                            <lable class="control-label">Routific Duration</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->

                                </div>
                                <!--check-box-wapper-row-close-->


                                <!--check-box-wapper-row-open-->
                                <div class="row check-box-wapper-row">
                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="24" type="checkbox">
                                            <lable class="control-label">Plan Estimated Total Time</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="25" type="checkbox">
                                            <lable class="control-label">Gas</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="26" type="checkbox">
                                            <lable class="control-label">Truck Cost</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="27" type="checkbox">
                                            <lable class="control-label">Tech</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="28" type="checkbox">
                                            <lable class="control-label">Hours</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="29" type="checkbox">
                                            <lable class="control-label">Flag Bonus</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->

                                </div>
                                <!--check-box-wapper-row-close-->


                                <!--check-box-wapper-row-open-->
                                <div class="row check-box-wapper-row">
                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="30" type="checkbox">
                                            <lable class="control-label">Flag Deduction</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="31" type="checkbox">
                                            <lable class="control-label">Manual Adjustment</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="32" type="checkbox">
                                            <lable class="control-label">Plan Applied Amount</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="33" type="checkbox">
                                            <lable class="control-label">Payout Without Tax</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="34" type="checkbox">
                                            <lable class="control-label">Tax Amount</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="35" type="checkbox">
                                            <lable class="control-label">Tax %</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->

                                </div>
                                <!--check-box-wapper-row-close-->


                                <!--check-box-wapper-row-open-->
                                <div class="row check-box-wapper-row">
                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="36" type="checkbox">
                                            <lable class="control-label">Tax On</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="37" type="checkbox">
                                            <lable class="control-label">Final Payout</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input checked class="datatable-column-handle"
                                                   data-targeted-column="38" type="checkbox">
                                            <lable class="control-label">Route Type</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input class="datatable-column-handle" data-targeted-column="39"
                                                   type="checkbox">
                                            <lable class="control-label">Payout Actual Duration</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input class="datatable-column-handle" data-targeted-column="40"
                                                   type="checkbox">
                                            <lable class="control-label">Payout Total Duration</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->


                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input class="datatable-column-handle" data-targeted-column="41"
                                                   type="checkbox">
                                            <lable class="control-label">Cost Per Drop On Hourly</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->

                                </div>
                                <!--check-box-wapper-row-close-->


                                <!--check-box-wapper-row-open-->
                                <div class="row check-box-wapper-row">
                                    <!--apply-system-parameters-box-open-->
                                    <div class="col-md-2 col-sm-4 col-xs-6 apply-show-hide-col-joeypayout">
                                        <div class="form-group">
                                            <input class="datatable-column-handle" data-targeted-column="42"
                                                   type="checkbox">
                                            <lable class="control-label">Show Calculation</lable>
                                        </div>
                                    </div>
                                    <!--apply-system-parameters-box-open-->

                                </div>
                                <!--check-box-wapper-row-close-->


                            </div>
                            <!--apply-system-parameters-sec-wrap-close-->

                            <!--table-top-form-from-close-->
                            <div class="table-responsive">
                                <div id="joey-report-payout_wrapper" class="dataTables_wrapper no-footer"><div class="dt-buttons"><button class="dt-button buttons-csv buttons-html5" tabindex="0" aria-controls="joey-report-payout" type="button"><span>CSV Export</span></button> </div><div id="joey-report-payout_filter" class="dataTables_filter"><label>Search:<input type="search" class="" placeholder="" aria-controls="joey-report-payout"></label></div><div class="dataTables_length" id="joey-report-payout_length"><label>Show <select name="joey-report-payout_length" aria-controls="joey-report-payout" class=""><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="-1">All</option></select> entries</label></div><div class="dataTables_scroll"><div class="dataTables_scrollHead" style="overflow: hidden; position: relative; border: 0px; width: 100%;"><div class="dataTables_scrollHeadInner" style="box-sizing: content-box; width: 4954px; padding-right: 0px;"><table class="table table-striped table-bordered table-hover dataTable no-footer" role="grid" style="margin-left: 0px; width: 4954px;"><thead>

                                    <tr role="row"><th class="text-center sorting_asc" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" style="width: 100px;" aria-sort="ascending" aria-label="
                                            #
                                        : activate to sort column descending">
                                            #
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Route
                                        : activate to sort column ascending" style="width: 100px;">
                                            Route
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Joey Id
                                        : activate to sort column ascending" style="width: 100px;">
                                            Joey Id
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Joey Name
                                        : activate to sort column ascending" style="width: 100px;">
                                            Joey Name
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Broker
                                        : activate to sort column ascending" style="width: 100px;">
                                            Broker
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Zone
                                        : activate to sort column ascending" style="width: 100px;">
                                            Zone
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            City
                                        : activate to sort column ascending" style="width: 100px;">
                                            City
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Vendor Id
                                        : activate to sort column ascending" style="width: 100px;">
                                            Vendor Id
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Vendor Name
                                        : activate to sort column ascending" style="width: 100px;">
                                            Vendor Name
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            # Of Drops
                                        : activate to sort column ascending" style="width: 100px;">
                                            # Of Drops
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            # Of Picked
                                        : activate to sort column ascending" style="width: 100px;">
                                            # Of Picked
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            # Of Drops Completed
                                        : activate to sort column ascending" style="width: 100px;">
                                            # Of Drops Completed
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            # Of Returns
                                        : activate to sort column ascending" style="width: 100px;">
                                            # Of Returns
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            # Of Unattempted
                                        : activate to sort column ascending" style="width: 100px;">
                                            # Of Unattempted
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Plan Type
                                        : activate to sort column ascending" style="width: 100px;">
                                            Plan Type
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Plan Name
                                        : activate to sort column ascending" style="width: 100px;">
                                            Plan Name
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            First Pickup Scan
                                        : activate to sort column ascending" style="width: 100px;">
                                            First Pickup Scan
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            First Drop Scan
                                        : activate to sort column ascending" style="width: 100px;">
                                            First Drop Scan
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Last Drop Off Scan
                                        : activate to sort column ascending" style="width: 100px;">
                                            Last Drop Off Scan
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Total KM
                                            
                                        : activate to sort column ascending" style="width: 100px;">
                                            Total KM
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="KM by routific"></i></div>
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Actual KM
                                            
                                        : activate to sort column ascending" style="width: 100px;">
                                            Actual KM
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="Covered by joey"></i></div>
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Total Duration
                                            
                                        : activate to sort column ascending" style="width: 100px;">
                                            Total Duration
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="First pickup to last drop"></i></div>
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Actual Duration
                                            
                                        : activate to sort column ascending" style="width: 100px;">
                                            Actual Duration
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="First drop to last drop"></i></div>
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Routific Duration
                                            
                                        : activate to sort column ascending" style="width: 100px;">
                                            Routific Duration
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="Duration by routific"></i></div>
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Plan Estimated Total Time
                                            
                                        : activate to sort column ascending" style="width: 100px;">
                                            Plan Estimated Total Time
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="Show estimated time of total hours by plan"></i></div>
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Gas
                                        : activate to sort column ascending" style="width: 100px;">
                                            Gas
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Truck Cost
                                        : activate to sort column ascending" style="width: 100px;">
                                            Truck Cost
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Tech
                                        : activate to sort column ascending" style="width: 100px;">
                                            Tech
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Hours
                                        : activate to sort column ascending" style="width: 100px;">
                                            Hours
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Flag Bonus
                                        : activate to sort column ascending" style="width: 100px;">
                                            Flag Bonus
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Flag Deduction
                                        : activate to sort column ascending" style="width: 100px;">
                                            Flag Deduction
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Manual Adjustment
                                        : activate to sort column ascending" style="width: 100px;">
                                            Manual Adjustment
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Plan Applied Amount
                                            
                                        : activate to sort column ascending" style="width: 100px;">
                                            Plan Applied Amount
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="Only show amount applied per drop plan"></i></div>
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Payout Without Tax
                                        : activate to sort column ascending" style="width: 100px;">
                                            Payout Without Tax
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Tax Amount
                                        : activate to sort column ascending" style="width: 100px;">
                                            Tax Amount
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Tax %
                                        : activate to sort column ascending" style="width: 100px;">
                                            Tax %
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Tax On
                                        : activate to sort column ascending" style="width: 100px;">
                                            Tax On
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Final Payout
                                        : activate to sort column ascending" style="width: 100px;">
                                            Final Payout
                                        </th><th class="text-center sorting" tabindex="0" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Route Type
                                        : activate to sort column ascending" style="width: 100px;">
                                            Route Type
                                        </th></tr>
                                    </thead></table></div></div><div class="dataTables_scrollBody" style="position: relative; overflow: auto; width: 100%;"><table class="table table-striped table-bordered table-hover dataTable no-footer" id="joey-report-payout" role="grid" aria-describedby="joey-report-payout_info"><thead>

                                    <tr role="row" style="height: 0px;"><th class="text-center sorting_asc" aria-controls="joey-report-payout" rowspan="1" colspan="1" style="width: 100px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-sort="ascending" aria-label="
                                            #
                                        : activate to sort column descending"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            #
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Route
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Route
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Joey Id
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Joey Id
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Joey Name
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Joey Name
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Broker
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Broker
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Zone
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Zone
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            City
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            City
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Vendor Id
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Vendor Id
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Vendor Name
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Vendor Name
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            # Of Drops
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            # Of Drops
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            # Of Picked
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            # Of Picked
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            # Of Drops Completed
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            # Of Drops Completed
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            # Of Returns
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            # Of Returns
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            # Of Unattempted
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            # Of Unattempted
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Plan Type
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Plan Type
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Plan Name
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Plan Name
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            First Pickup Scan
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            First Pickup Scan
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            First Drop Scan
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            First Drop Scan
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Last Drop Off Scan
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Last Drop Off Scan
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Total KM
                                            
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Total KM
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="KM by routific"></i></div>
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Actual KM
                                            
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Actual KM
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="Covered by joey"></i></div>
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Total Duration
                                            
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Total Duration
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="First pickup to last drop"></i></div>
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Actual Duration
                                            
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Actual Duration
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="First drop to last drop"></i></div>
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Routific Duration
                                            
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Routific Duration
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="Duration by routific"></i></div>
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Plan Estimated Total Time
                                            
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Plan Estimated Total Time
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="Show estimated time of total hours by plan"></i></div>
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Gas
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Gas
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Truck Cost
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Truck Cost
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Tech
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Tech
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Hours
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Hours
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Flag Bonus
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Flag Bonus
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Flag Deduction
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Flag Deduction
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Manual Adjustment
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Manual Adjustment
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Plan Applied Amount
                                            
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Plan Applied Amount
                                            <div><i class="fa fa-info-circle" data-toggle="tooltip" title="" data-original-title="Only show amount applied per drop plan"></i></div>
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Payout Without Tax
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Payout Without Tax
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Tax Amount
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Tax Amount
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Tax %
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Tax %
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Tax On
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Tax On
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Final Payout
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Final Payout
                                        </div></th><th class="text-center sorting" aria-controls="joey-report-payout" rowspan="1" colspan="1" aria-label="
                                            Route Type
                                        : activate to sort column ascending" style="padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px; width: 100px;"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">
                                            Route Type
                                        </div></th></tr>
                                    </thead>
                                    
                                    <tbody>


                                    <tr class="odd"><td valign="top" colspan="39" class="dataTables_empty">No data available in table</td></tr></tbody>
                                </table></div></div><div class="dataTables_info" id="joey-report-payout_info" role="status" aria-live="polite">Showing 0 to 0 of 0 entries</div><div class="dataTables_paginate paging_simple_numbers" id="joey-report-payout_paginate"><a class="paginate_button previous disabled" aria-controls="joey-report-payout" data-dt-idx="0" tabindex="-1" id="joey-report-payout_previous">Previous</a><span></span><a class="paginate_button next disabled" aria-controls="joey-report-payout" data-dt-idx="1" tabindex="-1" id="joey-report-payout_next">Next</a></div></div>
                            </div>

                            <!--totals table open-->
                            <!--table-top-form-from-close-->
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover"
                                       id="joey-report-payout-total">
                                    <thead>
                                    <tr>
                                        <th class="text-center ">Total Routes</th>
                                        <th class="text-center ">Sum Of Total Drops</th>
                                        <th class="text-center ">Sum Of Total Picked</th>
                                        <th class="text-center ">Sum Of Total Completed</th>
                                        <th class="text-center ">Sum Of Total Returns</th>
                                        <th class="text-center ">Sum Of Total Unattempted</th>
                                        <th class="text-center ">Sum Of Total KM</th>
                                        <th class="text-center ">Sum Of Actual KM</th>


                                        <th class="text-center ">Sum Of Gas</th>
                                        <th class="text-center ">Sum Of Vehicle</th>
                                        <th class="text-center ">Sum Of Tech</th>
                                        <th class="text-center ">Sum Of Hours</th>
                                        <th class="text-center ">Sum Of Flag Bonus</th>
                                        <th class="text-center ">Sum Of Flag Deduction</th>
                                        <th class="text-center ">Sum Of Manual Adjustment</th>
                                        <th class="text-center ">Sum Of Payout Without Tax</th>
                                        <th class="text-center ">Sum Of Tax Amount</th>
                                        <th class="text-center ">Sum Of Final Payout</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr>
                                        <td class="text-center total-routes">0</td>
                                        <td class="text-center total-drops">0</td>
                                        <td class="text-center total-picked">0</td>
                                        <td class="text-center total-drops-completed">0</td>
                                        <td class="text-center total-returns">0</td>
                                        <td class="text-center total-unattempted">0</td>
                                        <td class="text-center total-km">0</td>
                                        <td class="text-center total-actual-km">0</td>


                                        <td class="text-center total-gas">0</td>
                                        <td class="text-center total-vehicle">0</td>
                                        <td class="text-center total-tech">0</td>
                                        <td class="text-center total-hours">0</td>
                                        <td class="text-center total-flag-bonus">0</td>
                                        <td class="text-center total-flag-deduction">0</td>
                                        <td class="text-center total-manaul-adjustment">0</td>
                                        <td class="text-center total-payout-without-tax">0</td>
                                        <td class="text-center total-tax-amount">0</td>
                                        <td class="text-center total-final-payout">0</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--totals table close-->

                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->

                        <!-- Dashboard All Graph -->

        {{--@if($graph == null)
            @if (count($data)== 0)
                <div class="row">
                    @include('backend.dashboard-layout.montreal')
                    @include('backend.dashboard-layout.ottawa')
                    @include('backend.dashboard-layout.ctc')
                </div>
            @endif

                <div class="row">
                    @if(in_array("montreal_dashboard", $data))
                        @include('backend.dashboard-layout.montreal')
                    @endif
                    @if(in_array("ottawa_dashboard", $data))
                        @include('backend.dashboard-layout.ottawa')
                    @endif
                    @if(in_array("ctc_dashboard", $data))
                        @include('backend.dashboard-layout.ctc')
                    @endif

                    @if(in_array("montreal_dashboard", $data) && in_array("ottawa_dashboard", $data) && in_array("ctc_dashboard", $data))

                    @else
                        @if(in_array("montreal_dashboard", $data))
                            @if(in_array("ottawa_dashboard", $data))

                            @elseif(in_array("ctc_dashboard", $data))

                            @else

                            @endif


                        @elseif(in_array("ottawa_dashboard", $data))
                            @if(in_array("montreal_dashboard", $data))

                            @elseif(in_array("ctc_dashboard", $data))

                            @else

                            @endif

                        @elseif(in_array("ctc_dashboard", $data))
                            @if(in_array("montreal_dashboard", $data))
                            @elseif(in_array("ottawa_dashboard", $data))
                            @else
                            @endif
                        @endif

                    @endif

                </div>
            @endif

        @if($graph == 'montreal')
            @if (count($data)== 0)
                <div class="row">
                    @include('backend.dashboard-layout.montreal')
                </div>
            @endif

                <div class="row">
                    @if(in_array("montreal_dashboard", $data))
                        @include('backend.dashboard-layout.montreal')
                    @endif
                </div>

        @endif

        @if($graph == 'ottawa')
            @if (count($data)== 0)
                <div class="row">
                    @include('backend.dashboard-layout.ottawa')
                </div>
            @endif

                <div class="row">
                    @if(in_array("ottawa_dashboard", $data))
                        @include('backend.dashboard-layout.ottawa')
                    @endif

                </div>

        @endif

        @if($graph == 'ctc')
            @if (count($data)== 0)
                <div class="row">
                    @include('backend.dashboard-layout.ctc')

                </div>
            @endif

                <div class="row">
                    @if(in_array("ctc_dashboard", $data))
                        @include('backend.dashboard-layout.ctc')

                    @endif

                </div>

        @endif
 --}}
    </div>
    <!-- /footer content -->
    <!-- /#page-wrapper -->
@endsection