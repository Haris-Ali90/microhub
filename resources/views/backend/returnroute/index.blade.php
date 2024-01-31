
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet"/>
<link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet"/>

<?php
use App\Vehicle;
use App\StatusMap;
$user = Auth::user();
if($user->email!="admin@gmail.com")
{

    $data = explode(',', $user['rights']);
    $dataPermission = explode(',', $user['permissions']);
}

else{
    $data = [];
    $dataPermission=[];
}
?>
@extends( 'backend.layouts.app' )

@section('title', 'Custom Routing')

@section('CSSLibraries')

@endsection
<style>
    .pac-container.pac-logo {
        z-index: 9999;
    }
    body.nav-md {
        padding: 0PX !IMPORTANT;
    }
    button.btn.btn {
        color: #333;
    }
    button.btn.btn-primary.bootprompt-accept {

        background-color: #c6dd38;
        border-color: #c6dd38;
    }

    .loader {
        position: relative;
        right: 0%;
        top: 0%;
        justify-content: center;
        text-align: center;
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
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    td.ab .view {
        display: none !important;
    }
    .loader-iner-warp {
        position: relative;
        width: 100%;
        text-align: center;
        top: 40%;
    }
    @media (min-width: 300px) {
        div#datatable_length label {
            padding: 5px;
        }

    }
    @media (max-width: 300px) {
        #trackID {
            width: 100% !important;
        }
    }
    .confirmation-text {
        text-align: left;
        font-size: 14px;
    }
    hr {
        margin-top: 1px !important;
        margin-bottom: 1px !important;
        border: 0;
        border-top: 1px solid #eee;
    }
    .text-right
    {
        margin-top: 6px;
    }
    .trackID
    {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }
    .c_date
    {
        padding-left: 0px !important;
        padding-right: 0px !important;
    }
    .lable_text
    {
        padding-right: 5px !important;

    }




</style>
@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://unpkg.com/bootprompt@6.0.2/bootprompt.js"></script>


@endsection


