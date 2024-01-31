@extends( 'backend.layouts.app' )

@section('title', 'Dashboard')

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

        .charts-box-ajax-data-loader-wra.show
        {
            display: block;
        }
        .charts-box-ajax-data-loader-inner-wrap {
            position: relative;
            background-color: #0000006e;
            height: 100%;
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
                    // getting selected data
                    let selected_date = $('.data-selector').val();
                    <?php

                    // Logged In user....
                    $auth_user = Auth::user();
                    $auth_hub_id = $auth_user->hub_id;
                    ?>
                    // sending ajax request
                    $.ajax({
                        type: "get",
                        url: "{{route('statistics-flag-order-list-pie-chart-data')}}",
                        data: {
                            'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>
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

    <!-- /footer content -->

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
                    //`Running Late ${52}%`,
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
            function getOTDDay() {
                let selected_date = $('.data-selector').val();
                // let hubId = $('#hub-id').val();

                <?php

                // Logged In user....
       $auth_user = Auth::user();
       $auth_hub_id = $auth_user->hub_id;
       ?>

       $('.otd-day').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/statistics/day/otd",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id ?>},
                    /* beforeSend: function (xhr) {
                         xhr.overrideMimeType("text/plain; charset=x-user-defined");
                     },*/
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
                    //`Running Late ${52}%`,
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

            function getOTDWeek() {
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                $('.otd-week').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/statistics/week/otd",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id ?>},
                    /* beforeSend: function (xhr) {
                         xhr.overrideMimeType("text/plain; charset=x-user-defined");
                     },*/
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
                    //`Running Late ${52}%`,
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

            function getOTDMonth() {
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>

                $('.otd-month').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/statistics/month/otd",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id ?>},
                    /* beforeSend: function (xhr) {
                         xhr.overrideMimeType("text/plain; charset=x-user-defined");
                     },*/
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
                    //`Running Late ${52}%`,
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

            function getOTDYear() {
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>

                $('.otd-year').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/statistics/year/otd",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>},
                    /* beforeSend: function (xhr) {
                         xhr.overrideMimeType("text/plain; charset=x-user-defined");
                     },*/
                    success: function (data) {
                        console.log(data);
                        doughnutChart4Init.data.labels[0] = 'On Time Deliveries '+data['y2']+'%';
                        doughnutChart4Init.data.labels[1] = 'Off Time Deliveries '+data['y1']+'%';
                        doughnutChart4Init.data.datasets[0].data[0] = data['ontime'];
                        doughnutChart4Init.data.datasets[0].data[1] = data['offtime'];
                        doughnutChart4Init.update();
                        $('#year-on-value').text(data['ontime']);
                        $('#year-off-value').text(data['offtime']);
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

            function getTotalOrderDataCount() {
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
                    url: "<?php echo URL::to('/'); ?>/statistics/all/counts",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>},
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
                        $('.total-order').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.total-order').removeClass('show');
                    }
                });
            }

            function getInprogressOrderDataCount() {
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
                    url: "<?php echo URL::to('/'); ?>/statistics/inprogress",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>},
                    success: function (data) {
                        $('#sorted_remain').text(data['remaining_sorted']);
                        $('#picked_remain').text(data['remaining_pickup']);
                        $('#route_picked_remain').text(data['remaining_route']);
                        // hide loader
                        $('.total-summary').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.total-summary').removeClass('show');
                    }
                });
            }

            function getFailedOrderDataCount() {
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
                    url: "<?php echo URL::to('/'); ?>/statistics/failed/counts",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>},
                    success: function (data) {
                        $('#failed_orders').text(data['failed']);
                        $('#system_failed_orders').text(data['system_failed_order']);
                        $('#not_system_failed_orders').text(data['not_in_system_failed_order']);
                        // hide loader
                        $('.failed-order').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.failed-order').removeClass('show');
                    }
                });
            }

            function getCustomOrderDataCount() {
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
                    url: "<?php echo URL::to('/'); ?>/statistics/custom/counts",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>},
                    success: function (data) {
                        $('#custom_orders').text(data['custom_order']);
                        $('.custom-order').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.custom-order').removeClass('show');
                    }
                });
            }
            function getManualOrderDataCount() {
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
                    url: "<?php echo URL::to('/'); ?>/statistics/manual/counts",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>},
                    success: function (data) {
                        $('#manual_orders').text(data['manual']);
                        $('.manual-order').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.manual-order').removeClass('show');
                    }
                });
            }

            function getRouteDataCounts() {
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
                    url: "<?php echo URL::to('/'); ?>/statistics/route/counts",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>},
                    success: function (data) {
                        $('#total_route').text(data['total_route']);
                        $('#normal_route').text(data['normal_route']);
                        $('#custom_route').text(data['custom_route']);
                        $('#big_box_route').text(data['big_box_route']);
                        $('.route-count').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.route-count').removeClass('show');
                    }
                });
            }

            function getOnTimeCounts() {
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
                    url: "<?php echo URL::to('/'); ?>/statistics/on-time/counts",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>},
                    success: function (data) {
                        $('#sorting_time').text(data['sorting']+' Hours');
                        $('#picked_time').text(data['pickup']+' Hours');
                        $('.time-order').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.time-order').removeClass('show');
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

            function getgraph(type) {
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>

                $('.graph-loader').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/statistics/graph",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>,'type':type},

                    success: function (data) {
                        var i = 0;
                        jQuery.each(data, function(index, record) {
                            myChart.data.datasets[0].data[i] = record['y2'];
                            myChart.data.datasets[1].data[i] = record['y1'];
                            myChart.data.labels[i] = index;
                            i++
                        })
                        myChart.update();
                        $('.graph-loader').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.graph-loader').removeClass('show');
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

            function topbrookers(){
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                $('.broker-joey').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/statistics/brooker",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>},
                    success: function(data) {


                        $('#order_row_list').html('<div id="order_row_list_brooker"></div>');
                        jQuery.each(data, function(index, record) {
                            $('#order_row_list_brooker').append(`
                                <div class="order_row">
                                    <div class="info">
                                        <h4 class="title">${record.name}<span class="broker_count color-green">${record.count} orders</span></h4>
                                    </div>
                                    <div class="actions">
                                        <a href="<?php echo URL::to('/'); ?>/statistics/brooker-detail?datepicker=${selected_date}&hub=${<?php echo $auth_hub_id?>}&rec=${record.brooker_id}" target="_blank" class="ico_btn"><i class="icofont-eye-alt"></i></a>
                                    </div>
                                </div>
                            `)
                        })




                        /*   $('#order_row_list_brooker').owlCarousel(
                               sliderConfig
                           );*/
                        // $('#topPerformers').trigger('resize.owl.carousell');
                        $('.broker-joey').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.broker-joey').removeClass('show');
                    }
                })
            };

            function topPerformers(){
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                $('.top-ten').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/statistics/top-ten/joeys",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>},
                    success: function(data) {
                        $('#topPerformers').html('<div id="topPerformersSlider" class="owl-carousel"></div>');
                        jQuery.each(data, function(index, record) {

                            $('#topPerformersSlider').append(`
                                    <a href="<?php echo URL::to('/'); ?>/statistics/joey-detail?datepicker=${selected_date}&hub=${<?php echo $auth_hub_id?>}&rec=${record.encode_joey_id}" target="_blank">
                                    <div class="item">
                                        <img src="${record.image}" alt="">
                                        <h5 class="title">${record.name} (${record.joey_id})</h5>
                                        <p class="count color-green">${record.count} orders</p>
                                    </div>
                                    </a>
                                `)
                        })

                        console.log(data);
//




                        $('#topPerformersSlider').owlCarousel(
                            sliderConfig
                        );
                        // $('#topPerformers').trigger('resize.owl.carousell');
                        $('.top-ten').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.top-ten').removeClass('show');
                    }
                })
            };// topPerformers();


            function leastPerformers(){
                let selected_date = $('.data-selector').val();
                <?php
                // Logged In user....
                $auth_user = Auth::user();
                $auth_hub_id = $auth_user->hub_id;
                ?>
                $('.least-ten').addClass('show');
                $.ajax({
                    type: "GET",
                    url: "<?php echo URL::to('/'); ?>/statistics/least-ten/joeys",
                    data: {'datepicker': selected_date, 'hub_id': <?php echo $auth_hub_id?>},
                    success: function(data){
                        $('#leastPerformers').html('<div id="leastPerformersSlider" class="owl-carousel"></div>');
                        jQuery.each(data, function(index, record) {

                            $('#leastPerformersSlider').append(`
                                   <a href="<?php echo URL::to('/'); ?>/statistics/joey-detail?datepicker=${selected_date}&hub=${<?php echo $auth_hub_id?>}&rec=${record.encode_joey_id}" target="_blank">
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
                        $('.least-ten').removeClass('show');
                    },
                    error: function (error) {
                        console.log(error);
                        // hide loader
                        $('.least-ten').removeClass('show');
                    }
                })
            };// leastPerformers();


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
            }, 1000);


        })


    </script>
@endsection
