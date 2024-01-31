@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
@endsection
<div class="form-group{{ $errors->has('full_name') ? ' has-error' : '' }}">
    {{ Form::label('full_name', 'Full Name *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('full_name', null, ['class' => 'form-control col-md-7 col-xs-12','required' => 'required']) }}
    </div>
    @if ( $errors->has('full_name') )
        <p class="help-block">{{ $errors->first('full_name') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
    {{ Form::label('email', 'Email', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::email('email', null, ['class' => 'form-control col-md-7 col-xs-12','readonly' ]) }}
    </div>
    @if ( $errors->has('email') )
        <p class="help-block">{{ $errors->first('email') }}</p>
    @endif
</div>
<div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
    {{ Form::label('phone', 'Mobile', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('phone', null, ['class' => 'class="date-picker form-control col-md-7 col-xs-12" ','required' => 'required']) }}
    </div>
    @if ( $errors->has('phone') )
        <p class="help-block">{{ $errors->first('phone') }}</p>
    @endif
</div>
<div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
    {{ Form::label('address', 'Address(Optional)', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('address', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('address') )
        <p class="help-block">{{ $errors->first('address') }}</p>
    @endif
</div>
<div class="form-group{{ $errors->has('rights') ? ' has-error' : '' }}">
    {{ Form::label('rights', 'Rights ', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        <select class="js-example-basic-multiple form-control col-md-7 col-xs-12" name="rights[]" required=""
                multiple="multiple">
            <option value="statistics" {{(in_array('statistics', $rights)) ? 'Selected' : ''}}>Statistics</option>
            <option value="subadmins" {{(in_array('subadmins', $rights)) ? 'Selected' : ''}}> Sub Admin</option>
            <option value="montreal_dashboard" {{(in_array('montreal_dashboard', $rights)) ? 'Selected' : ''}}> Amazone
                Montreal
            </option>
            <option value="ottawa_dashboard" {{(in_array('ottawa_dashboard', $rights)) ? 'Selected' : ''}}> Amazone
                Ottawa
            </option>
            <option value="ctc_dashboard" {{(in_array('ctc_dashboard', $rights)) ? 'Selected' : ''}}>Ctc</option>
            <option value="walmart_dashboard" {{(in_array('walmart_dashboard', $rights)) ? 'Selected' : ''}}>Walmart
            </option>
            <option value="other_action" {{(in_array('other_action', $rights)) ? 'Selected' : ''}}>Other Action
            </option>
        </select>
    </div>
    @if ( $errors->has('rights') )
        <p class="help-block">{{ $errors->first('rights') }}</p>
    @endif
</div>
<div class="form-group{{ $errors->has('permissions') ? ' has-error' : '' }}">
    {{ Form::label('permissions', 'Permissions ', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
    <!-- {{ Form::select('institute_id',  array('1' => 'Anees Hussain', '2' => 'Ahmedabad', '3' => 'Aligarh Institute'),null,['class' => 'form-control col-md-7 col-xs-12']) }} -->
        <select class="js-example-basic-multiple form-control col-md-7 col-xs-12" name="permissions[]" required=""
                multiple="multiple">
            <option value="read" {{(in_array('read', $permissions)) ? 'Selected' : ''}}> View</option>
            <option value="add" {{(in_array('add', $permissions)) ? 'Selected' : ''}}> Add</option>
            <option value="edit" {{(in_array('edit', $permissions)) ? 'Selected' : ''}}> Edit</option>
            <option value="delete" {{(in_array('delete', $permissions)) ? 'Selected' : ''}}> Delete</option>
        </select>
    </div>
    @if ( $errors->has('permissions') )
        <p class="help-block">{{ $errors->first('permissions') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
    {{ Form::label('password', 'Password', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::password('password', ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('password') )
        <p class="help-block">{{ $errors->first('password') }}</p>
    @endif
</div>
<div class="form-group{{ $errors->has('profile_picture') ? ' has-error' : '' }}">
    {{ Form::label('profile_picture', 'Profile picture', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::file('profile_picture', null, ['class' => 'form-control col-md-7 col-xs-12','required' => 'required']) }}
    </div>
    @if ( $errors->has('profile_picture') )
        <p class="help-block">{{ $errors->first('profile_picture') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('avatar_view') ? ' has-error' : '' }}">
    <div class="col-md-6 col-sm-6 col-xs-12">
        <img onClick="ShowLightBox(this);" src="{{$user->profile_picture}}" style = "width:50px;height:50px; margin-left: 51.5%;" class="avatar" alt="Avatar"/>
    </div>

</div>



<!-- <div class="form-group{{ $errors->has('confirmpwd') ? ' has-error' : '' }}">
        {{ Form::label('confirmpwd', 'Confirm Password', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
        <div class="col-md-6 col-sm-6 col-xs-12">
                {{ Form::text('confirmpwd', null, ['class' => 'form-control col-md-7 col-xs-12','required' => 'required']) }}
        </div>
        @if ( $errors->has('confirmpwd') )
    <p class="help-block">{{ $errors->first('confirmpwd') }}</p>
        @endif
        </div> -->
<div class="ln_solid"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
        {{ Html::link( backend_url('/'), 'Cancel', ['class' => 'btn btn-default']) }}
    </div>
</div>
@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/moment/min/moment.min.js') }}"></script>
    <script src="{{ backend_asset('libraries//bootstrap-daterangepicker/daterangepicker.js') }}"></script>
@endsection

@section('inlineJS')
    <script>
        $(document).ready(function () {
            $('#birthday').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                calender_style: "picker_4"
            }, function (start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
            });
        });
    </script>
@endsection
