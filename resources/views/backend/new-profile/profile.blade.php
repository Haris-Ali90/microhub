<?php
$own_joey_check = "";
$not_own_joey_check = "";

if($hub_user_data == 1){
    $own_joey_check = "checked";
}

if($hub_user_data == 0){
    $not_own_joey_check = "checked";
}

?>
@extends( 'backend.layouts.app' )

@section('title', 'New Profile')

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
        .form-group .form-control:not(select):not(textarea) {
            height: 44px;
        }
        .col-md-3.left_col {
            display: none !important;
        }
        .page-content-wrapper.cutomMainbox_us {
            margin-left: 0;
        }
        .form-group .form-check .form-check-label, .form-group .form-check .form-radio-label, .form-group .form-radio .form-check-label, .form-group .form-radio .form-radio-label {
            display: -webkit-box;
            display: -moz-box;
            display: box;
            display: -webkit-flex;
            display: -moz-flex;
            display: -ms-flexbox;
            display: flex;
            border-radius: 10px;
            color: #e46d29;
            border-color: #e46d29;
            box-shadow: 0 1px 3px #eae6e4;
        }
        .bfsize {
            font-size: 50px !important;
        }
        .divider-after:after {
            content: "";
            display: block;
            background: #e46d29;
            margin: 15px auto;
            width: 75px;
            position: absolute;
             top: unset;
            height: 2px;
            bottom: -15px;
        }
        .custom-radio.form-radio.custom-control-inline input {
            opacity: 0;
            display: none;
        }
        .section-inner h4 {
            color: #f2782f;
            font-size: 22px;
            font-weight: 600;
        }
        section.progress_sec {
            float: left;
            width: 100%;
            display: grid;
            justify-content: flex-start;
            margin-bottom: 50px;
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

        <div class="content_header_wrap">
            <!-- Row -->
            <div class="row align-items-center">
                <div class="col-12 col-md-8">
                    <div class="hgroup divider-after left">
                        <h1 class="lh-10"><span class="bfsize bf-color regular dp-block">Welcome</span></h1>
                    </div>
                </div>

            </div>
        </div>
        <section class="progress_sec" style="padding-top: 0px">
            <div class="container">
                <div role="progressbar" aria-valuenow="67" aria-valuemin="0" aria-valuemax="100" style="--value: 50"></div>
            </div>
        </section>
                <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
        <aside id="right_content" class="col-12 col-lg-6">
        <div class="inner">
                    <!-- Row - Upload profile photo & Subscribe -->
            <form method="POST" action="{{url('microhub/newprofile/update')}}" id="account-form" class="needs-validation ssr-validation" enctype="multipart/form-data">

                <section class="form-section">
                    <div class="section-inner">
                        <h4><i class="fa fa-edit"></i> Personal Information</h4>
                        <div class="form-row">
                            <div class="col-12 col-md-12">
                                <div class="row">
                                    <div class="col-sm-6 col-12">
                                        <div class="form-group">

                                            <label for="Fullname">Full Name*</label>
                                            <input required="" type="text" class="form-control form-control-lg" placeholder="e.g: John" name="full_name" value="{{$user->full_name}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="number">Phone Number *</label>
                                            <input type="text" class="form-control form-control-lg " placeholder="e.g: 9052814440" name="phone_no" maxlength="14" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" value="{{$user->phone}}" required="">
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-12">
                                        <div class="form-group">
                                            <label for="number">Email</label>
                                            <input type="email" class="form-control form-control-lg" placeholder="Email_Address" name="email_address" required="" readonly=""  value="{{$user->email}}">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12 col-12">
                                        <label for="Address">Address *</label>
                                        <input type="text" class="form-control" id="search_input" placeholder="Add Location" onchange="" name="search_input" value="{{$user->address}}"/>
                                    </div>

                                    <div class="form-group" id="latitudeArea" style="display: none">
                                        <label>Latitude</label>
                                        <input type="text" id="latitude" name="latitude" class="form-control">
                                    </div>
                                    <div class="form-group" id="longtitudeArea" style="display: none">
                                        <label>Longitude</label>
                                        <input type="text" name="longitude" id="longitude" class="form-control">
                                    </div>


                                    <div class="form-group no-min-h  col-md-12">
                                        <label>I will manage my own riders (Joeys)</label>
                                        <div class="custom-radio form-radio custom-control-inline">
                                            <input {{$own_joey_check}} class="form-radio-input" type="radio" name="own_joeys" id="yes" value="1">
                                            <label class="form-radio-label mb-0" for="yes">Yes</label>
                                        </div>
                                        <div class="custom-radio form-radio custom-control-inline">
                                            <input {{$not_own_joey_check}} class="form-radio-input" type="radio" name="own_joeys" id="no" value="0">
                                            <label class="form-radio-label mb-0" for="no">No</label>
                                        </div>
                                    </div>
                                    <div class="form-group no-min-h  col-md-12">
                                        <label>Personal Detail</label>
                                    </div>
                                    <div class="form-group no-min-h col-md-6">
                                        <input required="" type="text" id="search_input2" placeholder="abc@example.com" name="search_input2" class="form-control form-control-lg" value="{{$jc_user_address}}">
                                    </div>
                                    <div class="form-group no-min-h col-md-6">
                                        <input required="" type="text" placeholder="+1 604 555 5555" name="jc_phone" class="form-control form-control-lg" value="{{$jc_user_phone}}">
                                    </div>
                                    <div class="form-group" id="latitudeArea" style="display: none">
                                        <label>Latitude</label>
                                        <input type="text" id="latitude2" name="latitude2" class="form-control">
                                    </div>
                                    <div class="form-group" id="longtitudeArea" style="display: none">
                                        <label>Longitude</label>
                                        <input type="text" name="longitude2" id="longitude2" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <input type="submit" class="next_btn next_page" value="NEXT">


            </form>
        </div>
        </aside>
        </div>
    <!-- /#page-wrapper -->


    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false&key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0"></script>
    <script type="text/javascript">
        google.maps.event.addDomListener(window, 'load', function () {
            var places = new google.maps.places.Autocomplete(document.getElementById('search_input'));
            google.maps.event.addListener(places, 'place_changed', function () {
                var place = places.getPlace();
                var address = place.formatted_address;
                var latitude = place.geometry.location.lat();
                var longitude = place.geometry.location.lng();
                var latlng = new google.maps.LatLng(latitude, longitude);
                var geocoder = geocoder = new google.maps.Geocoder();
                geocoder.geocode({ 'latLng': latlng }, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            var address = results[0].formatted_address;
                            var pin = results[0].address_components[results[0].address_components.length - 1].long_name;
                            var country = results[0].address_components[results[0].address_components.length - 2].long_name;
                            var state = results[0].address_components[results[0].address_components.length - 3].long_name;
                            var city = results[0].address_components[results[0].address_components.length - 4].long_name;

                            console.log(latitude)
                            console.log(longitude)
                            console.log(address)

                            document.getElementById('search_input').value = address;
                            document.getElementById('latitude').value = latitude;
                            document.getElementById('longitude').value = longitude;

                        }
                    }
                });
            });


        });
    </script>


    <script type="text/javascript">
        google.maps.event.addDomListener(window, 'load', function () {
            var places = new google.maps.places.Autocomplete(document.getElementById('search_input2'));
            google.maps.event.addListener(places, 'place_changed', function () {
                var place = places.getPlace();
                var address = place.formatted_address;
                var latitude = place.geometry.location.lat();
                var longitude = place.geometry.location.lng();


                var latlng = new google.maps.LatLng(latitude, longitude);
                var geocoder = geocoder = new google.maps.Geocoder();
                geocoder.geocode({ 'latLng': latlng }, function (results, status) {


                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            var address = results[0].formatted_address;
                            var pin = results[0].address_components[results[0].address_components.length - 1].long_name;
                            var country = results[0].address_components[results[0].address_components.length - 2].long_name;
                            var state = results[0].address_components[results[0].address_components.length - 3].long_name;
                            var city = results[0].address_components[results[0].address_components.length - 4].long_name;

                            console.log(latitude)
                            console.log(longitude)
                            console.log(address)

                            document.getElementById('search_input2').value = address;
                            document.getElementById('latitude2').value = latitude;
                            document.getElementById('longitude2').value = longitude;

                        }
                    }
                });
            });


        });
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyDTK4viphUKcrJBSuoidDqRhVA4AWnHOo0"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>





@endsection