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

    <script src="{{ backend_asset('libraries//bootstrap-daterangepicker/main.js') }}"></script>
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
        <div class="">
            <div class="portlet box blue">

                <div class="portlet-title">
                    <div class="col-md-4 caption">
                        <h3>Credit Payment Method
                            <small class="floatLeft">Payment Method is to chque</small>
                        </h3>
                    </div>
                </div>

                <div class="portlet-title">
                    <div class="col-md-4 caption">
                        <h3>Credit Card<small></small></h3>
                    </div>
                </div>

                <div class="portlet-body libs_customimize">

                    {{--                    <h4>&nbsp;</h4>--}}

                    <form method="POST" action="{{ route('subAdmin.create') }}" class="form-horizontal usCustomForm"
                          role="form" enctype="multipart/form-data">

                            <div class="form-group">
                                <label for="full_name" class="col-md-2 control-label">Credit Card Holder *</label>
                                <div class="col-md-12">
                                    <input type="text" name="full_name" maxlength="17" value="{{ old('full_name') }}"
                                           class="form-control" required pattern="^([A-Za-z]+[,.]?[ ]?|[A-Za-z]+['-]?)+$" title="Please enter valid first name"/>
                                </div>
                                @if ($errors->has('full_name'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('full_name') }}</strong>
                                        </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="email" class="col-md-2 control-label">Credit Card Number *</label>
                                <div class="col-md-12">
                                    <input type="number" name="email" maxlength="17" value="{{ old('email') }}"
                                           class="form-control" required pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Please enter valid email address"
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                    />
                                </div>
{{--                                @if ($errors->has('email'))--}}
{{--                                    <span class="help-block">--}}
{{--                                            <strong>{{ $errors->first('email') }}</strong>--}}
{{--                                        </span>--}}
{{--                                @endif--}}
                            </div>

                            <div class="form-group">
                                <label for="phone" class="col-md-2 control-label">Expiration Month & Year *</label>
                                <div class="col-md-12">
                                    <input type="month" name="phone" maxlength="14" value="{{ old('phone') }}"
                                           class="form-control" required/>
                                </div>
{{--                                @if ($errors->has('phone'))--}}
{{--                                    <span class="help-block">--}}
{{--                                            <strong>{{ $errors->first('phone') }}</strong>--}}
{{--                                        </span>--}}
{{--                                @endif--}}
                            </div>

    {{--                        <div class="form-group">--}}
    {{--                            <label for="address" class="col-md-2 control-label">Expiration Year*</label>--}}
    {{--                            <div class="col-md-12">--}}
    {{--                                <input type="number" min="1900" max="2099" step="1" value="2016" />--}}
    {{--                            </div>--}}
    {{--                            @if ($errors->has('address'))--}}
    {{--                                <span class="help-block">--}}
    {{--                                        <strong>{{ $errors->first('address') }}</strong>--}}
    {{--                                    </span>--}}
    {{--                            @endif--}}
    {{--                        </div>--}}

                            <div class="form-group">
                                <label for="rights" class="col-md-2 control-label">Postal Code *</label>
                                <div class="col-md-12">
                                    <input type="number" id="potalCoode" name="password" minlength="3" maxlength="6"
                                           class="form-control" required
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                    />
                                </div>
                            </div>

                            <div class="form-group form-check">
                                <label for="password" class="col-md-2 control-label">Check As Primary </label>
                                <div class="col-md-12">
                                    <input type="checkbox" id="chkbox" name="password" minlength="6" maxlength="32"
                                           class="form-control" required/>
                                </div>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                @endif
                            </div>
                        <div></div>

                        <div class="form-group">
                            <div class="col-md-12" style="padding-left: 0">
                                <input type="submit" class="btn orange" id="save" value="Add Card">
                            </div>
                        </div>
                    </form>


                    <div class="portlet-title">
                        <div class="col-md-4 caption" style="padding-top: 20px !important;">
                            <h3>Cards On File
                            </h3>
                            <div class="paymentMethod">
                                <span class="cardNumber">Credit Card Number: <small>#############5311</small></span>
                                <span class="cardNumber">Credit Card Holder: <small>John Deo</small></span>
                                <span class="cardNumber">Explration: <small>7/17</small></span>
                            </div>
                            <div class="primaryBtn">
                                <input type="submit" class="btn orange" id="save" value="Primary Credit Card">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END SAMPLE FORM PORTLET-->
        </div>
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
