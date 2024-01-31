<?php
use App\Joey;
use App\Vehicle;
use App\SlotsPostalCode;
use App\Sprint;
use App\MerchantIds;


$status = array(
    "124" => "At hub - processing",
    "133" => "Packages sorted",
    "61" => "Scheduled order",
    "13" => "At hub - processing",
    "135" => "Customer refused delivery",
    "108" => "Customer unavailable-Incorrect address",
    "106" => "Customer unavailable - delivery returned",
    "107" => "Customer unavailable - Left voice mail - order returned",
    "109" => "Customer unavailable - Incorrect phone number",
    "142" => "Damaged at hub (before going OFD)",
    "143" => "Damaged on road - undeliverable",
    "110" => "Delivery to hub for re-delivery",
    "111" => "Delivery to hub for return to merchant",
    "121" => "Out for delivery",
    "102" => "Joey Incident",
    "104" => "Damaged on road - delivery will be attempted",
    "105" => "Item damaged - returned to merchant",
    "141" => "Lost package",
    "121" => "Out for delivery",
    "102" => "Joey Incident"
);
$status_id = array("136" => "Client requested to cancel the order",
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
        "121" => "Out for delivery",
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
        "60" => "Task failure");
?>
@extends( 'backend.layouts.app' )

@section('title', 'Remove Unavailable Orders')

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

        span.select2-selection.select2-selection--multiple {
            height: 39px;
        }

        .form-control {
            height: 39px !important;
            border-radius: 5px
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
            width: 65% !important;
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

        #tracking_id_chosen {
            width: 280px !important;
            margin-right: 5px !important;
            float: left;

        }

        /* #sprint_id_chosen {
            width: 280px !important;
            margin-right: 5px !important;
            float: left;
        } */
        select {
            margin: 0px 5px 0 0 !important;
        }

        /* .tracking_id input {
            padding: 18px !important;
        }
        .sprint_id input {
            padding: 18px !important;
        } */
        span.select2.select2-container.select2-container--default {
            width: 100% !important;
        }

        /* span.select2-selection.select2-selection--multiple {
            height: 35px !important;
        } */
        .tracking_id {
            width: 25%;
            margin-right: 5px;
            float: left;
        }

        .sprint_id {
            width: 25%;
            margin-right: 5px;
            float: left;
        }
        button.btn.btn {
    color: #333;
}
button.btn.btn-primary.bootprompt-accept {
    
    background-color: #c6dd38;
    border-color: #c6dd38;
    margin: 6px;
}
button.btn.btn-secondary.btn-default.bootprompt-cancel {
    margin: 6px;
}
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 16px;
    
}
    </style>
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
    <script src="https://unpkg.com/bootprompt@6.0.2/bootprompt.js"></script>
    <!-- <script src="{{ backend_asset('libraries\bootstrap\js\bootprompt.js') }}"></script> -->
   
    <!-- <script src="https://unpkg.com/bootprompt@6.0.2/bootprompt.js"></script> -->
    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="{{ backend_asset('js/jquery-ui.js') }}"></script>
    <link href="{{ backend_asset('js/jquery-ui.css') }}" rel="stylesheet"> -->

@endsection

@section('inlineJS')
<script>
$(document).on('click','#checkAll',function(){
            //  aler('s');
            $('input:checkbox').not(this).prop('checked', this.checked);
    });
