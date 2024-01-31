@extends( 'backend.layouts.app' )

@section('title', 'Sub Admin')
@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/moment/min/moment.min.js') }}"></script>
    <script src="{{ backend_asset('libraries//bootstrap-daterangepicker/daterangepicker.js') }}"></script>
@endsection

@section('inlineJS')
    <script>
        $(document).ready(function() {
            $('#birthday').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },

                calender_style: "picker_4"
            }, function(start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
            });
        });
    </script>
@endsection

@section('content')



    <!-- page content -->
    <div class="right_col" role="main">

        <div class="widget_sidebar widget_filter">
            <h1 style="margin-top: 35px" class="widgetTitle"><i class="icofont-user-alt-5"></i> Filter Results</h1>
            <div class="widgetInfo">
                <form  method="GET" action="{{url('summary')}}" id="filter-summary" class="row formDivider needs-validation" novalidate>
                    <input type="hidden" name="timezone" id="timezone">
                    <div class="form-group col-md-6">
                        <label for="startDate">Start date</label>
                        <input type="date" id="startDate" name="startDate" class="form-control form-control-lg datemask"  value="" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="endDate">End date</label>
                        <input type="date" id="endDate" name="endDate" class="form-control form-control-lg datemask" value="" required>
                    </div>
{{--                    @if (session('error'))--}}
                        <div class="invalid-feedback">{{  session('error') }}</div>
{{--                    @endif--}}
                    <div class="btn-group nomargin col-md-6">
                        <button type="submit" disabled class="btn btn-primary btn-xs submitButton mb-fluid">Filter Results</button>
                    </div>
                </Form>
            </div>
        </div>

        <section class="section-content summary-section">
            <div class="section-inner">
                <h3>Results</h3>
                <div class="tbl-wrap">
                    <table class="table table-striped tbl-responsive mb_last_row_hightlight mb_last_row_center">
                        <thead>
                        <tr>
                            <th scope="col" width="145px">Ref. No</th>
                            <th scope="col" width="145px">Date/Time</th>
                            <th scope="col" width="220px">Description</th>
                            <th scope="col" width="120px">Payment Type</th>
                            <th scope="col" width="100px">Distance</th>
                            <th scope="col" width="120px">Duration</th>
                            <th scope="col" width="120px" class="">Debit</th>
                            <th scope="col" width="120px" class="align-center">Credit</th>
                            <th scope="col" width="120px" class="align-right">Balance</th>
                        </tr>
                        </thead>
                        <tbody>
                                <tr>
                                    <td scope="row"><span class="bold basecolor1"></span></td>


                                    <td><br/></td>


                                    <td></td>



                                    <td></td>
                                    <td>

                                    </td>
                                    <td></td>
                                    <td></td>


                                        <td class="align-center" ></td>
                                        <td></td>

                                        <td class="align-center"></td>
                                        <td class="align-center" ></td>



                                    <td class="semibold align-right" style="text-align: right;"></td>
                                </tr>
                            <tr><td style="text-align: center" colspan="9">No Record Found</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    </div>
    </div>
    <!-- /page content -->


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
        function checkPasswordMatch() {
            var password = $("#password").val();
            var confirmPassword = $("#confirmpassword").val();

            if (password != confirmPassword) {
                $("#message").html("Passwords does not match!");
                document.getElementById("save").style.pointerEvents = "none";
            } else {
                document.getElementById("save").style.pointerEvents = "";
                $("#message").html("");
            }

        }
        $(document).ready(function () {
            $("#confirmpassword").keyup(checkPasswordMatch);
            $("#password").keyup(checkPasswordMatch);
        });
    </script>
@endsection
