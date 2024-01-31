<?php
use App\Joey;
use App\Slots;
use App\RoutingZones;
use App\JoeyRouteLocations;


?>
@extends( 'backend.layouts.app' )

@section('title', 'First Mile Routes')

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
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#bad709), color-stop(100%,#afca09));
            background: -webkit-linear-gradient(top, #bad709 0%,#afca09 100%);
            background: linear-gradient(to bottom, #bad709 0%,#afca09 100%);
        }
        .black-gradient,
        .black-gradient:hover {
            color: #fff;
            background: #535353;
            background: -moz-linear-gradient(top,  #535353 0%, #353535 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#535353), color-stop(100%,#353535));
            background: -webkit-linear-gradient(top,  #535353 0%,#353535 100%);
            background: linear-gradient(to bottom,  #535353 0%,#353535 100%);
        }
        div#transfer select#joey_id {
            width: 100% !important;
        }
        #transfer .custom_dropdown {
            width: 100%;
            margin-bottom: 10px;
        }
        .red-gradient,
        .red-gradient:hover {
            color: #fff;
            background: #da4927;
            background: -moz-linear-gradient(top,  #da4927 0%, #c94323 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#da4927), color-stop(100%,#c94323));
            background: -webkit-linear-gradient(top,  #da4927 0%,#c94323 100%);
            background: linear-gradient(to bottom,  #da4927 0%,#c94323 100%);
        }

        .orange-gradient,
        .orange-gradient:hover {
            color: #fff;
            background: #f6762c;
            background: -moz-linear-gradient(top,  #f6762c 0%, #d66626 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f6762c), color-stop(100%,#d66626));
            background: -webkit-linear-gradient(top,  #f6762c 0%,#d66626 100%);
            background: linear-gradient(to bottom,  #f6762c     0%,#d66626 100%);
        }
        .modal-dialog.map-model {
            width: 94%;
        }
        .btn{
            font-size : 12px;
        }

        .modal.fade {
            opacity: 1
        }

        .modal-header {
            font-size: 16px;
        }

        .modal-body h4 {
            background: #f6762c;
            padding: 8px 10px;
            margin-bottom: 10px;
            font-weight: bold;
            color: #fff;
        }

        .form-control {
            display: block;
            width: 100%;
            height: 34px;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
            -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
            -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        }

        .form-control:focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
        }

        .form-group {
            margin-bottom: 15px;
        }

        #ex1 form{
            padding: 10px;
        }
        div#transfer .modal-content, div#details .modal-content {
            padding: 20px;
        }

        #details .modal-content {
            overflow-y: scroll;
            height: 500px;
        }
        div#map5 {
            width: 100% !important;
        }

        .jconfirm .jconfirm-box{
            border : 5px solid #bad709
        }
        .btn-info {
            color: #fff;
            background-color: #cd692e;
        }
        .jconfirm .jconfirm-box .jconfirm-buttons button.btn-default {
            background-color: #b8452b;
            color: #fff !important;
        }

        .jconfirm-content {
            color: #535353;
            font-size: 16px;
        }

        .jconfirm button.btn:hover {
            background: #e46d29 !important;
        }

        .select2 {
            width: 70% !important;
            margin: 0 0 5px 10px;
        }

        /* start */
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
        .control-size {
            width: 70px;
        }



        /*boxes css*/
        .montreal-dashbord-tiles h3 {
            color: #fff;
        }
        .montreal-dashbord-tiles .count {
            color: #fff;
        }
        .montreal-dashbord-tiles .tile-stats
        {
            border: 1px solid #c6dd38;
            background: #c6dd38;
        }

        .montreal-dashbord-tiles .tile-stats {
            border: 1px solid #c6dd38;
            background: #c6dd38;
        }
        .montreal-dashbord-tiles .icon {
            color: #e36d28;
        }
        .tile-stats .icon i {
            margin: 0;
            font-size: 60px;
            line-height: 0;
            vertical-align: bottom;
            padding: 0;
        }

        @media only screen and (max-width: 1680px){
            .top_tiles .tile-stats {
                padding-right: 70px;
            }
            .tile-stats .count {
                font-size: 30px;
                font-weight: bold;
                line-height: 1.65857;
                overflow: hidden;
                box-sizing: border-box;
                text-overflow: ellipsis;
            }
            .tile-stats h3 {
                font-size: 12px;
            }
            .top_tiles .icon {
                font-size: 40px;
                position: absolute;
                right: 10px;
                top: 0px;
                width: auto;
                height: auto;
                font-size: 40px;
            }
            .top_tiles .icon .fa {
                vertical-align: middle;
                font-size: inherit;
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="{{ backend_asset('js/jquery-ui.js') }}"></script>
    <link href="{{ backend_asset('js/jquery-ui.css') }}" rel="stylesheet"> -->

@endsection

@section('inlineJS')
{{--    <script type="text/javascript">--}}
{{--        $(document).ready(function () {--}}
{{--            $('#datatable').DataTable({--}}
{{--                "lengthMenu": [25,50,100, 250, 500, 750, 1000 ]--}}
{{--            });--}}
{{--            $(".group1").colorbox({height:"50%",width:"50%"});--}}
{{--        });--}}
{{--    </script>--}}
@endsection

@section('content')
    <meta type="hidden" name="csrf-token" content="{{ csrf_token() }}" />
    <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
      <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
    <div class="right_col" role="main">
        <div class="">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">�</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">�</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif
            <div class="page-title">
                <div class="title_left amazon-text">
                    <h3>Mi Job List<small></small></h3>
                </div>
            </div>
                <div class="alert-message"></div>

            <div class="clearfix"></div>
            <?php
            if(empty($_REQUEST['date'])){
                $date = date('Y-m-d');
            }
            else{
                $date = $_REQUEST['date'];
            }
            ?>
            <div class="row">
                <div class="notifiedBox">
                    <h3>Note</h3>
                    <p>

                    </p>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">

                        <div class="col-md-6">
                            <a href="{{ url('mid/mile/routes/list') }}" class="btn btn green-gradient" data-id="25" >Mid Mile Routes List
                                <i class="fa fa-list-alt"></i>
                            </a>
                        </div>


                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <div class="table-responsive">
                                <table id="datatable" class="table table-striped table-bordered">
                                    <thead stylesheet="color:black;">
                                    <tr>
                                        <th style="width: 11%;">ID</th>
                                        <th style="width: 11%;">Title</th>
                                        <th style="width: 11%;">Start Address</th>
                                        <th style="width: 11%;">Execution Time</th>
                                        <th style="width: 11%;">Type</th>
                                        <th style="width: 20%;">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @if(count($miJobs) > 0)
                                        @foreach($miJobs as $key => $miJob)
                                            <tr>
                                                <td>{{ $miJob->id }}</td>
                                                <td>{{ $miJob->title }}</td>
                                                <td>{{ $miJob->start_address }}</td>
                                                <td>{{ $miJob->execution_time }}</td>
                                                <td>{{ $miJob->type }}</td>
                                                <td>
                                                    <button type='button' class='route btn btn black-gradient actBtn'
                                                            data-id='{{$miJob->id}}'>Submit For Route
                                                        <i class='fa fa-eye'></i>
                                                    </button>
                                                    <a href="{{ route('mid.mile.mi.job.detail', $miJob->id) }}">
                                                        <button type='button' class='btn btn black-gradient actBtn'>Detail
                                                            <i class='fa fa-eye'></i>
                                                        </button>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>No Record Found</td>
                                        </tr>
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

    <div id="ex10" class="modal" style="display: none">
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"> Submit For Route</h4>
                </div>
                <form action='../create/route' method='get' class='submitForRoute'>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="job_id" name="job_id" value="">
                    <input type="hidden" id="create_date" name="create_date" value=<?php echo date("Y-m-d") ?> >
                    <div class="form-group">
                        <p><b>Are you sure you want to Submit For Route ?</b></p>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn green-gradient btn-xs">Yes</button>
                        <button type="button" class="btn red-gradient btn-xs" data-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // Datatable
        $(document).ready(function () {
            $('#datatable').DataTable({
                "lengthMenu": [25, 50, 100, 250, 500, 750, 1000]
            });
            $(".group1").colorbox({height: "50%", width: "50%"});

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

        //show Route submit conformation alert
        $(function () {
            $(".route").click(function () {
                var element = $(this);
                var job_id = element.attr("data-id");
                $('#job_id').val(job_id);
                $('#ex10').modal();
            });
        })

        //Submit Route Request for First mile 2022-03-21
        $('.submitForRoute').submit(function (event) {
            event.preventDefault();
            // get the form data
            var data = new FormData();
            data.append('job_id', $('input[name=job_id]').val());
            data.append('create_date', $('input[name=create_date]').val());
            $('#ex10').modal('toggle');
            // process the form
            $(".loader").show();
            $(".loader-background").show();
            //run ajax with data
            $.ajax({
                url: $('.submitForRoute').attr('action'),
                type: 'POST',
                contentType: false,
                processData: false,
                data: data,
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                },
                //show success response
                success: function (data) {
                    $(".loader").hide();
                    $(".loader-background").hide();
                    $('#ex20').modal('hide');
                    if (data.status == 200) {

                        $('.alert-message').html('<div class="alert alert-success alert-green"><button style="color:#f5f5f5"; type="button" class="close" data-dismiss="alert"><strong><b><i  class="fa fa-close"></i><b></strong></button><strong>' + data.output + '</strong>');
                    } else {
                        var parseData = JSON.parse(data);
                        $('.alert-message').html('<div class="alert alert-danger alert-red"><button style="color:#f5f5f5"; type="button" class="close" data-dismiss="alert"><strong><b><i  class="fa fa-close"></i><b></strong></button><strong>' + parseData.output + '</strong>');
                    }
                },
                failure: function (result) {
                    $('#ex20').modal('hide');
                    $(".loader").hide();
                    $(".loader-background").hide();
                    bootprompt.alert(result);
                }
            });
            event.preventDefault();
        });
    </script>
@endsection