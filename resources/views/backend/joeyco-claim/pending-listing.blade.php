<?php
use App\Joey;
use App\Vehicle;
use App\SlotsPostalCode;
use App\Slots;
use App\Sprint;



?>
@extends( 'backend.layouts.app' )
@section('title', 'Pending Claims List')
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

                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            <div class="x_title">
                                <form method="get" class="row d-flex align-item-end" action="" id="searchform">
                                    <div class="col-md-3">
                                        <label>Search By Start Date :</label>
                                        <input type="" id="datepicker1" name="datepicker1" class="data-selector form-control"
                                               required=""
                                               value="{{ isset($_GET['datepicker1'])?$_GET['datepicker1']: date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Search By End Date :</label>
                                        <input type="" id="datepicker2" name="datepicker2" class="data-selector form-control"
                                               required=""
                                               value="{{ isset($_GET['datepicker2'])?$_GET['datepicker2']: date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Vendor</label>
                                        <select class="form-control" name="vendor_id" id="">
                                            <option value="">Select an option</option>
                                            @foreach ($vendors as $vendorKey => $vendorValue)
                                                <option <?php if($vendorKey==$filterVendor){echo "selected";} ?> value="{{$vendorKey}}">{{$vendorValue}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>POD</label>
                                        <select class="form-control" name="pos" id="">
                                            <option  <?php if($filterpos==''){echo "selected";} ?>  value="">Select an option</option>

                                            <option  <?php if($filterpos!=''&&$filterpos==1){echo "selected";} ?> value="1">Yes</option>
                                            <option <?php if($filterpos!=''&&$filterpos==0){echo "selected";} ?> value="0">No</option>

                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Broker</label>
                                        <select class="form-control" name="brooker_id" id="">
                                            <option value="">Select an option</option>
                                            @foreach ($brokers as $brokersKey => $brokersValue)
                                                <option <?php if($brokersKey==$filterBroker){echo "selected";} ?> value="{{$brokersKey}}">{{$brokersValue}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Status</label>
                                        <select class="form-control" name="sprint_status_id" id="">
                                            <option value="">Select an option</option>
                                            @foreach ($sprint_statuses as $sprint_statusKey=>$sprint_statusValue)
                                                <option <?php if($sprint_statusKey==$filterSprintStatus){echo "selected";} ?> value="{{$sprint_statusKey}}">{{$sprint_statusValue}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Driver</label>
                                        <select class="form-control" name="joey_id" id="">
                                            <option value="">Select an option</option>
                                            @foreach ($joeys as $joeyKey => $joeyValue)
                                                <option  <?php if($joeyKey==$filterJoey){echo "selected";} ?> value="{{$joeyKey}}">{{$joeyValue}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn blue btn green-gradient"  type="button"  onclick="checkDateDiff()" style="margin-top: 25px;margin-bottom:0 !important;">Go</a> </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            
                        </div>
                        <br>


                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet box blue  margin-tops">
               
                <div class="col-lg-6">
                <h3>Pending Claims List </h3>    
                </div>
                <div class="col-lg-6 text-right align-items-baseline">
                                @if(can_access_route('pendingClaims.delete',$userPermissoins))
                                    <button  type='button' onclick="deleteClaims()"  class='btn btn-warning'> Delete All</button>
                                @endif
                                <button  type='button' onclick="exportTableToCSV('JoeyCo-Claims-Pending-List.csv')"  class='btn blue btn green-gradient' style="margin:0 !important">Generate Report in CSV</button>
                            </div>
                     <div class="x_content">
                     <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover yajrabox" id="sample_1">
                                    <thead>
                                    <tr>
                                        <th style="width: 5% !important;"  class="text-center "><input type="checkbox" onclick="checkall()" class="checkbox checkall text-center"></th>
                                        <th style="width: 20% !important;"  class="text-center ">Tracking Id / Merchant No</th>
                                        <th style="width: 10% !important;"  class="text-center ">Vendor</th>
                                        <th style="width: 10% !important;"  class="text-center ">Route No</th>
                                        <th style="width: 5% !important;"  class="text-center ">POD</th>
                                        <th style="width: 10% !important;"  class="text-center ">Broker</th>
                                        <th style="width: 5% !important;"  class="text-center ">Driver #</th>
                                        <th style="width: 10% !important;"  class="text-center ">Driver Name</th>
                                        <th style="width: 10% !important;"  class="text-center ">Order Status</th>
                                        <th style="width: 10% !important;"  class="text-center ">Value</th>
                                        <th style="width: 10% !important;"   class="text-center ">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                     </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
                <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- /#page-wrapper -->
    <!-- Modal -->
    <div id="openStatusChangeModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Change Status</h4>
                </div>
                <form method="post" action="{{route('claims.statusUpdate')}}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="claim_id" id="claim_id">
                        <label for="select status">Select Status*</label>
                        {{-- <select onchange="changeStatus('{{$record->id}}',this,'claims/status-update')" class="form-control" name="statusId-{{$record->id}}" id="statusId" onFocus="saveValue(this);"> --}}
                        <select required onchange="getReasons(this,'claims/get-reasons')" class="form-control" name="status_id" id="status_id">
                            <option disabled value="" selected>Select an option</option>
                            <option value="1">Approved</option>
                            <option  value="2">Not Approved</option>
                        </select>
                        <label for="reason">Select Reason*</label>
                        <select required  class="form-control" name="reason" id="reason_id">
                        </select>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update Status</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    {{-- Modal --}}
    <!-- Modal -->
    <div class="modal fade" id="uploadImageModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close closemodal" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Upload Image</h4>
                </div>
                <div class="modal-body">
                    <p><input type="file" name="file" id="file"  accept="image/*">
                        <input type="hidden" name="id" id="hiddenIdImageUploadModal">
                    </p>
                    <span style="background: #f1bfbf;;color:red">The image must be a file of type: jpeg, png, jpg.</span>

                </div>
                <div class="modal-footer">
                    <button type="button" onclick="uploadImageBtn('claims/status-upload-image')" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default closemodal" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
    <!-- Modal -->

    <script type="text/javascript">


        $(function () {
            appConfig.set('yajrabox.ajax', '{{ route('claims.pendingList-data') }}');
            appConfig.set('dt.order', [0, 'desc']);
            appConfig.set('yajrabox.scrollx_responsive',false);
            appConfig.set('yajrabox.ajax.data', function (data) {
                data.datepicker1 = jQuery('[name=datepicker1]').val();
                data.datepicker2 = jQuery('[name=datepicker2]').val();
                data.vendor_id = jQuery('select[name=vendor_id]').val();
                data.brooker_id = jQuery('select[name=brooker_id]').val();
                data.sprint_status_id = jQuery('select[name=sprint_status_id]').val();
                data.pos = jQuery('select[name=pos]').val();
                data.joey_id = jQuery('select[name=joey_id]').val();

            });
            appConfig.set('yajrabox.columns', [
                {data: 'checkbox',   orderable: false,   searchable: false, className:'text-center'},
                {data: 'tracking_id',  orderable: true,   searchable: true, className:'text-center'},
                {data: 'vendors',   orderable: false,    searchable: false, className:'text-center'},
                {data: 'route_ordinal',   orderable: false,    searchable: false, className:'text-center'},
                {data: 'image',  orderable: false,   searchable: false, className:'text-center' },
                {data: 'brookersUsers', orderable: false,   searchable: false, className:'text-center' },
                {data: 'joey_id', orderable: false,   searchable: false, className:'text-center' },
                {data: 'joey', orderable: false,   searchable: false, className:'text-center' },
                {data: 'sprint_status_id',   orderable: false,   searchable: false, className:'text-center' },
                {data: 'amount',   orderable: true,   searchable: true, className:'text-center' },
                // {data: 'status',   orderable: false,   searchable: false, className:'text-center' },
                // {data: 'reason',   orderable: false,   searchable: false, className:'text-center' },
                {data: 'action',   orderable: false,   searchable: false, className:'text-center actBtnFixWidth' },
            ]);
        })

        function exportTableToCSV(filename) {
            var csv = [];
            var rows = document.querySelectorAll("#sample_1 tr");

            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");

                for (var j = 0; j < cols.length; j++)
                    row.push(cols[j].innerText.replace(',',' ').replace(',',' ').replace(',',' ').replace(',',' '));

                csv.push(row.join(","));
            }

            // Download CSV file
            downloadCSV(csv.join("\n"), filename);
        }

        function downloadCSV(csv, filename) {
            var csvFile;
            var downloadLink;

            // CSV file
            csvFile = new Blob([csv], {type: "text/csv"});

            // Download link
            downloadLink = document.createElement("a");

            // File name
            downloadLink.download = filename;

            // Create a link to the file
            downloadLink.href = window.URL.createObjectURL(csvFile);

            // Hide download link
            downloadLink.style.display = "none";

            // Add the link to DOM
            document.body.appendChild(downloadLink);

            // Click download link
            downloadLink.click();
        }

        $('#reasonId').on('change keyup', function() {
            if (confirm("Are you sure you want to save reason?")) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('claims.reasonUpdate') }}",
                    data: {id: ids, _token: "{{ csrf_token() }}"},
                    success: function (res) {
                        // console.log(res);
                        $(".customrow").remove();
                        res.forEach(appendRows);
                        $('.trackingtablediv').css('display', 'block');
                    }
                });
            }
        });


        function deleteClaims() {
            var claimsId='';
            if($('table input[type=checkbox].checkone:checked').length==0) {
                alert('Atleast one claim should be checked.');
            }
            else{
                $("input:checkbox[name=claims_id]:checked").each(function(){
                    claimsId+=$(this).val()+',';
                });
                claimsId=claimsId.slice(0, -1)
                var url=  "{{url('claims/delete/')}}/"+claimsId;
                window.location.href = url;
            }
        }
        function checkall() {
            if($('table input[type=checkbox].checkall:checked').length==0) {
                $("input:checkbox[name=claims_id]").each(function(){
                    $(this).prop('checked', false);
                });
            }else{
                $("input:checkbox[name=claims_id]").each(function(){
                    $(this).prop('checked', true);
                });
            }
        }
    </script>


@endsection