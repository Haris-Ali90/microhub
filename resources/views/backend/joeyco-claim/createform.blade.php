<?php
use App\Joey;
use App\Vehicle;
use App\SlotsPostalCode;
use App\Slots;
use App\Sprint;
?>
@extends( 'backend.layouts.app' )
@section('title', 'JoeyCo Claim Create')
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
                            <i class="fa fa-plus"></i> Al Rafeeq Claim Create
                        </div>
                    </div>

                    <div class="portlet-body">
                        <label for="">Select Type *</label>
                        {{-- <div class="form-check-inline" onclick="hideTable()">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" value="tracking_id" name="type" checked>Tracking Ids
                            </label>

                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" value="merchant_order_num" name="type">Merchant No
                            </label>
                        </div> --}}
                        <div class="form-check-inline cs-radio-list" onclick="hideTable()">
                            <label class="form-check-label cs-radio">
                                <input type="radio" class="form-check-input" value="tracking_id" name="type" checked>
                                <span>Tracking Ids</span>
                            </label>

                            <label class="form-check-label cs-radio">
                                <input type="radio" class="form-check-input" value="merchant_order_num" name="type">
                                <span>Merchant No.</span>
                            </label>
                        </div>
                        <br>
                        <label for="">Select Input Format *</label>
                        {{-- <div class="form-check"> --}}
                        {{-- <input class="form-check-input" type="radio" name="flexRadioDefault" onclick="displayDiv('textdiv','csvdiv');" id="flexRadioDefault1"> --}}
                        {{-- <input class="form-check-input" type="radio" name="flexRadioDefault"  id="flexRadioDefault1"> --}}
                        {{-- <label class="form-check-label" for="flexRadioDefault1"> --}}
                        {{-- Enter Manually --}}
                        {{-- </label> --}}
                        {{-- </div> --}}
                        {{-- <div class="form-check"> --}}
                        {{-- <input class="form-check-input" type="radio" name="flexRadioDefault" onclick="displayDiv('csvdiv','textdiv');" id="flexRadioDefault2"> --}}
                        {{-- <input class="form-check-input" type="radio" name="flexRadioDefault"  id="flexRadioDefault2"> --}}
                        {{-- <label class="form-check-label" for="flexRadioDefault2"> --}}
                        {{-- Upload CSV File --}}
                        {{-- </label> --}}
                        {{-- </div> --}}
                        <div class="form-check cs-radio-list">
                            <label class="form-check-label cs-radio" for="flexRadioDefault1">
                                <input class="form-check-input" type="radio" name="flexRadioDefault"  id="uniform-flexRadioDefault1">
                                <span>Enter Manually</span>
                            </label>
                            <label class="form-check-label cs-radio" for="flexRadioDefault2">
                                <input class="form-check-input" type="radio" name="flexRadioDefault"  id="uniform-flexRadioDefault2">
                                <span>Upload CSV File</span>
                            </label>
                        </div>
                        {{-- <div id="textdiv" style="display: none">
                            <div class="col-md-3">
                                <textarea rows='1' cols="180"  name="tracking_id" id="tracking_ids" class="form-control"
                                value="" style="margin-top:5px; margin-bottom:5px; border-radius: 5px; margin-right:5px;float: left;height: 3em;resize: none;"
                                placeholder="Tracking Id/Merchant No. eg:JoeyCo001,JoeyCo002" title='Search with multiple Tracking Id/Merchant No.'></textarea>
                            </div>
                            <div class="col-md-3">
                                <button type="button" onclick="validateTrackingId();" class="btn blue btn btn-primary" style="margin-top: 6px;">Submit</button>
                            </div>
                        </div> --}}
                        <div id="textdiv" class="row" style="display: none">
                            <div class="col-md-4">
                        <textarea rows='5' cols="18"  name="tracking_id" id="tracking_ids" class="form-control"
                                  value=""
                                  placeholder="Tracking Id/Merchant No. eg:JoeyCo001,JoeyCo002" title='Search with multiple Tracking Id/Merchant No.' style="max-width: 100%; min-width: 100%;"></textarea>
                            </div>
                            <div class="col-md-3">
                                <button type="button" onclick="hideTable();validateTrackingId();" class="btn blue btn btn-primary" style="margin-top: 6px;">Submit</button>
                            </div>
                        </div>
                        {{-- <div id="csvdiv" style="display: none">
                            <div class="col-md-3">
                                <input type="file" id="fileUpload" style="margin-top: 6px;border:0.5px solid #e5e5e5;width: 100%;" class="btn"/>
                            </div>
                            <div class="col-md-3">
                                <input type="button" id="upload" value="Upload" class="btn blue btn btn-primary"  style="margin-top: 6px;" />
                            </div>
                        </div> --}}
                        <div id="csvdiv" class="row" style="display: none">
                            <div class="col-md-4">
                                <input type="file" id="fileUpload" style="margin-top: 6px;border:0.5px solid #e5e5e5;width: 100%;" class="btn"/>
                            </div>
                            <div class="col-md-3">
                                <input type="button" id="upload" value="Upload" onclick="hideTable()" class="btn blue btn btn-primary"  style="margin-top: 6px;" />
                            </div>
                        </div>

                        {{-- <div id="dvCSV">
                        </div> --}}
                        <div class="col-md-12 trackingtablediv" style="display: none">
                            <div class="row" style="margin-top: 20px;" >
                                <div class="col-md-3">
                                    <label for="value">Value $</label>
                                    <input type="text" placeholder="10.01" step="0.0001" class="form-control" onchange="setAmount(this);">
                                    {{-- <select class="form-control" onchange="setAmount(this);">
                                        <option>Select amount</option>
                                        <option value="4">4</option>
                                        <option value="4.5">4.5</option>

                                    </select> --}}
                                </div>
                            </div>
                            <form style="margin-top: 20px;" id="single" method="post" action="{{route('claims.store')}}">
                                @csrf
                                <input type="hidden" name="claim_on" id="claim_on">
                                <table id="trackingtable" class="table">
                                    <thead>
                                    <tr>
                                        <th style="    text-align: center;">Tracking Id / Merchant No</th>
                                        <th style="    text-align: center;">Value $</th>
                                        <th style="    text-align: center;">Action</th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <div class="col-md-3">
                                    <button style="display:none;" id="submitBtnSave" type="submit" class="btn blue btn btn-primary" style="margin-top: 6px;">Save</button>
                                </div>
                            </form>
                        </div>

                        <h4>&nbsp;</h4>
                        <h4>&nbsp;</h4>



                    </div>
                </div>
                <!-- END SAMPLE FORM PORTLET-->

                </div>
    </div>

            <script type="text/javascript">
                function hideTable() {
                    console.log(1);
                    $("#trackingtable tbody").html("");
                    $('.trackingtablediv').css('display','none');
                }
                function validateTrackingId() {
                    // alert(type);
                    ids=$('#tracking_ids').val();
                    if(ids==null || ids==''){
                        alert('Tracking Id / Merchant No is required!');
                    }

                    else{
                        validateidajax(ids)
                    }

                }
                function validateidajax(ids){
                    search_type=$("input[name=type]:checked").val();
                    $('#submitBtnSave').css('display','none');
                    $.ajax({
                        type:'POST',
                        url:"{{ route('claims.validateTrackingId') }}",
                        data:{id:ids,_token: "{{ csrf_token() }}",type:search_type},
                        success:function(res){
                            // console.log(res);
                            $(".customrow").remove();
                            // console.log(res.length);
                            if(res.length > 0){
                                $('#claim_on').val(search_type);
                                res.forEach(appendRows);
                                $('.trackingtablediv').css('display','block');
                            }
                        }
                    });
                }

                function appendRows(item, index) {
                    //   text += index + ": " + item + "<br>";
                    // console.log("item= "+item.tracking_id+" index= "+index);
                    var data='';

                    if(item.is_valid==1){
                        $('#submitBtnSave').css('display','block');
                        if(item.is_required==0){
                            data=`  <tr class="customrow"><td>
                    <input type="hidden" name="task_id[]" value='${item.task_id}'>
                    <input type="hidden" name="vendor_id[]" value='${item.vendor_id}'>
                    <input type="hidden" name="sprint_id[]" value='${item.sprint_id}'>
                    <input type="hidden" name="sprint_status_id[]" value='${item.sprint_status_id}'>
                    <input readonly  type="text" value='${item.tracking_id}' name="tracking_id[]" required class="form-control tracking_id"/></td>
                    <td><input  type="number" ${item.amount_readonly} step="0.0001" name="amount[]" value='${item.amount}'  class="form-control amount changeamount-${item.amount_readonly}"/><span class="alert-danger">${item.msg}</span>
                    </td><td style="text-align: center;"><button class="btn btn-danger btn-sm" onclick = deleterow(this)>Remove</button>
                    </td></tr>`;
                        }else{
                            data=`  <tr class="customrow"><td>
                    <input type="hidden" name="task_id[]" value='${item.task_id}'>
                    <input type="hidden" name="vendor_id[]" value='${item.vendor_id}'>
                    <input type="hidden" name="sprint_id[]" value='${item.sprint_id}'>
                    <input type="hidden" name="sprint_status_id[]" value='${item.sprint_status_id}'>
                    <input readonly  type="text" value='${item.tracking_id}' name="tracking_id[]" required class="form-control tracking_id"/></td>
                    <td><input  type="number" ${item.amount_readonly} step="0.0001" name="amount[]" value='${item.amount}' required class="form-control amount changeamount-${item.amount_readonly}"/><span class="alert-danger">${item.msg}</span>
                    </td><td style="text-align: center;"><button class="btn btn-danger btn-sm" onclick = deleterow(this)>Remove</button>
                    </td></tr>`;
                        }

                    }else{
                        data=`<tr class="customrow"><td><input readonly  type="text" value='${item.tracking_id}'   class="form-control tracking_id"/></td><td style="text-align: center;">This tracking id / merchant no. is not valid.</td><td style="text-align: center;"><button class="btn btn-danger btn-sm" onclick = deleterow(this)>Remove</button></td></tr>`;
                    }
                    // $('#trackingtable tr:last').after(data);
                    $("#trackingtable tbody").append(data);
                }
                function displayDiv(showdiv_id,hidediv_id){
                    $('#'+hidediv_id).css('display','none');
                    $('#'+showdiv_id).css('display','block');
                }
                $(function () {
                    var t_ids='';
                    $("#upload").on("click", function () {
                        var t_ids='';


                        var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.csv|.txt)$/;
                        if (regex.test($("#fileUpload").val().toLowerCase())) {
                            if (typeof (FileReader) != "undefined") {
                                var reader = new FileReader();
                                reader.onload = function (e) {
                                    var table = $("<table />");
                                    var rows = e.target.result.split("\n");
                                    for (var i = 0; i < rows.length; i++) {
                                        var row = $("<tr />");
                                        var cells = rows[i].split(",");
                                        if (cells.length > 0) {
                                            for (var j = 0; j < cells.length; j++) {
                                                var cell = $("<td />");
                                                cell.html(cells[j]);
                                                row.append(cell);
                                                // console.log(cells[j]);
                                                // t_ids.push(cells[j]+',');
                                                t_ids+=cells[j]+',';
                                            }
                                            // table.append(row);
                                        }
                                        // console.log(t_ids);
                                    }
                                    // $("#dvCSV").html('');
                                    // $("#dvCSV").append(table);
                                    validateidajax(t_ids);
                                }
                                reader.readAsText($("#fileUpload")[0].files[0]);
                            } else {
                                alert("This browser does not support HTML5.");
                            }
                        } else {
                            alert("Please upload a valid CSV file.");
                        }
                        // $("#fileUpload").val(null);
                    });
                    $('#uniform-flexRadioDefault1').click(function(e) {
                        $("#uniform-flexRadioDefault2 span").removeClass("checked");
                        $("#uniform-flexRadioDefault1 span").addClass( "checked" );
                        displayDiv('textdiv','csvdiv')
                    });
                    $('#uniform-flexRadioDefault2').click(function(e) {
                        $("#uniform-flexRadioDefault1 span").removeClass("checked");
                        $("#uniform-flexRadioDefault2 span").addClass( "checked" );
                        displayDiv('csvdiv','textdiv')
                    });
                });
                function deleterow(el) {
                    $(el).closest('tr').remove();
                }
                function setAmount(param) {
                    $('.changeamount-').val(param.value);
                }
            </script>


@endsection