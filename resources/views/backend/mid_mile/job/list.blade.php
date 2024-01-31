<?php

use App\Joey;
use App\Vehicle;
use App\SlotsPostalCode;


if (!isset($_REQUEST['date']) || empty($_REQUEST['date'])) {
    $date = "20" . date('y-m-d');
} else {
    $date = $_REQUEST['date'];
}

?>
@extends( 'backend.layouts.app' )

@section('title', 'First Mile Jobs Panel')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <!-- Image Viewer CSS -->
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <style>
        .green-gradient, .green-gradient:hover {
            color: #fff;
            background: #bad709;
            background: -moz-linear-gradient(top, #bad709 0%, #afca09 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #bad709), color-stop(100%, #afca09));
            background: -webkit-linear-gradient(top, #bad709 0%, #afca09 100%);
            background: linear-gradient(to bottom, #bad709 0%, #afca09 100%);
        }

        .black-gradient,
        .black-gradient:hover {
            color: #fff;
            background: #535353;
            background: -moz-linear-gradient(top, #535353 0%, #353535 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #535353), color-stop(100%, #353535));
            background: -webkit-linear-gradient(top, #535353 0%, #353535 100%);
            background: linear-gradient(to bottom, #535353 0%, #353535 100%);
        }

        .red-gradient,
        .red-gradient:hover {
            color: #fff;
            background: #da4927;
            background: -moz-linear-gradient(top, #da4927 0%, #c94323 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #da4927), color-stop(100%, #c94323));
            background: -webkit-linear-gradient(top, #da4927 0%, #c94323 100%);
            background: linear-gradient(to bottom, #da4927 0%, #c94323 100%);
        }

        .orange-gradient,
        .orange-gradient:hover {
            color: #fff;
            background: #f6762c;
            background: -moz-linear-gradient(top, #f6762c 0%, #d66626 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #f6762c), color-stop(100%, #d66626));
            background: -webkit-linear-gradient(top, #f6762c 0%, #d66626 100%);
            background: linear-gradient(to bottom, #f6762c 0%, #d66626 100%);
        }

        .btn {
            font-size: 12px;
        }

        .modal-dialog {
            width: 94%;
        }

        .modal-header .close {
            opacity: 1;
            margin: 5px 0;
            padding: 0;
        }

        .modal-footer .close {
            opacity: 1;
            margin: 5px 0;
            padding: 0;
        }

        .modal-header h4 {
            color: #000;
        }

        .modal-footer {
            padding: 0 10px;
            text-align: right;
            border-top: 1px solid #e5e5e5;
        }

        .modal-header {
            padding: 5px 15px;
            border-bottom: 1px solid #e5e5e5;
            background: #c6dd38;
        }

        /*button.button.orange-gradient {
            border: none;
            line-height: 12px;
            display: inline-block;
            margin: 0;
            border-radius: 4px;
            padding: 8px 20px;
            color: #fff;
            background: #e46d24;
        }*/
        .form-group {
            width: 100%;
            margin: 10px 0;
            padding: 0 15px;
        }

        .form-group input, .form-group select {
            width: 80% !important;
            height: 30px;
        }

        .form-group label {
            width: 25%;
            float: left;
            clear: both;
        }

        input#date {
            height: 30px;
            width: 194px;
        }

        .form-group label {
            width: 50px;
        }

        div#route .form-group {
            width: 25%;
            float: left;
        }

        div#route {
            position: absolute;
            z-index: 9999;
            top: 83px;
            width: 97%;
        }

        .
        div {
            display: block;
        }

        /* start */
        .iycaQH {
            position: absolute;
            background-color: white;
            border-radius: 0.286em;
            box-shadow: rgba(86, 102, 108, 0.24) 0px 1px 5px 0px;
            overflow: hidden;
            margin: 1.429em 0px 0px;
            z-index: 9999;
            width: 30%;
            top: 70px;
            left: 26px;
        }

        .cBZXtz {
            display: flex;
            -webkit-box-pack: justify;
            justify-content: space-between;
            -webkit-box-align: center;
            align-items: center;
        }

        .bdDqgn {
            padding: 0.6em 1em;
            background-color: white;
            border-bottom-left-radius: 0.286em;
            border-bottom-right-radius: 0.286em;
            max-height: 28.571em;
            overflow: scroll;
        }

        .cBZXtz {
            display: flex;
            -webkit-box-pack: justify;
            justify-content: space-between;
            -webkit-box-align: center;
            align-items: center;
        }

        .kikQSm {
            display: inline-block;
            max-width: 100%;
            font-size: 0.857em;
            font-family: Lato;
            font-weight: 700;
            color: rgb(86, 102, 108);
            margin-bottom: 0.429em;
        }

        .gdoBAT {
            font-size: 12px;
            margin: 0px 0px 5px 10px;
            color: rgb(86, 102, 108);
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

@endsection

@section('inlineJS')

@endsection

@section('content')

    <div class="right_col" role="main">
        <div class="alert-message"></div>
        <div class="">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @if ($message = Session::get('warning'))
                <div class="alert alert-warning alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @if ($message = Session::get('info'))
                <div class="alert alert-info alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    Please check the form below for errors
                </div>
            @endif
            <div class="page-title">
                <div class="title_left amazon-text">
                    <h3>Mid Mile Jobs
                        <small></small>
                    </h3>
                </div>
            </div>

            <div class="clearfix"></div>

            {{--@include('backend.layouts.modal')
            @include( 'backend.layouts.popups')--}}
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <form id="filter" style="padding: 10px;"
                                  action="" method="get">
                                <div class="col-md-3">
                                   <input id="date" name="date" style="width:35%" type="date" placeholder="date "
                                       value='{{$date}}' class="form-control1">
                                </div>
                                <div class="col-md-4">
                                     <button id="search" type="submit" class="btn green-gradient">Filter</button>
                                </div>

                            </form>


                            <div class="clearfix"></div>
                        </div>


                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <div class="table-responsive">
                                <table id="datatable" class="table table-striped table-bordered">
                                    <thead stylesheet="color:black;">
                                    <tr>
                                        <th>Id</th>
                                        <th>Job Id</th>
                                        <th>Title</th>
                                        <th>Start Address</th>
                                        <th>Execution Time</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($jobs)==0)

                                        <tr class="odd">
                                            <td valign="top" colspan="9" class="dataTables_empty">No data available in
                                                table
                                            </td>
                                        </tr>

                                    @else
                                        {{--*/ $i = 1 /*--}}
                                        @foreach( $jobs as $record )
                                            <tr class="">
                                                <td>{{ $i }}</td>
                                                <td>{{ 'MR-'.$record->id }}</td>
                                                <td>{{ $record->title }}</td>
                                                <td>{{ $record->start_address  }}</td>
                                                <td>{{ $record->execution_time  }}</td>
                                                <td>{{ $record->type  }}</td>
                                                <td>
                                                    <button type='button' class='route btn btn black-gradient actBtn'
                                                            data-id='{{$record->id}}'>Submit For route
                                                        <i class='fa fa-eye'></i>
                                                    </button>
                                                    <a href="#">
                                                        <button type='button' class='btn btn black-gradient actBtn'>Detail
                                                            <i class='fa fa-eye'></i>
                                                        </button>
                                                    </a>
                                                </td>
                                            </tr>
                                            {{--*/ $i++ /*--}}
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>


                        </div>
                    </div>
                </div>

            </div>


        </div>
    </div>

    <div id="wait" style="display:none;position:fixed;top:50%;left:50%;padding:2px;"><img
                src="{{app_asset('images/loading.gif')}} " width="104" height="64"/><br></div>


    <!-- /#page-wrapper -->


    <!-- CreateSLotsModal -->


    <!-- UpdateSLotModal -->
    <!-- DeleteSlotsModal -->
    <div id="ex4" class="modal" style="display: none">
        <div class='modal-dialog'>
            <div id="delteform">
            </div>
        </div>
    </div>





    <div id="ex7" class="modal" style="display: none">
        <div class='modal-dialog'>
            <div id='main_form'>
            </div>
        </div>
    </div>



    <div id="ex6" class="modal" style="display: none">
        <div class='modal-dialog'>
            <div class='main_form'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'>&times;</button>
                        <h4 class='modal-title'>Create Route</h4></div>
                    <div class='form-group'><p><b>Are you sure you want to pull these routes?</b></p></div>
                    <div class='form-group'>
                        <input type="hidden" name="engine" id="engine">
                        <input type="hidden" name="job_id" id="job_id">
                        <button type='submit' class='btn green-gradient btn-xs createRouteByJobId'>Yes</button>
                        <button type='button' class='btn red-gradient btn-xs' data-dismiss='modal'>No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">

        $(document).ready(function () {
            $(document).ajaxStart(function () {
                $("#wait").css("display", "block");
            });
            $(document).ajaxComplete(function () {
                $("#wait").css("display", "none");
            });
            $("button").click(function () {
                $("#txt").load("demo_ajax_load.asp");
            });
        });

        $(function () {
            $(".create").click(function () {
                var element = $(this);
                var del_id = element.attr("data-id");
                document.getElementById('main_form').innerHTML = "<div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'>&times;</button><h4 class='modal-title'>Create Route</h4></div><form action='./create/" + del_id + "/route' method='get'><div class='form-group'><p><b>Are you sure you want to pull these routes?</b></p></div><div class='form-group'><button type='submit' class='btn green-gradient btn-xs' >Yes</button><button type='button' class='btn red-gradient btn-xs' data-dismiss='modal' >No</button></div>  </form></div>";
                $('#ex7').modal();
            });
        });


        $(function () {
            $(".createCustom").click(function () {
                var element = $(this);
                var del_id = element.attr("data-id");
                var engine = element.attr("data-engine");
                $('#engine').val(engine);
                $('#job_id').val(del_id);
                $('#ex6').modal();
            });
        });


        $(function () {
            $(".createRouteByJobId").click(function () {
                var routeEngine = $('#engine').val();
                var jobId = $('#job_id').val();
                if(routeEngine != ''){
                    $(".loader").show();
                    $(".loader-background").show();

                    var url = ''
                    if(routeEngine == 1){ // engine 1 is routific
                        url = './create/'+jobId+'/route';
                    }else if(routeEngine == 2){ // engine 2 is beta routific
                        url = './create/'+jobId+'/route';
                    } else if(routeEngine == 3){ // engine 3 is logistic os
                        url = '../../../ctc/logistic/create/'+jobId+'/route';
                    }

                    $('#ex6').modal('hide');

                    $.ajax({
                        type: "get",
                        url: url,
                        // data: {hub_id: hub_id, engine: engine},
                        beforeSend: function (request) {
                            return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                        },
                        success: function (data) {
                            $(".loader").hide();
                            $(".loader-background").hide();
                            if(data.status_code == 400){
                                $('.alert-message').html('<div class="alert alert-danger alert-red"><button style="color:#f5f5f5"; type="button" class="close" data-dismiss="alert"><strong><b><i  class="fa fa-close"></i><b></strong></button><strong>' + data.output + '</strong>');
                                // window.location.href = "../mile/jobs";
                            }
                            if(data.status_code == 200){
                                $('.alert-message').html('<div class="alert alert-success alert-green"><button style="color:#f5f5f5"; type="button" class="close" data-dismiss="alert"><strong><b><i  class="fa fa-close"></i><b></strong></button><strong>' + data.output + '</strong>');
                                setTimeout(function(){
                                    window.location.href = '../mile/routes/list';
                                }, 1000);
                            }


                        },
                        error: function (error) {
                            alert(error)
                            $('#ex6').modal('hide');
                            $(".loader").hide();
                            $(".loader-background").hide();
                            bootprompt.alert('some error');
                        }
                    });
                }
            });
        });
        $(function () {
            $(".delete").click(function () {
                var element = $(this);
                var del_id = element.attr("data-id");
                document.getElementById('delteform').innerHTML = "<div class='modal-content'><div class='modal-header'><button type='button' class='close' data-dismiss='modal'>&times;</button><h4 class='modal-title'>Delete Route</h4></div><form action='./job/delete' method='post'><input type='hidden' name='_token' value='{{ csrf_token() }}'><input type='hidden' name='delete_id' value=" + del_id + "><div class='form-group'><p><b>Are you sure you want to delete this?</b></p></div><div class='form-group'> <button type='submit' class='btn green-gradient btn-xs' >Yes</button><button type='button' class='btn red-gradient btn-xs' data-dismiss='modal' >No</button></div></form></div>";
                $('#ex4').modal();
            });
        });
    </script>


@endsection