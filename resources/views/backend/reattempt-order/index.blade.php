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
    "147" => "Scanned at hub",
    "148" => "Scanned at Hub and labelled",
    "149" => "Bundle Pick From Hub",
    "150" => "Bundle Drop To Hub"
);
$statuses = array(
    "136" => "Client requested to cancel the order",
    "135" => "Customer refused delivery",
    "108" => "Customer unavailable-Incorrect address",
    "106" => "Customer unavailable - delivery returned",
    "107" => "Customer unavailable - Left voice mail - order returned",
    "109" => "Customer unavailable - Incorrect phone number",
    "142" => "Damaged at hub (before going OFD)",
    "143" => "Damaged on road - undeliverable",
    "110" => "Delivery to hub for re-delivery",
    "111" => "Delivery to hub for return to merchant",
    "102" => "Joey Incident",
    "104" => "Damaged on road - delivery will be attempted",
    "105" => "Item damaged - returned to merchant",
    "101" => "Joey on the way to pickup",
    "112" => "To be re-attempted",
    "131" => "Office closed - returned to hub",
    "145" => "Returned To Merchant",
    "146" => "Delivery Missorted, Incorrect Address",
    "147" => "Scanned at hub",
    "148" => "Scanned at Hub and labelled",
    "149" => "Bundle Pick From Hub",
    "150" => "Bundle Drop To Hub"
);
?>

@extends( 'backend.layouts.app' )

