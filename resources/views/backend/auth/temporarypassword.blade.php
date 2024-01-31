<?php
$user = Auth::user();
if ($user->email != "admin@gmail.com") {

    $data = explode(',', $user['rights']);
    $permissions = explode(',', $user['permissions']);
} else {
    $data = [];
    $permissions = [];
}

?>

@extends( 'backend.layouts.app' )

@section('title', 'Dashboard')

@section('CSSLibraries')
    <style>
        .dashboard-statistics-box {
            min-height: 400px;
            margin: 15px 0px;
            position: relative;
            box-sizing: border-box;
        }

        .dashboard-statistics-box.dashboard-statistics-tbl-show td {
            padding-top: 52px;
            padding-bottom: 52px;
        }
    </style>
@endsection
@section('JSLibraries')
    <script src="{{ backend_asset('libraries/Chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ backend_asset('nprogress/nprogress.js') }}"></script>
    <script src="{{ backend_asset('libraries/gauge.js/dist/gauge.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/skycons/skycons.js') }}"></script>
    <script src="{{ backend_asset('libraries/Chart.js/dist/Chart.min.js') }}"></script>

@endsection


@section('content')
    <!--right_col open-->
    <div class="page-content-wrapper cutomMainbox_us" style="min-height:1100px !important">
        <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
        <aside id="right_content" class="col-12 col-lg-9">
            <div class="inner">

                <!-- Row - Upload profile photo & Subscribe -->
                <form method="POST" action="{{url('microhub/temp/password')}}" id="account-form" class="needs-validation ssr-validation" enctype="multipart/form-data">

                    <section class="form-section">
                        <div class="section-inner">
                            <h4><i class="icofont-user-alt-5"></i> Generate Temporary Password </h4>
                            <div class="form-row">
                                <div class="col-12 col-md-8">
                                    <div class="row">
                                        <div class="col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="Fullname">Temporary Password (valid for 20 minutes)</label>
                                                <input required="" type="text" class="form-control form-control-lg" placeholder="XXXXXXX" name="password" id="password" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12 col-12 content_footer_wrap">
                                            <button type="submit" class="btn btn-primary submitButton" onclick="generatePassword()" id="generate">Generate</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>



                </form>
            </div>
        </aside>

    </div>
    <!-- /#page-wrapper -->

    <script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0&libraries=places" ></script>
    {{--    <script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbVKartNGg&libraries=places"></script>--}}

    <script>
        var password=document.getElementById("password");

        function generatePassword() {
            var chars = "0123456789abcdefghijklmnopqrstuvwxyz!@#$%^&*()ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            var passwordLength = 12;
            var password = "";
            for (var i = 0; i <= passwordLength; i++) {
                var randomNumber = Math.floor(Math.random() * chars.length);
                password += chars.substring(randomNumber, randomNumber +1);
            }
            document.getElementById("password").value = password;
        }

       const button = document.getElementById('generate')

        // Main Ajax Call For Setting Permission...
        $(document).ready(function() {
            function load() {

                $.ajax({
                    url: 'getGeneratedPassword',
                    type:'get',
                    dataType:'json',
                    success: function(response){

                        if(response != null){
                            button.disabled = true
                            console.log(response)
                        }
                        document.getElementById("password").value = response;
                    }
                });
            }

            window.onload = load;
        })


    </script>
@endsection