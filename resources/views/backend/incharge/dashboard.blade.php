@extends( 'backend.layouts.app' )

@section('title', 'Incharge Dashboard')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <!-- Image Viewer CSS -->
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <!-- Custom Light Box Css -->
    <link href="{{ backend_asset('css/custom_lightbox.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('css/icofont.min.css')}}" rel="stylesheet"/>
    <link href="{{ backend_asset('css/dashboard.css')}}" rel="stylesheet"/>
    <link href="{{ backend_asset('css/owl.carousel.min.css')}}" rel="stylesheet"/>
    <style>
        /*pie chart box css*/
        .row.charts-box-main-wrap {
            position: relative;
        }
        .charts-box-ajax-data-loader-wrap {
            position: absolute;
            top: 0px;
            left: 0px;
            z-index: 1;
            width: 100%;
            height: 100%;
            text-align: center;
            padding: 0px 10px;
            display: none;
        }
        .charts-box-ajax-data-loader-inner-wrap .lds-facebook {
            top: 47%;
        }

        .charts-box-ajax-data-loader-inner-wrap p
        {
            position: relative;
            top: 45%;
            color: #fff;
        }
        .dashboard-statistics-box {
            min-height: 600px;
            margin: 15px 0px;
            padding: 20px 0px;
            position: relative;
            box-sizing: border-box;
        }

        .lds-facebook {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
        }
        .lds-facebook div {
            display: inline-block;
            position: absolute;
            left: 8px;
            width: 16px;
            background: #fff;
            animation: lds-facebook 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
        }
        .lds-facebook div:nth-child(1) {
            left: 8px;
            animation-delay: -0.24s;
        }
        .lds-facebook div:nth-child(2) {
            left: 32px;
            animation-delay: -0.12s;
        }
        .lds-facebook div:nth-child(3) {
            left: 56px;
            animation-delay: 0;
        }
        @keyframes lds-facebook {
            0% {
                top: 8px;
                height: 64px;
            }
            50%, 100% {
                top: 24px;
                height: 32px;
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
    <script src="{{ backend_asset('libraries/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/Chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/owl.carousel.min.js')}}"></script>
    <!-- Custom Theme JavaScript -->
    <script src="{{ backend_asset('js/sweetalert2.all.min.js') }}"></script>
    <!-- Custom Light Box JS -->
    <script src="{{ backend_asset('js/custom_lightbox.js')}}"></script>
    <script src="{{ backend_asset('js/echarts.min.js') }}"></script>

@endsection

@section('inlineJS')
    <script type="text/javascript">
        let pieChartHendler = {
            "element_instance": echartPie_montreal = echarts.init(document.getElementById('flag-order-pie-chart-dev')),
            "methods":{
                "loadPieChartData": function () {
                    let selected_hub = $('.data-selector').val();
                    let selected_date = $('.hub-selector').val();
                    <?php

                    // Logged In user....
                    $auth_user = Auth::user();
                    $auth_hub_id = $auth_user->hub_id;
                    ?>
                    // sending ajax request
                    $.ajax({
                        type: "get",
                        url: "statistics/flag-order-list-pie-chart-data",
                        data: {
                            'datepicker': selected_date, 'hub_id': selected_hub
                        },
                        success: function (response) {
                            pieChartHendler.methods.hideLoader();

                            // updating pie chart options data
                            pieChartHendler.options.legend.data = response.body.legend;
                            pieChartHendler.options.series[0].data = response.body.data;

                            // init pieChart
                            pieChartHendler.element_instance.setOption(pieChartHendler.options);
                        },
                        error: function (error) {
                            pieChartHendler.methods.hideLoader();
                            // alert('some error occurred please see the console');
                            console.log(error);
                        }
                    });
                },
                "showLoader":function () {
                    $('.charts-box-ajax-data-loader-wrap').addClass('show');
                },
                "hideLoader":function () {
                    $('.charts-box-ajax-data-loader-wrap').removeClass('show');
                },
            },
            "options":{
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    x: 'center',
                    y: 'bottom',
                    data: []
                },
                toolbox: {
                    show: true,
                    feature: {
                        magicType: {
                            show: true,
                            type: ['pie', 'funnel'],
                            option: {
                                funnel: {
                                    x: '25%',
                                    width: '50%',
                                    funnelAlign: 'left',
                                    max: 1548
                                }
                            }
                        }
                    }
                },
                calculable: true,
                series: [{
                    name: 'Flag orders data ',
                    type: 'pie',
                    radius: '80%',
                    center: ['50%', '50%'],
                    data: []

                }],
            },
            "init":function () {

                pieChartHendler.methods.showLoader();
                pieChartHendler.methods.loadPieChartData()

            }

        };

        pieChartHendler.init();

    </script>
@endsection

@section('content')
    <div class="right_col" role="main">
        <div class="dashboard_pg">
            <!-- Header - [start] -->
            @if(empty($hub_id))

            @else
                <div class="dash_header_wrap">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="dash_heading">
                                <h1>{{getHubTitle()}}</h1>

                            </div>
                        </div>

                        <div class="col-md-4">

                            <form method="get" action="">
                                <div class="row">
                                    <div class="col-md-5">
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <input type="date" name="datepicker" class="data-selector form-control" value="{{ isset($_GET['datepicker'])?$_GET['datepicker']: date('Y-m-d') }}">

                                        </div>
                                    </div>


                                    <div class="col-md-2 col-sm-3 col-xs-3">
                                        <button type="submit" class="btn btn-primary btn-lg">Search</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="main_heading">
                    <h3>Orders Steps</h3>
                </div>
                <div class="step_formRow">
                    <div class="arrowCol">
                        <div class="stats_box step_formcol">
                            <input type="checkbox" name="check">
                            <h6>First Mile Routing</h6>
                            <img src="<?php echo e(asset('images/joey.png')); ?>">
                        </div>
                    </div>
                    <div class="arrowCol">
                        <div class="stats_box step_formcol">
                            <h6>Store Pickup</h6>
                            <img src="<?php echo e(asset('images/webstore.png')); ?>">
                        </div>
                    </div>
                    <div class="arrowCol">
                        <div class="stats_box step_formcol">
                            <h6>In Bound Scanning</h6>
                            <img src="<?php echo e(asset('images/third_party.png')); ?>">
                        </div>
                    </div>
                    <div class="arrowCol">
                        <div class="stats_box step_formcol">
                            <h6>Mid-Mile Routing</h6>
                            <img src="<?php echo e(asset('images/joey.png')); ?>">
                        </div>
                    </div>
                    <div class="arrowCol">
                        <div class="stats_box step_formcol">
                            <h6>Mid-Mile Pick &amp Drop</h6>
                            <img src="<?php echo e(asset('images/joey.png')); ?>">
                        </div>
                    </div>
                    <div class="arrowCol">
                        <div class="stats_box step_formcol">
                            <h6>Last-Mile Route</h6>
                            <img src="<?php echo e(asset('images/joey_icon.png')); ?>">
                        </div>
                    </div>
                    <div class="arrowCol">
                        <div class="stats_box step_formcol">
                            <h6>Last-Mile Sort</h6>
                            <img src="<?php echo e(asset('images/joey_icon.png')); ?>">
                        </div>
                    </div>
                    <div class="arrowCol">
                        <div class="stats_box step_formcol">
                            <h6>Last-Mile Pick &amp Drop</h6>
                            <img src="<?php echo e(asset('images/joey_icon.png')); ?>">
                        </div>
                    </div>
                    <div class="arrowCol">
                        <div class="stats_box step_formcol">

                            <h6>Last-Mile Written & Receive</h6>
                            <img src="<?php echo e(asset('images/joey_icon.png')); ?>">
                        </div>
                    </div>
                </div>
                <div class="stats section">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-6 stats_box_wrap">
                            <div class="dashbords-conts-tiles-loader-main-wrap  otd-day show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="stats_box pop_desc">
                                <h4>OTD By Day</h4>
                                <div class="circle_chart">
                                    <div class="doughnut_percentage" id="doughnutChart1_percentage">00.00%</div>
                                    <canvas id="doughnutChart1" height="180"></canvas>
                                </div>
                                <div class="row">
                                    <div class="attr col-md-6">
                                        <div class="swatch" style="background: #0fda8b;"></div>
                                        <div class="lbl" >On Time Delivery</div>
                                        <div class="value" id="day-on-value">0</div>
                                    </div>
                                    <div class="attr col-md-6">
                                        <div class="swatch" style="background: #ff6384;"></div>
                                        <div class="lbl" >Off Time Delivery</div>
                                        <div class="value" id="day-off-value">0</div>
                                    </div>
                                </div>
                                <div class="desc">
                                    <p>OTD For Selected Date.</p>
                                    <p>1-On Time Delivery = Orders delivered/returned before 9 PM.</p>
                                    <p>2-Off Time Delivery = Orders delivered/returned After 9 PM.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-6 stats_box_wrap">
                            <div class="dashbords-conts-tiles-loader-main-wrap  otd-week show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="stats_box pop_desc" >
                                <h4>OTD By Week</h4>
                                <div class="circle_chart">
                                    <div class="doughnut_percentage" id="doughnutChart2_percentage">00.00%</div>
                                    <canvas id="doughnutChart2" height="180"></canvas>
                                </div>
                                <div class="row">
                                    <div class="attr col-md-6">
                                        <div class="swatch" style="background: #0fda8b;"></div>
                                        <div class="lbl">On Time Delivery</div>
                                        <div class="value" id="week-on-value">0</div>
                                    </div>
                                    <div class="attr col-md-6">
                                        <div class="swatch" style="background: #ff6384;"></div>
                                        <div class="lbl">Off Time Delivery</div>
                                        <div class="value" id="week-off-value">0</div>
                                    </div>
                                </div>
                                <div class="desc">
                                    <p>OTD For Last Week.</p>
                                    <p>1-On Time Delivery = Orders delivered/returned before 9 PM.</p>
                                    <p>2-Off Time Delivery = Orders delivered/returned After 9 PM.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-6 stats_box_wrap">
                            <div class="dashbords-conts-tiles-loader-main-wrap  otd-month show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="stats_box pop_desc">
                                <h4>OTD By Month</h4>
                                <div class="circle_chart">
                                    <div class="doughnut_percentage" id="doughnutChart3_percentage">00.00%</div>
                                    <canvas id="doughnutChart3" height="180"></canvas>
                                </div>
                                <div class="row">
                                    <div class="attr col-md-6">
                                        <div class="swatch" style="background: #0fda8b;"></div>
                                        <div class="lbl">On Time Delivery</div>
                                        <div class="value" id="month-on-value">0</div>
                                    </div>
                                    <div class="attr col-md-6">
                                        <div class="swatch" style="background: #ff6384;"></div>
                                        <div class="lbl">Off Time Delivery</div>
                                        <div class="value" id="month-off-value">0</div>
                                    </div>
                                </div>
                                <div class="desc">
                                    <p>OTD For Last Month.</p>
                                    <p>1-On Time Delivery = Orders delivered/returned before 9 PM.</p>
                                    <p>2-Off Time Delivery = Orders delivered/returned After 9 PM.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-6 stats_box_wrap">
                            <div class="dashbords-conts-tiles-loader-main-wrap  otd-year show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="stats_box pop_desc">
                                <h4>OTD By 6 Month</h4>
                                <div class="circle_chart">
                                    <div class="doughnut_percentage" id="doughnutChart4_percentage">00.00%</div>
                                    <canvas id="doughnutChart4" height="180"></canvas>
                                </div>
                                <div class="row">
                                    <div class="attr col-md-6">
                                        <div class="swatch" style="background: #0fda8b;"></div>
                                        <div class="lbl">On Time Delivery</div>
                                        <div class="value" id="sixmonth-on-value">0</div>
                                    </div>
                                    <div class="attr col-md-6">
                                        <div class="swatch" style="background: #ff6384;"></div>
                                        <div class="lbl">Off Time Delivery</div>
                                        <div class="value" id="sixmonth-off-value">0</div>
                                    </div>
                                </div>
                                <div class="desc">
                                    <p>OTD For Last 6 Months.</p>
                                    <p>1-On Time Delivery = Orders delivered/returned before 9 PM.</p>
                                    <p>2-Off Time Delivery = Orders delivered/returned After 9 PM.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stats section">
                    <h2>First Mile</h2>
                    <div class="featured_numbers statistics">


                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-summary first-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/firstmile/orders?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Total Store Orders</h3>
                                    <p class="numbers" id="total_orderss">0</p>

                                    <div class="desc">
                                        <p>Total number of packages which belongs to my hub in first mile.</p>
                                    </div>


                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-summary first-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/firstmile/vendors?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Total Vendors</h3>
                                    <p class="numbers" id="total_vendors">0</p>
                                    <div class="desc">
                                        <p>Total number of vendors which are connected to my hub.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-summary first-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/firstmile/picked?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>"  target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Total orders picked up</h3>
                                    <p class="numbers" id="collected_order">0</p>
                                    <div class="desc">
                                        <p>Total orders already pickedup.</p>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order first-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/firstmile/remaining?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Remaining Orders</h3>
                                    <p class="numbers" id="remaining_order">0</p>
                                    <div class="desc">
                                        <p>My Orders at others hub.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order first-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/firstmile/routes?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Total Routes</h3>
                                    <p class="numbers" id="total_routes">0</p>
                                    <div class="desc">
                                        <p>Total Routes during first mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order first-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/firstmile/completeroutes?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Completed Routes</h3>
                                    <p class="numbers" id="complete_routes">0</p>
                                    <div class="desc">
                                        <p>Completed Routes during first mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order first-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/firstmile/ongoingroutes?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Ongoing Routes</h3>
                                    <p class="numbers" id="ongoing_routes">0</p>
                                    <div class="desc">
                                        <p>Ongoing Routes during first mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order first-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/firstmile/delayroutes?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Delayed Routes</h3>
                                    <p class="numbers" id="delay_routes">0</p>
                                    <div class="desc">
                                        <p>Delayed Routes during first mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>




                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order first-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/firstmile/joeys?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Total Joeys</h3>
                                    <p class="numbers" id="total_joeys">0</p>
                                    <div class="desc">
                                        <p>Total Joeys working in first mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
                <div class="stats section">
                    <h2>Mid Mile</h2>
                    <div class="featured_numbers statistics">


                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-summary mid-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/midmile/orders?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">My Total Orders</h3>
                                    <p class="numbers" id="mtotal_orderss">0</p>
                                    <div class="desc">
                                        <p>Total number of packages which belongs to my hub in mid mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-summary mid-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/midmile/mypicked?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">My Total orders picked up</h3>
                                    <p class="numbers" id="mmcollected_order">0</p>
                                    <div class="desc">
                                        <p>My Total orders picked up from other stores.</p>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-summary mid-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/midmile/picked?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Total orders picked up</h3>
                                    <p class="numbers" id="mcollected_order">0</p>
                                    <div class="desc">
                                        <p>Total orders already pickedup.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order mid-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/midmile/onotherhub?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">My Orders at Other Hubs</h3>
                                    <p class="numbers" id="mremaining_order">0</p>
                                    <div class="desc">
                                        <p>My Orders at others hub.</p>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order mid-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/midmile/otherorder?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Other Orders at My Hubs</h3>
                                    <p class="numbers" id="other_order">0</p>
                                    <div class="desc">
                                        <p>My Orders at others hub.</p>
                                    </div>
                                </div>
                            </a>
                        </div>




                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order mid-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/midmile/routes?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Total Routes</h3>
                                    <p class="numbers" id="mtotal_routes">0</p>
                                    <div class="desc">
                                        <p>Total Routes during first mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order mid-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/midmile/completeroutes?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Completed Routes</h3>
                                    <p class="numbers" id="mcomplete_routes">0</p>
                                    <div class="desc">
                                        <p>Completed Routes during mid mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order mid-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/midmile/ongoingroutes?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Ongoing Routes</h3>
                                    <p class="numbers" id="mongoing_routes">0</p>
                                    <div class="desc">
                                        <p>Ongoing Routes during mid mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order mid-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/midmile/delayroutes?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Delayed Routes</h3>
                                    <p class="numbers" id="mdelay_routes">0</p>
                                    <div class="desc">
                                        <p>Delayed Routes during mid mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order mid-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/midmile/joeys?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Total Joeys</h3>
                                    <p class="numbers" id="mtotal_joeys">0</p>
                                    <div class="desc">
                                        <p>Total Joeys working in first mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
                <div class="stats section">
                    <h2>Last Mile</h2>
                    <div class="featured_numbers statistics">


                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-summary last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/orders?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">My Total Orders</h3>
                                    <p class="numbers" id="ltotal_orderss">0</p>
                                    <div class="desc">
                                        <p>Total number of packages which belongs to my hub in mid mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-summary last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/picked?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Total orders picked up</h3>
                                    <p class="numbers" id="lcollected_order">0</p>
                                    <div class="desc">
                                        <p>Total orders already pickedup.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/complete?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Completed Orders</h3>
                                    <p class="numbers" id="lcomplete_order">0</p>
                                    <div class="desc">
                                        <p>My Completed Order.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/return?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Return Orders</h3>
                                    <p class="numbers" id="lreturn_order">0</p>
                                    <div class="desc">
                                        <p>My Returned Order.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/unattempt?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Unattempted Orders</h3>
                                    <p class="numbers" id="lunattempt_order">0</p>
                                    <div class="desc">
                                        <p>My Unattempted Order.</p>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/sort?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Sorted Orders</h3>
                                    <p class="numbers" id="lsort_order">0</p>
                                    <div class="desc">
                                        <p>My Sorted Order.</p>
                                    </div>
                                </div>
                            </a>
                        </div>



                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/delay?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Delayed Orders</h3>
                                    <p class="numbers" id="ldelay_order">0</p>
                                    <div class="desc">
                                        <p>My Delayed Order.</p>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/customorder?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Custom Route Orders</h3>
                                    <p class="numbers" id="lcustom_order">0</p>
                                    <div class="desc">
                                        <p>My Delayed Order.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/routes?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Total Routes</h3>
                                    <p class="numbers" id="ltotal_routes">0</p>
                                    <div class="desc">
                                        <p>Total Routes during first mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>



                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/completeroutes?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Completed Routes</h3>
                                    <p class="numbers" id="lcomplete_routes">0</p>
                                    <div class="desc">
                                        <p>Completed Routes during last mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/ongoingroutes?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Ongoing Routes</h3>
                                    <p class="numbers" id="longoing_routes">0</p>
                                    <div class="desc">
                                        <p>Ongoing Routes during last mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/delayroutes?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">
                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Delayed Routes</h3>
                                    <p class="numbers" id="ldelay_routes">0</p>
                                    <div class="desc">
                                        <p>Delayed Routes during last mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo URL::to('/'); ?>/incharge/lastmile/joeys?hub_id=<?php echo $hub_id; ?>&datepicker=<?php echo $date;?>" target="_blank">

                                <div class="inner pop_desc">
                                    <h3 class="basecolor1">Total Joeys</h3>
                                    <p class="numbers" id="ltotal_joeys">0</p>
                                    <div class="desc">
                                        <p>Total Joeys working in first mile.</p>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
                <div class="stats section">
                    <h2>Current Stats</h2>
                    <div class="featured_numbers statistics">


                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-summary last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="inner pop_desc">
                                <h3 class="basecolor1">Remaining To Be Picked Up</h3>
                                <p class="numbers" id="sorted_remain">0</p>

                                <div class="desc">
                                    <p>Total number of packages which are sorted and ready to pickup.</p>
                                </div>
                            </div>
                        </div>
                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-summary last-mile-loader show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="inner pop_desc">
                                <h3 class="basecolor1">Remaining Packages OFD</h3>
                                <p class="numbers" id="picked_remain">0</p>
                                <div class="desc">
                                    <p>Total number of packages which are picked up and ready to deliver.</p>
                                </div>
                            </div>
                        </div>
                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-summary show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="inner pop_desc">
                                <h3 class="basecolor1">Remaining Routes OFD</h3>
                                <p class="numbers" id="route_picked_remain">0</p>
                                <div class="desc">
                                    <p>Total routes of packages which are picked up and ready to deliver.</p>
                                </div>
                            </div>
                        </div>
                        <div class="number_box">
                            <div class="dashbords-conts-tiles-loader-main-wrap  total-order show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="inner pop_desc">
                                <h3 class="basecolor1">Order Completion Ratio</h3>
                                <p class="numbers" id="completion_order">0.00%</p>
                                <div class="desc">
                                    <p>Percentage of delivered packages.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="chart_stats section">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-xs-12">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4>On Time Delivery And Off Time Delivery</h4>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group chart_filter_control">
                                        <select name="select_graph" id="select_graph" class="form-control form-control-xs tb_padding">
                                            <option value="week" selected >By Week</option>
                                            <option value="month">By Month</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div style="position: relative;">
                                <div class="dashbords-conts-tiles-loader-main-wrap  graph-loader show " style="padding: 0px 0px 0px 0px; !important;" >
                                    <div class="dashbords-conts-tiles-loader-inner-wrap">
                                        <div class="lds-roller">
                                            <div class="dot-1"></div>
                                            <div class="dot-2"></div>
                                            <div class="dot-3"></div>
                                            <div class="dot-4"></div>
                                            <div class="dot-5"></div>
                                            <div class="dot-6"></div>
                                            <div class="dot-7"></div>
                                            <div class="dot-8"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="stats_box">
                                    <canvas id="myChart" width="760" height="455"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-xs-12" style="padding-top: 58px;">
                            <div class="stats_box mb-20 center pop_desc">
                                <div class="dashbords-conts-tiles-loader-main-wrap  manual-order show nopadding">
                                    <div class="dashbords-conts-tiles-loader-inner-wrap">
                                        <div class="lds-roller">
                                            <div class="dot-1"></div>
                                            <div class="dot-2"></div>
                                            <div class="dot-3"></div>
                                            <div class="dot-4"></div>
                                            <div class="dot-5"></div>
                                            <div class="dot-6"></div>
                                            <div class="dot-7"></div>
                                            <div class="dot-8"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="numbers">Manual Status Mark</div>
                                <div class="title" id="manual_orders">0</div>
                                <div class="desc">
                                    <p>Total number of orders which status has marked manually. </p>
                                </div>
                            </div>
                            <div class="stats_box mb-20 center pop_desc">
                                <div class="dashbords-conts-tiles-loader-main-wrap  time-order show nopadding">
                                    <div class="dashbords-conts-tiles-loader-inner-wrap">
                                        <div class="lds-roller">
                                            <div class="dot-1"></div>
                                            <div class="dot-2"></div>
                                            <div class="dot-3"></div>
                                            <div class="dot-4"></div>
                                            <div class="dot-5"></div>
                                            <div class="dot-6"></div>
                                            <div class="dot-7"></div>
                                            <div class="dot-8"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="numbers">Sorting Time</div>
                                <div class="title" id="sorting_time">00:00 Hours</div>
                                <div class="desc">
                                    <p>Total sorting time of orders</p>
                                </div>
                            </div>
                            <div class="stats_box center pop_desc">
                                <div class="dashbords-conts-tiles-loader-main-wrap  time-order show nopadding">
                                    <div class="dashbords-conts-tiles-loader-inner-wrap">
                                        <div class="lds-roller">
                                            <div class="dot-1"></div>
                                            <div class="dot-2"></div>
                                            <div class="dot-3"></div>
                                            <div class="dot-4"></div>
                                            <div class="dot-5"></div>
                                            <div class="dot-6"></div>
                                            <div class="dot-7"></div>
                                            <div class="dot-8"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="numbers">Picked Up Time</div>
                                <div class="title" id="picked_time">00:00 Hours</div>
                                <div class="desc">
                                    <p>Total picked up time of orders</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stats section">
                    <div class="row">

                        <div class="col-lg-4 col-md-6 col-xs-12 pop_desc" >
                            <div class="dashbords-conts-tiles-loader-main-wrap  broker-joey show">
                                <div class="dashbords-conts-tiles-loader-inner-wrap">
                                    <div class="lds-roller">
                                        <div class="dot-1"></div>
                                        <div class="dot-2"></div>
                                        <div class="dot-3"></div>
                                        <div class="dot-4"></div>
                                        <div class="dot-5"></div>
                                        <div class="dot-6"></div>
                                        <div class="dot-7"></div>
                                        <div class="dot-8"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="data_box pb-0">
                                <h4>Top 10 Brokers </h4>
                                <div class="order_list" id="order_row_list"></div>
                            </div>
                            <div class="desc">
                                <p>The name of brokers their joeys have highest number of on time deliveries.</p>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-xs-12">
                            <div class="stats_box mb-20 pop_desc">
                                <div class="dashbords-conts-tiles-loader-main-wrap  top-ten show nopadding">
                                    <div class="dashbords-conts-tiles-loader-inner-wrap">
                                        <div class="lds-roller">
                                            <div class="dot-1"></div>
                                            <div class="dot-2"></div>
                                            <div class="dot-3"></div>
                                            <div class="dot-4"></div>
                                            <div class="dot-5"></div>
                                            <div class="dot-6"></div>
                                            <div class="dot-7"></div>
                                            <div class="dot-8"></div>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="color-green">Top 10 Joeys Performers</h4>
                                <div class="joeys_list">
                                    <div id="topPerformers"> </div>
                                </div>
                                <div class="desc">
                                    <p>The name of joeys they have highest number of on time deliveries.</p>
                                </div>
                            </div>

                            <div class="stats_box pop_desc">
                                <div class="dashbords-conts-tiles-loader-main-wrap  least-ten show nopadding">
                                    <div class="dashbords-conts-tiles-loader-inner-wrap">
                                        <div class="lds-roller">
                                            <div class="dot-1"></div>
                                            <div class="dot-2"></div>
                                            <div class="dot-3"></div>
                                            <div class="dot-4"></div>
                                            <div class="dot-5"></div>
                                            <div class="dot-6"></div>
                                            <div class="dot-7"></div>
                                            <div class="dot-8"></div>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="color-red">10 Least Joeys Performers</h4>
                                <div class="joeys_list">
                                    <div id="leastPerformers"> </div>
                                </div>
                                <div class="desc">
                                    <p>The name of joeys they have lowest number of off time deliveries.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row charts-box-main-wrap">
                    <div class="charts-box-ajax-data-loader-wrap">
                        <div class="charts-box-ajax-data-loader-inner-wrap">
                            <div class="lds-facebook"><div></div><div></div><div></div></div>
                            <p>Loading...</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Flag Orders Pie Chart</h2>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="dashboard-statistics-box" id="flag-order-pie-chart-dev"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>


        $(function(){

            // Chart 1
            var onTime = 100;
            var offTime = 0;
            console.log(onTime)
            const doughnutChart1Data = {
                labels: [
                    `On Time Deliveries ${onTime}%`,
                    `Off Time Deliveries ${offTime}%`,
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [onTime, offTime],
                    backgroundColor: ['#0fda8b', '#ff6384',],
                    hoverOffset: 30
                }]
            };
            var doughnutChart1 = document.getElementById('doughnutChart1');
            var doughnutChart1Init = new Chart(doughnutChart1, {
                type: 'doughnut',
                data: doughnutChart1Data,
                options: {
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    aspectRatio: 1,
                    plugins: {
                        title: {
                            display: false,
                            text: 'Chart.js Doughnut Chart'
                        }
                    }
                },
            })
            function getOTDDay(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                console.log("selected_date",selected_date)

                <?php

                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;

                ?>

                $('.otd-day').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('/incharge'); ?>/statistics/day/otd",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},

                    success: function (data) {
                        doughnutChart1Init.data.labels[0] = 'On Time Deliveries '+data['y2']+'%';
                        doughnutChart1Init.data.labels[1] = 'Off Time Deliveries '+data['y1']+'%';
                        doughnutChart1Init.data.datasets[0].data[0] = data['ontime'];
                        doughnutChart1Init.data.datasets[0].data[1] = data['offtime'];
                        doughnutChart1Init.update();
                        $('#day-on-value').text(data['ontime']);
                        $('#day-off-value').text(data['offtime']);
                        $('#doughnutChart1_percentage').text(data['y2']+'%');
                        $('.otd-day').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.otd-day').removeClass('show');
                    }
                })
            }


            // Chart 2
            var onTime = 100;
            var offTime = 0;
            const doughnutChart2Data = {
                labels: [
                    `On Time Deliveries ${onTime}%`,
                    `Off Time Deliveries ${offTime}%`,
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [onTime, offTime],
                    backgroundColor: ['#0fda8b', '#ff6384',],
                    hoverOffset: 30
                }]
            };
            var doughnutChart2 = document.getElementById('doughnutChart2');
            var doughnutChart2Init = new Chart(doughnutChart2, {
                type: 'doughnut',
                data: doughnutChart2Data,
                options: {
                    aspectRatio: 1,
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    plugins: {
                        title: {
                            display: false,
                            text: 'Chart.js Doughnut Chart'
                        }
                    }
                },
            })

            function getOTDWeek(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                $('.otd-week').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/week/otd",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},

                    success: function (data) {
                        doughnutChart2Init.data.labels[0] = 'On Time Deliveries '+data['y2']+'%';
                        doughnutChart2Init.data.labels[1] = 'Off Time Deliveries '+data['y1']+'%';
                        doughnutChart2Init.data.datasets[0].data[0] = data['ontime'];
                        doughnutChart2Init.data.datasets[0].data[1] = data['offtime'];
                        doughnutChart2Init.update();
                        $('#week-on-value').text(data['ontime']);
                        $('#week-off-value').text(data['offtime']);
                        $('#doughnutChart2_percentage').text(data['y2']+'%');
                        $('.otd-week').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.otd-week').removeClass('show');
                    }
                })
            }

            // Chart 3
            var onTime = 100;
            var offTime = 0;
            const doughnutChart3Data = {
                labels: [
                    `On Time Deliveries ${onTime}%`,
                    `Off Time Deliveries ${offTime}%`,
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [onTime, offTime],
                    backgroundColor: ['#0fda8b', '#ff6384',],
                    hoverOffset: 30
                }]
            };
            var doughnutChart3 = document.getElementById('doughnutChart3');
            var doughnutChart3Init = new Chart(doughnutChart3, {
                type: 'doughnut',
                data: doughnutChart3Data,
                options: {
                    aspectRatio: 1,
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    plugins: {
                        title: {
                            display: false,
                            text: 'Chart.js Doughnut Chart'
                        }
                    }
                },
            })

            function getOTDMonth(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>

                $('.otd-month').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/month/otd",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},

                    success: function (data) {
                        doughnutChart3Init.data.labels[0] = 'On Time Deliveries '+data['y2']+'%';
                        doughnutChart3Init.data.labels[1] = 'Off Time Deliveries '+data['y1']+'%';
                        doughnutChart3Init.data.datasets[0].data[0] = data['ontime'];
                        doughnutChart3Init.data.datasets[0].data[1] = data['offtime'];
                        doughnutChart3Init.update();
                        $('#month-on-value').text(data['ontime']);
                        $('#month-off-value').text(data['offtime']);
                        $('#doughnutChart3_percentage').text(data['y2']+'%');
                        $('.otd-month').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.otd-month').removeClass('show');
                    }
                })
            }

            // Chart 1
            var onTime = 100;
            var offTime = 0;
            const doughnutChart4Data = {
                labels: [
                    `On Time Deliveries ${onTime}%`,
                    `Off Time Deliveries ${offTime}%`,
                ],
                datasets: [{
                    label: 'My First Dataset',
                    data: [onTime, offTime],
                    backgroundColor: ['#0fda8b', '#ff6384',],
                    hoverOffset: 30
                }]
            };
            var doughnutChart4 = document.getElementById('doughnutChart4');
            var doughnutChart4Init = new Chart(doughnutChart4, {
                type: 'doughnut',
                data: doughnutChart4Data,
                options: {
                    aspectRatio: 1,
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    plugins: {
                        title: {
                            display: false,
                            text: 'Chart.js Doughnut Chart'
                        }
                    }
                },
            })

            function getOTDYear(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>

                $('.otd-year').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/sixmonth/otd",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},

                    success: function (data) {
                        console.log(data);
                        doughnutChart4Init.data.labels[0] = 'On Time Deliveries '+data['y2']+'%';
                        doughnutChart4Init.data.labels[1] = 'Off Time Deliveries '+data['y1']+'%';
                        doughnutChart4Init.data.datasets[0].data[0] = data['ontime'];
                        doughnutChart4Init.data.datasets[0].data[1] = data['offtime'];
                        doughnutChart4Init.update();
                        $('#sixmonth-on-value').text(data['ontime']);
                        $('#sixmonth-off-value').text(data['offtime']);
                        $('#doughnutChart4_percentage').text(data['y2']+'%');
                        $('.otd-year').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.otd-year').removeClass('show');
                    }
                })
            }

            function getTotalOrderDataCount(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                // show loader
                $('.total-order').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/all/counts",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub },
                    success: function (data) {
                        $('#total_orders').text(data['total']);
                        $('#return_orders').text(data['return_orders']);
                        $('#hub_return_scan').text(data['hub_return_scan']);
                        $('#hub_not_return_scan').text(data['hub_not_return_scan']);
                        $('#sorted_orders').text(data['sorted']);
                        $('#picked_orders').text(data['pickup']);
                        $('#delivered_orders').text(data['delivered_order']);
                        $('#notscan_orders').text(data['notscan']);
                        $('#reattempted_orders').text(data['reattempted']);
                        $('#completion_order').text(data['completion_ratio']);
                        // hide loader
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                    }
                });
            }

            function getInprogressOrderDataCount(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                // show loader
                $('.total-summary').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/inprogress",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},
                    success: function (data) {
                        $('#sorted_remain').text(data['remaining_sorted']);
                        $('#picked_remain').text(data['remaining_pickup']);
                        $('#route_picked_remain').text(data['remaining_route']);
                        // hide loader
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                    }
                });
            }

            function getFailedOrderDataCount(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                // show loader
                $('.failed-order').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/failed/counts",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},
                    success: function (data) {
                        $('#failed_orders').text(data['failed']);
                        $('#system_failed_orders').text(data['system_failed_order']);
                        $('#not_system_failed_orders').text(data['not_in_system_failed_order']);
                        // hide loader
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                    }
                });
            }

            function getCustomOrderDataCount(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                // show loader
                $('.custom-order').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/custom/counts",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub },
                    success: function (data) {
                        $('#custom_orders').text(data['custom_order']);
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                    }
                });
            }

            function getManualOrderDataCount(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                // show loader
                $('.manual-order').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/manual/counts",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},
                    success: function (data) {
                        $('#manual_orders').text(data['manual']);
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                    }
                });
            }

            function getRouteDataCounts(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                // show loader
                $('.route-count').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/route/counts",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},
                    success: function (data) {
                        $('#total_route').text(data['total_route']);
                        $('#normal_route').text(data['normal_route']);
                        $('#custom_route').text(data['custom_route']);
                        $('#big_box_route').text(data['big_box_route']);
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                    }
                });
            }

            function getOnTimeCounts(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                // show loader
                $('.time-order').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/on-time/counts",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},
                    success: function (data) {
                        $('#sorting_time').text(data['sorting']+' Hours');
                        $('#picked_time').text(data['pickup']+' Hours');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                    }
                });
            }




            var ctx = document.getElementById('myChart').getContext('2d');
            const data = {
                labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                datasets: [
                    {
                        label: ['On Time Delivery'],
                        data: [90,80,85,95,80,85,90,95],
                        borderColor: '#0fda8b',
                        fill: false,
                        lineTension: 0,

                    },
                    {
                        label: ['Off Time Delivery'],
                        data: [10,20,15,5,20,15,10, 5],
                        borderColor: '#ff6384',
                        fill: false,
                        lineTension: 0,
                    }
                ]
            };
            var myChart = new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    bezierCurve: false,
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        title: {
                            display: true,
                            text: 'Chart.js Line Chart'
                        }
                    },
                    hover: {
                        mode: 'index',
                        intersec: false
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        },
                        yAxes: [{
                            display: true,
                            ticks: {
                                beginAtZero: true,
                                steps: 11,
                                stepValue: 5,
                                max: 100
                            }
                        }]
                    }
                },
            });

            function getgraph(type,value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>

                $('.graph-loader').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/graph",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub,'type':type},

                    success: function (data) {
                        var i = 0;
                        jQuery.each(data, function(index, record) {
                            myChart.data.datasets[0].data[i] = record['y2'];
                            myChart.data.datasets[1].data[i] = record['y1'];
                            myChart.data.labels[i] = index;
                            i++
                        })
                        myChart.update();
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                    }
                })
            }

            var sliderConfig = {
                loop:false,
                margin:10,
                nav:true,
                dots: false,
                responsive:{
                    0:{
                        items:2
                    },
                    600:{
                        items:4
                    },
                    1000:{
                        items:6
                    }
                }
            };

            function topbrookers(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                $('.broker-joey').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/brooker",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},
                    success: function(data) {


                        $('#order_row_list').html('<div id="order_row_list_brooker"></div>');
                        jQuery.each(data, function(index, record) {
                            $('#order_row_list_brooker').append(`
                                <div class="order_row">
                                    <div class="info">
                                        <h4 class="title">${record.name}<span class="broker_count color-green">${record.count} orders</span></h4>
                                    </div>
                                    <div class="actions">
                                        <a href="<?php echo URL::to('incharge/'); ?>/statistics/brooker-detail?datepicker=${selected_date}&hub=${selected_hub}&rec=${record.brooker_id}" target="_blank" class="ico_btn"><i class="icofont-eye-alt"></i></a>
                                    </div>
                                </div>
                            `)
                        })


                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                    }
                })
            };

            function topPerformers(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                $('.top-ten').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/top-ten/joeys",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},
                    success: function(data) {
                        $('#topPerformers').html('<div id="topPerformersSlider" class="owl-carousel"></div>');
                        jQuery.each(data, function(index, record) {

                            $('#topPerformersSlider').append(`
                                    <a href="<?php echo URL::to('incharge/'); ?>/statistics/joey-detail?datepicker=${selected_date}&hub=${selected_hub}&rec=${record.encode_joey_id}" target="_blank">
                                    <div class="item">
                                        <img src="${record.image}" alt="">
                                        <h5 class="title">${record.name} (${record.joey_id})</h5>
                                        <p class="count color-green">${record.count} orders</p>
                                    </div>
                                    </a>
                                `)
                        })

                        console.log(data);

                        $('#topPerformersSlider').owlCarousel(
                            sliderConfig
                        );
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                    }
                })
            };// topPerformers();


            function leastPerformers(value) {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                $('.least-ten').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('incharge/'); ?>/statistics/least-ten/joeys",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},
                    success: function(data){
                        $('#leastPerformers').html('<div id="leastPerformersSlider" class="owl-carousel"></div>');
                        jQuery.each(data, function(index, record) {

                            $('#leastPerformersSlider').append(`
                                   <a href="<?php echo URL::to('incharge/'); ?>/statistics/joey-detail?datepicker=${selected_date}&hub=${<?php echo $auth_hub_id?>}&rec=${record.encode_joey_id}" target="_blank">
                                    <div class="item">
                                        <img src="${record.image}" alt="">
                                        <h5 class="title">${record.name} (${record.joey_id})</h5>
                                        <p class="count color-green">${record.count} orders</p>
                                    </div>
                                    </a>
                                `)
                        })

                        $('#leastPerformersSlider').owlCarousel(
                            sliderConfig
                        );
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                    }
                })
            };// leastPerformers();

            function GetFirstMileOrder() {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/firstmileorder",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},
                    success: function(data) {
                        console.log("My Data: ");
                        console.log(data['total_orders']);

                        //Total orders...
                        const total_order = document.getElementById("total_orderss");
                        const total_vendors = document.getElementById("total_vendors");
                        const collected_order = document.getElementById("collected_order");
                        const remaining_order = document.getElementById("remaining_order");
                        const total_routes = document.getElementById("total_routes");
                        const completed_routes = document.getElementById("complete_routes");
                        const ongoing_routes = document.getElementById("ongoing_routes");
                        const delayed_routes = document.getElementById("delay_routes");
                        const total_joeys = document.getElementById("total_joeys");
                        total_order.innerHTML = data['total_orders'];
                        total_vendors.innerHTML = data['total_vendors']
                        collected_order.innerHTML = data['collected_orders'];
                        remaining_order.innerHTML = data['remaining_orders'];
                        total_routes.innerHTML = data['routes'];
                        completed_routes.innerHTML = data['completed_routes'];
                        ongoing_routes.innerHTML = data['ongoing_routes'];
                        delayed_routes.innerHTML = data['delayed_routes'];
                        total_joeys.innerHTML = data['joeys'];
                        $('.first-mile-loader').removeClass('show');

                    },
                    error: function (error) {
                        console.log(error);
                    }
                })
            };// GetFirstMileOrder();

            function GetMidMileOrder() {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/midmileorder",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},
                    success: function(data) {
                        console.log("My Mid Mile Data: ");
                        console.log(data);

                        //Total orders...

                        const total_order = document.getElementById("mtotal_orderss");
                        // const total_vendors = document.getElementById("mtotal_vendors");
                        const collected_order = document.getElementById("mcollected_order");
                        const mcollected_order = document.getElementById("mmcollected_order");
                        const remaining_order = document.getElementById("mremaining_order");
                        const total_routes = document.getElementById("mtotal_routes");
                        const total_joeys = document.getElementById("mtotal_joeys");
                        const other_order = document.getElementById("other_order");
                        const remaincc_order = document.getElementById("rcc_order");
                        const pickcc_order = document.getElementById("pcc_order");
                        const completed_route = document.getElementById("mcomplete_routes");
                        const ongoing_routes = document.getElementById("mongoing_routes");
                        const delayed_routes = document.getElementById("mdelay_routes");
                        total_order.innerHTML = data['total_orders'];
                        collected_order.innerHTML = data['collected_orders'];
                        mcollected_order.innerHTML = data['mycollected_orders'];
                        remaining_order.innerHTML = data['my_orders_at_otherhub'];
                        total_routes.innerHTML = data['routes'];
                        total_joeys.innerHTML = data['joeys'];
                        other_order.innerHTML = data['other_order_at_myhub'];
                        completed_route.innerHTML = data['completed_routes'];
                        ongoing_routes.innerHTML = data['ongoing_routes'];
                        delayed_routes.innerHTML = data['delayed_routes'];
                        $('.mid-mile-loader').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                    }
                })
            };// GetMidMileOrder();

            function GetLastMileOrder() {
                let selected_hub = $('.hub-selector').val();
                let selected_date = $('.data-selector').val();
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/lastmileorder",
                    data: {'datepicker': selected_date, 'hub_id': selected_hub},
                    success: function(data) {
                        console.log("My Last Mile Data: ");
                        console.log(data['total_orders']);

                        //Total orders...

                        const total_order = document.getElementById("ltotal_orderss");
                        const collected_order = document.getElementById("lcollected_order");
                        const complete_order = document.getElementById("lcomplete_order");
                        const return_order = document.getElementById("lreturn_order");
                        const unattempt_order = document.getElementById("lunattempt_order");
                        const sorted_order = document.getElementById("lsort_order");
                        const delayed_order = document.getElementById("ldelay_order");
                        const total_routes = document.getElementById("ltotal_routes");
                        const completed_route = document.getElementById("lcomplete_routes");
                        const ongoing_routes = document.getElementById("longoing_routes");
                        const delayed_routes = document.getElementById("ldelay_routes");
                        const total_joeys = document.getElementById("ltotal_joeys");
                        const total_custom = document.getElementById("lcustom_order");

                        total_order.innerHTML = data['total_orders'];
                        complete_order.innerHTML = data['completed_orders']
                        collected_order.innerHTML = data['collected_orders'];
                        return_order.innerHTML = data['return_orders'];
                        unattempt_order.innerHTML = data['unattempted_order'];
                        sorted_order.innerHTML = data['sorted_order'];
                        delayed_order.innerHTML = data['delayed_order'];
                        total_custom.innerHTML = data['custom_order'];
                        total_routes.innerHTML = data['routes'];
                        total_joeys.innerHTML = data['joeys'];
                        completed_route.innerHTML = data['completed_routes'];
                        ongoing_routes.innerHTML = data['ongoing_routes'];
                        delayed_routes.innerHTML = data['delayed_routes'];

                        $('.last-mile-loader').removeClass('show');

                    },
                    error: function (error) {
                        console.log(error);
                    }
                })
            };// GetLastMileOrder();

            $('#select_graph').on('change', function() {
                var type  = this.value;
                getgraph(type);
            });
            setTimeout(function () {
                getOTDDay();
                getOTDWeek();
                getOTDMonth();
                getOTDYear();
                getTotalOrderDataCount();
                getInprogressOrderDataCount();
                getFailedOrderDataCount();
                getCustomOrderDataCount();
                getManualOrderDataCount();
                getOnTimeCounts();
                topbrookers();
                topPerformers();
                leastPerformers();
                getgraph('week');
                getRouteDataCounts();
                GetFirstMileOrder();
                GetMidMileOrder();
                GetLastMileOrder();
            }, 1000);


        })



    </script>
@endsection
