@extends( 'backend.layouts.app' )

@section('title', 'Edit Sub Admin')

@section('CSSLibraries')
    <!-- Custom Light Box Css -->
    <link href="{{ backend_asset('css/custom_lightbox.css') }}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <!-- Custom Light Box JS -->
    <script src="{{ backend_asset('js/custom_lightbox.js')}}"></script>

@endsection

@section('content')

    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet box blue">

                <div class="portlet-title">
                    <div class="col-md-4 caption">
                        <h3><i class="fa fa-edit"></i> Edit Sub Admin</h3>
                    </div>
                </div>

                <div class="portlet-body libs_customimize">

                    <h4>&nbsp;</h4>

                    <form method="POST" action="{{ route('subAdmin.update', $user->id) }}" class="form-horizontal colrhtme"
                          role="form" enctype="multipart/form-data">
                        <div class="usCustomForm" style="width: 100%;">
                            <div class="form-group">
                                <label for="full_name" class="col-md-2 control-label">First Name *</label>
                                <div class="col-md-12">
                                    <input type="text" name="full_name" maxlength="150"
                                           value="{{ old('full_name', $user->full_name) }}" class="form-control" required
                                           pattern="^([A-Za-z]+[,.]?[ ]?|[A-Za-z]+['-]?)+$"
                                           title="Please enter valid first name" />
                                </div>
                                @if ($errors->has('full_name'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('full_name') }}</strong>
                            </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="email" class="col-md-2 control-label">Email *</label>
                                <div class="col-md-12">
                                    <input type="text" name="email" value="{{ old('email', $user->email) }}"
                                           class="form-control" readonly />
                                </div>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="phone" class="col-md-2 control-label">Phone *</label>
                                <div class="col-md-12">
                                    <input type="text" name="phone" maxlength="14" value="{{ old('phone', $user->phone) }}"
                                           class="form-control" required />
                                </div>
                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="address" class="col-md-2 control-label">Address (Optional)</label>
                                <div class="col-md-12">
                                    <input type="text" name="address" value="{{ old('address', $user->address) }}"
                                           class="form-control" />
                                </div>
                                @if ($errors->has('address'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('address') }}</strong>
                            </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="rights" class="col-md-2 control-label">Role Type *</label>
                                <div class="col-md-12">
                                    <select class="form-control col-md-7 col-xs-12 role-type js-example-basic-multiple"
                                            name="hubPermission[]" multiple required>
                                        <option value="">Select an option</option>

                                        @foreach($hubProcess as $processType)
                                            <option value="{{$processType->id}}">
                                                {{$processType->deliveryProcess->process_title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('role'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('role') }}</strong>
                            </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-md-2 control-label">Password</label>
                                <div class="col-md-12">
                                    <input type="password" name="password" class="form-control" />
                                </div>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                                @endif

                            </div>


                            <div class="form-group">
                                <div>

                                    {{ Form::label('profile_picture', 'Upload Picture', ['class'=>'col-md-2 control-label'])
                                    }}
                                    <div class="col-md-12">
                                        {{ Form::file('profile_picture', ['class' => 'form-control
                                        ','onchange'=>"checkFileExtension(this)"]) }}
                                        <img style="max-width: 350px;height: 150px;margin-top: 4px" onClick="preview(this);"
                                             src="{{$user->profile_picture}}" />

                                    </div>
                                    @if ( $errors->has('profile_picture') )
                                        <p class="help-block">{{ $errors->first('profile_picture') }}</p>
                                    @endif


                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                                {{ Form::label('type', 'Manager', ['class'=>'control-label col-md-3 col-sm-3 col-lg-6 ']) }}
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="checkbox" id="checkbox" style="height: 19px; width: 19px" name="type" maxlength="150" {{ $user->type == 'manager' ? 'checked' : '' }}
                                    value="manager" class="form-control"/>
                                </div>
                                @if ( $errors->has('type') )
                                    <p class="help-block">{{ $errors->first('type') }}</p>
                                @endif
                            </div>
                        </div>






                        <div class="col-md-10">
                            <input type="submit" class="btn orange" id="save" value="Save">
                            <a href="{{url('subadmins')}}" class="btn btn-default">Cancel</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
        <!-- END SAMPLE FORM PORTLET-->

    </div>
    </div>
    <!-- /page content -->
@endsection
@section('inlineJS')
    <script src="{{ backend_asset('js/custom.js')}}"></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            make_multi_option_selected('.role-type', '{{$selectedPermission}}');
        });
    </script>

@endsection