@section('content')
    <div class="right_col" role="main">

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


            <div class="">
                <div class="page-title">
                    <div class="title_left amazon-text">

                    </div>
                </div>

                <div class="clearfix"></div>
                <!--Count Div Row Open-->
                <div class="">

                    {{--@include('backend.layouts.modal')
                    @include( 'backend.layouts.popups')--}}
                    <div class="row"><!---row-->

                        <div class="col-md-12 col-sm-12 col-xs-12">  <!---col-md-12 col-sm-12 col-xs-12-->

                            <div class="x_panel"> <!---x_panel-->

                                <div class="x_title">   <!---x_title-->

                                    <h1 style="position: relative;padding-right: 116px;"><b>Return Custom Routing </b> <button   style="position: absolute;right: 0px;" data-id='{{$id}}'class='btn btn clearOrder' >Clear Order </button></h1>

                                    <div class="clearfix">  <!---clearfix-->

                                    </div> <!---clearfix end-->

                                </div>

                                <!---x_title end-->

                                <div class="x_title">
                                    <!---x_title start-->
                                    <input type="hidden" class ="address" class='google-address' >
                                    <input type="hidden" class ="hub_id" name="hub_id" value="{{$id}}">
                                    <div class="col-md-6 col-sm-6 trackID" >
                                        <div class="col-md-4 col-sm-4 trackID">
                                            <input id="trackID" name="trackID" type="text"  placeholder='Tracking id....'  class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 c_date">
                                        <div class="col-md-8 col-sm-8 text-right c_date">
                                            <label class='lable_text'>Please Select The Date Before Scanning The Tracking Id</label>
                                        </div>
                                        <div class="col-md-4 col-sm-4 c_date">
                                            <input type="date" class ="form-control" id="date"  min="{{ date('Y-m-d')}}" name="date" value="{{$date}}">
                                        </div>
                                    </div>



                                    <div class="clearfix">
                                        <!---clearfix start-->
                                    </div>
                                    <!---clearfix end-->
                                </div>
                                <!---x_title end-->
                                <div class="x_content">
                                    <!---x_content start-->
                                    @include( 'backend.layouts.notification_message' )

                                    <div class="table-responsive">
                                        <!---table-responsive start-->
                                        <div class="loader-background" style="
    position: fixed;
    top: 0px;
    left: 0px;
    z-index: 9999;
    width: 100%;
    height: 100%;
    background-color: #000000ba; display: none "
                                        >
                                            <div class="loader-iner-warp">
                                                <div class="loader" style="display: none" ></div>
                                            </div>
                                        </div>
                                        <table class="table table-striped table-bordered"  border="1" style="overflow-x: unset;">
                                            <thead>
                                            <tr>
                                                <th>Total Tracking Id </th>
                                                <th>Valid Tracking Id</th>
                                                <th>Invalid Tracking Id </th>
                                                <!-- <th>No of joeys </th>
                                                <th>Joey Capacity </th> -->
                                                <th>Action </th>

                                            </tr>
                                            </thead><tbody>
                                            <tr>

                                                <td id='file_total_order'>
                                                    {{$total_count}}</td>
                                                <td id="file_total_valid_order" >{{$valid_id}}</td>
                                                <td id="file_total_invalid_order">{{$total_count-$valid_id}}</td>
                                                <input type="hidden" name="joey_route_detail_count" id="joey_route_detail_count"  value={{$joey_route_detail_count}} min=1>
                                                <!-- <td><input type="number" name="joey_capacity" value=1 min=1></td>  -->
                                                <td> <button   style="" class='btn btn createRoute' >Create Job Id </button> </td>


                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!---table-responsive end-->
                                </div>

                                <button style="" class='btn btn addRow'>Add Joey Vehicle</button>

                                <div class="x_title">   <!---x_title-->

                                    <h2><br> <b>Joeys Vehicle Detail </b></br> </h2>

                                </div>


                                <div class="clearfix">  <!---clearfix-->

                                </div> <!---clearfix end-->



                                <div class="x_content">
                                    <!---x_content start-->
                                    <div class="table-responsive" style="overflow-x: unset;">
                                        <!---table-responsive start-->
                                        <table class="table table-striped table-bordered"   id="routedetailtable" border="1">
                                            <thead>
                                            <tr>
                                                <th>Vehicle </th>
                                                <th> Joeys </th>
                                                <!-- <th>Custom Capacity </th> -->

                                                <th>  Action </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php

                                            foreach($joey_route_detail as $data)
                                            {
                                                echo "<tr>";

                                                echo "<td>";
                                                if($data->vehicle_id==1)
                                                {
                                                    echo "Bicycle</td>";
                                                }
                                                else if($data->vehicle_id==2)
                                                {
                                                    echo "Scooter</td>";
                                                }
                                                else if($data->vehicle_id==3)
                                                {
                                                    echo "Car</td>";

                                                }
                                                else if($data->vehicle_id==4)
                                                {
                                                    echo "Truck</td>";

                                                }
                                                else if($data->vehicle_id==5)
                                                {
                                                    echo "SUV</td>";
                                                }
                                                else
                                                {
                                                    echo "Van</td>";
                                                }

                                                echo "<td>".$data->joeys_count."</td>";
                                                echo "<td><button style='' class='btn btn editrow' data-id='".$data->id."' >Edit</button><button data-id='".$data->id."' style=' background: #e46d28;' class='btn btn removerow'>Remove</button></td>";
                                                echo "</tr>";

                                            }
                                            ?>
                                            <!-- <tr ><td ><span class='view'>Bicycle</span><select style='display: none;' class="form-control" name="vehicle" id="vehicle"><option value="1">Bicycle</option><option value="2">Scooter</option><option value="3">Car</option><option value="4">Truck</option><option value="5">SUV</option><option value="6">Van</option></select></td><td><input type='text' name='joey_count' style='display: none;' value = '1'  ><span class='view'> 1 </span></td><td><button   class='btn btn editrow' >edit</button><button class="btn btn removerow">Remove</button></td></tr> -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!---table-responsive end-->


                                </div>

                                <div class="x_title">   <!---x_title-->
                                    <button type="button" class="btn btn-success btn-md"  style="float:right;margin-bottom: 10px;">Delete Record</button><br>
                                    <h2><br> <b>Scanned Order Details </b></h2>

                                    <div class="clearfix">  <!---clearfix-->

                                    </div> <!---clearfix end-->

                                </div>
                                <div class="x_title">   <!---x_title-->
                                    <input type="checkbox" onClick="selectAll(this)" /> Select All<br/>
                                    <div class="clearfix">  <!---clearfix-->

                                    </div> <!---clearfix end-->
                                </div>
                                <!---x_title end-->
                                <!---x_content end-->
                                <div class="x_content">
                                    <!---x_content start-->
                                    @include( 'backend.layouts.notification_message' )

                                    <div class="table-responsive" >
                                        <!---table-responsive start-->
                                        <table class="table table-striped table-bordered"  id="datatable" border="2">
                                            <thead>
                                            <tr>
                                                <th>Checkbox</th>
                                                <th>Tracking Id </th>
                                                <th>Vendor Id </th>
                                                <th> Name </th>
                                                <th> Phone No </th>
                                                <th> Address </th>
                                                <th> Postal Code </th>
                                                <th style="width: 120px;"> Date </th>
                                                <th style="width: 200px;" >  Reason </th>
                                                <th>  Action </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @php ($tracking_ids = [])
                                            @foreach ($tracking_id_data as $data)
                                                <tr>
                                                    <td><input type='checkbox' name='foo'  class='delete-id' value='{{$data->tracking_id}}'></td>
                                                    <td>{{$data->tracking_id}}</td>
                                                    <td>{{$data->vendor_id}}</td>
                                                    <td>{{$data->name}}</td>
                                                    <td>{{$data->contact_no}}</td>
                                                    <td>{{$data->address}}</td>
                                                    <td>{{$data->postal_code}}</td>
                                                    <td>{{$data->route_enable_date}}</td>
                                                    <td>{{$data->reason}}</td>
                                                    @if($data->valid_id==1)
                                                        @php (array_push($tracking_ids,$data->tracking_id))
                                                        <td><button data-id='{{$data->tracking_id}}' style='background-color: #c6dd38;' class='btn btn edit'>Edit</button> <button data-id='{{$data->tracking_id}}' class='btn btn  remove' style=' background: #e46d28;'>Remove</button></td>
                                                    @elseif($data->valid_id==2)
                                                        <td><button data-id='{{$data->tracking_id}}' style='background-color: #c6dd38;' class='btn btn reattempt'>Create reattempt</button> <button data-id='{{$data->tracking_id}}' class='btn btn  remove' style=' background: #e46d28;'>Remove</button></td>
                                                    @elseif($data->valid_id==3)
                                                        <td><button data-id='{{$data->tracking_id}}' style='background-color: #c6dd38;' class='btn btn updateStatus'>Update Status</button> <button data-id='{{$data->tracking_id}}' class='btn btn  remove' style=' background: #e46d28;'>Remove</button></td>
                                                    @elseif($data->valid_id==4)
                                                        <td> <button data-id='{{$data->tracking_id}}' class='btn btn  remove' style=' background: #e46d28;'>Remove</button></td>
                                                    @elseif($data->valid_id==5)
                                                        <td> <button data-id='{{$data->tracking_id}}' class='btn btn  mark_return_merchant' style=' background: #c6dd38;'>Return To Merchant</button></td>

                                                    @else
                                                        <td>
                                                            <button data-id='{{$data->tracking_id}}'  class='btn btn  remove' style=' background: #e46d28;'>Remove</button></td>

                                                @endif
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!---table-responsive end-->
                                </div>
                                <!---x_content end-->
                            </div>
                            <!---clearfix end-->
                        </div><input type='hidden' id='tracking_idss' name[]='tracking_idss' value='{{json_encode($tracking_ids)}}' >
                    </div>
                    <!---x_panel end-->
                </div>
                <!---col-md-12 col-sm-12 col-xs-12 end-->

            </div>
            <!---row-- end>
       </div>
       <!-- page-wrapper -->

            <!-- /#page-wrapper -->

            <!-- Edit Modal -->
            <div class="modal fade" id="myModaledit" role="dialog">
                <form action="{{ URL::to('custom/create/order')}}" method="post" class="Editorder">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Edit Order</h4>
                            </div>
                            <div class="modal-body">
                                <!-- <label>Vendor:</label> -->
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="tracking_id" id="tracking_id" class="form-control"  required/>

                                <label>Name:</label>
                                <input type="text" name="edit_name" id="edit_name"  placeholder='Name' class="form-control"  required/>
                                <label>Phone no:</label>
                                <input type="text" name="edit_phone" id="edit_phone" placeholder='Phone no'  class="form-control"  required/>
                                <label>Address:</label>
                                <input type="text" name="edit_address"  id="edit_address" class="google-address form-control"  required/>
                                <label>Postal code:</label>
                                <input type="text" name="edit_postal_code" id="edit_postal_code" placeholder='Postal Code' class="form-control"  required/>

                                <br />
                            </div>
                            <div class="modal-footer">
                                <button type="submit" style='background-color: #c6dd38;' class="btn btn">Edit Order</button>
                            </div>
                        </div>

                    </div>

            </div>
            </form>
        </div>
        <!-- Modal end-->

        <!-- Create Job ID  Modal -->
        <!-- Edit Modal -->
        <div class="modal fade" id="RouteDetailModel" role="dialog">
            <form action="{{ URL::to('returncustom/add/joey/count')}}" method="post" class="addRouteDetail">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Add Joey Vehicle </h4>
                        </div>
                        <div class="modal-body">
                            <!-- <label>Vendor:</label> -->
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="tracking_id" id="tracking_id" class="form-control"  required/>

                            <label>Vehicle:</label>
                            <select class="form-control" name="vehicle_id" id="vehicle_id">
                                <option value="1">Bicycle</option>
                                <option value="2">Scooter</option>
                                <option value="3">Car</option>
                                <option value="4">Truck</option>
                                <option value="5">SUV</option>
                                <option value="6">Van</option></select>
                            <label>Joeys Count:</label>
                            <input type="number" name="joey_c" id="joey_c" min='1' placeholder='Joey Count' class="form-control"  required/>


                            <br />
                        </div>
                        <div class="modal-footer">
                            <button type="submit" style='background-color: #c6dd38;' class="btn btn">Add Joey Vehicle</button>
                        </div>
                    </div>

                </div>

        </div>
        </form>
    </div>
    <!-- Modal end-->
    <!-- Edit Modal -->
    <div class="modal fade" id="editRouteDetailModel" role="dialog">
        <form action="{{ URL::to('returncustom/edit/joey/count')}}" method="post" class="editRouteDetail">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit Joey Vehicle</h4>
                    </div>
                    <div class="modal-body">
                        <!-- <label>Vendor:</label> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="edit_id" id="edit_id" class="form-control"  required/>

                        <label>Vehicle:</label>
                        <select class="form-control" name="edit_vehicle_id" id="edit_vehicle_id">
                            <option value="1">Bicycle</option>
                            <option value="2">Scooter</option>
                            <option value="3">Car</option>
                            <option value="4">Truck</option>
                            <option value="5">SUV</option>
                            <option value="6">Van</option></select>
                        <label>Joeys Count:</label>
                        <input type="number" name="edit_joey_c" id="edit_joey_c" min='1' placeholder='Joey Count' class="form-control"  required/>


                        <br />
                    </div>
                    <div class="modal-footer">
                        <button type="submit" style='background-color: #c6dd38;' class="btn btn">Edit Joey Vehicle</button>
                    </div>
                </div>

            </div>

    </div>
    </form>
    </div>
    <!-- Modal end-->
    <!-- Modal end-->
    <div class="modal fade" id="reattemptOrder" role="dialog">
        <form action="" method="post" class="reattemptOrder">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header" style="padding: 10px 15px 3px !important;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="modal-title">Are you sure you want to reattempt this Tracking id?</div>
                    </div>
                    <div class="modal-body" style="padding: 0px 15px 0 !important;">
                        <!-- <label>Vendor:</label> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="_tracking_id" name="reattempt_tracking_id" >
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary btn-default bootprompt-cancel" data-dismiss="modal">Cancel</button>
                        <button type="submit"  class="btn btn-primary bootprompt-accept">OK</button></div></div>

            </div>

    </div>

    </div>
    </form>
    </div>

    <div class="modal fade" id="updateStatus" role="dialog">
        <form action="{{ URL::to('custom/create/order')}}" method="post" class="updateStatusModel">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header" style="padding: 10px 15px 3px;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <div class="modal-title">Update Status Of Tracking ID</div>
                    </div>
                    <div class="modal-body" style="padding: 10px 15px 10px;" >
                        <!-- <label>Vendor:</label> -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="update_tracking_id" id="update_tracking_id" class="form-control" />
                        <label>Tracking ID:</label>
                        <label style="font-weight: 300" id="update_tracking_"></label>
                        <br>
                        <label>Select Status:</label>
                        <select class="form-control" name="status_id" id="status_id" required>
                            @foreach($returnStatus as $key => $status_id)
                                <option value='{{$key}}'> {{$status_id}} </option>
                            @endforeach

                        </select>
                    </div>
                    <div class="modal-footer" style="padding: 10px 15px 3px;">
                        <button type="submit" style='background-color: #c6dd38;' class="btn btn">Update</button>
                    </div>
                </div>

            </div>

    </div>
    </form>
    </div>



    @include( 'backend.layouts.notification_message' )
