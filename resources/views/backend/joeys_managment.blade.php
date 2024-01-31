@extends( 'backend.layouts.app' )

@section('title', 'Joeys Statistics')
@section('CSSLibraries')
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{!! backend_asset('plugins/select2/select2.css') !!}"/>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>   
    <link rel="stylesheet" type="text/css" href="{!! backend_asset('plugins/select2/select2-metronic.css') !!}"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css"
          rel="stylesheet"/>
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet"/>
    <link href="{{ backend_asset('libraries/first-mile-hub/index.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
@section('content')

    <div class="page-content-wrapper cutomMainbox_us">
        <div class="page-content" style="min-height:1013px !important">
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">Joey Statistics
                        <small></small>
                    </h3>
                    <ol class="breadcrumb">

                        <li class="breadcrumb-item active">Drivers Statistics</li>

                    </ol>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE HEADER-->

            <div>
                <!-- Filter bar -->
                <form method="get" action="">
                    <div class="row">
                        <div class="col-md-6 filter_bar">
                            <div class="filter_form">
                                <select name="days" id="days" class="form-control">
                                    <option value="all" name="all">All</option>
                                    <option value="3days" name="3days">Last 3 days</option>
                                    <option value="lastweek" name="lastweek">Last week</option>
                                    <option value="15days" name="15days">Last 15 days</option>
                                    <option value="lastmonth" name="lastmonth">Last Month ({{$lastMonth}})</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
                <form method="POST" action="{{ route('addJoey') }}">
                    <div class="row">
                        <div class="col-md-6 filter_bar find_rider">
                            <div class="total_joeys_signup">
                                <h3 class="totalSignupNumber">ADD</h3>
                                <p>New Drivers</p>
                            </div>
                            <div class="filter_form">
                                <select name="joeys" id="joeys" class="form-control joeys-list">
                                    <option value="" name="Liam">Select Driver</option>
                                    @foreach($joeys_list as $joey)
                                    <option value="{{$joey->id}}" name="Liam">{{$joey->first_name.' '.$joey->last_name}}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </div>
                    </div>
                </form>


                <!-- signup stages list - [start] -->
                <div class="stages_list">
                    <div class="col active">
                        <div class="stage_box link-wrap">
                            <i class="fa fa-caret-down"></i>
                            <a href="#" class="link" data-id="totalApplicationSubmissionTable"></a>
                            <div class="number">{{$totalApplicationSubmissionCount}}</div>
                            <div class="label">Application Submission</div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="stage_box link-wrap">
                            <i class="fa fa-caret-down"></i>
                            <a href="#" class="link" data-id="totalQuizPassedTable"></a>
                            <div class="number">{{$totalQuizPassedCount}}</div>
                            <div class="label">Quiz Passed</div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="stage_box link-wrap basicRegistration ">
                            <i class="fa fa-caret-down"></i>
                            <a href="#" class="link" data-id="joeysOnDuty"></a>
                            <div class="number">{{$basicRegistration}}</div>
                            <div class="label">Drivers On Duty</div>
                        </div>
                    </div>
                </div>
                <!-- signup stages list - [/end] -->

                <div class="graph_n_data_wrap">
                    <div class="row graph_n_data">

                        <div class="col-md-12">
                            <div class="loading_wrap" style="display: none;">
                                <img src="<?php echo (asset('assets/admin/img/giphy.gif')); ?>" alt="">
                            </div>
                            <div class="data_list_wrap " style="display: none">
                                <div class="portlet-body">
                                    <table id="totalApplicationSubmissionTable" class="table table-striped table-bordered table-hover hidden data-table" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th style="width: 5%" class="text-center ">ID</th>
                                            <th style="width: 30%" class="text-center ">Name</th>
                                            <th style="width: 10%" class="text-center ">Address</th>
                                            <th style="width: 10%" class="text-center ">Email</th>
                                            <th style="width: 20%" class="text-center ">Phone</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>

                                    <table id="totalQuizPassedTable" class="table table-striped table-bordered table-hover hidden data-table" style="width: 100%;" >
                                        <thead>
                                        <tr>
                                            <th style="width: 5%" class="text-center ">ID</th>
                                            <th style="width: 30%" class="text-center ">Name</th>
                                            <th style="width: 10%" class="text-center ">Address</th>
                                            <th style="width: 10%" class="text-center ">Email</th>
                                            <th style="width: 20%" class="text-center ">Phone</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                    <table id="joeysOnDuty" class="table table-striped table-bordered table-hover hidden data-table" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th style="width: 5%" class="text-center ">ID</th>
                                            <th style="width: 30%" class="text-center ">Name</th>
                                            <th style="width: 10%" class="text-center ">Address</th>
                                            <th style="width: 10%" class="text-center ">Email</th>
                                            <th style="width: 20%" class="text-center ">Phone</th>
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
    </div>
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>



        $(function(){

            var totalSignups = $('.total_joeys_signup .totalSignupNumber').text();
            console.log('totalSignups: ', totalSignups)
            $('.stage_box a').click(function(e){
                e.preventDefault();

                var thisId = $(this).data('id'),

                    thisNumber = $(this).closest('.stage_box').find('.number').text();
                calculation= Math.round(parseInt(thisNumber) / parseInt(totalSignups) * 100);

                percentage =(calculation)?calculation:0;

                $('#percentageCircle').attr('stroke-dasharray', percentage + ', 100');
                $('.percentage').attr('data-test', 'test').text(percentage + '%');
                console.log('percentage: ', percentage);

                $('.dataTables_wrapper').addClass('hidden');
                $('#' + thisId).removeClass('hidden');
                $('.total_number').text(thisNumber);

                $('#'+thisId).attr('data-test', 'test');
                $('#'+thisId).DataTable().destroy();

                var filterVal=$('#days').val();
                $('#'+thisId).dataTable().fnDestroy();

                $('#'+thisId).DataTable( {
                    ajax: {
                        url: '{{url("joey")}}'+'/'+thisId+'?days='+filterVal,
                        type: "GET",
                    },
                    pageLength:  10,
                    lengthMenu : [10, 200, 300, 400,500],
                    "processing": true,
                    "serverSide": true,
                    columns: [
                        { data: 'id'},
                        { data: 'full_name'},
                        { data: 'address'},
                        { data: 'email'},
                        { data: 'phone'},
                    ]
                });




            });

            $('.stage_box').on('click', function(){

                $('.col').removeClass('active');
                $(this).closest('.col').addClass('active');

                $('.graph_n_data_wrap .loading_wrap').css('display', 'block');
                $('.graph_n_data_wrap .data_list_wrap').css('display', 'none');

                setTimeout(function(){
                    $('.graph_n_data_wrap .loading_wrap').css('display', 'none');
                    $('.graph_n_data_wrap .data_list_wrap').css('display', 'block');
                }, 1000);
            })
        })

        $('#days').val('<?php echo ( isset($_GET['days']) ) ? $_GET['days']: 'all'; ?>');



    </script>

    <script>
        $(document).ready(function () {
            $('#joeys').select2({
                minimumInputLength: 2,
                placeholder: "Search a joey",
                allowClear: true,
            });

        });
    </script>


@endsection