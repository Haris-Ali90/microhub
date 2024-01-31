<?php

$user_auth = Auth::user();
$user_id = $user_auth->id;
$user_name = $user_auth->full_name;

?>

<div class="header_sec">
    <div class="row-1 row align-items-center">
        <div class="col-6 col-md-6 col-lg-6">
            <div id="logo">
                <img src="{{ asset('images/logo-no-background.png')}}" alt="Micro-Hub" width="140" height="80">
            </div>
        </div>
        <div class="col-6 col-md-6 col-lg-6">
            <div class="nav_menu">
                    <nav>

                        <ul class="nav navbar-nav navbar-right">
                            <li class="">
                                <span><strong><?php echo $user_name?>'s MiHub</strong><br><a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                                                                                             aria-expanded="false">
                                    <span>Logout</span>
                                        <ul class="dropdown-menu dropdown-usermenu pull-right">


                                    @if($user_auth->userType == 'admin')
                                                <li><a href="{{backend_url('microhub/profile')}}"><i class="fa fa-edit pull-right"></i>Edit Profile</a>
                                                @endif

                                            </li>
                                                @if(can_access_route('account-security.edit',$userPermissoins))
                                                    <li>
                                                <a href="{{ backend_url('account/security/edit/'.base64_encode(auth()->user()->id)) }}"><i class="fa fa-lock pull-right"></i>Account Security
                                                </a>
                                            </li>
                                                @endif
                                                @if(can_access_route('sub-admin-change.password',$userPermissoins))
                                                    <li>
                                                <a href="{{ backend_url('changepwd') }}"><i class="fa fa-key pull-right"></i>
                                                Change Password
                                                </a>
                                            </li>
                                                @endif
                                                <li>
                                        <a href="#" onclick="document.getElementById('logout-form').submit();"><i
                                                    class="fa fa-sign-out pull-right"></i> Logout</a>
                                        <form id="logout-form" action="{{ url('logout') }}" method="POST">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>

                                </ul>
                                </a></span>


                               <a href="#" class="profile"> <img src="{{ asset('images/profile_img.jpg')}}" alt=""></a>

                            </li>


                        </ul>
                    </nav>
            </div>
        </div>
    </div>
</div>