@endsection


@section('inlineJS')

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0&libraries=places" type="text/javascript" ></script>
    <script >
        var Tracking_ids=[];
        var valid_count=0;
        var context=null;
        $(document).ready(function(){
            var  count=$('#joey_route_detail_count').val();
            var  val_count=$('#file_total_valid_order').text();
            if(count==0 || val_count==0)
            {
                $(".createRoute").prop('disabled', true);
            }
            else
            {
                $(".createRoute").prop('disabled', false);
            }

        });

        $(document).on('click','.reattempt',function(){
            let element = $(this);
            context=element;
            let id = element.attr("data-id");
            $('#_tracking_id').val(id);
            $('#reattemptOrder').modal();

        });


        $(document).on('click','.updateStatus',function(){
            let element = $(this);
            context=element;

            let id = element.attr("data-id");
            $('#update_tracking_id').val(id);
            $("#update_tracking_").html(id);
            $('#updateStatus').modal();

        });

        function selectAll(source) {

            checkboxes = document.getElementsByName('foo');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }
        function initialize() {

            // clearOrder
            $(document).on('click','.clearOrder',function(){
                element = $(this);
                var tracking_ids=$('#tracking_idss').val();
                var del_id = element.attr("data-id");
                bootprompt.confirm({

                    message: "Are you sure you want to remove Order that are in route?",
                    callback: (result) => {

                        if(result)
                        {
                            $(".loader").show();
                            $(".loader-background").show();
                            $.ajax({
                                type: "post",
                                url: "{{ URL::to('returnremove/order/inroute')}}",
                                data:{id:del_id,tracking_ids:tracking_ids},
                                beforeSend: function (request) {
                                    return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                                },
                                success: function (data) {
                                    $(".loader").hide();
                                    $(".loader-background").hide();
                                    if(data.status_code==200)
                                    {
                                        bootprompt.alert({
                                            message: "Routed orders removed from custom routing  successfully.",
                                            callback: (result) => {  location.reload();/* result is a boolean; true = OK, false = Cancel*/ }
                                        });
                                    }
                                    else
                                    {
                                        bootprompt.alert('No Order to clear.');
                                    }


                                },
                                error:function (error) {
                                    $(".loader").hide();
                                    $(".loader-background").hide();

                                    bootprompt.alert('some error');
                                }
                            });


                        }
                    }
                });
            });





            // Joey Count for Route edit button function
            $(document).on('click','.editrow',function(){
                element = $(this);
                context=element;
                var del_id = element.attr("data-id");
                $.ajax({
                    type: "get",
                    url: "{{ URL::to('returncustom/joey/count')}}",
                    data:{id:del_id},
                    success: function (data) {
                        if(data.status_code==200)
                        {
                            $('#edit_vehicle_id').val(""+data.vehicle_id);
                            $('#edit_joey_c').val(""+data.joeys_count);
                            $('#edit_id').val(""+data.id);
                            //editRouteDetailModel
                            $('#editRouteDetailModel').modal();
                        }
                        else
                        {

                            bootprompt.alert(data['error']);
                        }



                    },
                    error:function (error) {

                        bootprompt.alert("some error");

                    }
                });

            });
            // Joey Count for Route add Row button function
            $(document).on('click','.addRow',function(){
                element = $(this);
                context=element;
                //
                $('#RouteDetailModel').modal();

            });
            // Joey Count for Route remove Row button function
            $(document).on('click','.removerow',function(){
                element = $(this);
                var del_id = element.attr("data-id");
                bootprompt.confirm({

                    message: "Are you sure you want to remove this?",
                    callback: (result) => {
                        if(result)
                        {
                            $.ajax({
                                type: "post",
                                url: "{{ URL::to('returnremove/joeycount')}}",
                                data:{id:del_id},
                                beforeSend: function (request) {
                                    return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                                },
                                success: function (data) {
                                    $(".loader").hide();
                                    $(".loader-background").hide();
                                    var count=  $('#joey_route_detail_count').val();
                                    if(count==1)
                                    {
                                        count--;
                                        $(".createRoute").prop('disabled', true);
                                        $('#joey_route_detail_count').val(count);
                                    }
                                    else
                                    {
                                        count--;
                                        $('#joey_route_detail_count').val(count);
                                    }

                                    $('#routedetailtable').DataTable().row(element.parents('tr') ).remove().draw();

                                    //    location.reload();
                                    // bootprompt.alert("Job Created and Job Id is "+data['Job_id']);




                                },
                                error:function (error) {
                                    $(".loader").hide();
                                    $(".loader-background").hide();

                                    bootprompt.alert('some error');
                                }
                            });


                        }
                    }
                });

                $(".addRow").prop('disabled', false);
            });

            //Create Job id function
            $(document).on('click','.createRoute',function(){

                //   alert('dd');
                $(".loader").show();
                $(".loader-background").show();

                let hub_id=document.getElementsByName('hub_id')[0].value;
                $.ajax({
                    type: "post",
                    url: "{{ URL::to('returncreate/route/custom/routing')}}",
                    data:{hub_id:hub_id},
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                    },
                    success: function (data) {
                        $(".loader").hide();
                        $(".loader-background").hide();
                        if(data['status_code']==200)
                        {


                            bootprompt.alert("Job Created and Job Id is "+data['Job_id'], () => {
                                // location.reload();
                            });


                        }
                        else
                        {

                            bootprompt.alert(data['error'], () => {
                                // location.reload();
                            });

                        }



                    },
                    error:function (error) {
                        $(".loader").hide();
                        $(".loader-background").hide();

                        bootprompt.alert('some error');
                    }
                });
            });
            // Craete order button function
            $(document).on('click','.createOrder',function(){
                element = $(this);
                context=element;
                // element.closest("tr .edit").remove();
                var acInputs = document.getElementsByClassName("google-address");

                for (var i = 0; i < acInputs.length; i++) {

                    var autocomplete = new google.maps.places.Autocomplete(acInputs[i],null);
                    autocomplete.setComponentRestrictions({
                        country: ["ca"],
                    });
                    autocomplete.inputId = acInputs[i].id;
                }


                let hub_id=document.getElementsByName('hub_id')[0].value;

                var del_id = element.attr("data-id");
                if(hub_id==19)
                {
                    const regex = new RegExp('^(J|j)(O|o)(E|e)(Y|y)(C|c)(O|o)');
                    const ctc_regex = new RegExp('^(J|j)(Y|y)');

                    // if(regex.test(del_id))
                    // {
                    $.ajax({
                        url: "{{ URL::to('get/ctc/vendors')}}",
                        type: "GET",
                        cache:false,
                        data:{hub_id:hub_id,is_true:regex.test(del_id),is_true_ctc:ctc_regex.test(del_id)},
                        beforeSend: function (request) {
                            return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                        },
                        success:function(response){

                            var vendorselect = $("#vendorselect").empty();

// Build list
                            vendorselect.append("<option value=''>Select Vendor</option>");
                            response.vendors.forEach((vendor)=>
                            {
                                vendorselect.append($("<option/>").attr("value",vendor.id).text(vendor.first_name+" "+vendor.last_name+" ("+vendor.id+")"));
                            });



                        }
                    });
                    // }
                }
                $('#tracking_Id').val(''+del_id);
                $('#myModal').modal();
            });
            // Edit Order Function
            $(document).on('click','.edit',function(){

                element = $(this);
                context=element;
                // element.closest("tr .edit").remove();
                var acInputs = document.getElementsByClassName("google-address");

                for (var i = 0; i < acInputs.length; i++) {

                    var autocomplete = new google.maps.places.Autocomplete(acInputs[i],null);
                    autocomplete.setComponentRestrictions({
                        country: ["ca"],
                    });
                    autocomplete.inputId = acInputs[i].id;
                }

                currentRow=element.closest("tr");
                var name=currentRow.find("td:eq(3)").text();
                var phone=currentRow.find("td:eq(4)").text();
                var address =currentRow.find("td:eq(5)").text();
                var postal_code=currentRow.find("td:eq(6)").text();

                var del_id = element.attr("data-id");
                $('#tracking_id').val(''+del_id);
                $('#edit_name').val(''+name);
                $('#edit_phone').val(''+phone);
                $('#edit_address').val(''+address);
                $('#edit_postal_code').val(''+postal_code);

                $('#myModaledit').modal();

            });
            //removing multiple order at singal time

            $(".btn-success").click(function(){

                var id = [];
                var contect_array=[];
                $(".delete-id:checked").each(function(){
                    id.push($(this).val());
                    element = this;
                    contect_array.push($(this));
                });


                if (id.length > 0) {
                    if (confirm("Are you sure want to delete this records")) {
                        $.ajax({
                            url: "{{ URL::to('returnremove/multipletrackingid')}}",
                            type: "POST",
                            cache:false,
                            data:{deleteId:id},
                            beforeSend: function (request) {
                                return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                            },
                            success:function(response){


                                alert("record deleted successfully");
                                for (var i=0; i<contect_array.length; i++) {

                                    $('#datatable').DataTable().row(contect_array[i].parents('tr') ).remove().draw();
                                }

                            }
                        });
                    }
                }else{
                    alert("Please select atleast one checkbox");
                }
            });
