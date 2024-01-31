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
@section('content')
    <div class="right_col" role="main" style="min-height: 895px;">
         <div class="inner-page page-serviceDetail">

        <div id="main_banner" class="style2 withBottomBg pt-0">

            <div class="heading_area">
                <div class="row">
                    <div class="col-12 col-sm-8 col-md-6">
                        <h1 class="main_heading nomargin">JoeyCo Terms Of Use</h1>
                    </div>
                </div>
            </div>
        </div>

{{--        <section class="section">--}}
{{--            <div class="container">--}}
{{--                <article class="article">--}}
{{--                    {!! $record->copy !!}--}}
{{--                </article>--}}
{{--            </div>--}}

{{--        </section>--}}
    </div>
    </div>
@endsection

