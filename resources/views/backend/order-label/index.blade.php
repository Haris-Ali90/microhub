@extends( 'backend.layouts.app' )
@section('title', 'Orders Label List')
@section('CSSLibraries')
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css"
          rel="stylesheet"/>
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet"/>
    <link href="{{ backend_asset('libraries/first-mile-hub/index.css') }}" rel="stylesheet">
    <style>
        #print-label-modal {
            transform: translate(-25%, 50%) !important;
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
    <script>

        $(function () {
            appConfig.set('yajrabox.ajax', '{{ route('label-order.data') }}');
            appConfig.set('dt.order', [0, 'desc']);
            appConfig.set('yajrabox.scrollx_responsive',true);
            appConfig.set('yajrabox.autoWidth', false);
            appConfig.set('yajrabox.ajax.data', function (data) {
                data.datepicker = jQuery('[name=tracking]').val();

            });

            appConfig.set('yajrabox.columns', [
                {data: 'check_box', orderable: false, searchable: false, className: 'text-center'},
                {data: 'sprint_id', orderable: true, searchable: true, className: 'text-center'},
                {data: 'tracking_id', orderable: true, searchable: true, className: 'text-center'},
                {data: 'joey_name', orderable: true, searchable: true,className: 'text-center'},
                {data: 'customer_contact', orderable: true, searchable: true, className: 'text-center'},
                {data: 'task_status_id', orderable: true, searchable: true, className: 'text-center'},
                {data: 'created_at', orderable: true, searchable: true, className: 'text-center'},
                {data: 'action', orderable: false, searchable: false, className: 'text-center'},
            ]);
        })


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
                                    class="fa fa-close"></i></b></strong></button>
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
                    <h3>Orders Label List<small></small></h3>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panell">
                        <div class="x_title">
                            <?php
                            if (!isset($_REQUEST['tracking'])) {
                                $tracking = "";
                            } else {
                                $tracking = $_REQUEST['tracking'];
                            }
                            ?>
                            <form method="get" class="row d-flex align-item-end" action="" class="col-md-6">
                                <div class="col-lg-3">
                                    <label>Search By Tracking Id's</label>
                                    <input type="text" name="tracking" id="tracking" placeholder="Tracking Id's"
                                           value="<?php echo $tracking ?>" class="form-control">
                                </div>
                                <div class="col-lg-6">
                                    <button class="btn btn-primary" type="submit">
                                        Filter
                                    </button>
                                </div>
                            </form>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <div class="table-responsive">
                                <div class="x_title">   <!---x_title-->
                                    <input type="checkbox" onClick="selectAll(this)" /> Select All
                                    <button type="button" class="btn btn-warning btn-md print-label"  style="float:right;margin-bottom: 10px;">Print Selected</button><br>
                                    <div class="clearfix">  <!---clearfix-->

                                    </div> <!---clearfix end-->
                                </div>
                                <table class="table table-striped table-bordered yajrabox" id="yajra-reload">
                                    <thead stylesheet="color:black;">
                                    <tr>
                                        <th>CheckBox</th>
                                        <th>JoeyCo Order #</th>
                                        <th>Tracking Id</th>
                                        <th>Joey</th>
                                        <th>Customer Address</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
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
    {{--  Loader  --}}
    <div id="wait" style="display:none;position:fixed;top:50%;left:50%;padding:2px;"><img
            src="{{app_asset('images/loading.gif')}} " width="104" height="64"/><br></div>
    <!--model-for-zone-create-open-->
    <div class="modal fade" id="print-label-modal" role="dialog">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content" style="width: 400px; margin: 0 auto;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body" style=" justify-content: center; display: flex;">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-12 hoverable-dropdown-main-wrap">
                                <!--model-append-html-main-wrap-open-->
                                <div class="col-sm-12 model-zone-append-html-main-wrap">
                                    <p class="confirm-para">Are you sure to print label of selected Id?</p>
                                    <form class="form-horizontal table-top-form-from"  method="get" action="{{route('label-order.printLabel', 0)}}">
                                        {{ csrf_field() }}
                                        <input type="hidden" id="sprintId" name="sprintIds[]" value="">
                                        <div class="table-top-form-col-warp inline-form-btn-margin">
                                            <button class="btn orange btn-primary form-submit-btn" style="display: table; margin: 0 auto"> Save </button>
                                        </div>
                                    </form>
                                </div>
                                <!--model-append-html-main-wrap-close-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <!--model-for-zone-create-close-->

@endsection
