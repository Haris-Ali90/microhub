<?php
$user = Auth::user();
if ($user->email != "admin@gmail.com") {

    $data = explode(',', $user['rights']);
    $dataPermission = explode(',', $user['permissions']);
} else {
    $data = [];
    $dataPermission = [];
}
$userPermissoins = Auth::user()->getPermissions();
$user_id = Auth::user()->id;
$hubProcessPermission = \App\MicroHubPermission::where('micro_hub_user_id', $user_id)->whereNull('deleted_at')->pluck('hub_process_id')->toArray();
$hubProcess = \App\HubProcess::whereIn('id', $hubProcessPermission)->where('is_active', 1)->whereNull('deleted_at')->pluck('process_id')->toArray();
$microHubPermissoins = \App\DeliveryProcessType::whereIn('id', $hubProcess)->whereNull('deleted_at')->pluck('process_label')->toArray();
$approved_order_list = \App\ReturnAndReattemptProcessHistory::where('verified_by', '!=', null)
    ->whereNull('deleted_at')
    ->where('is_processed', 0)
    ->where('created_by', auth()->user()->id)
    ->count();
?>
<style>
    .badge {
        position: absolute;
        top: 5px;
        right: 40px;
        border-radius: 50%;
        background-color: red;
        color: white;
    }
