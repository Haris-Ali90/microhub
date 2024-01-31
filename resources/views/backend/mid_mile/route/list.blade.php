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
                    <h3>Mid Mile Route List<small></small></h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <?php
            if(empty($_REQUEST['date'])){
                $date = date('Y-m-d');
            }
            else{
                $date = $_REQUEST['date'];
            }
            ?>
            {{--@include('backend.layouts.modal')
            @include( 'backend.layouts.popups')--}}
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="col-md-6 c-nop">
                            <form id="filter"  action="" method="get">
                                <div class="col-md-3" style="padding-left:0 !important">
                                    <input id="date" name="date" style="width:35%px" type="date" placeholder="date" value="<?php echo $date ?>"  class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <button  id="search" type="submit" class="btn green-gradient">Submit</button>
                                </div>

                            </form>
                        </div>

                        <div class="col-md-6 text-right" style="padding-right:0 !important">
                            <a href="{{ url('mid/mile/mi/job') }}"  class='btn btn green-gradient actBtn'>Back To Main <i class='fa fa-list'></i></a>
                        </div>
{{--                        <div class="x_title">--}}
{{--                            <button type='button'  class='dselect btn btn green-gradient actBtn' data-toggle='modal' data-target='#ex50' onclick='fullmap()' >Map <i class='fa fa-map'></i></button>--}}

{{--                            <div class="clearfix"></div>--}}
{{--                        </div>--}}


                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <div class="table-responsive">
                                <table id="datatable" class="table table-striped table-bordered">
                                    <thead stylesheet="color:black;">
                                    <tr>
                                        <th>Id</th>
                                        <th>Route No</th>
                                        <th>Driver Id</th>
                                        <th>Stores Count</th>
                                        <th>Bundle Count</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $routeJoeyId=[];
                                    foreach($routes as $key => $route){
                                            $routeLocationCount = JoeyRouteLocations::where('route_id', $route->route_id)->whereNull('deleted_at')->count();
                                            $orderCount = JoeyRouteLocations::getOrderCount($route->route_id, $route->joey_id);
                                            $routeJoeyId[] = $route->joey_id;
                                        ?>
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $route->route_id }}</td>
                                            <td>{{ ($route->joey_id == '') ? 'N/A' : $route->first_name.' '. $route->last_name.' ('. $route->joey_id . ')'  }}</td>
                                            <td>{{ $routeLocationCount }}</td>
                                            <td>{{ $orderCount }}</td>
                                            <td>{{ $route->date }}</td>
                                            <td>
                                                <button class='details green-gradient btn control-size' data-route-id="{{$route->route_id}}" data-joey-id="{{$route->joey_id }}" title='Details'>D</button>
                                                <a class=' orange-gradient btn control-size' target='_blank' href='../../../mid/mile/route/{{$route->route_id}}/edit?date={{$date}}' title='Edit Route'>E</a>
                                                <button class='transfer  black-gradient btn control-size' data-route-id="{{$route->route_id}}" title='Transfer'>T</button>
                                                <button type='button' class=' red-gradient btn control-size' data-toggle='modal' data-target='#ex5' onclick='initialize("{{$route->route_id}}","{{ $date }}")' title='Map of Whole Route'>M</button>
{{--                                                <button type='button' class=' orange-gradient btn control-size' data-toggle='modal' data-target='#ex5' onclick='currentMap("{{$route->route_id}}")' title='Map of Current Route'>CM</button>--}}
                                                <button type='button'  class='delete  red-gradient btn control-size'  data-id="{{$route->route_id}}" title='Delete Route'>R</button>
{{--                                                <button class='reroute  orange-gradient btn control-size' data-re="{{$route->route_id}}"  title='Re Route'>RR</button>--}}
{{--                                                <a class=' orange-gradient btn control-size' target='_blank' href='../route/{{$route->route_id}}/history' title='Route History'>RH</a>--}}
                                            </td>
                                        </tr>
                                    <?php } ?>
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


    <div id="ex5" class='modal fade' role='dialog'>
        <div class='modal-dialog'>

            <div class='modal-content'>
                <div class='modal-header'>
                    <h4 class='modal-title'>Map </h4>
                    <p class='route-id'></p>
                </div>
                <div class='modal-body'>

                    <div id='map5' style="width: 430px; height: 380px;"></div>
                    <a class="btn black-gradient" data-dismiss="modal" aria-hidden="true">Close</a>

                </div>
            </div>
        </div>
    </div>


    <div id="details" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-body">
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <p><strong class="order-id green"></strong></p>
                    <p><strong class="count orange"></strong></p>

                    <div id="rows"></div>
                    <div class="modal-footer">
                        <a class="btn black-gradient" data-dismiss="modal" aria-hidden="true">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="transfer" class="modal" role="dialog" aria-hidden="true">
        <div class="modal-body">
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <p><strong class="order-id green"></strong></p>
                    <form action='../route/transfer' method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <label>Please Select a Joey</label>
                        <select name="joey_id" id="joey_id" class="form-control select2 chosen-select">
                            <?php

                            $joeys = Joey::whereNull('deleted_at')
                                ->where('is_enabled', '=', 1)
                                ->whereNull('email_verify_token')
                                ->whereNOtNull('plan_id')
                                ->orderBy('first_name')
                                ->get();

