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
                <h3 class="page-title">Joey Plan List <small></small></h3>
                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="https://finance.joeyco.com/dashboard">Dashboard</a></li>


                    <li class="breadcrumb-item active">Joey Plan List</li>

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
                            <div class="btn-group">

                            </div>
                        </div>
                        <!-- Add New Button Code Moved Here -->
                    </div>
                </div>
                <!-- Action buttons Code End -->


                <!-- BEGIN EXAMPLE TABLE PORTLET-->
                <div class="portlet box blue">

                    <div class="portlet-title">
                        <div class="caption">
                            Joey Plan List
                        </div>
                    </div>

                    <div class="portlet-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="datatable">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="dataTables_length" id="datatable_length">
                                            <label>Show 
                                                <select name="datatable_length" aria-controls="datatable" class="form-control input-sm">
                                                    <option value="100">100</option><option value="200">200</option>
                                                    <option value="300">300</option><option value="400">400</option>
                                                    <option value="-1">All</option>
                                                </select> entries
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div id="datatable_filter" class="dataTables_filter">
                                            <label>Search:<input type="search" class="form-control input-sm" placeholder="" aria-controls="datatable"></label>
                                        </div>
                                    </div>
                                </div>
                                <thead>
                                <tr>
                                    <th class="text-center ">#</th>
                                    <th class="text-center ">ID</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Internal Name</th>
                                    <th class="text-center">Category Using Type</th>
                                    <th class="text-center">Plan Type</th>
                                    <th class="text-center">Created At</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="text-center ">1</td>
                                    <td class="text-center">146</td>
                                    <td class="text-center">AadilCO Broker - Toronto</td>
                                    <td class="text-center">AadilCO</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2022-03-21 12:57:14</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/146"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary"
                                           data-placement="bottom" data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/146/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/146"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">2</td>
                                    <td class="text-center">145</td>
                                    <td class="text-center">Joey (Ottawa) Hourly Plan</td>
                                    <td class="text-center">Joey (Ottawa) Hourly Plan</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Sub Hourly &amp; Downtown Hourly &amp; Sub Hourly Custom
                                        Routing &amp; Downtown Hourly Custom Routing &amp; Sub Hourly Big Box &amp;
                                        Downtown Hourly Big Box
                                    </td>
                                    <td class="text-center">2022-02-22 19:57:36</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/145"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary"
                                           data-placement="bottom" data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/145/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/145"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">3</td>
                                    <td class="text-center">113</td>
                                    <td class="text-center">Toronto E-Com (A 4.0)</td>
                                    <td class="text-center">(A 4.0 - B 4.0 - C4.0 - BG 4.0)</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Group Zone Pricing Per Drop &amp; Custom Route &amp; Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-12-21 12:58:37</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/113"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary"
                                           data-placement="bottom" data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/113/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/113"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">4</td>
                                    <td class="text-center">112</td>
                                    <td class="text-center">Toronto E- Com (A 2.10)</td>
                                    <td class="text-center">(A 2.10 - B 2.25 - C 3.0 - BG 3.5)</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Group Zone Pricing Per Drop &amp; Custom Route &amp; Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-12-20 16:48:14</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/112"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary"
                                           data-placement="bottom" data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/112/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/112"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">5</td>
                                    <td class="text-center">109</td>
                                    <td class="text-center">MaskooneeCO Broker - Montreal</td>
                                    <td class="text-center">MaskooneeCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Sub Contractor &amp; Sub Contractor Custom Routing &amp;
                                        Sub Contractor Big Box
                                    </td>
                                    <td class="text-center">2021-12-17 16:38:14</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/109"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary"
                                           data-placement="bottom" data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/109/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/109"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">6</td>
                                    <td class="text-center">96</td>
                                    <td class="text-center">Joey Hourly Plan (OTT)</td>
                                    <td class="text-center">Joey Hourly Plan (OTT)</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Sub Hourly &amp; Downtown Hourly &amp; Sub Hourly Custom
                                        Routing &amp; Downtown Hourly Custom Routing &amp; Sub Hourly Big Box &amp;
                                        Downtown Hourly Big Box
                                    </td>
                                    <td class="text-center">2021-11-15 15:18:31</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/96"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/96/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/96"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">7</td>
                                    <td class="text-center">95</td>
                                    <td class="text-center">Joey Hourly Plan (MTL)</td>
                                    <td class="text-center">Joey Hourly Plan (MTL)</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Sub Hourly &amp; Downtown Hourly &amp; Sub Hourly Custom
                                        Routing &amp; Downtown Hourly Custom Routing &amp; Sub Hourly Big Box &amp;
                                        Downtown Hourly Big Box
                                    </td>
                                    <td class="text-center">2021-11-15 15:09:39</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/95"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/95/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/95"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">8</td>
                                    <td class="text-center">94</td>
                                    <td class="text-center">KevinCO Broker - Montreal</td>
                                    <td class="text-center">KevinCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-11-10 23:38:33</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/94"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/94/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/94"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">9</td>
                                    <td class="text-center">93</td>
                                    <td class="text-center">FaiqCO Broker - Toronto</td>
                                    <td class="text-center">FaiqCO Broker - Toronto</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-11-10 23:29:07</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/93"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/93/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/93"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">10</td>
                                    <td class="text-center">92</td>
                                    <td class="text-center">MikeCO Broker - Ottawa</td>
                                    <td class="text-center">MikeCO Broker - Ottawa</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-11-10 23:23:52</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/92"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/92/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/92"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">11</td>
                                    <td class="text-center">65</td>
                                    <td class="text-center">SaifCO Broker - Montreal</td>
                                    <td class="text-center">SaifCO - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Sub Contractor &amp; Sub Contractor Custom Routing &amp;
                                        Sub Contractor Big Box
                                    </td>
                                    <td class="text-center">2021-09-02 15:56:49</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/65"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/65/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/65"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">12</td>
                                    <td class="text-center">62</td>
                                    <td class="text-center">Individual Joey - Ottawa</td>
                                    <td class="text-center">Individual Joey - Ottawa</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Sub Contractor &amp; Sub Contractor Custom Routing &amp;
                                        Sub Contractor Big Box
                                    </td>
                                    <td class="text-center">2021-06-09 14:52:56</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/62"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/62/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/62"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">13</td>
                                    <td class="text-center">61</td>
                                    <td class="text-center">Individual Joey - Montreal</td>
                                    <td class="text-center">Individual Joey - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Sub Contractor &amp; Sub Contractor Custom Routing &amp;
                                        Sub Contractor Big Box
                                    </td>
                                    <td class="text-center">2021-06-09 14:51:00</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/61"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/61/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/61"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">14</td>
                                    <td class="text-center">60</td>
                                    <td class="text-center">SahibCO Broker - Montreal</td>
                                    <td class="text-center">SahibCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-05-31 18:44:09</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/60"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/60/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/60"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">15</td>
                                    <td class="text-center">59</td>
                                    <td class="text-center">ShaibanCO Broker - Montreal</td>
                                    <td class="text-center">ShaibanCO Broker (Inactive) - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-05-31 18:42:32</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/59"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/59/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/59"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">16</td>
                                    <td class="text-center">58</td>
                                    <td class="text-center">ShaibanCO Broker - Ottawa</td>
                                    <td class="text-center">ShaibanCO Broker - Ottawa</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-05-31 18:38:54</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/58"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/58/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/58"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">17</td>
                                    <td class="text-center">57</td>
                                    <td class="text-center">SubhiCO Broker - Montreal</td>
                                    <td class="text-center">SubhiCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-05-31 18:37:08</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/57"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/57/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/57"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">18</td>
                                    <td class="text-center">56</td>
                                    <td class="text-center">SubhiCO Broker - Ottawa</td>
                                    <td class="text-center">SubhiCO Broker - Ottawa</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-05-31 18:34:02</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/56"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/56/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/56"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">19</td>
                                    <td class="text-center">55</td>
                                    <td class="text-center">KishanCO Broker - Montreal</td>
                                    <td class="text-center">KishanCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-05-31 18:12:49</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/55"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/55/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/55"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">20</td>
                                    <td class="text-center">51</td>
                                    <td class="text-center">MikeCO Broker - Montreal</td>
                                    <td class="text-center">MikeCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-04-05 19:36:53</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/51"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/51/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/51"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">21</td>
                                    <td class="text-center">50</td>
                                    <td class="text-center">Toronto Per Drop Per Zone (Broker)</td>
                                    <td class="text-center">Toronto Per Drop Per Zone (Broker)</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Sub Per Drop &amp; Downtown Per Drop &amp; Sub Per Drop
                                        Custom Routing &amp; Downtown Per Drop Custom Routing &amp; Sub Per Drop Big
                                        Box &amp; Downtown Per Drop Big Box
                                    </td>
                                    <td class="text-center">2021-03-31 21:00:35</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/50"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/50/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/50"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">22</td>
                                    <td class="text-center">49</td>
                                    <td class="text-center">Toronto Per Drop Per Zone (JOEY)</td>
                                    <td class="text-center">Toronto Per Drop Per Zone (JOEY)</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Sub Per Drop &amp; Downtown Per Drop &amp; Sub Per Drop
                                        Custom Routing &amp; Downtown Per Drop Custom Routing &amp; Sub Per Drop Big
                                        Box &amp; Downtown Per Drop Big Box
                                    </td>
                                    <td class="text-center">2021-03-31 20:51:31</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/49"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/49/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/49"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">23</td>
                                    <td class="text-center">47</td>
                                    <td class="text-center">Toronto Joey Duration</td>
                                    <td class="text-center">Toronto Joey Duration</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Sub Hourly &amp; Downtown Hourly &amp; Sub Hourly Custom
                                        Routing &amp; Downtown Hourly Custom Routing &amp; Sub Hourly Big Box &amp;
                                        Downtown Hourly Big Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:39:13</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/47"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/47/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/47"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">24</td>
                                    <td class="text-center">46</td>
                                    <td class="text-center">EmadCO Broker - Toronto</td>
                                    <td class="text-center">EmadCO Broker - Toronto</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:16:53</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/46"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/46/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/46"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">25</td>
                                    <td class="text-center">45</td>
                                    <td class="text-center">ChristineCO/Xero Broker - Toronto</td>
                                    <td class="text-center">ChristineCO/Xero Broker - Toronto (INACTIVE)</td>
                                    <td class="text-center">Using JoeyCo Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:16:12</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/45"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/45/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/45"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">26</td>
                                    <td class="text-center">44</td>
                                    <td class="text-center">KheraCO Broker - Toronto</td>
                                    <td class="text-center">KheraCO Broker - Toronto</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:15:07</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/44"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/44/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/44"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">27</td>
                                    <td class="text-center">43</td>
                                    <td class="text-center">GraceCO Broker - Ottawa</td>
                                    <td class="text-center">GraceCO Broker - Ottawa (INACTIVE)</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:07:28</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/43"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/43/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/43"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">28</td>
                                    <td class="text-center">42</td>
                                    <td class="text-center">VaishakCO Broker - Ottawa</td>
                                    <td class="text-center">VaishakCO Broker - Ottawa</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:06:55</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/42"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/42/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/42"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">29</td>
                                    <td class="text-center">41</td>
                                    <td class="text-center">KhalidCO Broker - Ottawa</td>
                                    <td class="text-center">KhalidCO Broker - Ottawa</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:06:26</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/41"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/41/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/41"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">30</td>
                                    <td class="text-center">40</td>
                                    <td class="text-center">MaryCO Broker - Ottawa</td>
                                    <td class="text-center">MaryCO Broker - Ottawa</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:05:51</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/40"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/40/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/40"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">31</td>
                                    <td class="text-center">39</td>
                                    <td class="text-center">VannaCO Broker - Ottawa</td>
                                    <td class="text-center">VannaCO Broker - Ottawa</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:05:29</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/39"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/39/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/39"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">32</td>
                                    <td class="text-center">38</td>
                                    <td class="text-center">MathewCO Broker - Ottawa</td>
                                    <td class="text-center">MathewCO Broker - Ottawa</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:05:04</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/38"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/38/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/38"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">33</td>
                                    <td class="text-center">37</td>
                                    <td class="text-center">IhabCO Broker - Ottawa</td>
                                    <td class="text-center">IhabCO Broker - Ottawa</td>
                                    <td class="text-center">Using JoeyCo Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:04:40</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/37"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/37/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/37"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">34</td>
                                    <td class="text-center">36</td>
                                    <td class="text-center">GabrielCO Broker - Ottawa</td>
                                    <td class="text-center">GabrielCO Broker - Ottawa</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:04:12</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/36"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/36/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/36"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">35</td>
                                    <td class="text-center">35</td>
                                    <td class="text-center">KhalilCO Broker - Montreal</td>
                                    <td class="text-center">KhalilCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:03:14</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/35"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/35/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/35"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">36</td>
                                    <td class="text-center">34</td>
                                    <td class="text-center">TaqiCO Broker - Montreal</td>
                                    <td class="text-center">TaqiCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:02:44</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/34"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/34/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/34"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">37</td>
                                    <td class="text-center">33</td>
                                    <td class="text-center">JalalCO Broker - Montreal</td>
                                    <td class="text-center">JalalCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:02:10</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/33"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/33/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/33"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">38</td>
                                    <td class="text-center">32</td>
                                    <td class="text-center">HaniCO Broker - Montreal</td>
                                    <td class="text-center">HaniCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:01:20</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/32"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/32/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/32"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">39</td>
                                    <td class="text-center">31</td>
                                    <td class="text-center">MohamedCO Broker - Montreal</td>
                                    <td class="text-center">MohamedCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 17:00:38</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/31"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/31/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/31"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">40</td>
                                    <td class="text-center">30</td>
                                    <td class="text-center">FernandoCO Broker - Montreal</td>
                                    <td class="text-center">FernandoCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 16:59:57</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/30"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/30/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/30"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">41</td>
                                    <td class="text-center">29</td>
                                    <td class="text-center">MarioCO Broker - Montreal</td>
                                    <td class="text-center">MarioCO Broker - Montreal (INACTIVE)</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 16:58:43</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/29"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/29/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/29"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">42</td>
                                    <td class="text-center">28</td>
                                    <td class="text-center">MatarCO Broker - Montreal</td>
                                    <td class="text-center">MatarCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-24 16:57:00</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/28"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/28/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/28"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center ">43</td>
                                    <td class="text-center">27</td>
                                    <td class="text-center">MaanCO Broker - Montreal</td>
                                    <td class="text-center">MaanCO Broker - Montreal</td>
                                    <td class="text-center">Using Personal Vehicle</td>
                                    <td class="text-center">Brooker &amp; Brooker Custom Routing &amp; Brooker Big
                                        Box
                                    </td>
                                    <td class="text-center">2021-03-23 13:50:43</td>
                                    <td class="text-center action-td">
                                        <a class="btn btn-xs btn-primary orange" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/27"
                                           title="Detail">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a class="btn btn-xs btn-primary" data-placement="bottom"
                                           data-toggle="tooltip" href="https://finance.joeyco.com/joey-plan/27/edit"
                                           title="Edit">
                                            <i class="fa fa-pencil-square"></i>
                                        </a>
                                        <a class="btn btn-xs btn-success"
                                           data-placement="bottom" data-toggle="tooltip"
                                           href="https://finance.joeyco.com/plan-assign-to-joeys-view/27"
                                           title="Assign Plan to joeys">
                                            <i class="fa fa-link"></i>
                                        </a>


                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
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