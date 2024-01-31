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
        <div class="">
            <div class="portlet box blue">

                <div class="portlet-title">
                    <div class="col-md-4 caption">
                    
                    </div>
                </div>

                <div class="portlet-body libs_customimize">

{{--                    <h4>&nbsp;</h4>--}}

                    <form method="POST" action="{{ route('subAdmin.create') }}" class="form-horizontal usCustomForm row d-flex"
                          role="form" enctype="multipart/form-data">
                          <h3>Add Sub Admin<small></small></h3>
                        <div class="form-group col-md-6 col-12">
                            
                            <div class="col-md-12">
                          
                                <label for="full_name" class="col-md-2 control-label">First Name *</label>
                                <input type="text" name="full_name" maxlength="150" value="{{ old('full_name') }}"
                                       class="form-control" required pattern="^([A-Za-z]+[,.]?[ ]?|[A-Za-z]+['-]?)+$" title="Please enter valid first name"/>
                            </div>
                            @if ($errors->has('full_name'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('full_name') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <div class="col-md-12">
                                <label for="email" class="col-md-2 control-label">email *</label>
                                <input type="text" name="email" maxlength="32" value="{{ old('email') }}"
                                       class="form-control" required pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Please enter valid email address"/>
                            </div>
                            @if ($errors->has('email'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <div class="col-md-12">
                                <label for="phone" class="col-md-2 control-label">phone *</label>
                                <input type="text" name="phone" maxlength="14" value="{{ old('phone') }}"
                                       class="form-control" required/>
                            </div>
                            @if ($errors->has('phone'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <div class="col-md-12">
                                <label for="address" class="col-md-2 control-label">address (Optional)</label>
                                <input type="text" name="address"  value="{{ old('address') }}"
                                       class="form-control" />
                            </div>
                            @if ($errors->has('address'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <div class="col-md-12">
                                <label for="rights" class="col-md-2 control-label">Role Type *</label>
                                <select class="form-control col-md-7 col-xs-12 role-type js-example-basic-multiple" name="hubPermission[]" required multiple>
                                    @foreach($deliveryProcessType as $processType)
                                        <option value="{{$processType->id}}">{{$processType->process_title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($errors->has('role'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('role') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <div class="col-md-12">
                                <label for="password" class="col-md-2 control-label">password *</label>
                                <input type="password" id="password" name="password" minlength="6" maxlength="32"
                                       class="form-control" required/>
                            </div>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="form-group col-md-6 col-12">
                            <div class="col-md-12">
                                <label for="confirmpwd" class="col-md-2 control-label">Confirm Password *</label>
                                <input type="password" name="confirmpwd" id="confirmpassword" minlength="6" class="form-control" required/>
                                <span id='message'></span>
                                </label>
                            </div>
                            @if ($errors->has('confirmpwd'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('confirmpwd') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group col-md-6 col-12" >
                            <div class="col-md-12">
                                {{ Form::label('profile_picture', 'Upload Picture', ['class'=>'col-md-2 control-label']) }}
                                {{ Form::file('profile_picture', ['class' => 'form-control ']) }}
                            </div>
                            @if ( $errors->has('profile_picture') )
                                <p class="help-block">{{ $errors->first('profile_picture') }}</p>
                            @endif
                        </div>

                       
                        <div class="form-group col-md-6 col-12{{ $errors->has('type') ? ' has-error' : '' }}">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                {{ Form::label('type', 'Manager', ['class'=>'col-md-6 col-sm-6 col-xs-12']) }}
                                <input type="checkbox" style="height: 15px; width: 15px" name="type" maxlength="150"
                                       value="manager"
                                       class="form-control"/>
                            </div>
                            @if ( $errors->has('type') )
                                <p class="help-block">{{ $errors->first('type') }}</p>
                            @endif
                        </div>

                        <div class="form-group col-md-12 col-12">
                            <div class="col-md-12">
                                <input type="submit" class="btn orange" id="save" value="Save">
                            </div>
                        </div>
                    </form>
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
