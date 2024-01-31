<?php
use Illuminate\Support\Facades\Auth;
$user= Auth::user();
use App\SlotsPostalCode;
?>
@extends( 'backend.layouts.app' )
@section('title', 'Micro Hub Zones')
@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <!-- Image Viewer CSS -->
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css"
          rel="stylesheet"/>
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet"/>
    <style>
        .btn.green-gradient.btn-xs.accordion {
            width: 20px;
            position: relative;
        }
        .small_btn {
            width: auto !important;
        }
        .btn.green-gradient.btn-xs.accordion i.fa.fa-angle-down {
            color: white;
            display: flex;
            justify-content: center;
            height: 100%;
            left: 2px;
            position: relative;
            top: -7px;
            font-size: 17px;
        }
        .alert.alert-success.alert-block {
            display: inline-block;
            width: 100%;
            background: #3e3e3e;
            opacity: 0.4;
            border-color: #3e3e3e;
            padding: 25px 15px;
        }

        .green-gradient, .green-gradient:hover {
            /*color: #fff;*/
            color: #3e3e3e;
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
            border-radius: 6px;
            padding: 5px 15px;
            border-bottom: 1px solid #e5e5e5;
            background: #c6dd38;
        }

        .form-group {
            width: 100%;
            margin: 10px 0;
            padding: 0 15px;
        }

        .form-group input, .form-group select {
            width: 65% !important;
            height: 30px;
        }

        .form-group label {
            width: 25%;
            float: left;
            clear: both;
        }

        .lineEdit {
            width: 100%;
            float: left;
            margin: 5px 0;
        }

        .addInputs {
            width: 75%;
            float: left;
        }

        .lineEdit input {
            width: 80% !important;
            float: left;
        }

        button.remScntedit {
            height: 30px;
            margin: 0 5px;
        }

        button.remScnt {
            height: 30px;
            margin-top: 2px;
        }

        .addMoresec {
            text-align: right;
            padding: 0 50px;
        }

        #ex3 .form-group p {
            width: 75%;
            background: #f5f5f5;
            float: right;
            padding-left: 7px;
        }

        #ex3 .form-group label {
            float: none;
        }

        .panell {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.2s ease-out;
        }

        button.btn.green-gradient.btn-xs.accordion {
            height: 17px;
            line-height: 0;
            margin: 0;
        }

        td li {
            width: 80%;
            float: left;
            list-style: none;
        }

        td ol {
            padding: 0;
        }

        .alert.alert {
            margin-top: 50px;
        }

        .form-control {

            font-size: 13px;

            color: #555;

        }

        .loader {
            position: relative;
            right: 0%;
            top: 0%;
            justify-content: center;

            /* text-align: center; */
            border: 18px solid #e36d28;
            border-radius: 50%;
            border-top: 16px solid #34495e;
            width: 120px;
            height: 120px;
            -webkit-animation: spin 4s linear infinite;
            animation: spin 2s linear infinite;
            margin: 0 auto;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .loader-iner-warp {
            position: relative;
            width: 100%;

            top: 40%;
        }

        @media screen and (max-width: 480px) {
            #d_orders {
                padding-top: 15px;
            }
        }

        .select2-container--default .select2-selection--single, .select2-container--default .select2-selection--multiple {
            min-height: 33px;
        }
    </style>
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
@endsection

@section('inlineJS')

@endsection