//                            echo "<option value='' required='required'>Please Select Joey</option>";

                            foreach ($joeys as $joey) {
                                echo "<option value=" . $joey->id . ">" . $joey->first_name . " " . $joey->last_name . "(" . $joey->id . ")</option>";
                            }
                            ?>
                        </select>
                        <input type="hidden" name="route_id" id="route-id">
                        <br>
                        <a type="submit" data-selected-row="false"  onclick="transfer()" class="btn green-gradient transfer-model-btn">Transfer</a>

                        <a class="btn black-gradient" data-dismiss="modal" aria-hidden="true">Close</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- map model -->
    <div id="ex5" class='modal fade' role='dialog'>
        <div class='modal-dialog'>

            <div class='modal-content'>
                <div class='modal-header'>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Map</h4>
                    <p class="route-id"></p>
                </div>
                <div class='modal-body'>

                    <div id='map5' style="width: 570px; height: 380px;" ></div>
                    <a class="btn black-gradient" data-dismiss="modal" aria-hidden="true">Close</a>
                </div>
            </div>    </div>
    </div>
    <script>

        $(document).ready(function() {

            $('#joey_id').select2({
                dropdownParent: $("#transfer")
            });

            $('#zone').select2({
                maximumSelectionLength: 1
            });
            // $("map5").empty();
        });

        arr=['blu-blank','blu-circle','blu-diamond','blu-square','blu-stars',
            'red-blank','red-circle','red-diamond','red-square','red-stars',
            'grn-blank','grn-circle','grn-diamond','grn-square','grn-stars',
            'pink-blank','pink-circle','pink-diamond','pink-square','pink-stars',
            'purple-blank','purple-circle','purple-diamond','purple-square','purple-stars',
            'wht-blank','wht-circle','wht-diamond','wht-square','wht-stars'];



        function Routemap(data) {
            if(data['data'].length==1)
            {
                var mapmarker=1;
            }
            else
            {
                var mapmarker=0;
            }
            var latlng;
            var geocoder;
            var directionsDisplay;
            var directionsService = new google.maps.DirectionsService();
            var map = null;
            var bounds = null;


            document.getElementById('map50').innerHTML = "";
            directionsDisplay = new google.maps.DirectionsRenderer();

            var bounds = new google.maps.LatLngBounds();

            var latlng = new google.maps.LatLng({
                lat: parseFloat(data['data'][0][0]['latitude']),
                lng: parseFloat(data['data'][0][0]['longitude'])
            });

            var myOptions = {
                zoom: 12,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("map50"), myOptions);
            directionsDisplay.setMap(map);
            var infowindow = new google.maps.InfoWindow();

            var marker, i, j = 1;
            var request = {
                travelMode: google.maps.TravelMode.DRIVING
            };
            for (var i = 0; i < data['data'].length; i++) {
                for (var k = 0; k < data['data'][i].length; k++) {
                    //  if (data[i]['type'] == "dropoff") {

                    var latlng = new google.maps.LatLng({
                        lat: parseFloat(data['data'][i][k]['latitude']),
                        lng: parseFloat(data['data'][i][k]['longitude'])
                    });
                    var a="R-"+data['data'][i][k]['route_id']+"\nCR-"+data['data'][i][k]['sprint_id']+"\n("+data['data'][i][k]['address']+")";
                    bounds.extend(latlng);
                    if(mapmarker==1)
                    {
                        var icon_marker="https://assets.joeyco.com/images/marker/marker_red"+(k+1)+".png";
                    }
                    else
                    {
                        var icon_marker="http://maps.google.com/mapfiles/kml/paddle/"+arr[i%30]+".png";
                    }
                    var marker = new google.maps.Marker({
                        position: latlng,
                        map: map,
                        icon: icon_marker,
                        //"http://maps.google.com/mapfiles/kml/paddle/"+arr[i%30]+".png",
                        //"https://assets.joeyco.com/images/marker/marker_red"+(k+1)+".png",
                        title:   "R-"+data['data'][i][k]['route_id']+"\nCR-"+data['data'][i][k]['sprint_id']+"\n("+data['data'][i][k]['address']+")"
                    });


                    google.maps.event.addListener(marker, 'click', (function(marker, j) {
                        return function() {
                            infowindow.setContent(marker.title);
                            infowindow.open(map, marker);
                        }
                    })(marker, j));

                    if (k == 0) request.origin = marker.getPosition();
                    // else if (i == data['store'].length - 1) request.destination = marker.getPosition();
                    else {
                        if (!request.waypoints) request.waypoints = [];
                        request.waypoints.push({
                            location: marker.getPosition(),
                            stopover: true
                        });
                    }
                    j++;
                    //    }
                }
            }

            // zoom and center the map to show all the markers
            directionsService.route(request, function(result, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(result);
                }
            });

            map.fitBounds(bounds);
            //  google.maps.event.addDomListener(window, "load", initialize);
        }


        function fullmap() {
            var directionsService = new google.maps.DirectionsService();
            document.getElementById('map50').innerHTML = "";
            directionsDisplay = new google.maps.DirectionsRenderer();
            var bounds = new google.maps.LatLngBounds();
            <?php    if(empty($_REQUEST['date'])){
                $date= date('Y-m-d');
            }
            else{
                $date= $_REQUEST['date'];
            }  ?>
            $.ajax({

                url : '../../../../backend/ctc/allroute/0/location/joey?date=<?php echo $date; ?>',
                type : 'GET',
                dataType:'json',
                success : function(data) {
                    console.log(data);
                    // initialize map center on first point
                    if(data['key'].length==1)
                    {
                        var mapmarker=1;
                    }
                    else
                    {
                        var mapmarker=0;
                    }


                    for(var i=0;i<data['key'].length;i++)
                    {
                        if(i==0)
                        {
                            document.getElementById('mdd').innerHTML="";
                        }


                        document.getElementById('mdd').innerHTML =document.getElementById('mdd').innerHTML+"<div id='ooo'><input type='checkbox' data-id='"+data['key'][i]+"'  class='delete_check cb-element'  name='del[]'  >  <label class='sc-VigVT hFIOIT' id='"+data['key'][i]+"' for='unselect_all'>R-"+data['key'][i]+"</label></div>";

                    }
                    var latlng = new google.maps.LatLng({
                        lat:parseFloat(data['data'][0][0]['latitude']),
                        lng:parseFloat(data['data'][0][0]['longitude'])
                    });


                    var myOptions = {
                        zoom: 20,
                        center: latlng,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    };

                    map = new google.maps.Map(document.getElementById("map50"), myOptions);
                    directionsDisplay.setMap(map);
                    var infowindow = new google.maps.InfoWindow();

                    var marker,k=0;

                    var request = {
                        travelMode: google.maps.TravelMode.DRIVING
                    };

                    for(var i=0;i< data['data'].length; i++){
                        // if(data['hub'][i]['type']=="dropoff"){
                        for(var j=0;j<data['data'][i].length;j++){


                            var latlng = new google.maps.LatLng({lat:parseFloat(data['data'][i][j]['latitude']),
                                lng:parseFloat(data['data'][i][j]['longitude'])});
                            bounds.extend(latlng);
                            var a="R-"+data['data'][i][j]['route_id']+"\nCR-"+data['data'][i][j]['sprint_id']+"\n("+data['data'][i][j]['address']+")";
                            if(mapmarker==1)
                            {
                                var icon_marker="https://assets.joeyco.com/images/marker/marker_red"+(j+1)+".png";
                            }
                            else
                            {
                                var icon_marker="http://maps.google.com/mapfiles/kml/paddle/"+arr[i%30]+".png";
                            }
                            var marker = new google.maps.Marker({
                                position: latlng,
                                map: map,
                                icon:icon_marker,
                                title: "R-"+data['data'][i][j]['route_id']+"\nCR-"+data['data'][i][j]['sprint_id']+"\n("+data['data'][i][j]['address']+")"
                            });
                            google.maps.event.addListener(marker, 'click', (function(marker, j) {
                                return function() {
                                    infowindow.setContent(marker.title);
                                    infowindow.open(map, marker);
                                }
                            })(marker, j));

                            if (j == 0) request.origin = marker.getPosition();
                                // else if (i == data['store'].length - 1)
                            // request.destination = marker.getPosition();
                            else {
                                if (!request.waypoints) request.waypoints = [];
                                request.waypoints.push({
                                    location: marker.getPosition(),
                                    stopover: true
                                });
                            }

                            k++;
                            // }
                        }
                    }

                    // zoom and center the map to show all the markers
                    directionsService.route(request, function(result, status) {
                        if (status == google.maps.DirectionsStatus.OK) {
                            directionsDisplay.setDirections(result);
                        }
                    });

                    map.fitBounds(bounds);
                    google.maps.event.addDomListener(window, "load", fullmap);
                    google.maps.event.trigger(map, 'resize');


                },
                error : function(request,error)
                {

                }
            });

        }

        //    $('#checkAll').click(function(){
        //       $('.delete_check').each(function(i,item){
        //           if($(this).prop('checked') == false)
        //               $(item).prop('checked',true);
        //           else
        //               $(item).prop('checked',false);
        //       })
        //   })

        $('#checkAll').click(function(){
            $('.cb-element').prop('checked',this.checked);
        });

        $('.cb-element').click(function(){
            if ($('.cb-element:checked').length == $('.cb-element').length){
                $('#checkall').prop('checked',true);
            }
            else {
                $('#checkall').prop('checked',false);
            }
        });

        // $(".rselect").click(function(){
        //
        //     var del_id=[];
        //     $('.delete_check').each(function(){
        //         if($(this).is(':checked')){
        //             var element = $(this);
        //             del_id.push(element.attr("data-id"));
        //         }
        //     });
        //
        //     if(del_id.length==0)
        //     {
        //         alert('Please Select Route Id:');
        //     }
        //     else{
        //         $.ajax({
        //             type: "POST",
        //             url: '../../../ctc/route/map/location',
        //             headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
        //             data:{ids : del_id},
        //             success: function(data){
        //                 data=JSON.parse(data);
        //
        //                 Routemap(data);
        //             }
        //         });
        //     }
        // });


        // function selectSlots(){
        //
        //     $('#slots').empty().trigger("change");
        //     zoneId = $('#zone').val();
        //     $.ajax({
        //
        //         url: '../../../zone/' + zoneId + '/slots',
        //         type: 'GET',
        //         dataType: 'json',
        //         success: function(data) {
        //
        //             console.log(data);
        //             slots="";
        //             data.forEach(function(slot){
        //                 slots+= "<option>SL-"+slot.id+"</option>";
        //             });
        //
        //             $('#slots').html(slots);
        //
        //         },
        //         error: function(request, error) {}
        //     });
        // }

        function initialize(id, date) {

            $.ajax({
                url: '../../../mid/mile/route/' + id + '/map?date='+ date,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#ex5 .route-id').text("R-" + id);
                    mapCreate(data);
                },
                error: function(request, error) {}
            });

        }

        // function currentMap(id) {
        //
        //     $.ajax({
        //         url: '../../../ctc/route/' + id + '/remaining',
        //         type: 'GET',
        //         dataType: 'json',
        //         success: function(data) {
        //             // initialize map center on first point
        //
        //             $('#ex5 .route-id').text("R-" + id);
        //             mapCreate(data);
        //         },
        //         error: function(request, error) {}
        //     });
        // }

        //map create
        function mapCreate(data){

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
                // if (data[i]['type'] == "pickup") {

                    var latlng = new google.maps.LatLng({
                        lat: parseFloat(data[i]['latitude']),
                        lng: parseFloat(data[i]['longitude'])
                    });

                    bounds.extend(latlng);

                    if(data[i]['type'] == 'pickup'){
                        icon = "https://microhub.joeyco.com/backend/pet-store.png";
                    }else{
                        icon = "https://microhub.joeyco.com/backend/default.png"
                    }

                    var marker = new google.maps.Marker({
                        position: latlng,
                        map: map,
                        icon: icon,
                        title:   "JOEY"
                    });


                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                        return function() {
                            infowindow.setContent(data[i]['name'] +"\n("+ data[i]['type'] +")" + "\n(" + data[i]['address'] + ")");
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
                // }
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

        //order detail of route.....
        $(document).on('click', '.details', function(e) {
            e.preventDefault();
            var routeId = this.getAttribute('data-route-id');
            var joeyId = this.getAttribute('data-joey-id');
            let date = document.getElementById('date').value;

            $.ajax({

                url: '../../../mid/mile/route/' +  routeId + '/detail?date=' + date,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var i = 0;
                    var html = "";
                    $('#details .count').text("Count : " + data.routes.length);
                    // let startOrdinal = data.routes[0].ordinal - 1;
                    // let endOrdinal = 0;

                    var heading = "<h4>MR-" + data.reference_id  + "-A</h4>";
                    var type = "<p>Type : start</p>";
                    var name = "<p>Name : " + data.pickUpHub.title + "</p>";
                    var address = "<p>Address : " + data.pickUpHub.start_address + "</p>";
                    html += heading + type + name + address;

                    data.routes.forEach(function(route, index) {
                        // if(route.get_pickup_sprint_tasks != 0){
                        //     console.log(route.get_pickup_sprint_tasks)
                        var heading = "<h4>" + route.id + "</h4>";
                        var hub = "<p>Hub Name : " + route.hub_name + "</p>";
                        var address = "<p>Address : " + route.address + "</p>";
                        var bundleId = "<p>Bundle Id : " + route.bundle_id + "</p>";
                        var noOfOrder = "<p>Order Count : " + route.no_of_order + "</p>";
                        //  var arrival = "<p>Arrival Time : "+route.arrival_time+"</p>";
                        //  var finish = "<p>Finish Time : "+route.finish_time+"</p>";
                        // var distance = "<p>Distance : " + Math.round(route.distance / 1000).toFixed(2); +"KM</p>";
                        //  var duration = "<p>Duration : "+route.duration+"</p>";
                        html += heading + hub + address + bundleId + noOfOrder
                        // +arrival+finish
                        // +
                        // distance;
                        //+duration;
                        i++;
                        //endOrdinal = route.ordinal + 1;
                        // }
                    });
                    var heading = "<h4>MR-" + data.reference_id + "-1</h4>";
                    var type = "<p>Type : dropoff</p>";
                    var name = "<p>Name : " + data.hub.title + "</p>";
                    var address = "<p>Address : " + data.hub.address + "</p>";
                    html += heading + type + name + address;
                    $('#rows').html(html);
                },
                error: function(request, error) {
                    alert("Request: " + JSON.stringify(request));
                }
            });

            $('#details').modal();
            $('#details .order-id').text("R-" + routeId);

            return false;
        });

        //transfer joey model
        $(document).on('click', '.transfer', function(e) {
            e.preventDefault();
            var routeId = this.getAttribute('data-route-id');
            $('#route-id').val(routeId);
            $('#transfer').modal();
            $('#transfer .order-id').text("R-" + routeId);

            return false;
        });

        // transfer route to joey ajax
        function transfer() {
            let joey_id = $('#joey_id').val();
            let route_id = $('#route-id').val()

            if(joey_id  == 'undefined'){
                alert('please select the joey');
            }
            else{

                $.ajax({
                    type: "POST",
                    url: '../../../mid/mile/route/transfer',
                    headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                    data:{joey_id : joey_id,route_id:route_id},
                    success: function (data) {
                        if(data.status)
                        {
                            $('.route-tbl-tr-'+data.body.route_id).find('.routes-td-joye-name').val('update');
                            alert('Your route has been updated '+data.body.route_id+' please refresh your page to see changes')
                        }
                        else
                        {
                            alert('error');
                        }
                    },
                    error: function (error) {
                    }
                });
            }
        }

        //page reload
        // function pageReload() {
        //     location.reload();
        // }

        $( document ).ready(function() {
            window.onclick = function(event) {
                $('#details').modal('hide');
            }
        });

        $( document ).ready(function() {
            setTimeout(() => {   i=$('#datatable').DataTable().rows()
                .data().length;
                console.log(i);

                if(i!=0)
                {
                    $(".right_col").css({"min-height": "auto"});
                } }, 1000);
        });


        //re route
        $(document).on('click', '.reroute', function(e){
            let date = document.getElementById('date').value;
            var $form = $(this);
            $.confirm({
                title: 'A secure action',
                content: 'Are you sure you want to Reroute this route ?',
                icon: 'fa fa-question-circle',
                animation: 'scale',
                closeAnimation: 'scale',
                opacity: 0.5,
                buttons: {
                    'confirm': {
                        text: 'Proceed',
                        btnClass: 'btn-info',
                        action: function () {
                            console.log($form.attr("data-re"));

                            var id = $form.attr("data-re");
                            let hubId = <?php echo auth()->user()->hub_id; ?>;
                            $.ajax({
                                type: "GET",
                                url: '../hub/'+hubId+'/re_route/'+id+'?date='+date,
                                success: function(message){
                                    $.alert(message);
                                    // location.reload();
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

        //delete route
        $(document).on('click', '.delete', function(e){

            var $form = $(this);
            $.confirm({
                title: 'A secure action',
                content: 'Are you sure you want to delete this route ?',
                icon: 'fa fa-question-circle',
                animation: 'scale',
                closeAnimation: 'scale',
                opacity: 0.5,
                buttons: {
                    'confirm': {
                        text: 'Proceed',
                        btnClass: 'btn-info',
                        action: function () {
                            var id = $form.attr("data-id");

                            $.ajax({
                                type: "GET",
                                url: '../route/'+id+'/delete',
                                success: function(message){
                                    $.alert(message);
                                    location.reload();
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

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0"></script>

@endsection