</style>
<div class="col-md-3 left_col navBar">
    <div class="left_col scroll-view">
        <div class="nav toggle">
            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
        </div>
        <br/>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <ul class="nav side-menu">

                    @if (can_user_access_route("dashboard") == true )
                        @if(can_access_route(['dashboard'],$microHubPermissoins))
                            <li class="active">
                                <a><i class="fa fa-dashboard"></i><label>Dashboard</label><i
                                            class="icofont-caret-up"></i><span
                                            class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ backend_url('dashboard') }}"> Dashboard View</a></li>
                                    <li><a href="{{ backend_url('inbound') }}"> In Bound </a></li>
                                    <li><a href="{{ backend_url('outbound') }}">Out Bound</a></li>
                                    <li><a href="{{ backend_url('microhub/summary') }}">Summary</a></li>
                                </ul>
                            </li>
                        @endif
                    @endif

                    @if (can_user_access_route("sub-admin") == true )
                        @if(can_access_route(['sub-admin'],$microHubPermissoins))
                            <li class="">
                                <a><i class="fa fa-users"></i><label>Sub Admin</label><i
                                            class="icofont-caret-up"></i><span
                                            class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ backend_url('subadmins') }}"> Sub Admin List </a></li>
                                    <li><a href="{{ backend_url('subadmin/add') }}"> Add Sub Admin </a></li>
                                </ul>
                            </li>
                        @endif
                    @endif

                    <li>
                        <a><i class="fa fa-truck"></i><label>Routing</label><i class="icofont-caret-up"></i> <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            @if(can_user_access_route("first-mile") == true )
                                @if(can_access_route(['first-mile'],$microHubPermissoins))
                                    <li><a href="{{ backend_url('first/mile/hub/list')}}"> First Mile Routing </a></li>
                                @endif
                            @endif

                            @if(can_user_access_route("mid-mile-routing") == true )
                                @if(can_access_route(['mid-mile-routing'],$microHubPermissoins))
                                    <li><a href="{{ backend_url('mid/mile/mi/job')}}"> Mid Mile Routing </a></li>
                                @endif
                            @endif

                            @if(can_user_access_route("last-mile") == true )
                                @if(can_access_route(['last-mile'],$microHubPermissoins))
                                    <li><a href="{{ backend_url('last/mile/zones')}}"> Last Mile Routing </a></li>
                                @endif
                            @endif
                            @if(can_user_access_route("last-mile-routing") == true )
                                @if(can_access_route(['last-mile-routing'],$microHubPermissoins))
                                    <li><a href="{{ backend_url('last/mile/routes/only/list')}}"> Last Mile Direct Routes </a></li>
                                @endif
                            @endif
                            @if(can_user_access_route("last-mile-route-list") == true )
                                @if(can_access_route(['last-mile-route-list'],$microHubPermissoins))
                                    <li><a href="{{ backend_url('last/mile/routes/list')}}"> Last Mile Route List </a></li>
                                @endif
                            @endif
                        </ul>
                    </li>


                    @if (can_user_access_route("first-mile-reporting") == true )
                        @if(can_access_route(['first-mile-reporting'],$microHubPermissoins))
                            <li>
                                <a><i class="fa fa-tachometer"></i><label>First Mile Reporting</label><i
                                            class="icofont-caret-up"></i><span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ backend_url('first-mile-summary').'?days=1days' }}">First Mile Summary</a></li>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    @endif

                    @if (can_user_access_route("last-mile-reporting") == true )
                        @if(can_access_route(['last-mile-reporting'],$microHubPermissoins))
                            <li>
                                <a><i class="fa fa-tachometer"></i><label>Last Mile Reporting</label><i
                                            class="icofont-caret-up"></i> <span
                                            class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    {{--<li><a href="{{ backend_url('last-mile-dashboard') }}"> Client Dashboard</a></li>--}}
                                    <li><a href="{{ backend_url('new/last-mile/card-dashboard') }}"> Last Mile
                                            Statistics</a></li>
                                    {{--<li><a href="{{ backend_url('new/last-mile/order') }}"> Last Mile Dashboard</a></li>--}}
                                    <li><a href="{{ backend_url('new/last-mile/sorted') }}"> Sorted Order</a></li>
                                    <li><a href="{{ backend_url('new/last-mile/picked/up') }}"> Out For Delivery</a>
                                    </li>
                                    <li><a href="{{ backend_url('new/last-mile/not/scan') }}"> Not Scan</a></li>
                                    <li><a href="{{ backend_url('new/last-mile/delivered') }}"> Delivered Orders</a>
                                    </li>
                                    <li><a href="{{ backend_url('new/last-mile/returned') }}">Returned Orders</a></li>
                                    <li><a href="{{ backend_url('new/last-mile/custom-route') }}"> Create By Custom
                                            Route</a></li>
                                    {{--<li><a href="{{ backend_url('last-mile/graph') }}"> Last Mile Graph</a></li>--}}
                                    <li><a href="{{ backend_url('last-mile/route-info') }}"> Route Information</a></li>

                                    </li>
                                </ul>
                            </li>

                        @endif
                    @endif
                    @if (can_user_access_route("joeys-management") == true )
                        @if(can_access_route(['joeys-management'],$microHubPermissoins))
                            <li>

                                <a><i class="fa fa-tasks"></i><label>Driver Management</label><i
                                            class="icofont-caret-up"></i><span
                                            class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ backend_url('joeys') }}">Driver Management</a></li>
                                </ul>
                            </li>
                        @endif
                    @endif

                    @if(can_user_access_route("scanning-bundle-order") == true || can_user_access_route("bundle-scanning-list") == true)

                        @if(can_access_route(['scanning-bundle-order','bundle-scanning-list'],$microHubPermissoins))
                            <li>
                                <a>
                                    <i class="fa fa-exchange"></i><label>Micro Hub Scanning</label><i class="icofont-caret-up"></i><span
                                            class="fa fa-chevron-down"></span>
                                </a>
                                <ul class="nav child_menu">
                                    @if(can_user_access_route("scanning-bundle-order") == true )
                                        @if(can_access_route(['scanning-bundle-order'],$microHubPermissoins))
                                            <li><a href="{{ backend_url('scanning-bundle/order')}}"> Micro Hub Scanning </a></li>
                                        @endif
                                    @endif
                                    @if(can_user_access_route("bundle-scanning-list") == true )
                                        @if(can_access_route(['bundle-scanning-list'],$microHubPermissoins))
                                            <li><a href="{{ backend_url('bundle-scanning/list')}}"> Micro Hub Bundle Scanning </a></li>
                                        @endif
                                    @endif
                                </ul>
                            </li>
                        @endif
                    @endif

                    @if (can_user_access_route("claims") == true )
                        @if(can_access_route(['claims'],$microHubPermissoins))
                            <li>
                                <a><i class="fa fa-tasks"></i><label>Claims</label><i class="icofont-caret-up"></i><span
                                            class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ route('claims.create') }}"> Add Claims </a></li>
                                    <li><a href="{{ backend_url('claims/pending-list') }}"> Pending Claims List</a></li>
                                    <li><a href="{{ backend_url('claims/approved-list') }}"> Approved Claims List </a>
                                    </li>
                                    <li><a href="{{ backend_url('claims/not-approved-list') }}"> Not Approved Claims
                                            List</a></li>
                                    <li><a href="{{ backend_url('claims/re-submitted-list') }}"> Re-Submitted Claims
                                            List </a></li>
                                </ul>
                            </li>
                        @endif
                    @endif

                    @if (can_user_access_route("return-portal") == true )
                        @if(can_access_route(['return-portal'],$microHubPermissoins))
                            <li>
                                <a><i class="fa fa-exchange"></i><label>Return Portal<span class="badge">{{$approved_order_list}}</span></label><i
                                            class="icofont-caret-up"></i><span
                                            class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{backend_url('reattempt/order')}}">Reattempt Order List</a>
                                    </li>
                                    <li><a href="{{backend_url('customer/support/approved')}}">Customer
                                            Approved<span class="badge">{{$approved_order_list}}</span></a>
                                    </li>
                                    <li><a href="{{backend_url('reattempt/history')}}">History</a></li>
                                </ul>
                            </li>
                        @endif
                    @endif

                    @if (can_user_access_route("customer-support") == true )
                        @if(can_access_route(['customer-support'],$microHubPermissoins))
                            <li>
                                <a><i class="fa fa-list"></i><label>Customer Support</label><i
                                            class="icofont-caret-up"></i><span
                                            class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ backend_url('order/under-review') }}"> Order Confirmation
                                            List</a>
                                    </li>
                                    <li><a href="{{ backend_url('order/history') }}"> History</a></li>
                                    <li><a href="{{ backend_url('return/order') }}"> Return To Merchant</a></li>
                                    <li><a href="{{ backend_url('returned/order') }}"> Returned Order</a></li>
                                </ul>
                            </li>
                        @endif
                    @endif

                    @if (can_user_access_route("finance") == true )
                        @if(can_access_route(['finance'],$microHubPermissoins))
                            <li>
                                <a><i class="fa fa-users"></i><label>Finance</label><i
                                            class="icofont-caret-up"></i><span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ backend_url('subadmins') }}"> Plan List </a></li>
                                    <li><a href="{{ backend_url('subadmin/add') }}"> Create List</a></li>
                                    <li><a href="#"> Payout</a></li>
                                </ul>
                            </li>
                        @endif
                    @endif

                    @if (can_user_access_route("dispatch") == true )
                        @if(can_access_route(['dispatch'],$microHubPermissoins))
                            <li>
                                <a><i class="fa fa-users"></i><label>Dispatch</label><i
                                            class="icofont-caret-up"></i><span class="fa fa-chevron-down"></span></a>
                            </li>
                        @endif
                    @endif
                    @if(can_user_access_route("update-status") == true)
                        @if(can_access_route(['update-status'],$microHubPermissoins))
                            <li>
                                <a><i class="fa fa-calendar"></i><label>Other Actions</label> <i
                                            class="icofont-caret-up"></i><span
                                            class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ backend_url('search/trackingid/multiple')}}"> Search
                                            Order </a></li>
                                    <li><a href="{{ backend_url('update/multiple/trackingid')}}"> Update
                                            Order </a></li>
                                    <li><a href="{{ backend_url('manual/route')}}"> Update Route</a></li>
                                </ul>
                            </li>
                        @endif
                    @endif


                    @if (can_user_access_route("order-label") == true )
                        @if(can_access_route(['order-label'],$microHubPermissoins))
                            <li class="">
                                <a><i class="fa fa-users"></i><label>Order Label</label><i
                                            class="icofont-caret-up"></i><span
                                            class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ backend_url('label-order-index') }}"> Order Label </a></li>
                                </ul>
                            </li>
                        @endif
                    @endif

                    @if (can_user_access_route("return-routing") == true )
                        @if(can_access_route(['return-routing'],$microHubPermissoins))
                            <li>
                                <a><i class="fa fa-calendar"></i><label>Return Routing</label> <i
                                            class="icofont-caret-up"></i><span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ backend_url('returncustom/routing/17/hub')}}"> Return Custom
                                            Routing </a></li>
                                    <li><a href="{{ backend_url('returnroutific/17/job')}}"> Return Jobs </a></li>
                                    <li><a href="{{ backend_url('return/routes')}}"> Return Routes </a></li>
                                </ul>
                            </li>

                        @endif
                    @endif

                    @if (can_user_access_route("remove_unavailable_order") == true )
                        @if(can_access_route(['remove_unavailable_order'],$microHubPermissoins))
                            <li>
                                <a><i class="fa fa-calendar"></i><label>Remove Unavailable Orders</label> <i
                                            class="icofont-caret-up"></i><span class="fa fa-chevron-down"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ backend_url('mark/incomplete')}}"> Remove Unavailable Orders</a></li>
                                </ul>
                            </li>
                        @endif
                    @endif
                    <li>
                        <a><i class="fa fa-user"></i><label>Profile</label> <i class="icofont-caret-up"></i><span
                                    class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">


                            @if($user->userType == 'admin')
                                <li><a href="{{backend_url('microhub/profile')}}"> Profile </a></li>
                            @endif
                            <li><a href="{{backend_url('microhub/document')}}"> Documents </a></li>
                            <li><a href="{{backend_url('microhub/training')}}"> Training </a></li>
                                <li><a href="{{backend_url('microhub/financial-summary')}}"> Financial Summary </a></li>
                                <li><a href="{{backend_url('microhub/financial-information')}}"> Financial Information </a></li>
                        </ul>
                    </li>

                    <li>
                        <a><i class="fa fa-cogs"></i><label>Setting</label> <i class="icofont-caret-up"></i><span
                                    class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{backend_url('microhub/setting')}}"> Setting </a></li>
                            <li><a href="{{backend_url('microhub/temporarypassword')}}"> Temporary Password </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>

        </div>
        <!-- /sidebar menu -->

    </div>
</div>

<!-- top navigation -->
<div class="top_nav">

</div>
<!-- /top navigation -->