$(document).on('click','.markIncomplete',function(){
    var ids = [];
    $.each($("input[name='check']:checked"), function(){
        ids.push($(this).val());
        });
        if(ids==0)
        {
            bootprompt.alert('Please  select the  order to mark them  Unattempt Orders');
        }
        else
        {
            bootprompt.confirm({
            
            message: "Are you sure you want to mark these   Orders Unattempt?",
            callback: (result) => { 
            if(result)
            {
                $.ajax({
              type: "post",
              url: "{{ URL::to('/mark/incomplete')}}",
              data:{ids:ids},
              beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token',"{{ csrf_token() }}");
                    },
              success: function (data) {
                $(".loader").hide();
                $(".loader-background").hide();
                if(data.status_code==200)
                 {
                    bootprompt.alert("Orders marked Unattempt successfully.", () => {
                        location.reload();
                            });
                  
                }
                else
                {
                    bootprompt.alert('Error.');
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
        }
       
        
        
    });
</script>
<script type="text/javascript">
      
        $(document).ready(function () {

            $('#datatable').DataTable({
              "lengthMenu": [ 50,100, 250, 500, 750, 1000]
            });

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
    </script>


<script type="text/javascript">
        $(document).ready(function () {
            $('#sprint_id').select2();
        });
        $(document).ready(function () {
            $('#tracking_id').select2();
        });

    </script>

    <script type="text/javascript">

        $(document).ready(function () {


            $(".tracking_id").show();
//    $(".sprint_id").hide();
//    $('#tracking_id').prop('required',false);
//     $('#tracking_id').prop('required',false);


        });

        // function myFunction() {

        // if (document.typeCV.type.value == "2")  {
        // $(".tracking_id").show();
        // //$('.sprint_id').removeAttr('required');​​​​​
        // $('#tracking_id').prop('required',true);
        // $(".sprint_id").hide();
        // $('#sprint_id').prop('required',false);
        // }
        // else if(document.typeCV.type.value == "1"){
        // $(".sprint_id").show();
        // $('#tracking_id').prop('required',false);
        // $(".tracking_id").hide();
        // $('#sprint_id').prop('required',true);
        // }
        // else{
        // $(".tracking_id").hide();
        //    $(".sprint_id").hide();
        // }
        // }
        $(".chosen-select").chosen({
            no_results_text: "Oops, nothing found!"
        })
      
        

         $(document).on('click', '.check', function(e) {
        var checked = $(this).prop('checked');
        // console.log(checked);
        if(checked){
            $('.transfer').prop('disabled', false);
        } else{
            $('.transfer').prop('disabled', true);
        }
   });
    </script>
@endsection

@section('content')

    <div class="right_col" role="main">
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <div class="">
            <div class="page-title">
                <div class="title_left amazon-text">
                    <h1><b>Remove Unavailable Orders</b>
                        <small></small>
                    </h1>
                </div>
            </div>

            <div class="clearfix"></div>

            {{--@include('backend.layouts.modal')
            @include( 'backend.layouts.popups')--}}
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <!-- <button style="margin-left:10px" class="btn green-gradient" data-toggle="modal" data-target="#ex1"> <i class="fa fa-plus"></i> Create Slot</button> -->
                            <div class="clearfix"></div>
                        </div>

                        <!-- <div class="x_title"> -->
                            <form method="get" action="">
                              
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Route Id </label>
                                        <input type="text" name="route_id" class="form-control" required=""
                                        value="{{ (isset($_GET['route_id']) && $_GET['route_id']!=null ) ? trim($_GET['route_id']):null}}"
                                               placeholder="Route Id">
                                    </div>
                                    <input type="hidden" name="hub" value="{{ auth()->user()->hub_id }}">

                                    <div class="col-md-3">
                                        <button class="btn btn-lg green-gradient" type="submit" style="margin-top: 24px;">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="x_title">
                        <h3 style="color: #333;position: relative;padding-right: 116px;"><b>Filter Route Location</b> </h3>
                            <div class="clearfix"></div>
                        </div>
                        <form method="get" action="">
                              
                              <div class="row">
                              <div style='display:none' class="col-md-2">
                                        <label>Route Id </label>
                                        <input type="text" name="route_id" class="form-control" required=""
                                        value="{{ (isset($_GET['route_id']) && $_GET['route_id']!=null ) ? trim($_GET['route_id']):null}}"
                                               placeholder="Route Id">
                                    </div>
                                   <input type="hidden" name="hub" value="{{ auth()->user()->hub_id }}">
                             
                                  
                                  <div class="col-md-3">
                                      <label>Select Status </label>
                                      <select class="js-example-basic-multiple col-md-4 col-sm-4 col-xs-4 form-control"  name="status_id">
                                      <option value="">Please Select Status</option>
                                    <?php
                                   foreach($status as $key => $oc){ 
                                            echo "<option value='".$key."'>".$oc."</option>";
                                      }
                                        ?>
                                  
                                  </select>
                                  </div>
                                  <div class="col-md-2">
                                      <button class="btn btn-lg green-gradient" type="submit" style="margin-top: 24px;">
                                          Filter
                                      </button>
                                  </div>
                              </div>
                          </form>

                            

                            
                        <!-- </div> -->
                        <div class="x_title">   <!---x_title-->
                           
                        <h3 style="color: #333;position: relative;padding-right: 116px;"><b>Order Detail</b>  <button  style='background: #3e3e3e;background-color: #c6dd38;position: absolute;right: 0px;' class='btn btn markIncomplete'>Remove Unavailable Orders</button></h3>
                               <div class="clearfix">  <!---clearfix-->
                                   
                               </div> <!---clearfix end-->
                               
                           </div>
                           
                            <!---x_title end-->
                                <!---x_content end-->
                                <div class="x_content">
                                    <!---x_content start-->
                                            @include( 'backend.layouts.notification_message' )

                                                <div class="table-responsive">
                                                <!---table-responsive start-->
                                                <table class="table table-striped table-bordered"  id="datatable" border="2">
                                                <thead>
                                                <tr>
                                                                    <th><input class='check' type='checkbox' name='check' value="0" id="checkAll"> </th> 
                                                                    <th>Tracking Id </th>       
                                                                    <th> Route Id </th>
                                                                    <th>Status</th>
                                                                    <th> Address </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                      <?php 
          
             foreach($tracking_id_data as $data) {
                    echo "<tr>";
                    echo "<td><input class='check' id='check' type='checkbox' name='check' value='".$data->id."'></td>";
                    echo "<td>".$data->tracking_id."</td>";
                    echo "<td>R-".$data->route_id."-".$data->ordinal."</td>";
                    echo "<td>".$status_id[$data->status_id]."</td>";
                    echo "<td>".$data->address." ".$data->postal_code."</td>";
                    echo "</tr>";
                         
             }             
               ?>
                      </tbody>
                                                </table>      
                                                </div>
                                    <!---table-responsive end-->
                                </div>
                                <!---x_content end-->
                    </div>
                    
                </div>
                

            </div>


        </div>
    </div>
    <!-- /#page-wrapper -->


    <!-- CreateSLotsModal -->

    <!-- UpdateSLotModal -->









    
@endsection