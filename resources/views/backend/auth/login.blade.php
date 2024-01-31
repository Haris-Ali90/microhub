@extends('backend.layouts.app-guest')

@section('title', 'Login')
<style>
    .divider-after:after {
        display: none !important;
    }
</style>
@section('content')
<main id="main" class="page-login">
    <!--<img src="{{ asset('images/under-maintenance.png')}}" alt="" style="height: 100vh; width: 100%">-->
    <div class="pg-container container-fluid">
        <div class="row_1 row align-items-top no-gutters justify-content-end">
            <!-- Login left column - [Start] -->
            <aside class="left-column col-12 col-md-5 full-h d-none d-sm-block">

            </aside>
            <!-- Login left column - [/end] -->

            <!-- Login right column - [Start] -->
            <aside class="right-column col-12 col-sm-7">
                <div class="inner full-h-min flexbox flex-center">
                    <div class="full-w">
                        <div id="logo" class="dp-table marginauto mb-20">
                            <img src="{{ asset('images/logo-no-background.png')}}" alt="">
                        </div>

                        <!-- BEGIN LOGIN FORM -->
                        <div class="row no-gutters justify-content-center">
                            <div class="col-10 col-md-9 col-lg-5 col-xl-5">
                                <div class="hgroup divider-after align-center">
                                    <h1>Login To Micro-Hub Portal</h1>
                                    <p class="f14">To login please enter your login credentials</p>
                                </div>
                                <!-- Login Form -->
                                <form method="POST" id="login-form" class="needs-validation" novalidate>
                                    {{ csrf_field() }}
                                    @if (session('status'))
                                    <div class="alert alert-success" style="font-size: 15px">
                                        {{ session('status') }}
                                    </div>
                                    @endif
                                    @if ( $errors->count() )
                                    <div class="alert alert-danger" style="font-size: 15px">
                                        {!! implode('<br />', $errors->all()) !!}
                                    </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="emailInput">Email / Username</label>
                                        <input type="email" name="email" class="form-control form-control-lg"
                                            id="emailInput" autofocus value="{{ old('email') }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="paswordInput">Password</label>
                                        <input type="password" name="password" class="form-control form-control-lg"
                                            id="paswordInput" required>
                                    </div>

                                    <div class="align-center mt-10">
                                        <button type="submit" class="btn btn-primary submitButton">Login</button>
                                    </div>

                                </form>
                                <div class="extra-info">
                                    <p class="forgot-pwd align-center">
                                        <a href="{{ backend_url('reset-password') }}"
                                            class="brd-bc1-light pr-10 none">Lost your password?</a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        @endsection