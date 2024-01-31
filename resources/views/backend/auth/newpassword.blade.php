@extends('backend.layouts.app-guest')

@section('title', 'Login')

@section('content')
    <div>
        <a class="hiddenanchor" id="signup"></a>
        <a class="hiddenanchor" id="signin"></a>

        <div class="login_wrapper">
            <div class="animate form login_form">
                <section class="login_content">
                    <img class="dashboard-logo-text" src="{{ url('/') }}/images/abc.png">
{{--                    <form role="form"method="post" action="{{ route('password.email') }}">--}}
                    <form role="form"method="post" action="{{url('microhub/change/newpassword')}}">
                        <h3>Change your temporary password</h3>
                        {{ csrf_field() }}
                        <input type="hidden" name="role_id"  value="2" />
                        <fieldset>
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
{{--                            <div class="form-group">--}}
{{--                                <input class="form-control" id="current" placeholder="Current Password" name="currentpassword" type="password" autofocus >--}}
{{--                                <label id="MatchCurrent"></label>--}}
{{--                            </div>--}}

                                <div class="form-group">
                                    <input class="form-control" id="new" placeholder="New Password" name="newpassword" type="password" autofocus data-rule-email="true" required>
                                </div>

                                <div class="form-group">
                                    <input class="form-control" id="confirm" placeholder="Confirm Password" name="confirmpassword" type="password" autofocus data-rule-email="true" data-rule-equalTo="#new-password" required>
                                <label id="CheckPasswordMatch"></label>
                                </div>

                            <button type="submit" class="btn btn-lg btn-success btn-block" id="submit">Change Password</button>
                        </fieldset>
                    </form>
                </section>
            </div>
        </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
        function checkPasswordMatch() {
            var current_password = $("#current").val();
            var password = $("#new").val();
            var confirmPassword = $("#confirm").val();
            var temporary_password = '';

        <?php
            if(!is_null($pass)){
            ?>

            temporary_password = <?php echo json_encode($pass); ?>;

            if(current_password != toString(temporary_password)){

                $("#MatchCurrent").html("Current Password is not correct!");
            }
            if (password != confirmPassword) {
                $("#CheckPasswordMatch").html("Passwords does not match!");
                document.getElementById("submit").style.pointerEvents = "none";
            } else {
                document.getElementById("submit").style.pointerEvents = "";
                $("#CheckPasswordMatch").html("");
                $("#MatchCurrent").html("");
            }

            <?php
            }
            ?>


        }
        $(document).ready(function () {
            $("#confirm").keyup(checkPasswordMatch);
        });
    </script>
@endsection