// mark_return_merchant
            $(document).on('click','.mark_return_merchant',function(){
                var element = $(this);
                var del_id = element.attr("data-id");
                bootprompt.confirm({

                    message: "Are you sure you want to mark return to merchant  this order?",
                    callback: (result) => { /* result is a boolean; true = OK, false = Cancel*/
                        if(result)
                        {
                            $.ajax({
                                type: "post",
                                url: "{{ URL::to('trackingid/retuned/metchant')}}",
                                data:{Tracking_id:del_id},
                                beforeSend: function (request) {
                                    return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                                },
                                success: function (data) {
                                    $(".loader").hide();
                                    $(".loader-background").hide();
                                    total_count=document.getElementById ( "file_total_order" ).innerText;
                                    total_count--;
                                    document.getElementById ( "file_total_order" ).innerText=total_count;
                                    if(data.valid==1)
                                    {
                                        valid_count=document.getElementById ( "file_total_valid_order" ).innerText;
                                        valid_count--;
                                        if(valid_count==0)
                                        {
                                            $(".createRoute").prop('disabled', true);
                                        }
                                        document.getElementById ( "file_total_valid_order" ).innerText=valid_count;
                                    }
                                    else
                                    {
                                        invalid_count=document.getElementById ( "file_total_invalid_order" ).innerText;
                                        invalid_count--;
                                        document.getElementById ( "file_total_invalid_order" ).innerText=invalid_count;
                                    }
                                    $('#datatable').DataTable().row( element.parents('tr') ).remove().draw();



                                },
                                error:function (error) {
                                    $(".loader").hide();
                                    $(".loader-background").hide();

                                    bootprompt.alert('some error');
                                }
                            });
                        }

                    }
                });

            });



            // Remove Order Function
            $(document).on('click','.remove',function(){
                var element = $(this);
                var del_id = element.attr("data-id");
                bootprompt.confirm({
                    message: "Are you sure you want to remove this order?",
                    callback: (result) => { /* result is a boolean; true = OK, false = Cancel*/
                        if(result)
                        {
                            $.ajax({
                                type: "post",
                                url: "{{ URL::to('returnremove/trackingid')}}",
                                data:{Tracking_id:del_id},
                                beforeSend: function (request) {
                                    return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                                },
                                success: function (data) {
                                    $(".loader").hide();
                                    $(".loader-background").hide();
                                    total_count=document.getElementById ( "file_total_order" ).innerText;
                                    total_count--;
                                    document.getElementById ( "file_total_order" ).innerText=total_count;
                                    if(data.valid==1)
                                    {
                                        valid_count=document.getElementById ( "file_total_valid_order" ).innerText;
                                        valid_count--;
                                        if(valid_count==0)
                                        {
                                            $(".createRoute").prop('disabled', true);
                                        }
                                        document.getElementById ( "file_total_valid_order" ).innerText=valid_count;
                                    }
                                    else
                                    {
                                        invalid_count=document.getElementById ( "file_total_invalid_order" ).innerText;
                                        invalid_count--;
                                        document.getElementById ( "file_total_invalid_order" ).innerText=invalid_count;
                                    }
                                    $('#datatable').DataTable().row( element.parents('tr') ).remove().draw();

                                    //    location.reload();
                                    // bootprompt.alert("Job Created and Job Id is "+data['Job_id']);




                                },
                                error:function (error) {
                                    $(".loader").hide();
                                    $(".loader-background").hide();

                                    bootprompt.alert('some error');
                                }
                            });
                        }

                    }
                })

            });


            var acInputs = document.getElementsByClassName("google-address");

            for (var i = 0; i < acInputs.length; i++) {

                var autocomplete = new google.maps.places.Autocomplete(acInputs[i],null);
                autocomplete.setComponentRestrictions({
                    country: ["ca"],
                });
                autocomplete.inputId = acInputs[i].id;
            }
        }

        initialize();
    </script>
    <script>

        // on Change function for tracking id
        $( "#trackID" ).on( "keydown", function(event) {
            let date=document.getElementsByName('date')[0].value;
            if(event.which == 13)
            {
                let val=$(this).val();
                let hub_id=document.getElementsByName('hub_id')[0].value;
                document.getElementById('trackID').value="";
                let date=document.getElementsByName('date')[0].value;
                if(!findCommonElement(val) && val!="")
                {
                    if(date!="")
                    {
                        document.getElementById('trackID').value="";
                        $.ajax({
                            type: "get",
                            url: "{{ URL::to('returntracking/detail')}}",
                            data:{tracking_id:val,hub_id:hub_id,date:date},
                            success: function (data) {
                                console.log(data)
                                // if(data['status_code']==800)
                                // {
                                //     alert('This order is not for return policy!');
                                //
                                // }
                                if(data['status_code']==200)
                                {
                                    //  $('.odd').remove();

                                    if(data.data.exist==0)
                                    {

                                        total_count=document.getElementById ( "file_total_order" ).innerText;
                                        total_count++;

                                        document.getElementById ( "file_total_order" ).innerText=total_count;
                                        if(data.data.valid==1)
                                        {
                                            var count=  $('#joey_route_detail_count').val();
                                            if(count!=0)
                                            {
                                                $(".createRoute").prop('disabled', false);
                                            }

                                            valid_count=document.getElementById ( "file_total_valid_order" ).innerText;
                                            valid_count++;
                                            document.getElementById ( "file_total_valid_order" ).innerText=valid_count;

                                        }
                                        else
                                        {
                                            invalid_count=document.getElementById ( "file_total_invalid_order" ).innerText;
                                            invalid_count++;
                                            document.getElementById ( "file_total_invalid_order" ).innerText=invalid_count;
                                        }




                                        var value =
                                            {"tracking_id":data.data.tracking_id,"vendor_id":data.data.vendor_id,"name":data.data.name,"phone":data.data.phone,"date":data.data.route_enable_date,"address":data.data.address,"postal_code":data.data.postal_code,
                                                "valid":data.data.valid,"reason":data.data.reason};
                                        $('#datatable').DataTable().row.add(formatItem(value) ).draw();
                                        var acInputs = document.getElementsByClassName("google-address");

                                        for (var i = 0; i < acInputs.length; i++) {

                                            var autocomplete = new google.maps.places.Autocomplete(acInputs[i],null);
                                            autocomplete.setComponentRestrictions({
                                                country: ["ca"],
                                            });
                                            autocomplete.inputId = acInputs[i].id;
                                        }

                                    }



                                }
                                else if(data['status_code']==404)
                                {
                                    bootprompt.alert(data['error']);
                                }
                                else if(data['status_code']==405)
                                {
                                    dialogBox(data,val,hub_id,date);
                                }
                                else if(data['status_code']==406)
                                {
                                    dialogBox(data,val,hub_id,date);
                                }
                                else
                                {

                                    bootprompt.alert(data['error']);
                                }



                            },
                            error:function (error) {

                                bootprompt.alert("some error");

                            }
                        });
                    }
                    else
                    {
                        bootprompt.alert("Date  is required", () => {
                            location.reload();
                        });
                    }


                }
            }

        });

        // function for format item into row in table
        function formatItem(item) {
            if(item.valid==1)
            {
                let tracking=item.tracking_id;

                return ["<input type='checkbox' class='delete-id' value='"+item.tracking_id+"'>",item.tracking_id ,item.vendor_id , item.name, item.phone,item.address, item.postal_code,item.date,item.reason,"<button data-id='"+item.tracking_id+"' style='background-color: #c6dd38;' class='btn btn edit'>Edit</button><button data-id='"+tracking+"' class='btn btn  remove' style=' background: #e46d28;'>Remove</button></td>"];
            }
            else if(item.valid==2)
            {
                return ["<input type='checkbox' class='delete-id' value='"+item.tracking_id+"'>",item.tracking_id ,item.vendor_id, item.name, item.phone,item.address, item.postal_code,item.date,item.reason,"<button data-id='"+item.tracking_id+"' style='background-color: #c6dd38;' class='btn btn reattempt'>Create reattempt</button><button data-id='"+item.tracking_id+"' class='btn btn  remove' style=' background: #e46d28;'>Remove</button></td>"]
            }//updateStatus
            else if(item.valid==3)
            {
                return ["<input type='checkbox' class='delete-id' value='"+item.tracking_id+"'>",item.tracking_id ,item.vendor_id, item.name, item.phone,item.address, item.postal_code,item.date,item.reason,"<button data-id='"+item.tracking_id+"' style='background-color: #c6dd38;' class='btn btn updateStatus'>Update Status</button><button data-id='"+item.tracking_id+"' class='btn btn  remove' style=' background: #e46d28;'>Remove</button></td>"]
            }
            else if(item.valid==4)
            {
                return ["<input type='checkbox' class='delete-id' value='"+item.tracking_id+"'>",item.tracking_id ,item.vendor_id, item.name, item.phone,item.address, item.postal_code,item.date,item.reason,"<button data-id='"+item.tracking_id+"' class='btn btn  remove' style=' background: #e46d28;'>Remove</button></td>"]
            }
            else if(item.valid==5)
            {
                return ["<input type='checkbox' class='delete-id' value='"+item.tracking_id+"'>",item.tracking_id ,item.vendor_id, item.name, item.phone,item.address, item.postal_code,item.date,item.reason,"<button data-id='"+item.tracking_id+"' class='btn btn  mark_return_merchant' style=' background: #c6dd38;'>Return To Merchant</button></td>"]
            }
            else
            {

                // return ["<input type='checkbox' class='delete-id' value='"+item.tracking_id+"'>",item.tracking_id ,item.vendor_id, item.name, item.phone,item.address, item.postal_code,item.date,item.reason,"<button data-id='"+item.tracking_id+"' style='background-color: #c6dd38;' class='btn btn createOrder'>Create</button><button data-id='"+item.tracking_id+"' class='btn btn  remove' style=' background: #e46d28;'>Remove</button></td>"];
            }

        }
        // Create Job ID function
        $(document).on('click','.create',function(){

            $(".loader").show();
            $(".loader-background").show();


            var joey_count=document.getElementsByName('joey_count')[0].value;
            var joey_capacity=document.getElementsByName('joey_capacity')[0].value;
            let hub_id=document.getElementsByName('hub_id')[0].value;
            $.ajax({
                type: "post",
                url: "{{ URL::to('returncreate/route/custom/routing')}}",
                data:{Tracking_ids:Tracking_ids,hub_id:hub_id,joey_count:joey_count,joey_capacity:joey_capacity},
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                },
                success: function (data) {
                    $(".loader").hide();
                    $(".loader-background").hide();
                    if(data['status_code']==200)
                    {


                        bootprompt.alert("Job Created and Job Id is "+data['Job_id'], () => {
                            // location.reload();
                        });


                    }
                    else
                    {

                        bootprompt.alert(data['error'], () => {
                            // location.reload();
                        });

                    }



                },
                error:function (error) {
                    $(".loader").hide();
                    $(".loader-background").hide();

                    bootprompt.alert('some error');
                }
            });
        });





        function findCommonElement(value)
        {
            // Loop for array1
            for(let i = 0; i < Tracking_ids.length; i++) {
                // $(".create").prop('disabled', false);
                if(Tracking_ids[i] ===value ) {
                    return true;
                }

            }

            // Return if no common element exist
            return false;
        }
    </script>
    <script type="text/javascript">

        $(document).ready(function () {


            // $('#datatable').DataTable({
            //     "lengthMenu": [ 50,100, 250, 500, 750, 1000]
            // });

            $(".group1").colorbox({height:"50%",width:"50%"});

            $(document).on('click', '.status_change', function(e){
                var Uid = $(this).data('id');

                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to change user status??',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    btns: {
                        'confirm': {
                            text: 'Proceed',
                            btnClass: 'btn-info',
                            action: function () {

                                $.ajax({
                                    type: "GET",
                                    url: "<?php echo URL::to('/'); ?>/api/changeUserStatus/"+Uid,
                                    data: {},
                                    success: function(data)
                                    {
                                        if(data== '0' || data== 0 )
                                        {
                                            var DataToset = '<btn type="btn" class="btn btn-warning btn-xs status_change" data-toggle="modal" data-id="'+Uid+'" data-target=".bs-example-modal-sm">Blocked</btn>';
                                            $('#CurerntStatusDiv'+Uid).html(DataToset);
                                        }
                                        else
                                        {
                                            var DataToset = '<btn type="btn" class="btn btn-success btn-xs status_change" data-toggle="modal" data-id="'+Uid+'" data-target=".bs-example-modal-sm">Active</btn>'
                                            $('#CurerntStatusDiv'+Uid).html(DataToset);
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

            $(document).on('click', '.form-delete', function(e){

                var $form = $(this);
                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to delete user ??',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    btns: {
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

    </script>
    <script>
        $(document).ready(function() {
            $('#birthday').daterangepicker({
                singleDatePicker: true,
                calender_style: "picker_4"
            }, function(start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
            });
        });
        $(document).ready(function() {

            $('.reattemptOrder').submit(function(event) {

                event.preventDefault();
                $(".loader").show();
                $(".loader-background").show();

                $.ajax({
                    type: "post",
                    url: "{{ URL::to('create/reattempt')}}",
                    data:{tracking_id:$('input[name=reattempt_tracking_id]').val(),hub_id:document.getElementsByName('hub_id')[0].value},
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                    },
                    success: function (data) {
                        $('#reattemptOrder').modal('toggle');
                        $(".loader").hide();
                        $(".loader-background").hide();
                        if(data['status_code']==200)
                        {
                            var  count=$('#joey_route_detail_count').val();

                            if(count==0)
                            {
                                $(".createRoute").prop('disabled', true);
                            }
                            else
                            {
                                $(".createRoute").prop('disabled', false);
                            }
                            valid_count=document.getElementById ( "file_total_valid_order" ).innerText;
                            total_count=document.getElementById ( "file_total_order" ).innerText;
                            valid_count++;
                            document.getElementById ( "file_total_valid_order" ).innerText=valid_count;
                            document.getElementById ( "file_total_invalid_order" ).innerText=total_count-valid_count;


                            var value ={"tracking_id":data.data.tracking_id,
                                "vendor_id":data.data.vendor_id,
                                "name":data.data.name,
                                "phone":data.data.phone,
                                "address":data.data.address,
                                "postal_code":data.data.postal_code,
                                "date":data.data.route_enable_date,
                                "valid":data.data.valid,
                                'reason':data.data.reason};

                            $('#datatable').DataTable().row(context.parents('tr')).data(formatItem(value)).draw();
                            $(window).scrollTop(0);
                            bootprompt.alert("Reattempt created");
                        }
                        else if(data['status_code']==405)
                        {
                            var value ={"tracking_id":data.data.tracking_id,
                                "vendor_id":data.data.vendor_id,
                                "name":data.data.name,
                                "phone":data.data.phone,
                                "address":data.data.address,
                                "postal_code":data.data.postal_code,
                                "date":data.data.route_enable_date,
                                "valid":data.data.valid,
                                'reason':data.data.reason};

                            $('#datatable').DataTable().row( context.parents('tr') ).data( formatItem(value) ).draw();
                            $(window).scrollTop(0);
                            bootprompt.alert(data.data.reason);
                        }
                        else
                        {

                            bootprompt.alert(data['error'], () =>
                            {
                                // location.reload();
                            });

                        }



                    },
                    error:function (error) {
                        $('#reattemptOrder').modal('toggle');
                        $(".loader").hide();
                        $(".loader-background").hide();

                        bootprompt.alert('some error');
                    }
                });
                event.preventDefault();
            });


            // update status
            $('.updateStatusModel').submit(function(event) {
                event.preventDefault();
                $(".loader").show();
                $(".loader-background").show();

                let tracking_id=$("#update_tracking_id").val();
                $.ajax({
                    type: "post",
                    url: "{{ URL::to('update/status/create/reattempt')}}",
                    data:{tracking_id:tracking_id,
                        hub_id:document.getElementsByName('hub_id')[0].value,
                        status_id:$('select[name=status_id]').children("option:selected").val()},
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                    },
                    success: function (data) {
                        $('#updateStatus').modal('toggle');
                        $(".loader").hide();
                        $(".loader-background").hide();
                        if(data['status_code']==200)
                        {
                            var  count=$('#joey_route_detail_count').val();

                            if(count==0)
                            {
                                $(".createRoute").prop('disabled', true);
                            }
                            else
                            {
                                $(".createRoute").prop('disabled', false);
                            }
                            valid_count=document.getElementById ( "file_total_valid_order" ).innerText;
                            total_count=document.getElementById ( "file_total_order" ).innerText;
                            valid_count++;
                            document.getElementById ( "file_total_valid_order" ).innerText=valid_count;
                            document.getElementById ( "file_total_invalid_order" ).innerText=total_count-valid_count;
                            var value ={"tracking_id":data.data.tracking_id,
                                "vendor_id":data.data.vendor_id,
                                "name":data.data.name,
                                "phone":data.data.phone,
                                "address":data.data.address,
                                "postal_code":data.data.postal_code,
                                "date":data.data.route_enable_date,
                                "valid":data.data.valid,
                                'reason':data.data.reason};

                            $('#datatable').DataTable().row( context.parents('tr') ).data( formatItem(value) ).draw();
                            $(window).scrollTop(0);
                            bootprompt.alert("Reattempt created");
                        }
                        else if(data['status_code']==405)
                        {
                            var value ={"tracking_id":data.data.tracking_id,
                                "vendor_id":data.data.vendor_id,
                                "name":data.data.name,
                                "phone":data.data.phone,
                                "address":data.data.address,
                                "postal_code":data.data.postal_code,
                                "date":data.data.route_enable_date,
                                "valid":data.data.valid,
                                'reason':data.data.reason};

                            $('#datatable').DataTable().row( context.parents('tr') ).data( formatItem(value) ).draw();
                            $(window).scrollTop(0);
                            bootprompt.alert(data.data.reason);
                        }
                        else
                        {

                            bootprompt.alert(data['error'], () =>
                            {
                                // location.reload();
                            });

                        }



                    },
                    error:function (error) {
                        $('#updateStatus').modal('toggle');
                        $(".loader").hide();
                        $(".loader-background").hide();

                        bootprompt.alert('some error');
                    }
                });
                event.preventDefault();
            });

// process  form for create Order
            $('.Createorder').submit(function(event) {

                event.preventDefault();

                // get the form data
                //$('#datatable').DataTable().row( context.parents('tr') ).data( ['2','3','2','3','2','3','s'] ).draw();
                var data = new FormData();
                data.append('tracking_Id',  $('input[name=tracking_Id]').val());
                data.append('name', $('input[name=name]').val());
                data.append('phone', $('input[name=phone]').val());
                data.append('address', $('input[name=address]').val());
                data.append('vendor_id', $('select[name=vendor_id]').children("option:selected").val());

                data.append('postal_code', $('input[name=postal_code]').val());


                // process the form
                $(".loader").show();
                $(".loader-background").show();

                $.ajax({
                    url: "{{ URL::to('custom/create/order')}}",
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data:data,
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                    },

                    success: function(data){
                        $('input[name=tracking_Id]').val("");
                        $('input[name=name]').val("");
                        $('input[name=phone]').val("");
                        $('input[name=address]').val("");
                        $('select[name=vendor_id]').val("");
                        $('input[name=postal_code]').val("");
                        $('#myModal').modal('toggle');

                        $(".loader").hide();
                        $(".loader-background").hide();
                        if(data.status_code==200)
                        {
                            var  count=$('#joey_route_detail_count').val();

                            if(count==0)
                            {
                                $(".createRoute").prop('disabled', true);
                            }
                            else
                            {
                                $(".createRoute").prop('disabled', false);
                            }
                            valid_count=document.getElementById ( "file_total_valid_order" ).innerText;
                            total_count=document.getElementById ( "file_total_order" ).innerText;
                            valid_count++;
                            document.getElementById ( "file_total_valid_order" ).innerText=valid_count;
                            document.getElementById ( "file_total_invalid_order" ).innerText=total_count-valid_count;


                            var value =
                                {"tracking_id":data.data.tracking_id,"vendor_id":data.data.vendor_id,"name":data.data.name,"phone":data.data.phone,"address":data.data.address,"postal_code":data.data.postal_code,"date":data.data.route_enable_date,
                                    "valid":data.data.valid,'reason':data.data.reason};

                            // $('<tr>', { html: formatItem(value) }).appendTo($("#tbdata"));

                            $('#datatable').DataTable().row( context.parents('tr') ).data( formatItem(value) ).draw();
                            // $('#datatable').DataTable().row.add(formatItem(value) ).draw();
                            $(window).scrollTop(0);

                            bootprompt.alert("Order created");

                        }
                        else if(data.status_code==400)
                        {
                            bootprompt.alert(data.error);
                        }
                        else
                        {
                            context_id.context.innerText = "Edit";

                            bootprompt.alert("Order Could not Created");

                        }

                    },
                    failure: function(result){
                        $('#myModal').modal('toggle');
                        $(".loader").hide();
                        $(".loader-background").hide();


                        bootprompt.alert(result);
                    }
                });

                // stop the form from submitting the normal way and refreshing the page
                event.preventDefault();
            });

// process the form edit order
            $('.Editorder').submit(function(event) {

                event.preventDefault();

                // get the form data

                var data = new FormData();
                data.append('tracking_id',  $('input[name=tracking_id]').val());
                data.append('name', $('input[name=edit_name]').val());
                data.append('phone', $('input[name=edit_phone]').val());
                data.append('address', $('input[name=edit_address]').val());
                data.append('postal_code', $('input[name=edit_postal_code]').val());


                // process the form
                $(".loader").show();
                $(".loader-background").show();

                $.ajax({
                    url: "{{ URL::to('custom/edit/order')}}",
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data:data,
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                    },

                    success: function(data){
                        $('input[name=tracking_id]').val("");
                        $('input[name=edit_name]').val("");
                        $('input[name=edit_phone]').val("");
                        $('input[name=edit_address]').val("");
                        $('input[name=edit_postal_code]').val("");
                        $('#myModaledit').modal('toggle');

                        $(".loader").hide();
                        $(".loader-background").hide();
                        if(data.status_code==200)
                        {

                            var value =
                                {"tracking_id":data.data.tracking_id,"vendor_id":data.data.vendor_id,"name":data.data.name,"phone":data.data.phone,"address":data.data.address,"postal_code":data.data.postal_code,"date":data.data.route_enable_date,
                                    "valid":data.data.valid,"reason":data.data.reason};

                            $('#datatable').DataTable().row( context.parents('tr') ).data( formatItem(value) ).draw();
                            $(window).scrollTop(0);

                            bootprompt.alert("Order Updated");

                        }
                        else
                        {
                            //    context_id.context.innerText = "Edit";

                            bootprompt.alert(data.error);

                        }

                    },
                    failure: function(result){
                        $('#myModaledit').modal('toggle');
                        $(".loader").hide();
                        $(".loader-background").hide();


                        bootprompt.alert(result);
                    }
                });

                // stop the form from submitting the normal way and refreshing the page
                event.preventDefault();
            });


// process  form Create Job id
            $('.Createjobid').submit(function(event) {

                event.preventDefault();

                // get the form data

                var data = new FormData();

                data.append('hub_id', document.getElementsByName('hub_id')[0].value);
                data.append('vehicle', $('select[name=vehicle]').children("option:selected").val());
                data.append('start_time', $('input[name=start_time]').val());
                data.append('end_time', $('input[name=end_time]').val());
                data.append('joey_count', $('input[name=joey_count]').val());
                data.append('joey_capacity', $('input[name=custom_capacity]').val());


                // process the form
                $(".loader").show();
                $(".loader-background").show();

                $.ajax({
                    url: "{{ URL::to('returncreate/route/custom/routing')}}",
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data:data,
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                    },

                    success: function(data){
                        data.append('hub_id', document.getElementsByName('hub_id')[0].value);
                        $('select[name=vehicle]').children("option:selected").val();
                        $('input[name=start_time]').val();
                        $('input[name=end_time]').val();
                        $('input[name=joey_count]').val();
                        $('input[name=custom_capacity]').val();
                        $('#myModaljobid').modal('toggle');

                        $(".loader").hide();
                        $(".loader-background").hide();
                        if(data.status_code==200)
                        {

                            $(window).scrollTop(0);
                            bootprompt.alert("Job Created and Job Id is "+data['Job_id']);

                        }
                        else
                        {

                            bootprompt.alert("Order Could not Update");
                        }

                    },
                    failure: function(result){
                        $('#myModaljobid').modal('toggle');
                        $(".loader").hide();
                        $(".loader-background").hide();


                        bootprompt.alert(result);
                    }
                });

                // stop the form from submitting the normal way and refreshing the page
                event.preventDefault();
            });

// process  form add joey Count
            $('.addRouteDetail').submit(function(event) {

                event.preventDefault();

                // get the form data

                var data = new FormData();

                data.append('hub_id', document.getElementsByName('hub_id')[0].value);
                data.append('vehicle_id', $('select[name=vehicle_id]').children("option:selected").val());
                data.append('joeys', $('input[name=joey_c]').val());


                // process the form
                $(".loader").show();
                $(".loader-background").show();

                $.ajax({
                    url: "{{ URL::to('returncustom/add/joey/count')}}",
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data:data,
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                    },

                    success: function(data){
                        // data.append('hub_id', document.getElementsByName('hub_id')[0].value);
                        $('select[name=vehicle]').children("option:selected").val();
                        $('input[name=joey_c]').val();

                        $('#RouteDetailModel').modal('toggle');
                        $count=  $('#joey_route_detail_count').val();
                        $(".createRoute").prop('disabled', false);
                        $(".loader").hide();
                        $(".loader-background").hide();
                        if(data.status_code==200)
                        {
                            var  count=$('#joey_route_detail_count').val();
                            count++;
                            var  val_count=$('#file_total_valid_order').text();
                            if(val_count==0)
                            {

                                $(".createRoute").prop('disabled', true);
                            }
                            else
                            {

                                $(".createRoute").prop('disabled', false);
                            }

                            $('#joey_route_detail_count').val(count);

                            var  count=$('#joey_route_detail_count').val();


                            if(data.vehicle_id==1)
                            {

                                var val=	["Bicycle",data.joeys_count,"<button data-id='"+data.id+"' style='background-color: #c6dd38;' class='btn btn editrow' >Edit</button><button data-id='"+data.id+"' style=' background: #e46d28;' class='btn btn removerow'>Remove</button>"];
                                $('#routedetailtable').DataTable().row.add(val).draw();
                            }
                            else if(data.vehicle_id==2)
                            {
                                var val=	["Scooter",data.joeys_count,"<button data-id='"+data.id+"' style='background-color: #c6dd38;' class='btn btn editrow' >Edit</button><button data-id='"+data.id+"' style=' background: #e46d28;' class='btn btn removerow'>Remove</button>"];
                                $('#routedetailtable').DataTable().row.add(val).draw();
                            }
                            else if(data.vehicle_id==3)
                            {
                                var val=	["Car",data.joeys_count,"<button data-id='"+data.id+"' style='background-color: #c6dd38;' class='btn btn editrow' >Edit</button><button data-id='"+data.id+"' style=' background: #e46d28;' class='btn btn removerow'>Remove</button>"];
                                $('#routedetailtable').DataTable().row.add(val).draw();

                            }
                            else if(data.vehicle_id==4)
                            {
                                var val=	["Truck",data.joeys_count,"<button data-id='"+data.id+"' style='background-color: #c6dd38;' class='btn btn editrow' >Edit</button><button data-id='"+data.id+"' style=' background: #e46d28;' class='btn btn removerow'>Remove</button>"];
                                $('#routedetailtable').DataTable().row.add(val).draw();
                            }
                            else if(data.vehicle_id==5)
                            {
                                var val=	["SUV",data.joeys_count,"<button data-id='"+data.id+"' style='background-color: #c6dd38;' class='btn btn editrow' >Edit</button><button data-id='"+data.id+"' style=' background: #e46d28;' class='btn btn removerow'>Remove</button>"];
                                $('#routedetailtable').DataTable().row.add(val).draw();  }
                            else
                            {
                                var val=	["Van",data.joeys_count,"<button data-id='"+data.id+"' style='background-color: #c6dd38;' class='btn btn editrow' >Edit</button><button data-id='"+data.id+"' style=' background: #e46d28;' class='btn btn removerow'>Remove</button>"];
                                $('#routedetailtable').DataTable().row.add(val).draw();
                            }


                            bootprompt.alert("Joeys Capacity Count Added");

                        }
                        else
                        {

                            //    bootprompt.alert("Order Could not Update");
                        }

                    },
                    failure: function(result){
                        $('#myModaljobid').modal('toggle');
                        $(".loader").hide();
                        $(".loader-background").hide();


                        bootprompt.alert(result);
                    }
                });

                // stop the form from submitting the normal way and refreshing the page
                event.preventDefault();
            });

//process form for edit joey count
            $('.editRouteDetail').submit(function(event) {

                event.preventDefault();

                // get the form data

                var data = new FormData();

                data.append('hub_id', document.getElementsByName('hub_id')[0].value);
                data.append('vehicle_id', $('select[name=edit_vehicle_id]').children("option:selected").val());
                data.append('joeys', $('input[name=edit_joey_c]').val());
                data.append('id', $('input[name=edit_id]').val());

                // process the form
                $(".loader").show();
                $(".loader-background").show();

                $.ajax({
                    url: "{{ URL::to('returncustom/edit/joey/count')}}",
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data:data,
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                    },

                    success: function(data){


                        $('select[name=edit_vehicle_id]').children("option:selected").val();
                        $('input[name=edit_joey_c]').val('');
                        $('input[name=edit_id]').val('')
                        $('#editRouteDetailModel').modal('toggle');

                        $(".loader").hide();
                        $(".loader-background").hide();
                        if(data.status_code==200)
                        {
                            if(data.vehicle_id==1)
                            {

                                var val=	["Bicycle",data.joeys_count,"<button data-id='"+data.id+"' style='background-color: #c6dd38;' class='btn btn editrow' >Edit</button><button data-id='"+data.id+"' style=' background: #e46d28;' class='btn btn removerow'>Remove</button>"];
                                $('#routedetailtable').DataTable().row( context.parents('tr') ).data( val).draw();

                            }
                            else if(data.vehicle_id==2)
                            {
                                var val=	["Scooter",data.joeys_count,"<button data-id='"+data.id+"' style='background-color: #c6dd38;' class='btn btn editrow' >Edit</button><button data-id='"+data.id+"'style=' background: #e46d28;'  class='btn btn removerow'>Remove</button>"];
                                $('#routedetailtable').DataTable().row( context.parents('tr') ).data( val).draw();
                            }
                            else if(data.vehicle_id==3)
                            {
                                var val=	["Car",data.joeys_count,"<button data-id='"+data.id+"' style='background-color: #c6dd38;' class='btn btn editrow' >Edit</button><button data-id='"+data.id+"' style=' background: #e46d28;' class='btn btn removerow'>Remove</button>"];
                                $('#routedetailtable').DataTable().row( context.parents('tr') ).data( val).draw();

                            }
                            else if(data.vehicle_id==4)
                            {
                                var val=	["Truck",data.joeys_count,"<button data-id='"+data.id+"' style='background-color: #c6dd38;' class='btn btn editrow' >Edit</button><button data-id='"+data.id+"' style=' background: #e46d28;' class='btn btn removerow'>Remove</button>"];
                                $('#routedetailtable').DataTable().row( context.parents('tr') ).data( val).draw();
                            }
                            else if(data.vehicle_id==5)
                            {
                                var val=	["SUV",data.joeys_count,"<button data-id='"+data.id+"' style='background-color: #c6dd38;' class='btn btn editrow' >Edit</button><button data-id='"+data.id+"' style=' background: #e46d28;' class='btn btn removerow'>Remove</button>"];
                                $('#routedetailtable').DataTable().row( context.parents('tr') ).data( val).draw();  }
                            else
                            {
                                var val=	["Van",data.joeys_count,"<button data-id='"+data.id+"' style='background-color: #c6dd38;' class='btn btn editrow' >Edit</button><button data-id='"+data.id+"' style=' background: #e46d28;' class='btn btn removerow'>Remove</button>"];
                                $('#routedetailtable').DataTable().row( context.parents('tr') ).data( val).draw();
                            }

                            bootprompt.alert("Joey Vehicle data Updated",);


                        }
                        else
                        {

                            //    bootprompt.alert("Order Could not Update");
                        }

                    },
                    failure: function(result){
                        $('#editRouteDetailModel').modal('toggle');
                        $(".loader").hide();
                        $(".loader-background").hide();


                        bootprompt.alert(result);
                    }
                });

                // stop the form from submitting the normal way and refreshing the page
                event.preventDefault();
            });


        });


    </script>

@endsection