@section('title', 'Reattempt List')

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
            margin-top: 26px;
        }
        .pop_divider {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tracking_id
        {
            margin-top: 26px;
        }
        .input-error {
            color: red;
            padding: 10px 0px;
        }
        .form-submit-btn {
            margin-top: 26px;
            width: 100%;
            background-color: #c6dd38;
        }
        .filter-out-button .filter-out
        {
            margin-top: 26px;
            text-align: center;
            background-color: #c6dd38;
        }
        .show-notes{
            background-color: #C6DD38;
            border-style:none;
            padding: 6px 9px 6px 9px;
        }
        /* dragable div for route count */

        #mydiv {
            position: absolute;
            z-index: 9;
            background-color: #f1f1f1;
            text-align: center;
            border: 1px solid #d3d3d3;
        }

        #mydivheader {
            padding: 10px;
            cursor: move;
            z-index: 10;
            background-color: #c7dd1f;
            color: black;
        }

        @media (max-width: 991px) {
            div#count_modal {
                margin-top: 200px !important;
            }
        }
        @media (max-width: 768px) {
            .pop_divider {
                display: flex;
                flex-wrap: wrap;
                align-content: center;
            }
            .pop_divider button#refresh_order {
                margin-top: 10px;
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0&libraries=places" type="text/javascript"></script>
    <!-- Custom JavaScript -->
    <script src="{{ backend_asset('js/joeyco-custom-script.js')}}"></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">

        $('#datatable').DataTable({
            "lengthMenu": [250, 500, 750, 1000],
            "pageLength": 250
        });
        var checkNotificationToneUrl = 'notNotificationUrl';
        <!-- Datatable -->
        $(document).ready(function () {

            // make select box selected for plans
            {{--var old_plan_val = "{{($old_request_data && isset($old_request_data['status'])) ? trim($old_request_data['status']): '' }}";--}}

            /*make_option_selected_trigger('.status-select',old_plan_val);*/

            //Set Columns In Table After Scan
            let tracking_datatable = $('.return-order-datatable').DataTable({
                "columns": [
                    {"data": "checkbox"},
                    {"data": "order_id"},
                    {"data": "tracking_id"},
                    //{"data": "route_no"},
                    {"data": "customer_address"},
                    {"data": "customer_phone"},
                    {"data": "status"},
                    {"data": "count_of_reattempt_left"},
                    {"data": "customer_support_note","className":"verified-note-td"},
                    {"data": "sub_note"},
                    {"data": "action"}
                ],
                scrollX: true,   // enables horizontal scrolling,
                scrollCollapse: true,
                /*columnDefs: [
                    { width: '20%', targets: 0 }
                ],*/
                fixedColumns: true,
            });

            // submit tracking form action
            $('.search-tracking-id').submit(function (e) {

                e.preventDefault();
                //let counter = 1;
                let action = $(this).attr('action');
                let method = $(this).attr('method');
                let tracking_id = $(this).find('.tracking_id').val();
                // removing old value for re-scan
                $(this).find('.tracking_id').val('');
                showLoader();
                // calling ajax
                $.ajax({
                    type: method,
                    url: action,
                    data: {"tracking_id": tracking_id},
                    success: function (res) {

                        if(res.routeCounts != null){
                            document.getElementById("mydiv").style.display = 'block';
                            document.getElementById("route-id").innerHTML = res.routeCounts.route_id;
                            document.getElementById("order-count").innerHTML = res.routeCounts.order_count;
                            document.getElementById("pickup-count").innerHTML = res.routeCounts.pickup_count;
                            document.getElementById("reattempt-count").innerHTML = res.routeCounts.reattempt_count;
                            document.getElementById("delivered-count").innerHTML = res.routeCounts.delivered_count;
                            document.getElementById("return-count").innerHTML = res.routeCounts.return_count;
                            console.log(res.routeCounts)
                        }
                        else{
                            document.getElementById("mydiv").style.display = 'none';
                            console.log(res.routeCounts)
                        }

                        hideLoader();

                        // status is false
                        if (res.status == false) {

                            if (res.reattemptSuccess == 'true')
                            {
                                ShowSessionAlert('success', res.message);
                            }
                            else
                            {
                                ShowSessionAlert('danger', res.message);
                                return false;
                            }

                        }

                        //checking the status contain type for model open
                        if("type" in res)
                        {
                            open_model_on_scan_for_status_change(res);
                            return false;
                        }

                        //$(".x_content").find('.alert').remove();
                        // getting responce body
                        var responce_data = res.body;
                        //add row for tracking order
                        tracking_datatable.rows.add([responce_data]).draw(false);

                    },
                    error: function (error) {
                        ShowSessionAlert('danger', 'Something critical went wrong');
                        console.log(error);
                        hideLoader();
                    }
                });

            });

            // function for scan order and if type is model for change status
            function open_model_on_scan_for_status_change(res)
            {
                // opening model for re-scan
                $('#status-change-model-for-reattempt').modal();
                $('#status-change-model-for-reattempt').find(".modal-title").text(res.message);

                // setting tracking id and sprint_id
                $('#status-change-model-for-reattempt').find(".scan-modal-status-change-tracking-id").val(res.data.tracking_id);
                $('#status-change-model-for-reattempt').find(".scan-modal-status-change-sp-id").val(res.data.sprint_id);
                $('#status-change-model-for-reattempt').find(".scan-modal-status-change-task-id").val(res.data.task_id);

                // select element
                let select_box = $('.scan-modal-status-change');
                // remove all old values and add default value
                select_box.empty().append('<option value="">Select an option</option>');
                //adding responce options
                $.each(res.selection_data, function(val, text) {
                    select_box.append("<option value="+val+">"+text+"</option>");
                });

            }

            // status update from submit function
            $(document).on('submit','.scan-modal-status-change-form',function (e) {
                e.preventDefault();
                let el = $(this);
                let action = $(this).attr('action');
                let method = $(this).attr('method');
                let from_data_unformated  = el.serializeArray();
                let from_data = {};

                // creating formated form data
                from_data_unformated.forEach(function (value) {
                    from_data[value.name] = value.value;
                });

                showLoader();
                $.ajax({
                    type: method,
                    url: action,
                    data:from_data,
                    success: function (res) {
                        hideLoader();
                        // status is false
                        if (res.status == false) {
                            ShowSessionAlert('danger', res.message);
                            return false;
                        }
                        // show message
                        ShowSessionAlert('success', res.message);
                        // closing modal
                        $('#status-change-model-for-reattempt').modal('hide');
                        //triggering the scan tarcking id again for rescan order after status update
                        $('.tracking_id').val(res.tracking_id.trim());
                        $('.search-tracking-id').submit();


                    },
                    error: function (error) {
                        ShowSessionAlert('danger', 'Something critical went wrong');
                        console.log(error);
                        hideLoader();
                    }
                });

            });

            //Transfer to customer support for reattempt
            // directly transfered to customer support using function in controller
            $(document).on('click', '.transfer-order', function (e) {
                //var $form = $(this);
                let el = $(this);
                var transfer_id = el.attr("data-id");
                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to transfer?',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    buttons: {
                        'confirm': {
                            text: 'Proceed',
                            btnClass: 'btn-info',
                            action: function () {
                                var id = transfer_id;
                                showLoader();
                                $.ajax({
                                    type: "GET",
                                    url: "{{URL::to('/')}}/transfer/order/" + id,
                                    success: function (res) {

                                        hideLoader();
                                        // checking responce
                                        if (res.status == false) {
                                            ShowSessionAlert('danger', res.message);
                                            return false;
                                        }

                                        ShowSessionAlert('success', res.message);
                                        // removing tr in data table
                                        /*tracking_datatable.row($(el).parents('tr'))
                                            .remove()
                                            .draw();*/
                                        // adding note
                                        console.log($(el).parents('tr').find('.verified-note-td'));
                                        $(el).parents('tr').find('.verified-note-td').html('<span class="label label-success"> Waiting to customer support for approval  </span>');
                                        //removing buttons
                                        $(el).parents('tr').find('.btn').hide();

                                    },
                                    error: function (error) {
                                        ShowSessionAlert('danger', 'Something critical went wrong');
                                        console.log(error);
                                        hideLoader();
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

            //open model for re-scan Reattempt Order
            /*$(document).on('click', '.scan-for-reattempt-order', function () {

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

            });*/

            // validation of re-scan for reattempt
            /*$(document).on('submit','.reattempt-order-from',function (e) {

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
            });*/

            //Multiple Reattempt Order
            $(document).on('click', '.multiple-reattempt-order', function (e) {
                let el = $(this);
                var data_object = JSON.parse(el.attr('data-object'));

                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to multiple reattempt order?',
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
                                    url: "{{URL::to('/')}}/multiple/reattempt/order",
                                    data: {
                                        data: data_object
                                    },
                                    success: function (res) {
                                        hideLoader();
                                        console.log(res);
                                        let errors = res.errors_data;
                                        let success = res.success_data;
                                        let error_message = '';
                                        let success_message = '';
                                        let is_table_row_change = false;
                                        // looping on errors to show
                                        for (var index in errors) {
                                            error_message += errors[index].message + '<br>';
                                        }

                                        // looping up success messages
                                        for (var index in success) {
                                            console.log(index)
                                            success_message += success[index].message + '<br>';
                                            is_table_row_change = true;
                                            tracking_datatable.rows('.tr-no-' + index).remove();
                                            tracking_datatable.row($('.scan-for-reattempt-order-'+index).parents('tr'))
                                                .remove()
                                                .draw();
                                        }

                                        // checking  the error exist
                                        if (error_message != '') {
                                            ShowSessionAlert('danger', error_message);
                                        }

                                        if (success_message != '') {
                                            ShowSessionAlert('success', success_message);
                                        }

                                        // re draw datatable
                                        if (is_table_row_change) {
                                            tracking_datatable.rows().draw();
                                        }

                                        $('.scan-for-multiple-reattempt-order').hide();
                                        $('.scan-for-multiple-reattempt-order').attr('data-object', "{}");
                                        $('.reattemptCheckbox').prop('checked', false);
                                        $('.select-all').prop('checked', false);

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

            //Reattempt Order
            $(document).on('click', '.reattempt-order', function (e) {
                checkNotificationToneUrl = 'scan_for_reattempt';
                let el = $(this);
                var reattempt_id = el.attr("data-tracking_id");
                var ids = el.attr('data-id');

                // closeing model for re-scan
                $('#reattempt-confirm-model').modal('hide');

                $.confirm({
                    title: 'Confirmation',
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
                                        tracking_datatable.row($('.scan-for-reattempt-order-'+reattempt_id).parents('tr'))
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

            // model for re-scan inputs empty when it is closed
            /*$('#reattempt-confirm-model').on('hidden.bs.modal', function (e) {
                // removeing re-attempt btn data
                $('.reattempt-order').attr({"data-id":"","data-tracking_id":""});
                //removing model from input data
                $('#reattempt-confirm-model').find(".tracking_id").val('');
                $('#reattempt-confirm-model').find(".id").val('');
                $('#reattempt-confirm-model').find(".re-scan-tracking-id").val('');
            })*/

            // ajax function to update column
            $(document).on('click', '.update-address-on-change', function () {
                $(this).siblings(".datatable-input-update-btn").show();
            });

            // ajax function to update column
            $(document).on('click', '.datatable-input-update-btn', function () {
                let input_el = $(this).siblings('input');
                ColumnValueChangeByAjax(input_el);

            });

            // ajax function to show google map address suggestion
            $(document).on('click', '.update-address-on-change', function () {
                var acInputs = document.getElementsByClassName("google-address");

                const map = new google.maps.Map(document.getElementById("map"), {
                    center: {lat: 40.749933, lng: -73.98633},
                    zoom: 13,
                });
                const options = {
                    componentRestrictions: {country: "ca"},
                    fields: ["formatted_address", "geometry", "name","address_components"],
                    origin: map.getCenter(),
                    strictBounds: false,
                    types: ["establishment"],
                };
                for (var i = 0; i < acInputs.length; i++) {

                    var autocomplete = new google.maps.places.Autocomplete(acInputs[i], options);
//                    autocomplete.setComponentRestrictions({
//                        country: ["ca"],
//                    });
                    autocomplete.inputId = acInputs[i].id;
                }
                var address_sorted_object = {};
                google.maps.event.addListener(autocomplete, 'place_changed', function () {
                    var place = autocomplete.getPlace();
                    var address_components = place.address_components;

                    address_components.forEach(function (currentValue) {
                        address_sorted_object[currentValue.types[0]] = currentValue;
                    });

                    //var last_element = hh[hh.length - 1];
                    // add lat lng
                    $(acInputs).attr('data-lat',place.geometry.location.lat());
                    $(acInputs).attr('data-lng',place.geometry.location.lng());
                    $(acInputs).attr('data-postal-code',address_sorted_object.postal_code.long_name);
                    $(acInputs).attr('data-city',address_sorted_object.locality.long_name);


                });

            });

            // function column value change by ajax
            function ColumnValueChangeByAjax(element) {


                let el = $(element);
                //Getting Value From Ajax Request
                let type = el.attr('data-type');
                let ids = el.attr('data-match');
                let id = el.attr('data-id');
                let val = el.val();
                let lat = el.attr('data-lat');
                let lng = el.attr('data-lng');
                let postalcode = el.attr('data-postal-code');
                let city_val = el.attr('data-city');
                let old_val = el.attr('data-old-val');

                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to update?',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    buttons: {
                        'confirm': {
                            text: 'Proceed',
                            btnClass: 'btn-info',
                            action: function () {

                                // show loader
                                showLoader();

                                // convert data type according to type
                                if (type == 'customer_address') {
                                    ids = parseInt(ids);
                                    // checking the id is not 0 else return for hire not send  any ajax request
                                    if (ids < 1) {
                                        hideLoader();
                                        return;
                                    }
                                }
                                else if (type == 'customer_phone') {
                                    ids = parseInt(ids);
                                    // checking the id is not 0 else return for hire not send  any ajax request
                                    if (ids < 1) {
                                        hideLoader();
                                        return;
                                    }
                                }
                                else {
                                    ids = JSON.parse(ids);
                                }

                                // sending ajax request
                                $.ajax({
                                    type: "get",

                                    url: "{{URL::to('/')}}/reattempt/order/column/update",
                                    data: {
                                        type: type,
                                        ids: ids,
                                        id: id,
                                        val: val,
                                        lat: lat,
                                        lng: lng,
                                        postalcode: postalcode,
                                        city_val: city_val
                                    },
                                    success: function (response) {

                                        // update event status
                                        el.attr('data-event-status', 'false');
                                        $('.datatable-input-update-btn').hide();
                                        // Hide Remove Button On Edit Column Action
                                        $('.remove-reattempt-order').hide();
                                        // checking responce status
                                        if (response.status == true) // notifying user  the update is completed
                                        {
                                            // show session alert
                                            ShowSessionAlert('success', response.message);

                                            // updating old value to new  value
                                            el.attr('data-old-val', val);

                                        }
                                        else if (response.status == false) // update  failed by server
                                        {
                                            // show session alert
                                            ShowSessionAlert('danger', response.message);

                                            // setting previous value
                                            el.val(old_val);
                                        }
                                        else // some thing went wrong
                                        {
                                            alert('some error occurred please see the console');
                                            console.log(response);
                                        }

                                        //hide loader
                                        hideLoader();

                                    },
                                    error: function (error) {
                                        alert('some error occurred please see the console');
                                        console.log(error);
                                    }
                                });
                            }
                        },
                        cancel: function () {
                            el.val(old_val);
                        }
                    }
                });

            }

            //Return Order
            /* $(document).on('click', '.return-order', function (e) {
                 //var $form = $(this);
                 let el = $(this);
                 var sprint_id = el.attr("data-sprint-id");
                 var return_process_id = el.attr('data-id');
                 $.confirm({
                     title: 'A secure action',
                     content: 'Are you sure you want to return?',
                     icon: 'fa fa-question-circle',
                     animation: 'scale',
                     closeAnimation: 'scale',
                     opacity: 0.5,
                     buttons: {
                         'confirm': {
                             text: 'Proceed',
                             btnClass: 'btn-info',
                             action: function () {
                                 var id = sprint_id;
                                 showLoader();
                                 $.ajax({
                                     type: "GET",
                                     url: "{{--{{URL::to('/')}}--}}/backend/Return/order/"+ id,
                                    data: {
                                        return_process_id: return_process_id
                                    },
                                    success: function (res) {
                                    	hideLoader();
                                        // checking responce
                                        if (res.status == false) {
                                            ShowSessionAlert('danger', res.message);
                                            return false;
                                        }

                                        ShowSessionAlert('success', res.message);
                                        //el.closest("tr").remove();
                                        // removing tr in data table
                                        tracking_datatable.row($(el).parents('tr'))
                                            .remove()
                                            .draw();

                                    },
                                    error: function (error) {
                                    	hideLoader();
                                    	console.log(error);
                                        ShowSessionAlert('danger', 'Something critical went wrong');
                                    }
                                });
                            }
                        },
                        cancel: function () {
                            //$.alert('you clicked on <strong>cancel</strong>');
                        }
                    }
                });
            });*/

            //Call Ajax For Delete Order History From Reattempt and Return
            $(document).on('click', '.remove-reattempt-order', function (e) {
                let el = $(this);
                var reattempt_return_id = el.attr("data-id");

                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to remove?',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    buttons: {
                        'confirm': {
                            text: 'Proceed',
                            btnClass: 'btn-danger',
                            action: function () {
                                var id = reattempt_return_id;
                                showLoader();
                                $.ajax({
                                    type: "GET",
                                    url: "{{URL::to('/')}}/Reattempt/delete/" + id,
                                    success: function (res) {
                                        hideLoader();
                                        // checking responce
                                        if (res.status == false) {
                                            ShowSessionAlert('danger', res.message);
                                            return false;
                                        }

                                        ShowSessionAlert('success', res.message);
                                        // removing tr in data table
                                        tracking_datatable.row($(el).parents('tr'))
                                            .remove()
                                            .draw();
                                    },
                                    error: function (error) {
                                        hideLoader();
                                        console.log(error);
                                        ShowSessionAlert('danger', 'Something critical went wrong');
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

            //Disable Scanning Field And Button On Filter
            /*$(document).on('click', '.form-submit-btn', function (){

            $('.tracking_id').attr('disabled','disabled');
            $('.search-order').attr('disabled','disabled');
        });*/

            /*let  date_value = $('.search_date').val();
            let  status_value = $('.status-select').val();
            //alert(status_value);
            if (date_value !== '' || status_value !== '' )
            {
                $('.tracking_id').attr('disabled','disabled');
                $('.search-order').attr('disabled','disabled');
                $('.filter-out').show();
            }*/

            {{--$('.filter-out').click(function() {--}}
            {{--window.location.href = "{{route('reattempt-order.index') }}";--}}
            {{--});--}}

        });

        //ajax function for select all and condition for check all and uncheck
        $('.select-all').click(function() {

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

        });

        //loadCheck(); // initial call this function to load data

        $(document).on('click', '.reattemptCheckbox', function (e)
        {

            single_checkbox_fn($(this),'single');
        });

        // single checkbox fucntion
        function single_checkbox_fn(el,type = 'load') {
            let element = el;
            let tracking_id = element.val();
            let data_reattempt_id = element.attr('data-reattempt');
            let btn_data = JSON.parse($('.scan-for-multiple-reattempt-order').attr('data-object'));
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
                $('.scan-for-multiple-reattempt-order').hide();
            }
            else if(type == 'single' && Object.keys(btn_data).length > 0)
            {
                $('.scan-for-multiple-reattempt-order').show();
            }
            // updated
            btn_data = JSON.stringify(btn_data);
            $('.scan-for-multiple-reattempt-order').attr('data-object',btn_data);
        }

        //Function for get and set value for reattempt
        function loadCheck(type) {

            // getting all check box
            var all_checkbox_count = $('.reattemptCheckbox');
            // getting checked boxes
            var reattemptCheckbox_checked = $('input[name="scan-reattempt"]:checked');
            // get button
            let button = $('.multiple-reattempt-order');

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

        }

        function selectAll(source) {

            checkboxes = document.getElementsByName('foo');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }
        $(".print-label").click(function(){

            var id = [];
            var contect_array = [];
            $(".print-label-id:checked").each(function(){
                id.push($(this).val());
                contect_array.push($(this));
            });
            if (id.length > 0) {
                $('#sprintId').val(id);

                let model_el = $('#print-label-modal').modal();
            }else{
                alert("Please select atleast one checkbox");
            }


        });

    </script>
    <script>
        //Make the DIV element draggagle:
        dragElement(document.getElementById("mydiv"));

        function dragElement(elmnt) {
            var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
            if (document.getElementById(elmnt.id + "header")) {
                /* if present, the header is where you move the DIV from:*/
                document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
            } else {
                /* otherwise, move the DIV from anywhere inside the DIV:*/
                elmnt.onmousedown = dragMouseDown;
            }

            function dragMouseDown(e) {
                e = e || window.event;
                e.preventDefault();
                // get the mouse cursor position at startup:
                pos3 = e.clientX;
                pos4 = e.clientY;
                document.onmouseup = closeDragElement;
                // call a function whenever the cursor moves:
                document.onmousemove = elementDrag;
            }

            function elementDrag(e) {
                e = e || window.event;
                e.preventDefault();
                // calculate the new cursor position:
                pos1 = pos3 - e.clientX;
                pos2 = pos4 - e.clientY;
                pos3 = e.clientX;
                pos4 = e.clientY;
                // set the element's new position:
                elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
                elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
            }

            function closeDragElement() {
                /* stop moving when mouse button is released:*/
                document.onmouseup = null;
                document.onmousemove = null;
            }
        }

        setInterval(ajaxCall, 5000); //300000 MS == 5 minutes
        var customer_count = $('#count_orders_now').val();
        console.log(checkNotificationToneUrl);

        function ajaxCall(checkNotificationToneUrl) {
            $.ajax({
                url: '{{ URL::to('approved/customer/order/count')}}',
                type: 'GET',
                success: function (response) {
                    if(customer_count != response.count){
                        if(checkNotificationToneUrl != 'scan_for_reattempt'){
                            var buzzer_valid = $('#valid_sound')[0];
                            $('#count_modal').css('display','block');
                            buzzer_valid.play();
                            customer_count = response.count;
                        }
                    }
                },
                error:function(err){
                    console.log(err);
                }
            })
        }

        $('#refresh_order').on('click',function(){
            location.reload();
        })

        $('#count_modal_close').on('click',function(){
            $('#count_modal').hide();
        })


    </script>
@endsection

@section('content')
    @include( 'backend.layouts.loader' )
    <div id="map"></div>
    <!--Open Main Div-->
    <div class="right_col" role="main">
        <!--MOdal for count-->
        <div class="alert alert-primary" id="count_modal" style="display:none;color: #004085;
                background-color: #cce5ff;
                border-color: #b8daff;
                margin-top: 15px;"
             role="alert">

            <button type="button" class="close" id="count_modal_close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="pop_divider">
                <input type="hidden" name="count_orders_now" id="count_orders_now" value="{{$approved_order_list_count}}">
                <audio id="valid_sound">
                    <source src="{{app_asset('/media/beep-07.mp3')}} "  type="audio/mpeg">
                </audio>

                Order Address/Phone-no has been updated through customer support, Please create reattempt!
                <button class="btn" id="refresh_order" style="background-color: #b2d304;"> Refresh</button>
            </div>

        </div>
        <!--MOdal for count-->
        <!--Open Heading Div-->
        <div class="page-title">
            <div class="title_left">
                <h3>Reattempt Order list
                    <small></small>
                </h3>
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
                        <form class="search-tracking-id" action="{{ route('tracking-order.search') }}" method="get">
                            <!--Open Form Row Div-->
                            <div class="row">
                                <!--Open Input Tracking ID Div-->
                                <div class="col-sm-4 col-md-4">
                                    <input class="form-control tracking_id" type="text" placeholder="Tracking Id" required="required"/>
                                </div>
                                <!--Close Input Tracking ID Div-->
                                <!--Open Search Button Div-->
                                <div class="col-sm-4 col-md-2">
                                    <button class="form-control search-order">Search</button>
                                </div>
                                <!--Close Search Button Div-->
                            </div>
                            <!--Close Form Row Div-->
                        </form>
                        <!--Close Form For Tracking ID Search-->

                      
                        <div class="clearfix"></div>
                    </div>
                    <!--Close x_title Div-->
                    <!--table-top-form-from-open-->
                {{--<form class="form-horizontal table-top-form-from">
                    <!--table-top-form-row-open-->
                    <div class="row table-top-form-row">
                        <!--table-top-form-col-warp-open-->
                        <div class="col-sm-3 col-md-4 table-top-form-col-warp">
                            <label class="control-label">Select Date</label>
                            <input name="search_date" max="{{date('Y-m-d')}}" value="@if($old_request_data){{trim($old_request_data['search_date'])}}@endif"  type="date" class="form-control search_date">
                        </div>
                        <!--table-top-form-col-warp-close-->

                        <!--table-top-form-col-warp-open-->
                        <div class="col-sm-3 col-md-4 table-top-form-col-warp">
                            <label class="control-label">Status</label>
                            <select name="status"   class="form-control status-select">
                                <option value="">Select an option</option>
                                @foreach($statuses as $key => $status)
                                    <option value="{{$key}}">{{$status}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--table-top-form-col-warp-close-->

                        <!--table-top-form-col-warp-open-->
                        <div class="col-sm-3 col-md-2 table-top-form-col-warp">
                            <button class="btn orange form-submit-btn"  type="submit"> Filter </button>
                        </div>
                        <!--table-top-form-col-warp-close-->

                        <!--Close Filter-Out Button Div Open-->
                        <div class="col-sm-3 col-md-2 filter-out-button ">
                            <a href="{{route('reattempt-order.index') }}" class="form-control filter-out" style="display: none">Clear Filter</a>
                            --}}{{--<button class="form-control filter-out" style="display: none">Clear Filter</button>--}}{{--
                        </div>
                        <!--Close Filter-Out Button Div Close-->
                    </div>
                    <!--table-top-form-row-close-->
                </form>--}}
                @include( 'backend.layouts.notification_message' )
                <!--table-top-form-from-open-->
                    <!--Open X_Content Div Of Table-->
                    <div class="x_content">
                    @include( 'backend.layouts.errors' )
                    <!--Open Table Responce Div-->
                        <!-- <div class="table-responsive"> -->
                        <div class="x_title">   <!---x_title-->
                            <!--Button For Re-Attempt Order Open-->
                            <input type="hidden" id="hiddenValue" />
                            <button type="submit"
                                    class="col-md-2 btn btn-success btn-sm multiple-reattempt-order scan-for-multiple-reattempt-order"
                                    id="showValue"
                                    data-object="{}"
                                    data-id=""
                                    data-tracking_id="" style="display: none">Scan for reattempt
                            </button>
                            <!--Button For Re-Attempt Order Close-->
                            <div class="clearfix">  <!---clearfix-->

                            </div> <!---clearfix end-->
                        </div>
                        <!--Open Table Tracking Order List-->
                        <table class="table table-striped table-bordered return-order-datatable" data-form="deleteForm">
                            <thead>
                            <tr>
                                <th style="width: 8%" data-orderable="false">All<input type="checkbox" name="tracking-ids[]" class="select-all"/></th>
                                <th style="width: 8%">Order Id</th>
                                <th style="width: 10%">Tracking Id</th>
                                {{--<th style="width: 7%">Route Number</th>--}}
                                <th style="width: 22%">Customer Address</th>
                                <th style="width: 15%">Customer Phone</th>
                                <th style="width: 7%">Status</th>
                                <th style="width: 5%">Count Of Reattempts Left</th>
                                <th style="width: 16%" >Customer Support Note</th>
                                <th style="width: 7%">Sub Notes</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($return_reattempt_history as $history)

                                <?php
                                //checking reattempt left is available
                                $reattempt_status = ( $history->reattempt_left > 0 ||  $history->reattempt_left == null) ? true: false;

                                ?>

                                <tr class="tr-no-{{$history->id}}">
                                    @if(in_array($history->status_id, $transfer_status_ids) && $reattempt_status == true && $history->verified_by < 1 )
                                        <td>

                                        </td>
                                    @elseif(!in_array($history->status_id,$confirm_return_status_ids) && $reattempt_status == true || $history->verified_by > 0)
                                        <td>
                                            <Input class="reattemptCheckbox" type="checkbox" value="{{$history->tracking_id }}" data-reattempt="{{$history->id }}" name="scan-reattempt">
                                        </td>
                                    @endif

                                    <td>{{$history->sprint_id}}</td>
                                    <td>{{$history->tracking_id}}</td>
                                    {{--<td>{{$history->route_id}}</td>--}}
                                <!--Open Table Input For Address-->
                                    <td>
                                        {{--comment this input becouse edit fucntion remove 2021-04-28--}}
                                        {{--@if(in_array($history->status_id, $transfer_status_ids) && $history->verified_by < 1 && $reattempt_status == true)--}}
                                        {{--<div class="table-input-with-btn">--}}
                                        {{--<input type="text" data-event-status="false"--}}
                                        {{--data-type="customer_address"--}}
                                        {{--data-match="{{$history->location_id}}"--}}
                                        {{--data-id="{{$history->id}}"--}}
                                        {{--data-lat=""--}}
                                        {{--data-lng=""--}}
                                        {{--data-city=""--}}
                                        {{--data-postal-code=""--}}
                                        {{--data-old-val="{{$history->customer_address.' '.$history->postal_code}}"--}}
                                        {{--class="form-control update-address-on-change google-address"--}}
                                        {{--value="{{$history->customer_address.', '.$history->postal_code}}"/>--}}
                                        {{--<button class="datatable-input-update-btn fa fa-pencil"--}}
                                        {{--style="display: none"></button>--}}
                                        {{--</div>--}}
                                        {{--@else--}}
                                        {{--{{$history->customer_address.', '.$history->postal_code}}--}}
                                        {{--@endif--}}
                                        {{$history->customer_address.', '.$history->postal_code}}
                                    </td>
                                    <!--Close Table Input For Address-->
                                    <!--Open Table Input For Phone-->
                                    <td>
                                        {{--@if(in_array($history->status_id, $transfer_status_ids) && $history->verified_by < 1 && $reattempt_status == true )--}}
                                        {{--<div class="table-input-with-btn">--}}
                                        {{--<input type="text" data-event-status="false" data-type="customer_phone"--}}
                                        {{--data-match="{{$history->sprint_contact_id}}"--}}
                                        {{--data-id="{{$history->id}}"--}}
                                        {{--data-old-val="{{$history->customer_phone}}"--}}
                                        {{--class="form-control update-address-on-change"--}}
                                        {{--value="{{$history->customer_phone}}" name="phone"/>--}}
                                        {{--<button class="datatable-input-update-btn fa fa-pencil"--}}
                                        {{--style="display: none"></button>--}}
                                        {{--</div>--}}
                                        {{--@else--}}
                                        {{--{{$history->customer_phone}}--}}
                                        {{--@endif--}}
                                        {{$history->customer_phone}}
                                    </td>
                                    <!--Close Table Input For Phone-->
                                    @if (isset($statusId[$history->status_id]))
                                        <td>{{ $statusId[$history->status_id]}}</td>
                                    @else
                                        <td></td>
                                    @endif
                                    <td>
                                        @if($history->reattempt_left == 1)
                                            {{$history->reattempt_left - 1}}
                                        @else
                                            {{$history->reattempt_left}}
                                        @endif
                                    </td>
                                    @if($history->status_id == 108)
                                        <td class="verified-note-td">{{$history->varify_note}} @if($history->verified_by > 0 ) <span class="label label-success">Ready for process  </span> @elseif($history->process_type == 'customer_support') <span class="label label-success"> Waiting to customer support for address approval </span> @endif</td>
                                    @elseif($history->status_id == 109)
                                        <td class="verified-note-td">{{$history->varify_note}} @if($history->verified_by > 0 ) <span class="label label-success">Ready for process  </span> @elseif($history->process_type == 'customer_support') <span class="label label-success"> Waiting to customer support for phone number approval </span> @endif</td>
                                    @else
                                        <td class="verified-note-td">{{$history->varify_note}} @if($history->verified_by > 0 ) <span class="label label-success">Ready for process  </span> @elseif($history->process_type == 'customer_support') <span class="label label-success"> Waiting to customer support for return to merchant approval </span> @endif</td>
                                    @endif
                                    <td>
                                        <a href="{{backend_url('notes/'.$history->id)}}" target="_blank" title="Detail" class="btn btn-success btn-sm show-notes"><i class="fa fa-tags notes-icon"></i></a>
                                    </td>
                                    <!--Open Action Button td-->
                                    <td>
                                        @if($history->process_type == 'routing_support')
                                            {{--<button type="submit" class="col-md-12 btn btn-warning btn-sm return-order" data-id="{{$history->id}}" data-sprint-id="{{$history->sprint_id}}">Return to merchant</button>--}}
                                            @if(in_array($history->status_id, $transfer_status_ids) && $reattempt_status == true && $history->verified_by < 1 )
                                                <button type="submit" class="col-md-12 btn btn-primary btn-sm transfer-order"
                                                        data-id="{{$history->id}}">Transfer to customer support
                                                </button>
                                            @elseif(!in_array($history->status_id,$confirm_return_status_ids) && $reattempt_status == true || $history->verified_by > 0)
                                                <button type="submit" class="col-md-12 btn btn-success btn-sm scan-for-reattempt-order reattempt-order scan-for-reattempt-order-{{$history->tracking_id}}"
                                                        data-id="{{$history->id}}"
                                                        data-tracking_id="{{$history->tracking_id}}">Scan for reattempt
                                                </button>
                                            @endif
                                            @if($history->is_action_applied < 1)
                                                <button type="submit" class="col-md-12 btn btn-danger btn-sm remove-reattempt-order"
                                                        data-id="{{$history->id}}">Remove
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                    <!--Close Action Button td-->
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                        <div id="mydiv" style="display: none;height: 300px;width: 300px;">
                            <div id="mydivheader">Route Details</div>
                            <div class="col-md-12 mt-4" style="margin-top: 12px">
                                <div class="col-md-6 text-left">Route Id:</div>
                                <div class="col-md-6 text-right" id="route-id"></div>
                            </div>
                            <div class="col-md-12" style="margin-top: 12px">
                                <div class="col-md-6 text-left">Order Count:</div>
                                <div class="col-md-6 text-right" id="order-count"></div>
                            </div>
                            <div class="col-md-12" style="margin-top: 12px">
                                <div class="col-md-6 text-left">Pickup Count:</div>
                                <div class="col-md-6 text-right" id="pickup-count"></div>
                            </div>
                            <div class="col-md-12" style="margin-top: 12px">
                                <div class="col-md-6 text-left">Delivered Count:</div>
                                <div class="col-md-6 text-right" id="delivered-count"></div>
                            </div>
                            <div class="col-md-12" style="margin-top: 12px">
                                <div class="col-md-6 text-left">Return Count:</div>
                                <div class="col-md-6 text-right" id="return-count"></div>
                            </div>
                            <div class="col-md-12" style="margin-top: 12px">
                                <div class="col-md-6 text-left">Reattempt Count:</div>
                                <div class="col-md-6 text-right" id="reattempt-count"></div>
                            </div>
                        </div>
                        <!--Close Table Tracking Order List-->
                        <!-- </div> -->
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
    {{--<div id="status-change-confirm-model" class="modal" style="display: none">
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Re-scan order for reattempt </h4>
                </div>
                <div class="modal-body">
                    <form action="" class="status-change-order-form">
                        <input type="hidden" class="id" name="id" value="">
                        <div class="form-group">
                            <p>Change Status</p>
                            <select class="form-control">
                                <option>
                                    Select Status
                                </option>
                            </select>
                        </div>
                    </form>
                    <div class="form-group">
                        <a type="submit" class="btn btn-success green-gradient  reattempt-order" data-id="" data-tracking_id="" >Reattempt</a>
                    </div>
                </div>
            </div>
        </div>
    </div>--}}
    <!--re-scan-for-reattempt-model-close-->

    <!--status-change-model-for-reattempt-open-->
    <div id="status-change-model-for-reattempt" class="modal" style="display: none">
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form action="{{route('update-status-of-scanned-order')}}" method="get" class="scan-modal-status-change-form">
                        <input type="hidden" class="scan-modal-status-change-tracking-id" name="tracking_id" value="">
                        <input type="hidden" class="scan-modal-status-change-sp-id" name="sp_id" value="">
                        <input type="hidden" class="scan-modal-status-change-task-id" name="task_id" value="">
                        <div class="form-group">
                            <p>Change Status</p>
                            <select name="scan_modal_status_change" class="form-control scan-modal-status-change" required></select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary green-gradient ">Update status</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <!--status-change-model-for-reattempt-open-->

@endsection