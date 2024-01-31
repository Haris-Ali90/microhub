<?php
use App\Joey;
use App\Vehicle;
use App\SlotsPostalCode;
use App\Slots;
use App\Sprint;
?>
@extends( 'backend.layouts.app' )
@section('title', 'Order Details List')
@section('CSSLibraries')
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css"
          rel="stylesheet"/>
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet"/>
    <link href="{{ backend_asset('libraries/first-mile-hub/index.css') }}" rel="stylesheet">
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
        <div class="custom_us">
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

            <!-- BEGIN SAMPLE FORM PORTLET-->
                <div class="portlet box blue">

                    <div class="portlet-title">
                        <div class="caption">
                            Order Details
                        </div>
                    </div>

                    <div class="portlet-body">
                        <div class="row">

                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_title">
                                        {{-- <h2>Order Details
                                            <small></small>
                                        </h2> --}}

                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <div class="col-md-2 col-sm-2 col-xs-12 profile_left">

                                        </div>
                                        <div class="col-md-12 col-sm-12 col-xs-12">

                                            <div class="" role="tabpanel" data-example-id="togglable-tabs">
                                                <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                                                    <li role="presentation" class="active"><a href="#tab_content1" id="home-tab"
                                                                                              role="tab" data-toggle="tab"
                                                                                              aria-expanded="true">Order Info </a>
                                                    </li>
                                                    <!-- <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Student Document</a>
                                                    </li> -->
                                                </ul>
                                                <div id="myTabContent" class="tab-content">
                                                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content1"
                                                         aria-labelledby="home-tab">

                                                        <!-- start user projects -->
                                                        <table class="table table-bordered">
                                                            <?php foreach($data as $response){
                                                            //dd($response->joey_lastname)
                                                            ?>
                                                            <thead>
                                                            <tr>
                                                                {{-- <th colspan="2" >Task Info </th>--}}
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td style="width: 30%;"><label>Tracking Id</label></td>
                                                                <td>@if($response->tracking_id!=null && str_contains($response->tracking_id, 'old_'))
                                                                        {{substr($response->tracking_id, strrpos($response->tracking_id, '_') + 1).' (Reattempted Order)'}}
                                                                    @else
                                                                        {{$response->tracking_id}}
                                                                    @endif</td>
                                                            </tr>
                                                            <tr>
                                                                <td><label>Merchant Order Num</label></td>
                                                                <td>{{$response->merchant_order_num}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><label>Route No</label></td>
                                                                <td>
                                                                    @if($response->route_id)
                                                                        {{$response->route_id ."-".$response->stop_number}}</td>
                                                                @endif
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <label>Customer Name</label>
                                                                </td>
                                                                <td>{{$response->name}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><label>Customer Phone</label></td>
                                                                <td>{{$response->phone }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><label>Customer Email</label></td>
                                                                <td>{{$response->email }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><label>Customer Address</label></td>
                                                                <td>@if($response->creator_id !=477260 && $response->creator_id!=477282)
                                                                        {{$response->address_line2 }}
                                                                    @else
                                                                        {{$response->address }}
                                                                    @endif
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td><label>Joey</label></td>
                                                                <td>@if($response->joey_id)
                                                                        {{$response->joey_firstname." ".$response->joey_lastname." (".$response->joey_id.")" }}
                                                                    @endif</td>
                                                            </tr>
                                                            <tr>
                                                                <td><label>Joey contact</label></td>
                                                                <td>{{$response->joey_contact }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><label>Merchant</label></td>
                                                                <td>{{$response->merchant_firstname." ".$response->merchant_lastname }}</td>
                                                            </tr>


                                                            <?php

                                                            $statuses = array_merge($response['status'], $response['status1'], $response['status2']
                                                            );

                                                            $sort_key = array_column($statuses, 'created_at');
                                                            $sort_id_key = array_column($statuses, 'id');
                                                            array_multisort($sort_key, SORT_ASC, $statuses);

                                                            // $status_array = array_merge(getStatusCodes('competed'), getStatusCodes('return'));
                                                            if (array_intersect($status_array, $sort_id_key))
                                                            {
                                                            ?>
                                                            <tr>

                                                                <?php
                                                                $image = \App\SprintConfirmation::where('task_id', '=', $response['id'])->whereNotNull('attachment_path')->orderBy('id', 'desc')->first();
                                                                if(!empty($image))
                                                                {
                                                                ?>
                                                                <td><label>Image</label></td>
                                                                <td>
                                                                    <img onclick="ShowLightBox(this);"
                                                                         src="{{$image->attachment_path}}" width='300' height='200'
                                                                         alt={{"CR-".$response['sprint_id']}}/>
                                                                </td>
                                                                <?php
                                                                }
                                                                ?>
                                                            </tr>
                                                            <?php }?>

                                                            <tr>
                                                                <?php
                                                                if($response->tracking_id!=null && str_contains($response->tracking_id, 'old_')){
                                                                    $tracking_id = substr($response->tracking_id, strrpos($response->tracking_id, '_') + 1);
                                                                }
                                                                else{
                                                                    $tracking_id = $response->tracking_id;
                                                                }
                                                                $order_image = \App\OrderImage::where('tracking_id', $tracking_id)->whereNull('deleted_at')->orderBy('id','desc')->first();
                                                                if(!empty($order_image))
                                                                {
                                                                ?>
                                                                <td><label>Additional Image</label></td>
                                                                <td>
                                                                    <img onclick="ShowLightBox(this);"
                                                                         src="{{$order_image->image}}" width='300' height='200'
                                                                         alt={{"CR-".$response['sprint_id']}}/>
                                                                </td>
                                                                <?php
                                                                }
                                                                ?>
                                                            </tr>

                                                            </tbody>
                                                        </table>
                                                        <!-- end user projects -->

                                                        <h5 style="clear:both;text-align:left" class="accordion">
                                                            <button class="btn btn-xs orange-gradient color:#000 !important;">Status
                                                                History
                                                                <i class="fa fa-angle-down"></i></button>
                                                        </h5>
                                                        <table id="main" class="table table-striped table-bordered panel"
                                                               style="display: none">

                                                            <thead>
                                                            <tr>
                                                                <th id="main">Code</th>
                                                                <th id="main">Description</th>
                                                                <th id="main">Date</th>
                                                            </tr>
                                                            </thead>

                                                            <tbody>
                                                            <?php
                                                            // dd($response);

                                                            /*
                                                                                                            foreach ($statuses as $status) {
                                                                                                                echo "<tr>";
                                                                                                                echo "<td>" . $status['id'] . "</td>";
                                                                                                                echo "<td>" . $status['description'] . "</td>";
                                                                                                                echo "<td>" . date("Y-m-d H:i:s", strtotime($status['created_at'])) . "</td>";
                                                                                                                //echo "<td>20".date("y-m-d h:i:s",strtotime($status['created_at']."- 4 hours"))."</td>";
                                                                                                                echo "</tr>";
                                                                                                            }*/
                                                            $firstattempt=[];
                                                            $secondattempt=[];
                                                            $thirdattempt=[];

                                                            if(!empty($response['status2'])){

                                                                $firstattempt = $response['status2'];
                                                                $secondattempt = $response['status'];
                                                                $thirdattempt = $response['status1'];

                                                            }
                                                            else if(!empty($response['status'])){
                                                                $firstattempt = $response['status'];
                                                                $secondattempt = $response['status1'];
                                                            }
                                                            else{
                                                                $firstattempt = $response['status1'];
                                                            }

                                                            echo "<tr class='reattemptrow'>";
                                                            echo "<td colspan='3'><strong>First Attempt</strong></td>";
                                                            echo "</tr>";
                                                            foreach ($firstattempt as $status){
                                                                echo "<tr>";
                                                                echo "<td>".$status['id']."</td>";
                                                                echo "<td>".$status['description']."</td>";
                                                                echo "<td>".$status['created_at']."</td>";
                                                                //echo "<td>20".date("y-m-d h:i:s",strtotime($status['created_at']."- 4 hours"))."</td>";
                                                                echo "</tr>";
                                                            }
                                                            if( $secondattempt)
                                                            {
                                                                echo "<tr class='reattemptrow'>";
                                                                echo "<td colspan='3'><strong>Second Attempt</strong></td>";
                                                                echo "</tr>";
                                                            }

                                                            foreach ( $secondattempt as $status){
                                                                if($status['id']==125 || $status['id']==61){
                                                                    continue;
                                                                }
                                                                echo "<tr>";
                                                                echo "<td>".$status['id']."</td>";
                                                                echo "<td>".$status['description']."</td>";
                                                                echo "<td>".$status['created_at']."</td>";
                                                                echo "</tr>";
                                                            }
                                                            if($thirdattempt)
                                                            {
                                                                echo "<tr class='reattemptrow'>";

                                                                echo "<td colspan='3'><strong>Third Attempt</strong></td>";

                                                                echo "</tr>";
                                                            }
                                                            foreach ( $thirdattempt  as $status){
                                                                if($status['id']==125 || $status['id']==61){
                                                                    continue;
                                                                }
                                                                echo "<tr>";
                                                                echo "<td>".$status['id']."</td>";
                                                                echo "<td>".$status['description']."</td>";
                                                                echo "<td>".$status['created_at']."</td>";
                                                                echo "</tr>";
                                                            }
                                                            ?>
                                                            </tbody>
                                                        </table>
                                                        <?php }?>
                                                    </div>

                                                    <div role="tabpanel" class="tab-pane fade" id="tab_content2"
                                                         aria-labelledby="profile-tab">

                                                        <!-- start user projects -->
                                                        <!--  -->
                                                        <!-- end user projects -->

                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>

                        </div>



                    </div>
                </div>
                <!-- END SAMPLE FORM PORTLET-->
        </div>
    </div>
    <!-- /#page-wrapper -->


    <script type="text/javascript">


        var acc = document.getElementsByClassName("accordion");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.display === "inline-table") {
                    panel.style.display = "none";
                } else {
                    panel.style.display = "inline-table";
                }
            });
        }

    </script>
    <script>
        // Get the modal
        var modal = document.getElementById('myModal');

        // Get the image and insert it inside the modal - use its "alt" text as a caption
        var img = document.getElementById('myImg');
        var modalImg = document.getElementById("img01");
        var captionText = document.getElementById("caption");
        img.onclick = function(){
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
        }

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }
    </script>


@endsection