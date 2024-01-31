@extends( 'backend.layouts.app' )

@section('title', 'Security')

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
            <div class="page-title">
                <div class="title_left">
                    <h3>Account Security</h3>
                </div>


            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">

                    <div class="x_panel">




                        <div class="x_title">
                            <h2>2 Factor Authentication  <small></small></h2>

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                        @include( 'backend.layouts.notification_message' )
                        {!! Form::model($user, ['url' => ['account/security',$user->id], 'method' => 'PUT','files' => true , 'class' => 'form-horizontal form-label-left', 'role' => 'form']) !!}
                        @include( 'backend.subadmin.securityform')
                        {!! Form::close() !!}

                        </div>
                       
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /page content -->
@endsection
