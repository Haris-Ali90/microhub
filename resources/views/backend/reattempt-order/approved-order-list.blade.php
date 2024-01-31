<?php
$statusId = array(
    "136" => "Client requested to cancel the order",
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
    "121" => "Pickup from Hub",
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
    "60" => "Task failure",
    "145" => "Returned To Merchant",
    "146" => "Delivery Missorted, Incorrect Address",
    '153' => 'Miss sorted to be reattempt',
    '154' => 'Joey unable to complete the route', '155' => 'To be re-attempted tomorrow'
);
?>

@extends( 'backend.layouts.app' )

@section('title', 'Customer Approved')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <!-- Image Viewer CSS -->
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <!--joey-custom-css-->
    <link href="{{ backend_asset('css/joeyco-custom.css') }}" rel="stylesheet">
   <style>

        .search-order {
            background-color: #c6dd38;
        }
        .input-error {
            color: red;
            padding: 10px 0px;
        }
        .form-submit-btn {
            margin-top: 26px;
            background-color: #c6dd38;
        }
        .show-notes{
            background-color: #C6DD38;
            border-style:none;
            padding: 6px 9px 6px 9px;
        }
    </style>


@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0&libraries=places" type="text/javascript"></script>
    <!-- Custom JavaScript -->
    <script src="{{ backend_asset('js/joeyco-custom-script.js')}}"></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">
        <!-- Datatable -->
        $(document).ready(function () {
            let datatable = $('.return-order-datatable').DataTable(
                {
                    "ordering": true,
                    "paging": false,
                    scrollX: true,   // enables horizontal scrolling,
                    scrollCollapse: true,
                    /*columnDefs: [
                        { width: '20%', targets: 0 }
                    ],*/
                    fixedColumns: true,
                }
            );

            //ajax function for select all and condition for check all and uncheck
            /*$('.select-all').click(function() {

                let is_checked = $(this).prop('checked');
                if (is_checked)
                {
                    $('.reattemptCheckbox').prop('checked', true); // Checks it
                }
                else
                {
                    $('.reattemptCheckbox').prop('checked', false); // Un-checks it
                }

                loadCheck(is_checked);

            });*/

            //loadCheck(); // initial call this function to load data


            /*$('.reattemptCheckbox').change(function()
            {
                single_checkbox_fn($(this),'single');
            });*/

            // single checkbox fucntion
            /*function single_checkbox_fn(el,type = 'load') {
                let element = el;
                let tracking_id = element.val();
                let data_reattempt_id = element.attr('data-reattempt');
                let btn_data = JSON.parse($('.scan-for-reattempt-order').attr('data-object'));
                if(element.prop("checked") == true)
                {
                    btn_data[data_reattempt_id] = {"tracking_id":tracking_id,"reattempt_id":data_reattempt_id};
                }
                else
                {
                    // remove uncheck all checked btn
                    if(type == 'single')
                    {
                        $('.select-all').prop('checked', false);
                    }

                    delete btn_data[data_reattempt_id];
                }

                // checking the object is empty
                if(type == 'single' && Object.keys(btn_data).length === 0)
                {
                    $('.scan-for-reattempt-order').hide();
                }
                else if(type == 'single' && Object.keys(btn_data).length > 0)
                {
                    $('.scan-for-reattempt-order').show();
                }
                // updated
                btn_data = JSON.stringify(btn_data);
                $('.scan-for-reattempt-order').attr('data-object',btn_data);
            }*/

            //Function for get and set value for reattempt
            /*function loadCheck(type) {

                // getting all check box
                var all_checkbox_count = $('.reattemptCheckbox');
                // getting checked boxes
                var reattemptCheckbox_checked = $('input[name="scan-reattempt"]:checked');
                // get button
                let button = $('.reattempt-order');

                //checking the total box checked then checek all show
                if(reattemptCheckbox_checked.length < all_checkbox_count.length)
                {
                    $('.select-all').prop('checked', false);
                }
                else
                {
                    $('.select-all').prop('checked', true);
                }

                // checking all checked type
                if(type)
                {
                    // looping up the checked boxes for set values
                    reattemptCheckbox_checked.each(function(index){
                        single_checkbox_fn($(this));

                    });
                    // show button
                    button.show();
                }
                else
                {
                    // make checked box to unchecked
                    all_checkbox_count.prop('checked', false);

                    // remove data from button
                    button.attr('data-object',"{}");
                    // hide button
                    button.hide();

                }

            }*/

            //Multiple Reattempt Order
            {{--$(document).on('click', '.reattempt-order', function (e) {--}}
            {{--let el = $(this);--}}
            {{--var data_object = JSON.parse(el.attr('data-object'));--}}

            {{--$.confirm({--}}
                {{--title: 'A secure action',--}}
                {{--content: 'Are you sure you want to multiple reattempt order?',--}}
                {{--icon: 'fa fa-question-circle',--}}
                {{--animation: 'scale',--}}
                {{--closeAnimation: 'scale',--}}
                {{--opacity: 0.5,--}}
                {{--buttons: {--}}
                {{--'confirm': {--}}
                {{--text: 'Proceed',--}}
                {{--btnClass: 'btn-info',--}}
                {{--action: function () {--}}
                    {{--showLoader();--}}
                    {{--$.ajax({--}}
                        {{--type: "GET",--}}
                        {{--url: "{{URL::to('/')}}/backend/multiple/reattempt/order",--}}
                        {{--data: {--}}
                            {{--data: data_object--}}
                        {{--},--}}
                        {{--success: function (res) {--}}
                            {{--hideLoader();--}}
                            {{--console.log(res);--}}
                            {{--let errors = res.errors_data;--}}
                            {{--let success = res.success_data;--}}
                            {{--let error_message = '';--}}
                            {{--let success_message = '';--}}
                            {{--let is_table_row_change = false;--}}
                            {{--// looping on errors to show--}}
                            {{--for ( var index in errors)--}}
                            {{--{--}}
                                {{--error_message+=errors[index].message+'<br>';--}}
                            {{--}--}}

                            {{--// looping up success messages--}}
                            {{--for ( var index in success)--}}
                            {{--{--}}
                                {{--success_message+=success[index].message+'<br>';--}}
                                {{--is_table_row_change = true;--}}
                                {{--datatable.rows('.tr-no-'+index).remove();--}}

                            {{--}--}}

                            {{--// checking  the error exist--}}
                            {{--if(error_message != '')--}}
                            {{--{--}}
                                {{--ShowSessionAlert('danger',error_message);--}}
                            {{--}--}}

                            {{--if(success_message != '')--}}
                            {{--{--}}
                                {{--ShowSessionAlert('success', success_message);--}}
                            {{--}--}}

                            {{--// re draw datatable--}}
                            {{--if(is_table_row_change)--}}
                            {{--{--}}
                                {{--datatable.rows().draw();--}}
                            {{--}--}}

                            {{--$('.scan-for-reattempt-order').hide();--}}
                            {{--$('.scan-for-reattempt-order').attr('data-object',"{}");--}}
                            {{--$('.reattemptCheckbox').prop('checked', false);--}}
                            {{--$('.select-all').prop('checked', false);--}}

                        {{--},--}}
                        {{--error: function (error) {--}}
                            {{--hideLoader();--}}
                            {{--ShowSessionAlert('danger', 'Something critical went wrong');--}}
                            {{--console.log(error);--}}
                        {{--}--}}
                    {{--});--}}
                {{--}--}}
                {{--},--}}
                {{--cancel: function () {--}}
                {{--//$.alert('you clicked on <strong>cancel</strong>');--}}
                {{--}--}}
                {{--}--}}
            {{--});--}}
            {{--});--}}

            //open model for re-scan Reattempt Order
            $(document).on('click', '.scan-for-reattempt-order', function () {

                // getting current element
                let el = $(this);
                var tracking_id = el.attr("data-tracking_id");
                var id = el.attr('data-id');

                // opening model for re-scan
                $('#reattempt-confirm-model').modal();
                //hiding reattempt submit button
                $('.reattempt-order').hide();
                //hide input error
                $('.input-error').hide();
                // focus input for re-scan
                $('#reattempt-confirm-model').find(".re-scan-tracking-id").focus();

                //setting up inputs
                $('#reattempt-confirm-model').find(".tracking_id").val(tracking_id);
                $('#reattempt-confirm-model').find(".id").val(id);

            });

            // validation of re-scan for reattempt
            $(document).on('submit','.reattempt-order-from',function (e) {

                let el = $(this);
                let from_data_unformated  = el.serializeArray();
                let from_data = {};

                // creating formated form data
                from_data_unformated.forEach(function (value) {
                    from_data[value.name] = value.value;
                });

                // now validating tracking id and scan id are matched
                if(from_data.tracking_id != from_data.re_scan_tracking_id)
                {
                    //show input error
                    $('.input-error').show();
                    // making re-scan input empty
                    $(this).find('.re-scan-tracking-id').val('');
                    $(this).find('.re-scan-tracking-id').focus();
                    return false;
                }
                else
                {
                    //hide input error
                    $('.input-error').hide();
                    // setting re-attempt btn data
                    $('.reattempt-order').attr({"data-id":from_data.id,"data-tracking_id":from_data.tracking_id});
                    //showing reattempt submit button
                    $('.reattempt-order').show();

                }

                return false;
            });

            // model for re-scan inputs empty when it is closed
            $('#reattempt-confirm-model').on('hidden.bs.modal', function (e) {
                // removeing re-attempt btn data
                $('.reattempt-order').attr({"data-id":"","data-tracking_id":""});
                //removing model from input data
                $('#reattempt-confirm-model').find(".tracking_id").val('');
                $('#reattempt-confirm-model').find(".id").val('');
                $('#reattempt-confirm-model').find(".re-scan-tracking-id").val('');
            });

            //Single Reattempt Order
            $(document).on('click', '.reattempt-order', function (e) {

                let el = $(this);
                var reattempt_id = el.attr("data-tracking_id");
                var ids = el.attr('data-id');

                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to reattempt?',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    buttons: {
                        'confirm': {
                            text: 'Proceed',
                            btnClass: 'btn-info',
                            action: function () {
                                var id = reattempt_id;
                                showLoader();
                                $.ajax({
                                    type: "GET",
                                    url: "{{URL::to('/')}}/reattempt/order/" + id,
                                    data: {
                                        ids: ids
                                    },
                                    success: function (res) {
                                        hideLoader();
                                        // checking responce
                                        if (res.status == false) {
                                            ShowSessionAlert('danger', res.message);
                                            console.log(res.error);
                                            return false;
                                        }
                                        // removing tr in data table
                                       // removing tr in data table
                                        datatable.row($('.scan-for-reattempt-order-'+reattempt_id).parents('tr'))
                                            .remove()
                                            .draw();
                                        ShowSessionAlert('success', res.message);
                                    },
                                    error: function (error) {
                                        hideLoader();
                                        ShowSessionAlert('danger', 'Something critical went wrong');
                                        console.log(error);
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



            // reattempt by scan
            $('.scan-for-reattempt').submit(function (e) {
                e.preventDefault();

                let scanned_tracking_id = $('.scan-for-reattempt-tracking-id').val().trim();

                // now finding the scanned tracking id exist in table
                let tr =  $('.tr-tracking-no-'+scanned_tracking_id);

                // validation for scanned tracking id
                if(tr.length <= 0)
                {
                    ShowSessionAlert('danger', 'Tracking id does not exist in current bucket');
                    return false;
                }

                //getting data values for scanned tracking id
                var tracking_id = tr.attr("data-tracking_id");
                var ids = tr.attr('data-id');

                //
                showLoader();
                $.ajax({
                    type: "GET",
                    url: "{{URL::to('/')}}/reattempt/order/"+tracking_id,
                    data: {
                        ids: ids
                    },
                    success: function (res) {
                        hideLoader();
                        // checking responce
                        if (res.status == false) {
                            ShowSessionAlert('danger', res.message);
                            console.log(res.error);
                            return false;
                        }
                        // removing tr in data table
                        // removing tr in data table
                        datatable.row(tr)
                            .remove()
                            .draw();
                        ShowSessionAlert('success', res.message);
                    },
                    error: function (error) {
                        hideLoader();
                        ShowSessionAlert('danger', 'Something critical went wrong');
                        console.log(error);
                    }
                });



            })


        });


    </script>
@endsection

@section('content')
    @include( 'backend.layouts.loader' )
    <!--Open Main Div-->
    <div class="right_col" role="main">

        <!--Open Heading Div-->
        <div class="page-title">
            <div class="title_left">
                <h2>Customer Service Review Completed (Awaiting Creation)
                    <small></small>
                </h2>
            </div>
        </div>
        <!--Close Heading Div-->

        <!--Open Row Div-->
        <div class="row">

            <!--Open Col Div-->
            <div class="col-md-12 col-sm-12 col-xs-12">
                <!--Open X_Panel Div-->
                <div class="x_panel">
                    <!--Open x_title Div-->
                    <div class="x_title">
                        <div class="clearfix"></div>
                        <!--Open Form For Tracking ID Search-->
                        <form class="scan-for-reattempt" action="#" method="get">
                            <!--Open Form Row Div-->
                            <div class="row">
                                <!--Open Input Tracking ID Div-->
                                <div class="col-sm-12 col-md-3">
                                    <input class="form-control scan-for-reattempt-tracking-id" type="text" placeholder="Tracking Id" required="required"/>
                                </div>
                                <!--Close Input Tracking ID Div-->
                                <!--Open Search Button Div-->
                                <div class="col-sm-4 col-md-2">
                                    <button class="form-control search-order us_custm_btn">Scan for reattempt</button>
                                </div>
                                <!--Close Search Button Div-->
                            </div>
                            <!--Close Form Row Div-->
                        </form>
                        <!--Close Form For Tracking ID Search-->
                        <div class="clearfix"></div>
                    </div>
                    <!--Close x_title Div-->
					<!--Button For Re-Attempt Order Open-->
                    {{--<input type="hidden" id="hiddenValue" />
                    <button type="submit"
                            class="col-md-2 btn btn-success btn-sm reattempt-order scan-for-reattempt-order"
                            id="showValue"
                            data-object="{}"
                            data-id=""
                            data-tracking_id="" style="display: none">Scan for reattempt
                    </button>--}}
                    <!--Button For Re-Attempt Order Close-->
                    <!--Open X_Content Div Of Table-->
                    <div class="x_content">
                    <!--Open Table Responce Div-->
                   <!--  <div class="table-responsive"> -->

                    <!--Open Table Tracking Order List-->
                        <table class="table table-striped table-bordered return-order-datatable" data-form="deleteForm">
                            <thead>
                            <tr>
                                {{--<th>All<input type="checkbox" name="tracking-ids[]" class="select-all"/></th>--}}
                                <th>Order Id</th>
                                <th>Tracking Id</th>
                                <th>Route Number</th>
                                <th style="width: 29%">Customer Address</th>
                                <th>Customer Phone</th>
                                <th>Status</th>
                                <th>Scan At</th>
                                <th>Process At</th>
                                <th>Scan By</th>
                                <th>Verified By</th>
                                <th>Verified At</th>
                                <th>Count Of Reattempts Left</th>
                                <th>Verify Note</th>
                               {{-- <th>Action</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($approved_order_list as $approve_order)
                                <tr class="tr-no-{{$approve_order->id}} tr-tracking-no-{{$approve_order->tracking_id}}"  data-id="{{$approve_order->id}}"
                                    data-tracking_id="{{$approve_order->tracking_id}}">
                                    {{--<td>
                                        <Input class="reattemptCheckbox" type="checkbox" value="{{$approve_order->tracking_id }}" data-reattempt="{{$approve_order->id }}" name="scan-reattempt">
                                    </td>--}}
                                    <td>{{$approve_order->sprint_id}}</td>
                                    <td>{{$approve_order->tracking_id}}</td>
                                    <td>{{$approve_order->route_id}}</td>
                                    <td>{{$approve_order->customer_address.', '.$approve_order->postal_code}}</td>
                                    <td>{{$approve_order->customer_phone}}</td>
                                    <td> @if (isset($statusId[$approve_order->status_id])) {{ $statusId[$approve_order->status_id]}} @endif</td>
                                    <td>{{ConvertTimeZone($approve_order->created_at,'UTC','America/Toronto')}}</td>
                                    <td>{{ConvertTimeZone($approve_order->proceed_at,'UTC','America/Toronto')}}</td>
                                    <td>@if (isset($approve_order->ScanByUser)) {{$approve_order->ScanByUser->full_name}}@endif</td>
                                    <td> @if (isset($approve_order->VerifiedByUser)) {{$approve_order->VerifiedByUser->full_name}}@endif</td>
                                   <td>{{ConvertTimeZone($approve_order->verified_at,'UTC','America/Toronto')}}</td>
                                    <td>{{$approve_order->reattempt_left}}</td>
                                    <td class="notes-td">
                                        {{$approve_order->varify_note}}
                                        <br>
                                        <a href="{{backend_url('notes/'.$approve_order->id)}}" target="_blank" title="Detail" class="btn btn-success btn-sm show-notes"><i class="fa fa-tags notes-icon"></i></a>
                                    </td>
                                   {{-- <td>
                                        <button type="submit" class="col-md-12 btn btn-success btn-sm scan-for-reattempt-order scan-for-reattempt-order-{{$approve_order->tracking_id}}"
                                                data-id="{{$approve_order->id}}"
                                                data-tracking_id="{{$approve_order->tracking_id}}">Scan for reattempt
                                        </button>
                                    </td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <!--Close Table Tracking Order List-->
                   <!--  </div> -->
                        <!--Close Table Responce Div-->
                </div>
                    <!--Close X_Content Div Of Table-->

                </div>
                <!--Open X_Panel Div-->
            </div>
            <!--Close Col Div-->
        </div>
        <!--Close Row Div-->
    </div>
    <!--Close Main Div-->

    <!--re-scan-for-reattempt-model-open-->
     <div id="reattempt-confirm-model" class="modal" style="display: none">
         <div class='modal-dialog'>
             <div class='modal-content'>
                 <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal">&times;</button>
                     <h4 class="modal-title">Re-scan order for reattempt </h4>
                 </div>
                 <div class="modal-body">
                     <form action="" class="reattempt-order-from">
                         <input type="hidden" class="tracking_id" name="tracking_id" value="">
                         <input type="hidden" class="id" name="id" value="">
                         <div class="form-group">
                             <p>Re-scan tracking id for confirmation</p>
                             <input name="re_scan_tracking_id" class="re-scan-tracking-id form-control" value="">
                             <p class="input-error">Scan id does not match, Please re-scan!</p>
                         </div>
                     </form>
                     <div class="form-group">
                         <a type="submit" class="btn btn-success green-gradient  reattempt-order" data-id="" data-tracking_id="" >Reattempt</a>
                     </div>
                 </div>
             </div>
         </div>
     </div>
    <!--re-scan-for-reattempt-model-close-->
@endsection