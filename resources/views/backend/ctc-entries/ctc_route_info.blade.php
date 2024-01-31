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

@section('title', 'Last Mile Route Info')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <!-- Image Viewer CSS -->
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <style>
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
        div#delay .modal-content, div#details .modal-content {
            padding: 20px;
        }

        #details .modal-content {
            overflow-y: scroll;
            height: 500px;
        }
        /*!*hoverable dropdown css*!*/
        .hoverable-dropdown-main-wrap {
            display: block;
            position: relative;
            box-sizing: border-box;
            margin: 0px 0px 0px 20px;
            width: 100%;
            padding: 0px;
        }
        .hoverable-dropdown-main-ul {
            display: inline-block;
        }
        .hoverable-dropdown-main-wrap ul
        {
            list-style: none;
            box-sizing: border-box;
            padding: 0px;
            margin: 0px;
        }
        .hoverable-dropdown-main-wrap ul li
        {
            box-sizing: border-box;
            cursor: pointer;
            position: relative;
            background: #f6f6f6;
            padding: 8px;
            width: 210px;
            margin: 1px 0;
            padding-right: 25px;
        }
        .hoverable-dropdown-ul
        {
            display: none;
        }
        /*.hoverable-dropdown-main-wrap  ul:hover*/
        /*{*/
        /*    display: block;*/
        /*}*/
        .hoverable-dropdown-main-ul > li:hover > .hoverable-dropdown-ul {
            display: block !important;
            z-index: 10;
            position: absolute;
            top: -1px;
            /*bottom: 0px;*/
            left: 100%;
            padding: 0% 0px 0px 5px;
        }
        .hoverable-dropdown-ul > li:hover > .hoverable-dropdown-ul {
            display: block !important;
            z-index: 10;
            position: absolute;
            top: -1px;
            /*bottom: 0px;*/
            left: 100%;
            padding: 0% 0px 0px 5px;
        }
        /*.hoverable-dropdown-main-wrap ul li:hover ul*/
        /*{*/
        /*    display: block;*/
        /*    z-index: 10;*/
        /*    position: absolute;*/
        /*    top: -1px;*/
        /*    left: 100%;*/
        /*    padding: 0% 0px 0px 5px;*/
        /*}*/
        /*.hoverable-dropdown-main-wrap ul > li:hover*/
        /*{*/
        /*    background: #ccc;*/
        /*}*/
        .hoverable-dropdown-main-ul .fa-angle-right {
            position: absolute;
            right: 10px;
        }
        .modal-content {
            width: 129%;
            height: 230px;
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
        div#map5 {
            width: 100% !important;
            height: 20px;
        }
        .modal-header {
            font-size: 16px;
            /*background: orange;*/
            /*color: #ffffff;*/
        }

        element.style {
            height: 200px;
            width: 60% !important;
            position: relative;
            overflow: hidden;
        }
        div#map5 {
            margin: auto;
            width: 100% !important;
            height: inherit;
        }
        .orange-gradient
            /*.orange-gradient:hover*/ {
            color: #fff;
            background: #f6762c;
            background: -moz-linear-gradient(top,  #f6762c 0%, #d66626 100%);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f6762c), color-stop(100%,#d66626));
            background: -webkit-linear-gradient(top,  #f6762c 0%,#d66626 100%);
            background: linear-gradient(to bottom,  #f6762c     0%,#d66626 100%);
        }

    </style>
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"
            type="text/javascript"></script>

    <script src="//cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="{{ backend_asset('js/jquery-ui.js') }}"></script>
    <link href="{{ backend_asset('js/jquery-ui.css') }}" rel="stylesheet"> -->

@endsection

@section('inlineJS')

    <script>
        $('#datatable').DataTable({
            "lengthMenu": [250, 500, 750, 1000],
            "pageLength": 250
        });

        /*  $('.buttons-excel').on('click', function (event) {

              event.preventDefault();

              alert('Processing for Download csv !')

              //showloader()


              //window.open(encodedUri);

              console.log('yes');
              let selected_date = $('.data-selector').val();
              $.ajax({
                  type: "get",
                  url: '{{ URL::to('ctc/route-info/list/') }}/' + selected_date,
                data: {},
                success: function (data) {
                    //hideloader()
                    // checking the rows of csv
                    /!*if(data.length <= 0)
                    {
                        alert('There is no data to download !');
                        return;
                    }*!/

                    let csvContent = "data:text/xls;charset=utf-8,";
                    data.forEach(function (rowArray) {
                        let row = rowArray.join(",");
                        csvContent += row + "\r\n";
                    });
                    var encodedUri = encodeURI(csvContent);
                    var link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    link.setAttribute("download", "Ctc-route-info-" + selected_date + ".csv");
                    document.body.appendChild(link); // Required for FF

                    link.click(); // This will download the data file named "my_data.csv".

                },
                error: function (error) {
                    //hideloader()
                    console.log(error);
                    alert('something went wrong !');
                }
            });

        });
*/

        //javascript function for excel download
        $(document).ready(function(){ var table = $('#datatable').DataTable();
            $('#btnExport').unbind().on('click', function(){
                $('<table>')
                    .append($(table.table().header()).clone())
                    .append(table.$('tr').clone())
                    .table2excel({
                        exclude: "#actiontab",
                        filename: "Route-info",
                        fileext: ".csv",
                        exclude_img: true,
                        exclude_links: true,
                        exclude_inputs: true
                    });  });      })


        //Call to open modal
        $(document).on('click', '.createFlag', function (e) {
            // getting data from button and send to model
            let passing_data = $(this).attr("data-flag_values");
            // showing model and getting el of model
            let model_el = $('#create-flag-modal').modal();
            // setting data to model
            $('#model_flag_data').val(passing_data);
        });

        //Create flag
        $('.can-apply-flag').click(function (e) {
            e.preventDefault();
            let el = $(this);
            //let child_flag_id = el.val();
            let child_flag_id = el.attr("data-id");
            //let order_data = JSON.parse($('#flag_data').val());
            let order_data = JSON.parse($('#model_flag_data').val());
            //getting previous flagged category count
            let previous_flagged_cat_count = $('.flag-tr-cat-bunch-' + child_flag_id).length;
            //let total_flag_cat_count = $('.flag-tr').length;

            // checking child data exist
            if (child_flag_id == '') {
                return false;
            }

            //multiple flagged errors
            let flagged_errors = {
                1: "This order is flagged 2nd time, would you like to re-flag this order",
                2: "This order is flagged 3rd time, would you like to re-flag this order",
                3: "This order is flagged 4th time, would you like to re-flag this order",
                4: "The joey of this order has been terminated already",
            };

            if (previous_flagged_cat_count >= 4) // this block check the total flag orders count
            {
                var confirmatoin = alert(flagged_errors[4]);
                if (!confirmatoin) {
                    location.reload();
                    return;
                }
            }
            if (previous_flagged_cat_count in flagged_errors) // this block check the order is already flagged or not
            {
                var confirmatoin = confirm(flagged_errors[previous_flagged_cat_count]);
                if (!confirmatoin) {
                    return;
                }

            }
            $.confirm({
                title: 'Confirmation',
                content: 'Are you sure you want to create flag?',
                icon: 'fa fa-question-circle',
                animation: 'scale',
                closeAnimation: 'scale',
                opacity: 0.5,
                buttons: {
                    'confirm': {
                        text: 'Proceed',
                        btnClass: 'btn-info',
                        action: function () {
                            showLoader();
                            $.ajax({
                                type: "GET",
                                url: "{{URL::to('/')}}/flag/create/" + child_flag_id,
                                data: order_data,
                                success: function (response) {
                                    hideLoader();
                                    if (response.status == true) // notifying user  the update is completed
                                    {
                                        // getting current url with query string
                                        $current_utl =  window.location.href;
                                        let url_without_query_string = $current_utl.split('?')[0];
                                        // converting query string into jason
                                        let query_json  = urlQueryTOJason($current_utl);
                                        // removeing old message form query string
                                        delete query_json['message'];
                                        // updating new message to query string
                                        query_json['message'] =  response.message;
                                        // creating url string
                                        let url = $.param(query_json);
                                        // redirecting
                                        window.location.href = url_without_query_string+'?'+url;

                                    }
                                    else // update  failed by server
                                    {
                                        // show session alert
                                        ShowSessionAlert('danger', response.message);
                                        $('#create-flag-modal').modal('hide');
                                    }

                                },
                                error: function (error) {
                                    hideLoader();
                                    ShowSessionAlert('danger', 'Something wrong');
                                    $('#create-flag-modal').modal('hide');
                                    console.log(error.responseText);
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

    </script>

    <script>
        $(document).on('click', '.delay', function(e) {

            e.preventDefault();

            var routeId = this.getAttribute('data-route-id');
            $('#route-id').val(routeId);

            $('#delay').modal();
            $('#delay .order-id').text("Route No. : " + routeId);

            return false;
        });


        function mark_delay() {
            let date = $('#delay-date').val();
            let route_id = $('#route-id').val()

            $.ajax({
                type: "POST",
                url: '../route/mark/delay',
                headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                data:{date : date,route_id:route_id},
                success: function (data) {

                    alert('Your route has been mark delay')
                    $('#delay').modal('toggle');

                },
                error: function (error) {
                }
            });
        }
    </script>
@endsection

@section('content')

    <!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left amazon-text">
                    <h3 class="text-center">  Last Mile Route Info<small></small></h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <!--Count Div Row Open-->
            <!--Count Div Row Close-->

            {{--@include('backend.layouts.modal')
            @include( 'backend.layouts.popups')--}}
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <form method="get" action="">
                                <div class="row d-flex align-item-end" style="">
                                    <div class="col-md-3" style="position: relative; min-height: 1px; float: left; padding-right: 10px; padding-left: 10px;">
                                        <label>Search By Date:</label>
                                        <input type="date" name="datepicker" class="data-selector" required=""  value="{{ isset($_GET['datepicker'])?$_GET['datepicker']: date('Y-m-d') }}" placeholder="Search">
                                    </div>
                                    <div class="col-md-3" style="position: relative; min-height: 1px; float: left; padding-right: 10px; padding-left: 10px; margin-top: 25px;">
                                        <button class="btn btn-primary" type="submit" style="margin-top: -3%,4%"> Go</a> </button>
                                    </div>
                                    <div class="col-md-6 sm_custm">
                                        <div class="excel-btn" style="float: right">
                                            <button id="btnExport" class="btn btn-circle buttons-excel buttons-html5 btn-sm btn-primary excelstyleclass">Export to Excel</button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </form>

                            <div class="clearfix"></div>
                        </div>


                        <div class="row dashbords-conts-tiles-main-wrap" id="TorontoCards">
                            <div class="top_tiles montreal-dashbord-tiles" id="Toronto-dashbord-tiles-id">
                                <div class="top_tiles Toronto-dashbord-tiles" id="">
                                    <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 dashbords-conts-tiles-main-wrap">
                                        <div class="tile-stats">
                                            <div class="icon">
                                                <i class="fa fa-cubes"></i>
                                            </div>
                                            <div class="count" id="total_orders">
                                                {{isset( $counts['route_counts'] ) ?  $counts['route_counts'] : 0}}
                                                <h3>Total Routes</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="top_tiles Toronto-dashbord-tiles" id="">
                                    <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 dashbords-conts-tiles-main-wrap">
                                        <div class="tile-stats">
                                            <div class="icon">
                                                <i class="fa fa-cubes"></i>
                                            </div>
                                            <div class="count" id="total_orders">
                                                {{isset(  $counts['TotalOrderDrops'] ) ?  $counts['TotalOrderDrops'] : 0}}
                                                <h3>Total Drop Orders</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="top_tiles Toronto-dashbord-tiles" id="">
                                    <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 dashbords-conts-tiles-main-wrap">
                                        <div class="tile-stats">
                                            <div class="icon">
                                                <i class="fa fa-cubes"></i>
                                            </div>
                                            <div class="count" id="total_orders">
                                                {{isset(  $counts['TotalSortedOrders'] ) ?  $counts['TotalSortedOrders'] : 0}}
                                                <h3>Total Sorted Orders</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="top_tiles Toronto-dashbord-tiles" id="">
                                    <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 dashbords-conts-tiles-main-wrap">
                                        <div class="tile-stats">
                                            <div class="icon">
                                                <i class="fa fa-cubes"></i>
                                            </div>
                                            <div class="count" id="total_orders">
                                                {{isset(  $counts['TotalOrderPicked'] ) ?  $counts['TotalOrderPicked'] : 0}}
                                                <h3>Total Pick Orders</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="top_tiles Toronto-dashbord-tiles" id="">
                                    <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 dashbords-conts-tiles-main-wrap">
                                        <div class="tile-stats">
                                            <div class="icon">
                                                <i class="fa fa-cubes"></i>
                                            </div>
                                            <div class="count" id="total_orders">
                                                {{isset(  $counts['TotalOrderDropsCompleted'] ) ?  $counts['TotalOrderDropsCompleted'] : 0}}
                                                <h3>Total Drop Complete Orders</h3>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="top_tiles Toronto-dashbord-tiles" id="">
                                    <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 dashbords-conts-tiles-main-wrap">
                                        <div class="tile-stats">
                                            <div class="icon">
                                                <i class="fa fa-cubes"></i>
                                            </div>
                                            <div class="count" id="total_orders">
                                                {{isset(  $counts['TotalOrderReturn'] ) ?  $counts['TotalOrderReturn'] : 0}}
                                                <h3>Total Return Orders</h3>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="top_tiles Toronto-dashbord-tiles" id="">
                                    <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 dashbords-conts-tiles-main-wrap">
                                        <div class="tile-stats">
                                            <div class="icon">
                                                <i class="fa fa-cubes"></i>
                                            </div>
                                            <div class="count" id="total_orders">
                                                {{isset(  $counts['TotalOrderNotScan'] ) ?  $counts['TotalOrderNotScan'] : 0}}
                                                <h3>Total Not Scan Orders</h3>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="top_tiles Toronto-dashbord-tiles" id="">
                                    <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 dashbords-conts-tiles-main-wrap">
                                        <div class="tile-stats">
                                            <div class="icon">
                                                <i class="fa fa-cubes"></i>
                                            </div>
                                            <div class="count" id="total_orders">
                                                {{isset(  $counts['TotalOrderUnattempted'] ) ?  $counts['TotalOrderUnattempted'] : 0}}
                                                <h3>Total Unattempt Orders</h3>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <div class="table-responsive">
                                <table id="datatable" class="table table-striped table-bordered">
                                    <thead stylesheet="color:black;">
                                    <tr>
                                        <th class="text-center ">Route#</th>
                                        <th class="text-center ">Joey Name</th>
                                        <th class="text-center ">Broker Name</th>
                                        <th class="text-center ">#of drops</th>
                                        <th class="text-center ">#of sorted</th>
                                        <th class="text-center ">#of picked</th>
                                        <th class="text-center ">#of drops completed</th>
                                        <th class="text-center ">#of Returns</th>
                                        <th class="text-center ">#of Not Scan</th>
                                        <th class="text-center ">#of unattempted</th>
                                        <th class="text-center ">Total Durations</th>
                                        <th class="text-center ">Drops Per Hour</th>
                                        <th id="actiontab">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach( $ctc_info as $record )
                                        <tr>
                                            <td>{{ $record->id }}</td>
                                            <td>
                                                @if($record->joey)
                                                    {{$record->Joey->first_name.' '.$record->Joey->last_name.' ('.$record->Joey->id.')'}}
                                                @else
                                                    {{" "}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($record->joey)
                                                    @if($record->Joey->joeyBrooker)

                                                        @if($record->Joey->joeyBrooker->brooker)
                                                            {{$record->Joey->joeyBrooker->brooker->name}}
                                                        @endif
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{$record->TotalOrderDropsCount()}}</td>
                                            <td>{{$record->TotalSortedOrdersCount()}}</td>
                                            <td>{{$record->TotalOrderPickedCount()}}</td>
                                            <td>{{$record->TotalOrderDropsCompletedCount()}}</td>
                                            <td>{{$record->TotalOrderReturnCount()}}</td>
                                            <td>{{$record->TotalOrderNotScanCount()}}</td>
                                            <td>{{$record->TotalOrderUnattemptedCount()}}</td>
                                            <td> {{$record->EstimatedTime()}}</td>
                                            <td>
                                                @if($record->TotalOrderDropsCompletedCount()!=0 || $record->TotalOrderReturnCount() !=0 )
                                                    {{$record->getDropPerHour()}}
                                                @else
                                                    {{"0"}}
                                                @endif
                                            </td>

                                            <td id="actiontab">

                                                <a href="{{backend_url('ctc/route/'.$record->id.'/edit/hub/17')}}" title="Route Details" target='_blank' class="btn btn-warning btn-xs" style="float: left;">Route Details
                                                </a><br>


                                            <!-- <button class='delay  btn btn-danger btn-xs' data-route-id="{{ $record->id }}" title='MArk Delay'>Mark Delay</button> --><br>

                                                @if (!is_null($record->FlagHistoryByRouteID))
                                                    @if($record->id == $record->FlagHistoryByRouteID->route_id && $record->FlagHistoryByRouteID->is_approved == 0)
                                                        @if(can_access_route('un-flag',$userPermissoins))
                                                            <a href="{{ backend_url('un-flag/'.$record->FlagHistoryByRouteID->id) }}"
                                                               class="btn btn-danger btn-xs">Un Flag Order</a>
                                                        @endif
                                                    @elseif($record->FlagHistoryByRouteID->is_approved == 1)
                                                        <a href="#" class="btn btn-primary btn-xs">Approved</a>
                                                    @endif
                                                @else
                                                    @if(can_access_route('flag.create',$userPermissoins))
                                                        <button
                                                                data-flag_values='{"joey_id":"0","route_id":"{{$record->id}}","flag_type":"route","hub_id":"{{$record->hub}}"}'
                                                                class='btn btn-warning btn-xs createFlag'>
                                                            Mark Flag
                                                        </button>
                                                    @endif
                                                @endif
                                                @php
                                                    $route_id=0;
                                                    $latitude=0;
                                                    $longitude=0;
                                                    $route_id = $record->id;
                                                   $cmap_data=\App\JoeyRouteLocations::maps($route_id);
                                                        $joey_location = \App\JoeyLocation::where('joey_id',$record->joey_id)->OrderBy('id','DESC')->first();

                                                        if(isset($joey_location->latitude)){
                                                             $latitude_value=substr($joey_location->latitude, 0, 8);
                                                            $latitude = intval($latitude_value);
                                                        }
                                                       if(isset($joey_location->longitude)){
                                                            $longitude_value=substr($joey_location->longitude, 0, 9);
                                                            $longitude = intval($longitude_value);

                                                       }
                                                       if(isset($joey_location->joey_id)){
                                                            $joeyID = $joey_location->joey_id;
                                                       }
                                                @endphp
                                                <button type='button' class='orange-gradient btn btn-xs @if($joey_location == null)hidden @else enabled @endif' data-toggle='modal' data-target='#ex5' onclick='initialize({{$route_id}},{{$latitude/1000000}},{{$longitude/1000000}},{{$joey_location}})' title='Map of Whole Route' id="joey-map">See Joey Map</button>
                                                <button type='button' class='orange-gradient btn btn-xs' data-toggle='modal' data-target='#ex5' onclick='initialized({{$route_id}})' title='Map'>Map</button>
                                                <button type='button' class='orange-gradient btn btn-xs @if($cmap_data == '[]')hidden @else enabled @endif' data-toggle='modal' data-target='#ex5' onclick='currentMap({{$route_id}})' title='Map of Current Route' id="cmap">CMap</button>

                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>


                                </table>
                            </div>


                        </div>
                    </div>
                </div>

            </div>


        </div>
    </div>
    <div id="delay" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-body">
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <p><strong class="order-id green"></strong></p>
                    <form method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <label>Please Select Date</label>
                        <input type="date" name="delay_date" id="delay-date" class="data-selector" required=""
                               value="{{ date('Y-m-d') }}"
                        >
                        <input type="hidden" name="route_id" id="route-id">
                        <br>
                        <br>
                        <a type="submit" data-selected-row="false"  onclick="mark_delay()" class="btn btn-success transfer-model-btn">Submit</a>
                        <a class="btn btn-warning " data-dismiss="modal" aria-hidden="true">Close</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="ex5" class='modal fade' role='dialog'>
        <div class='modal-dialog' >

            <div class='modal-content' style="height: auto;width: 96%!important;" class="rounded">
                <div class='modal-header' style="padding-top: 4px;padding-bottom: 4px">
                    <h4 class='modal-title'>Map </h4>
                    <p class='route-id'></p>
                </div>
                <div class='modal-body'>

                    <div id='map5' style=" height: 400px;width: 100%!important;"></div>
                    <a class="btn black-gradient mt-2-1" data-dismiss="modal" aria-hidden="true">Close</a>

                </div>
            </div>
        </div>
    </div>
    <!-- /#page-wrapper -->
    <!--model-for-flagged-open-->
    <div class="modal fade" id="create-flag-modal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create Flag</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-12 hoverable-dropdown-main-wrap">
                                <input type="hidden" id="model_flag_data" value=''>
                                <ul class="hoverable-dropdown-main-ul">
                                    @foreach($flagCategories as $category)
                                        @if($category->isFliterExist('is_show_on_route','1') && $category->isFliterExist('order_type','ecommerce') && $category->isFliterExist('portal','dashboard') && ( $category->isFliterExist('vendor_relation',$ctcVendorIds) || !$category->isFliterExist('vendor_relation')))
                                            <li>
                                                {{$category->category_name}}
                                                <?php $child_data = $category->getChilds->where('is_enable', 1); ?>
                                                @if(!$child_data->isEmpty())
                                                    <i class="fa fa-angle-right"></i>
                                                    <ul class="hoverable-dropdown-ul">
                                                        @foreach($child_data as $child)
                                                            <li data-id="{{$child->id}}"
                                                                class="child-flag-cat">
                                                                {{$child->category_name}}
                                                                <?php $grand_child_data = $child->getChilds->where('is_enable', 1); ?>
                                                                @if(!$grand_child_data->isEmpty())
                                                                    <i class="fa fa-angle-right"></i>
                                                                    <ul class="hoverable-dropdown-ul">
                                                                        @foreach($grand_child_data as $grand_child)
                                                                            <li data-id="{{$grand_child->id}}" class="child-flag-cat can-apply-flag">
                                                                                {{$grand_child->category_name}}
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <!--model-for-flagged-close-->
    <!-- <script src="{{ backend_asset('js/jquery-1.12.4.js') }}"></script>
<script src="{{ backend_asset('js/jquery-ui.js') }}"></script> -->

    <!-- <script src="{{ backend_asset('js/gm-date-selector.css') }}"></script>
<script src="{{ backend_asset('css/bootstrap.css') }}"></script>
<script src="{{ backend_asset('css/bootstrap.js') }}"></script>
 -->

    <!-- <script src="{{ backend_asset('js/bootstrap.js') }}"></script> -->


    {{--Added by Muhammad Raqib @date 16/11/2022--}}
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0" ></script>
    <script>
        function initialized( id) {

            $.ajax({

                url: '../route/' + id + '/map',
                type: 'GET',
                dataType: 'json',
                success: function(data) {

                    // initialize map center on first point

                    $('#ex5 .route-id').text("R-" + id);
                    mapCreated(data);
                },
                error: function(request, error) {

                }
            });

        }
        function currentMap(id) {
            $.ajax({
                url: '../route/' + id + '/remaining',
                type: 'GET',
                dataType: 'json',
                success: function(data) {

                    // initialize map center on first point

                    $('#ex5 .route-id').text("R-" + id);
                    mapCreated(data);
                },
                error: function(request, error) {}
            });
        }
        function mapCreated(data){

            var latlng;
            var geocoder;
            var directionsDisplay;
            var directionsService = new google.maps.DirectionsService();
            var map = null;
            var bounds = null;


            document.getElementById('map5').innerHTML = "";
            directionsDisplay = new google.maps.DirectionsRenderer();

            var bounds = new google.maps.LatLngBounds();

            var latlng = new google.maps.LatLng({
                lat: parseFloat(data[0]['latitude']),
                lng: parseFloat(data[0]['longitude'])
            });

            var myOptions = {
                zoom: 12,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("map5"), myOptions);
            directionsDisplay.setMap(map);
            var infowindow = new google.maps.InfoWindow();

            var marker, i, j = 1;
            var request = {
                travelMode: google.maps.TravelMode.DRIVING
            };
            for (var i = 0; i < data.length; i++) {
                if (data[i]['type'] == "dropoff") {

                    var latlng = new google.maps.LatLng({
                        lat: parseFloat(data[i]['latitude']),
                        lng: parseFloat(data[i]['longitude'])
                    });

                    bounds.extend(latlng);

                    var marker = new google.maps.Marker({
                        position: latlng,
                        map: map,
                        icon: "https://assets.joeyco.com/images/marker/marker_red"+data[i]['ordinal']+".png",
                        title:   "JOEY"
                    });


                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                        return function() {
                            infowindow.setContent("CR-" + data[i]['sprint_id'] + "\n(" + data[i]['address'] + ")");
                            infowindow.open(map, marker);
                        }
                    })(marker, i));

                    if (i == 0) request.origin = marker.getPosition();
                    // else if (i == data['store'].length - 1) request.destination = marker.getPosition();
                    else {
                        if (!request.waypoints) request.waypoints = [];
                        request.waypoints.push({
                            location: marker.getPosition(),
                            stopover: true
                        });
                    }
                    j++;
                }
            }

            // zoom and center the map to show all the markers
            directionsService.route(request, function(result, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(result);
                }
            });

            map.fitBounds(bounds);
            google.maps.event.addDomListener(window, "load", initialize);
        }
        function initialize(route_id,latitude,longitude) {

            $('#ex5 .route-id').text("R-" + route_id);
            mapCreate(latitude,longitude);

            // [START maps_interaction_restricted_mapoptions]
            // new google.maps.Map(document.getElementById("map5"), {
            //     zoom,
            //     center,
            //     minZoom: zoom - 3,
            //     maxZoom: zoom + 3,
            //     restriction: {
            //         latLngBounds: {
            //             north: -10,
            //             south: -40,
            //             east: 160,
            //             west: 100,
            //         },
            //     },
            // });

        }
        //    Map Create
        function mapCreate(latitude,longitude){
            var latlng;
            var geocoder;
            var directionsDisplay;
            var directionsService = new google.maps.DirectionsService();
            var map = null;
            var bounds = null;


            document.getElementById('map5').innerHTML = "";
            directionsDisplay = new google.maps.DirectionsRenderer();

            var bounds = new google.maps.LatLngBounds();

            var latlng = new google.maps.LatLng({
                lat: parseFloat(latitude),
                lng: parseFloat(longitude)
            });
            // setTimeout(() => {
            //     this.map.setZoom(zoom);
            // }, 3000);
            var myOptions = {
                zoom: 12,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("map5"), myOptions);
            directionsDisplay.setMap(map);
            var infowindow = new google.maps.InfoWindow();

            // var marker, i, j = 1;
            var request = {
                travelMode: google.maps.TravelMode.DRIVING
            };
            // for (var i = 0; i < data.length; i++) {
            //     if (data[i]['type'] == "dropoff") {

            var latlng = new google.maps.LatLng({
                lat: parseFloat(latitude),
                lng: parseFloat(longitude)
            });

            bounds.extend(latlng);

            var marker = new google.maps.Marker({
                position: latlng,
                map: map,
                // icon: "https://assets.joeyco.com/images/marker/marker_red1.png",
                icon: "https://assets.joeyco.com/images/map/pins/big/joey.png",

                title:   "JOEY"
            });


            // google.maps.event.addListener(marker, 'click', (function(marker, i) {
            //     return function() {
            //         infowindow.setContent("CR-" + data[i]['sprint_id'] + "\n(" + data[i]['address'] + ")");
            //         infowindow.open(map, marker);
            //     }
            // })(marker, i));

            // if (i == 0)
            request.origin = marker.getPosition();
            // else if (i == data['store'].length - 1)
            request.destination = marker.getPosition();
            // else {
            //     if (!request.waypoints) request.waypoints = [];
            //     request.waypoints.push({
            //         location: marker.getPosition(),
            //         stopover: true
            //     });
            // }
            // j++;
            // }
            // }

            // zoom and center the map to show all the markers
            // directionsService.route(request, function(result, status) {
            //     if (status == google.maps.DirectionsStatus.OK) {
            //         directionsDisplay.setDirections(result);
            //     }
            // });

            map.fitBounds(bounds);
            google.maps.event.addDomListener(window, "load", initialize);
        }
    </script>
    {{--End--}}

@endsection