@section('content')
    <div class="right_col" role="main">
        <div class="alert-message"></div>
        <div class="">
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-green">
                    <button style="color:#f5f5f5" ; type="button" class="close" data-dismiss="alert"><strong><b><i
                                        class="fa fa-close"></i><b></strong></button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-red">
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


                        <h3>Last Mile Zones List<small></small></h3>
                </div>
            </div>

            <div class="clearfix"></div>

            {{--@include('backend.layouts.modal')
            @include( 'backend.layouts.popups')--}}
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-6 c-nop">
                                    <form method="get" action="" class="d-flex align-item-end">
                                        <div class="col-md-6" style="padding-left:1 !important">
                                            <label>Search By Date</label>
                                            <input type="date" name="date" id="date" required="" placeholder="Search" class="form-control"
                                                   value="{{$date}}">
                                        </div>
                                        <div class="col-md-6 sm_custm">
                                            <button class="btn btn-primary" type="submit" style="margin-top: -3%,4%">
                                                Go</a> </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6 text-right p_top sm_custm" style="gap:0 !important">
                                        <button style="margin-left:10px;" class="btn green-gradient" data-toggle="modal"
                                                data-target="#ex1"><i class="fa fa-plus"></i> Create Zone
                                        </button>
                                        <a href="{{ url('last/mile/job/list') }}" class="btn green-gradient">Last Mile Routing Jobs <i class="fa fa-list"></i> </a>
                                        <a href="{{ url('last/mile/routes/list') }}" class="btn green-gradient">Last Mile Routes List <i class="fa fa-th-list"></i> </a>
                                        <a href="{{ url('custom/routing/129/hub') }}" class="btn green-gradient">Last Mile Custom Routing <i class="fa fa-th-list"></i> </a>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>


                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <div class="table-responsive">
                                <table id="datatable-" class="table table-striped table-bordered">
                                    <thead stylesheet="color:black;">
                                    <tr>
                                        <th style='text-align: center;'>ID</th>
                                        <th style="width: 10%;text-align: center;">Zone ID</th>
                                        <th style='text-align: center;text-align: center;'>Zone Title</th>
                                        <th style="width: 11%;text-align: center;">Zone Type</th>
                                        <th style="width: 11%;text-align: center;">Postal Codes</th>
                                        <th style='text-align: center;'>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $i = 1;
                                    $hub = '';
                                    foreach ($data as $zones) {

                                        echo "<tr>";
                                        echo "<td style='text-align: center;'>" . $i . "</td>";
                                        echo "<td style='text-align: center;'>" . $zones->id . "</td>";
                                        echo "<td style='width: 400px;text-align: center;'>" . $zones->title . "</td>";


                                        if (!empty($zones->zoneType)) {
                                            echo "<td style='width: 400px;text-align: center;'>" . $zones->zoneType->title . "</td>";
                                        } else {
                                            echo "<td style='text-align: center;'></td>";
                                        }
                                        echo "<td style='width: 20%text-align: center;'>";

                                        $SlotsPostalCode = SlotsPostalCode::where('slots_postal_code.zone_id', '=', $zones->id)->WhereNull('slots_postal_code.deleted_at')->get();
                                        if (count($SlotsPostalCode) > 1) {
                                            echo "<ol><button class='btn green-gradient btn-xs accordion'><i class='fa fa-angle-down'></i></button>";
                                        }
                                        $j = 1;


                                        foreach ($SlotsPostalCode as $postalCode) {

                                            if ($j == 1) {
                                                echo "<li class='pCode'>$j :" . $postalCode->postal_code . "</li>
                                          ";
                                            } else {
                                                echo "<li class='panell' >$j :" . $postalCode->postal_code . "</li>";
                                            }


                                            $j++;

                                        }
                                        echo "</ol>";
                                        echo "</td>";
                                        echo "</td>";
                                        echo "<td style='width: 600px;text-align: center;'>";
//                                        if (can_access_route(['micro-hub-zones-update.get', 'micro-hub-zones-update.post'], $userPermissoins)) {
                                            echo "<button type='button'  class='update btn btn green-gradient actBtn small_btn'  data-id='" . $zones->id . "'>Edit <i class='fa fa-pencil'></i></button>";
//                                        }
//                                        if (can_access_route(['micro-hub-zones-delete.post'], $userPermissoins)) {
                                            echo "<button type='button'  class='delete btn btn red-gradient actBtn small_btn' data-id='" . $zones->id . "'>Delete <i class='fa fa-trash'></i></button>";
                                            /*echo "<button type='button'  class='details btn btn orange-gradient actBtn'  data-id='".$zones->id."'>Details <i class='fa fa-eye'></i></button>";*/
//                                        }
//                                        if (can_access_route(['micro-hub-zones-count.get', 'micro-hub-ottawa-routes-add.post', 'micro-hub-montreal-routes-add.post', 'micro-hub-ctc-routes-add.post'], $userPermissoins)) {
                                            echo "<button type='button'  class='counts btn btn black-gradient actBtn small_btn'  data-id='" . $zones->id . "'>Count<i class='fa fa-eye'></i></button>";
//                                        }
//                                        if (can_access_route(['micro-hub-ottawa-routes-add.post', 'micro-hub-montreal-routes-add.post', 'micro-hub-ctc-routes-add.post'], $userPermissoins)) {
                                            echo "<button type='button'  class='route btn btn black-gradient actBtn small_btn'  data-id='" . $zones->id . "'>Submit For Route <i class='fa fa-eye'></i></button>";
//                                        }
//                                        if (can_access_route(['micro-hub-slot-list-hubid.get'], $userPermissoins)) {
                                            echo "<a href='slots/list/hubid/" . $zones->hub_id . "/zoneid/" . $zones->id . "' class='btn btn black-gradient actBtn small_btn'>View Slots <i class='fa fa-eye'></i></button>";
//                                        }
                                        echo "</td>";
                                        echo "</tr>";
                                        $i++;

                                    }

                                    if (isset($zones) && !empty($zones)) {
                                        if ($hubData != null && $hubData->parent_hub_id != null) {
                                            $hub = $hubData->parent_hub_id;
                                        } else {
                                            $hub = $zones->hub_id;
                                        }
                                    }
                                    ?>
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

    <!-- Create Zone Modal -->
    <div id="ex1" class="modal" style="display: none">
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Zones</h4>
                </div>
                <form action="{{ URL::to('last/mile/zone/create')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label style="width:100% !important">Zone Title</label>
                        
                        <input type="text" name="title" id="title" pattern="[A-Za-z0-9]{1}[A-Za-z 0-9()]{0,40}"
                               class="form-control" style="width:100% !important" required/>
                   
                    </div>
                    <div class="form-group">
                        <label>Zone Type</label>
                        <select class="form-control " name="zone_type" id="zone_type" style="width: 50%;" required>
                            <option value="">Please Select Zone Type</option>
                            @foreach ($zoneType as $zones)
                                <option value="{{$zones->id}}">{{$zones->title}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Postal Codes </label>
                        <select class="js-example-basic-multiple form-control" name="postal_code[]"
                                id="postal_code_select" style="width: 65%;min-height: 33px !important;"
                                multiple="multiple" required>
                            <!--@if($hubData!=null)-->
                            <!--<option></option>-->
                                @foreach ($hubData->HubPostalCode as $PostalCode)
                                    <option value="{{$PostalCode->postal_code}}">{{$PostalCode->postal_code}}</option>
                                @endforeach
                            <!--@endif-->
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn orange-gradient">
                            Create Zone <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Update Zone Modal -->
    <div id="ex2" class="modal" style="display: none">
        <div class='modal-dialog'>

            <div class='modal-content'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Update Zone</h4>
                </div>
                <form action="{{ URL::to('last/mile/zone/update')}}" method="post">

                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-group">
                        <input type="hidden" name="id_time" id="id_time" class="form-control" required/>
                    </div>


                    <div class="form-group">
                        <label style="width:100%">Zone Title</label>
                        <input type="text" name="title_edit" id="title_edit" style="width:100% !important"
                               pattern="[A-Za-z0-9]{1}[A-Za-z 0-9()]{0,40}" class="form-control" required/>
                    </div>
                    <div class="form-group">    
                        <label>Zone Type</label>
                        <select class="form-control testing" name="zone_type" id="zone_type" style="width: 50%;"
                                required>
                            <option value="" style="font-size: 12px;">Please Select Zone Type</option>
                            <?php
                            foreach ($zoneType as $zones) {
                                echo '<option style="    font-size: 12px;" value="' . $zones->id . '">' . $zones->title . '</option>';
                            }
                            ?>
                        </select>
                    </div>


                    <div class="form-group">
                        <label>Postal Codes :</label>
                        <div class="form-group">
                            <select class="js-example-basic-multiple form-control" name="postal_code_edit[]"
                                    id="postal_code_select_edit" style="width: 65%;min-height: 33px !important;"
                                    multiple="multiple" required>
                                @if($hubData!=null)
                                    @foreach ($hubData->HubPostalCode as $PostalCode)
                                        <option value="{{$PostalCode->postal_code}}" {{ ($hubData->id == $PostalCode->hub_id) ? 'selected' : null }}>{{$PostalCode->postal_code}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                    </div>


                    <div class="form-group">
                        <button type="submit" class="btn orange-gradient">
                            Update Zone</i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Order Count Model -->
    <div id="ex20" class="modal" style="display: none">
        <div class='modal-dialog'>
            <div class='modal-content '>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 style="    text-align: -webkit-center;
                font-size: x-large;" id="count_detail" class="modal-title">Count Details</h4>
                </div>
                <div style="padding-left: 30%;" class="form-group">
                    <label style=" font-size: 14px; width: 40%; color: black;  ">Title :</label>
                    <p style="    font-size: 14px;     margin-left: 35%;color: black; " id="name"></p>
                </div>
                <div style="padding-left: 30%;" class="form-group">
                    <label style=" font-size: 14px; width: 40%; color: black; ">Zone Id</label>
                    <p style="    font-size: 14px;     margin-left: 35%;color: black; " id="d"></p>
                </div>
                <div style="padding-left: 30%;" class="form-group">
                    <label style=" font-size: 14px; width: 40%; color: black; ">Order Count:</label>
                    <p style="    font-size: 14px;     margin-left: 35%;color: black; " id="order"></p>
                </div>
                <div style="padding-left: 30%;" class="form-group">
                    <label style="    font-size: 14px;width: 40%;color: black; ">Not In Route</label>
                    <p style="    font-size: 14px;    margin-left: 35%;color: black; " id="d_orders"></p>
                </div>
                <div style="padding-left: 30%;" class="form-group">
                    <label style="    font-size: 14px;width: 40%;color: black; ">Total Joeys Count</label>
                    <p style="    font-size: 14px;    margin-left: 35%;color: black; " id="joeys_count"></p>
                </div>
                <div class="c-set">
                    <a type='button' id="aaa" style="margin-top:10px;width: 168px; "
                       class='route btn btn black-gradient actBtn' data-id=''>Submit For Route <i class='fa fa-eye'></i></a>
                </div>
            </div>
        </div>
    </div>

    <!---table-responsive start-->
    <div class="loader-background" style="
    position: fixed;
    top: 0px;
    left: 0px;
    z-index: 9999;
    width: 100%;
    height: 100%;
    background-color: #000000ba; display: none ">
        <div class="loader-iner-warp">
            <div class="loader" style="display: none"></div>
        </div>
    </div>
    <!-- for counts in route or not in route -->

    <div id="ex21" class="modal" style="display: none">
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Get Total Orders Count</h4>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label>Total Orders</label>
                    <p id="total_orders"></p>
                </div>
                <div class="form-group">
                    <label>Not In Route</label>
                    <p id="not_in_route"></p>
                </div>
            </div>
        </div>
    </div>


    <div id="totalordercount" class="modal" style="display: none">
        <div class='modal-dialog'>

            <div class='modal-content'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"> Total Orders Count</h4>
                </div>

                <div class="form-group">
                    <label>Order Not In Route</label>
                    <p id="not_in_routes"></p>

                </div>


            </div>
        </div>
    </div>
    <!-- DeleteZonesModal -->
    <div id="ex4" class="modal" style="display: none">
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete Zone</h4>
                </div>
                <form action="{{ URL::to('last/mile/zone/delete')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="delete_id" name="delete_id" value="">
                    <div class="form-group">
                        <p><b>Are you sure you want to delete this?</b></p>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn green-gradient btn-xs">Yes</button>
                        <button type="button" class="btn red-gradient btn-xs" data-dismiss="modal">No</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="ex10" class="modal" style="display: none">
        <div class='modal-dialog'>

            <div class='modal-content'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"> Submit For Route</h4>
                </div>
                <?php
                //if ($hub == 19) {
//                    echo "<form action='ottawa/routes/add' method='post' class='submitForRoute' >";
//                } elseif ($hub == 16) {
//                    echo "<form action='montreal/routes/add' method='post' class='submitForRoute' >";
//                } elseif ($hub == 18) {
                    echo "<form action='routes/add' method='post' class='submitForRoute' >";
//                }?>

                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                        <label>Start Time</label>
                        <input type="time" class="form-control" name="start_time" id="start_time" required>
                    </div>
                    <div class="col-md-5">
                        <label>End Time</label>
                        <input type="time" class="form-control"  name="end_time" id="end_time" required>
                    </div>
                    <div class="col-md-1"></div>
                </div>



                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="zone" name="zone" value="">
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

        //get total order count
        $(document).on('click', '.totalOrderCount', function () {
            element = $(this);
            var hub_id = element.attr("data-id");
            let date = document.getElementsByName('date')[0].value;
            $(".loader").show();
            $(".loader-background").show();
            $.ajax({
                type: "post",
                url: "{{ URL::to('last/mile/total/order/notinroute')}}",
                data: {id: hub_id, date: date},
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                },
                success: function (data) {
                    $(".loader").hide();
                    $(".loader-background").hide();
                    if (data.status_code == 200) {


                        $('#totalordercount').modal();
                        $('#totalordercount #not_in_routes').text(data.count);
                    }


                },
                error: function (error) {
                    $(".loader").hide();
                    $(".loader-background").hide();

                    bootprompt.alert('some error');
                }
            });


        });

        //process form for edit joey count
        $('.submitForRoute').submit(function (event) {
            event.preventDefault();
            // get the form data
            var data = new FormData();
            data.append('zone', $('input[name=zone]').val());
            data.append('create_date', $('input[name=create_date]').val());
            data.append('start_time', $('input[name=start_time]').val());
            data.append('end_time', $('input[name=end_time]').val());

            $('#ex10').modal('toggle');

            // process the form
            $(".loader").show();
            $(".loader-background").show();

            $.ajax({
                url: $('.submitForRoute').attr('action'),
                type: 'POST',
                contentType: false,
                processData: false,
                data: data,
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                },

                success: function (data) {
                    console.log(data)
                    // $('#ex20').toggle();
                    $(".loader").hide();
                    $(".loader-background").hide();
                    $('#ex20').modal('hide');

                    if (data.status_code == 200) {

                        $('.alert-message').html('<div class="alert alert-success alert-green"><button style="color:#f5f5f5"; type="button" class="close" data-dismiss="alert"><strong><b><i  class="fa fa-close"></i><b></strong></button><strong>' + data.success + '</strong>');
                    } else {
                        $('.alert-message').html('<div class="alert alert-danger alert-red"><button style="color:#f5f5f5"; type="button" class="close" data-dismiss="alert"><strong><b><i  class="fa fa-close"></i><b></strong></button><strong>' + data.error + '</strong>');
                    }
                    //    location.reload();
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

        $(document).ready(function () {
            $(".accordion").click(function () {
                //toggleactiveclass
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                } else {
                    $(this).addClass('active');
                }

                //addcssClass
                if ($(this).hasClass('active')) {
                    $(this).parent().find(".panell").css({
                        "maxHeight": "20px",
                    });
                } else {
                    $(this).parent().find(".panell").css({
                        "maxHeight": "0px",
                    });
                }
            });
            var i = 0;
            $(document).ready(function () {
                i++;
                var scntDiv = $('#add_words');
                $('<div class="line"><label>Postal Code </label><input type="text" value="" name="postal[]" maxlength="3 " style="padding-left: 15px;" placeholder="Postal code" pattern="[A-Za-z]{1}[0-9]{1}[A-Za-z]{1}" required  /> <button class="remScnt btn red-gradient">x</button></div>').appendTo(scntDiv);
            });

            var scntDiv = $('#add_words');
            var wordscount = 1;
            // var i = $('.line').size() + 1;

            $('#add').click(function () {
                // alert()
                var inputFields = parseInt($('#inputs').val());
                for (var n = i; n < inputFields; ++n) {
                    wordscount++;
                    $('<div class="line"><label>Postal Code </label><input type="text" value="" name="postal[]" maxlength="3" placeholder="Postal code" pattern="[A-Za-z]{1}[0-9]{1}[A-Za-z]{1}" required  /> <button class="remScnt btn red-gradient">x</button></div>').appendTo(scntDiv);
                    i++;
                }
                return false;
            });

            //    Remove button
            $('#add_words').on('click', '.remScnt', function () {
                if (i > 1) {
                    $(this).parent().remove();
                    i--;
                }
                return false;
            });
        });

        // detailsFunc
        $(document).ready(function () {
            $(".details").click(function () {
                var a;
//    var element = $(this);
                var del_id = element.attr("data-id");

                // console.log(del_id);
                $.ajax({
                    type: "GET",
                    url: '../../zonestesting/' + del_id + '/detail',

                    success: function (data) {
                        a = JSON.parse(data);
                        console.log(a);

                        console.log(a['data']['hub_id']);


                        $('#zone_id').html('' + a['data']['id']);
                        $('#hub_id_d').html('' + a['data']['hub_id']);
                        // console.log($("#hub_id"))
                        $('#title_d').html('' + a['data']['title']);
                        $('#address_d').html('' + a['data']['address']);


                        arrNew_d = [];
                        var post = '';
                        $.each(a['postalcodedata'], function (i, val) {
                            //  arrNew_d.push(val['postal_code'])
                            if (post == "") {
                                post = val['postal_code'];
                            } else {
                                post = post + ',' + val['postal_code'];
                            }
                            $('#postal_code_d').html('' + post);
                        })


                        $('#ex3').modal();


                    }

                });
            });
        });
        //routeCount function routeCount
        $(document).ready(function () {
            $(".routeCount").click(function () {
                var a;

                // process the form
                $(".loader").show();
                $(".loader-background").show();
                var element = $(this);
                var date = $('#date').val()

                // var del_id = element.attr("data-id");

                var id = $('#hub_id').val();
                $.ajax({
                    type: "GET",
                    url: '../../zonestesting/count/' + id + '/' + date,


                    beforeSend: function () {
                        // Show image container
                        $("#wait").show();
                    },
                    success: function (data) {
                        $(".loader").hide();
                        $(".loader-background").hide();
                        a = JSON.parse(data);
                        console.log(a);

                        $('#total_orders').html('' + a.orders);
                        $('#not_in_route').html('' + a.d_orders);


                        $('#ex21').modal();


                    },
                    complete: function (data) {
                        $(".loader").hide();
                        $(".loader-background").hide();
                        // Hide image container
                        $("#wait").hide();
                    }
                });
            });
        });

        //last mile zone order count function
        $(document).ready(function () {
            $(".counts").click(function () {

                var a;
                var element = $(this);
                var date = $('#date').val()

                var del_id = element.attr("data-id");
                $(".loader").show();
                $(".loader-background").show();

                $.ajax({
                    type: "GET",
                    url: "{{URL::to('/last/mile/zone/order/count/')}}/" + date + '/' + del_id,
                    success: function (data) {
                        $(".loader").hide();
                        $(".loader-background").hide();
                        a = JSON.parse(data);
                        console.log(a);

                        $('#count_detail').html('' + a.title);
                        $('#name').html('' + a.title);

                        $('#d').html('' + a.id);
                        $('#order').html('' + a.orders);
                        $('#d_orders').html('' + a.d_orders);
                        $('#joeys_count').html('' + a.joeys_count);
                        // $('#slots_detail').html(''+a['slots_detail'][0]['name']+":"+""+a['slots_detail'][0]['joey_count']);.
                        console.log(a.slots_detail.length);
                        var x = '';
                        for (var i = 0; i < a.slots_detail.length; i++) {
                            x = x + a['slots_detail'][i].name + ":" + "" + a['slots_detail'][i].joey_count + ' ';
                        }
                        $('#slots_detail').html(x);

                        /* var d = document.getElementById("aaa");  //   Javascript

                         //console.log(d);
                         d.setAttribute('data-id' ,id);*/
                        $('#ex20').find('#aaa').attr('data-id', del_id);
                        $('#ex20').modal();


                    }
                });
            });
        });

        // update last mile zone
        $(document).ready(function () {
            $(".update").click(function () {
                var element = $(this);
                var del_id = element.attr("data-id");
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('/last/mile/zone')}}/" + del_id,
                    success: function (data) {

                        let microHubZoneData = JSON.parse(data);
                        $('#id_time').val('' + microHubZoneData['data']['id']);
                        $('#title_edit').val('' + microHubZoneData['data']['title']);
                        $('#address_edit').val('' + microHubZoneData['data']['address']);
                        $('.testing').val('' + microHubZoneData['data']['zone_type']);
                        $('#postal_code_select_edit').empty();
                        for (let postalcode of microHubZoneData['hubPostalCode']) {
                            if (microHubZoneData['postalcodedata'].includes(postalcode)) {
                                $('#postal_code_select_edit').append('<option selected value="' + postalcode + '">' + postalcode + '</option>');
                            } else {
                                $('#postal_code_select_edit').append('<option  value="' + postalcode + '">' + postalcode + '</option>');
                            }
                        }
                        $('#ex2').modal();
                    }
                });
            });
        });

        //DeleteFunc
        $(function () {
            $(".delete").click(function () {

                var element = $(this);
                var del_id = element.attr("data-id");
                $('#delete_id').val('' + del_id);
                $('#ex4').modal();
            });
        });
    </script>

    <script type="text/javascript">
        // Datatable
        $(document).ready(function () {

            $('#datatable').DataTable({

                "lengthMenu": [25, 50, 100, 250, 500, 750, 1000]


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
                                            var DataToset = '<button type="btn" class="btn btn-warning btn-xs status_change" data-toggle="modal" data-id="' + Uid + '" data-target=".bs-example-modal-sm">Blocked</button>';
                                            $('#CurerntStatusDiv' + Uid).html(DataToset);
                                        } else {
                                            var DataToset = '<button type="btn" class="btn btn-success btn-xs status_change" data-toggle="modal" data-id="' + Uid + '" data-target=".bs-example-modal-sm">Active</button>'
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
        //route
        $(function () {
            $(".route").click(function () {

                var element = $(this);
                var del_id = element.attr("data-id");

                $('#zone').val('' + del_id);
                $('#ex10').modal();
            });
        })
    </script>
{{--    show alert message hide in short time--}}
    <script>
        $(function() {
            setTimeout(function() { $(".alert-red").fadeOut(1500); }, 5000)

        })
    </script>
@endsection