@extends( 'backend.layouts.app' )

@section('title', 'Activity')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">
    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <!-- Image Viewer CSS -->
    <link href="{{ backend_asset('libraries/galleria/colorbox.css') }}" rel="stylesheet">
    <!-- Custom Light Box Css -->
    <link href="{{ backend_asset('css/custom_lightbox.css') }}" rel="stylesheet">

@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/galleria/jquery.colorbox.js') }}"></script>
    <!-- Custom Theme JavaScript -->
    <script src="{{ backend_asset('js/sweetalert2.all.min.js') }}"></script>
    <!-- Custom Light Box JS -->
    <script src="{{ backend_asset('js/custom_lightbox.js')}}"></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">
        <!-- Datatable -->
        $(document).ready(function () {

            $('#datatable').dataTable({
                "lengthMenu": [ 250, 500, 750, 1000 ]
            });
            $(".group1").colorbox({height:"50%",width:"50%"});

            $(document).on('click', '.status_change', function(e){

                var Uid = $(this).data('id');

                $.confirm({
                    title: 'A secure action',
                    content: 'Are you sure you want to change sub admin status??',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    buttons: {
                        'confirm': {
                            text: 'Proceed',
                            btnClass: 'btn-info',
                            action: function () {

                                $.ajax({
                                    type: "GET",
                                    data: {},
                                    success: function(data)
                                    {
                                        if(data== '0' || data== 0 )
                                        {
                                            var DataToset	=	'<button type="button" class="btn btn-warning btn-xs status_change" data-toggle="modal" data-id="'+Uid+'" data-target=".bs-example-modal-sm">Blocked</button>';
                                            $('#CurerntStatusDiv'+Uid).html(DataToset);
                                        }
                                        else
                                        {
                                            var DataToset	=	'<button type="button" class="btn btn-success btn-xs status_change" data-toggle="modal" data-id="'+Uid+'" data-target=".bs-example-modal-sm">Active</button>'
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
                    content: 'Are you sure you want to delete sub admin ??',
                    icon: 'fa fa-question-circle',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    opacity: 0.5,
                    buttons: {
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
    <style>
        .qA {
            font-size: 20px;
            font-weight: 600;
            transform: translateY(10px);
            display: table;
            margin-top: 15px;
            color: #b6a9a3;
        }
        .qA.tw {
            color: #3e3e3e;
            transform: translateY(-10px);
        }
        div#sorting {
            --secondary: #f6f2ef !important;
            margin-left: 0;
        }
        .clicktn {
            border: navajowhite;
            height: 100px;
            width: 100px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 12px;
        }
        .borderRight {
            border-right: 1px solid #E7DDD8;
        }
        .lgScanBtn {
            height: 48px;
            float: left;
            width: 35%;
            font-size: 20px;
            background: #dc6c1c;
            color: #fff;
            border-radius: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-bottom: 2px;
            margin-top: 15px;
        }
        .textLike i.fa.fa-thumbs-up {
            background: #d46b04;
            height: 30px;
            width: 30px;
            font-size: 20px;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            color: #fff;
            display: inline-flex;
        }
        .textLike small {
            font-size: 18px;
            color: #d46b04;
        }
        .textLike {
            margin-top: 20px;
            float: left;
            width: 100%;
        }
        small.qA.remove_gap {
            transform: unset;
        }
        .btnSlods {
            float: left;
            width: 100%;
            padding: 20px 0;
        }
        .btnSlods button {
            Width: 200px;
            Height: 70px;
            Top: 669px;
            Left: 341px;
            border-radius: 8px;
            Padding: 9px 18px 9px 18px;
            Gap: 1px;
            background: #fff;
            border: 1px solid #F6F2EF;
            color: #372C26;
            font-weight: 600;
            font-size: 18px;
            transition: all .4s ease-in-out;
        }
        .btnSlods .ist {
            Width: 200px;
            Height: 70px;
            Top: 669px;
            Left: 341px;
            border-radius: 8px;
            Padding: 9px 18px 9px 18px;
            Gap: 1px;
            background: #F6F2EF;
            border: 1px solid #F6F2EF;
            color: #db6b1b;
            font-size: 18px;
            font-weight: 600;
        }

        .btnSlods button:hover {
            background: #F6F2EF;
            color: #db6b1b;
        }
        .btnSlods button span {
            color: #9F9F9F;
            font-weight: 400;
            float: left;
            width: 100%;
        }
        .border {
            padding-left: 50px;
        }





        .sorting {
            height: 225px;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            display: flex;
        }
        .chart {
            padding: 0 20px;
        }
        @media (max-width: 1280px){
            .borderRight {
                border-right: 0px solid #E7DDD8;
            }
            .lgScanBtn {
                width: unset;
                padding: 2px 37px;
            }
            .col-lg-6.col-md-12 {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>

@endsection

@section('content')

    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="row addBorder" style="padding: 50px;float: left;width: 100%;">
                    <h3>Activities</h3>
                    <div class="col-lg-6 col-md-12 borderRight">
                        <div class="title_left">
                            <small class="qA">What's going on?</small>
                            <h3>Sorting 160 Orders</h3>
                            <small class="qA tw"><strong>56</strong> Remains Out Of <strong>160</strong></small>
                            <div class="sorting">
                                <div class="chart" data-size="200" data-value="77" data-arrow="down"></div>
                            </div>
                            <span class="textLike"><i class="fa fa-thumbs-up"></i> <small>Keep doing great, you’re almost there</small></span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 " style="padding-left: 30px">
                        <button class="clicktn"><img src="{{asset('images/click.png')}}"></button>
                        <div class="title_left">
                            <small class="qA">What's next?</small>
                            <h3>Time to Scan Orders</h3>
                            <small class="qA tw">There are 160 ordersto be sorted</small>
                            <a href="#" class="lgScanBtn">Start Scanning</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>
            <div class="border">
                <img src="{{asset('images/border.png')}}">
            </div>
            {{--@include('backend.layouts.modal')
            @include( 'backend.layouts.popups')--}}
            <div class="row">

                <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 50px">
                    <div class="title_left">
                        <small class="qA remove_gap">What else you can do?</small>
                    </div>
                    <div class="btnSlods">
                        <h5>First Mile</h5>
                        <button class="ist">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                        <button class="2nd">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                        <button class="2nd">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                        <button class="2nd">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                        <button class="2nd">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                    </div>
                    <div class="btnSlods">
                        <h5>Mid Mile</h5>
                        <button class="ist">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                        <button class="2nd">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                        <button class="2nd">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                        <button class="2nd">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                        <button class="2nd">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                    </div>
                    <div class="btnSlods">
                        <h5>Last Mile</h5>
                        <button class="ist">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                        <button class="2nd">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                        <button class="2nd">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                        <button class="2nd">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                        <button class="2nd">
                            Start Sorting
                            <span>in 50 min</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- /#page-wrapper -->
<script>
    class Dial {
        constructor(container) {
            this.container = container;
            this.size = this.container.dataset.size;
            this.strokeWidth = this.size / 8;
            this.radius = this.size / 2 - this.strokeWidth / 2;
            this.value = this.container.dataset.value;
            this.direction = this.container.dataset.arrow;
            this.svg;
            this.defs;
            this.slice;
            this.overlay;
            this.text;
            this.arrow;
            this.create();
        }

        create() {
            this.createSvg();
            this.createDefs();
            this.createSlice();
            this.createOverlay();
            this.createText();
            this.createArrow();
            this.container.appendChild(this.svg);
        }

        createSvg() {
            let svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svg.setAttribute("width", `${this.size}px`);
            svg.setAttribute("height", `${this.size}px`);
            this.svg = svg;
        }

        createDefs() {
            var defs = document.createElementNS("http://www.w3.org/2000/svg", "defs"),
                linearGradient = document.createElementNS(
                    "http://www.w3.org/2000/svg",
                    "linearGradient"
                ),
                stop1 = document.createElementNS("http://www.w3.org/2000/svg", "stop"),
                stop2 = document.createElementNS("http://www.w3.org/2000/svg", "stop"),
                linearGradientBackground = document.createElementNS(
                    "http://www.w3.org/2000/svg",
                    "background"
                );
            linearGradient.setAttribute("id", "gradient");
            stop1.setAttribute("stop-color", "#ffa000");
            stop1.setAttribute("offset", "0%");
            linearGradient.appendChild(stop1);
            stop2.setAttribute("stop-color", "#f25767");
            stop2.setAttribute("offset", "100%");
            linearGradient.appendChild(stop2);
            linearGradientBackground.setAttribute("id", "gradient-background");
            var stop1 = document.createElementNS("http://www.w3.org/2000/svg", "stop");
            stop1.setAttribute("stop-color", "rgba(0,0,0,0.2)");
            stop1.setAttribute("offset", "0%");
            linearGradientBackground.appendChild(stop1);
            var stop2 = document.createElementNS("http://www.w3.org/2000/svg", "stop");
            stop2.setAttribute("stop-color", "rgba(0,0,0,0.5)");
            stop2.setAttribute("offset", "1000%");
            linearGradientBackground.appendChild(stop2);
            defs.appendChild(linearGradient);
            defs.appendChild(linearGradientBackground);
            this.svg.appendChild(defs);
            this.defs = defs;
        }

        createSlice() {
            let slice = document.createElementNS("http://www.w3.org/2000/svg", "path");
            slice.setAttribute("fill", "none");
            slice.setAttribute("stroke", "#dc6c1c", 'transform', 0 );
            slice.setAttribute("stroke-width", this.strokeWidth);
            slice.setAttribute(
                "transform",
                `translate(${this.strokeWidth / 2},${this.strokeWidth / 2})`
            );
            slice.setAttribute("class", "animate-draw");
            this.svg.appendChild(slice);
            this.slice = slice;
        }

        createOverlay() {
            const r = this.size - this.size / 2 - this.strokeWidth / 2;
            const circle = document.createElementNS(
                "http://www.w3.org/2000/svg",
                "circle"
            );
            circle.setAttribute("cx", this.size / 2);
            circle.setAttribute("cy", this.size / 2);
            circle.setAttribute("r", r);
            circle.setAttribute("fill", "url(#gradient-background)");
            circle.setAttribute("class", "animate-draw");
            this.svg.appendChild(circle);
            this.overlay = circle;
        }

        createText() {
            const fontSize = this.size / 3.5;
            let text = document.createElementNS("http://www.w3.org/2000/svg", "text");
            text.setAttribute("x", this.size / 2 + fontSize / 5);
            text.setAttribute("y", this.size / 2 + fontSize / 3);
            text.setAttribute("font-family", "Century Gothic Lato");
            text.setAttribute("font-size", fontSize);
            text.setAttribute("fill", "#372C26");
            text.setAttribute("text-anchor", "middle");
            const tspanSize = fontSize / 2;
            text.innerHTML = `${0}% `;
            this.svg.appendChild(text);
            this.text = text;
        }

        createArrow() {
            var arrowSize = this.size / 10;
            var mapDir = {
                up: [(arrowYOffset = arrowSize / 2), (m = -1)],
                down: [(arrowYOffset = 0), (m = 1)]
            };
            function getDirection(i) {
                return mapDir[i];
            }
            var [arrowYOffset, m] = getDirection(this.direction);

            let arrowPosX = this.size / 2 - arrowSize / 2,
                arrowPosY = this.size - this.size / 3 + arrowYOffset,
                arrowDOffset = m * (arrowSize / 1.5),
                arrow = document.createElementNS("http://www.w3.org/2000/svg", "path");
            arrow.setAttribute(
                "d",
                `M 0 0 ${arrowSize} 0 ${arrowSize / 2} ${arrowDOffset} 0 0 Z`
            );
            arrow.setAttribute("fill", "none");
            arrow.setAttribute("opacity", "0.6");
            arrow.setAttribute("transform", `translate(${arrowPosX},${arrowPosY})`);
            this.svg.appendChild(arrow);
            this.arrow = arrow;
        }

        animateStart() {
            let v = 0;
            const intervalOne = setInterval(() => {
                const p = +(v / this.value).toFixed(2);
                const a = p < 0.95 ? 2 - 2 * p : 0.05;
                v += a;
                if (v >= +this.value) {
                    v = this.value;
                    clearInterval(intervalOne);
                }
                this.setValue(v);
            }, 10);
        }

        polarToCartesian(centerX, centerY, radius, angleInDegrees) {
            const angleInRadians = ((angleInDegrees - 180) * Math.PI) / 180.0;
            return {
                x: centerX + radius * Math.cos(angleInRadians),
                y: centerY + radius * Math.sin(angleInRadians)
            };
        }

        describeArc(x, y, radius, startAngle, endAngle) {
            const start = this.polarToCartesian(x, y, radius, endAngle);
            const end = this.polarToCartesian(x, y, radius, startAngle);
            const largeArcFlag = endAngle - startAngle <= 180 ? "0" : "1";
            const d = [
                "M",
                start.x,
                start.y,
                "A",
                radius,
                radius,
                0,
                largeArcFlag,
                0,
                end.x,
                end.y
            ].join(" ");
            return d;
        }

        setValue(value) {
            let c = (value / 100) * 360;
            if (c === 360) c = 359.99;
            const xy = this.size / 2 - this.strokeWidth / 2;
            const d = this.describeArc(xy, xy, xy, 180, 180 + c);
            this.slice.setAttribute("d", d);
            const tspanSize = this.size / 3.5 / 3;
            this.text.innerHTML = `${Math.floor(value)}% `;
        }

        animateReset() {
            this.setValue(0);
        }
    }

    const containers = document.getElementsByClassName("chart");
    const dial = new Dial(containers[0]);
    dial.animateStart();

</script>
@endsection