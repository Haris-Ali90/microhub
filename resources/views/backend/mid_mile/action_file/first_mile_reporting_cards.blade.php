<div class="row" id="ctcCards">
    <!--top_tiles Div Open-->
    <div class="top_tiles montreal-dashbord-tiles" id="ctc-dashbord-tiles-id">

        <!--Animated-a Div Open-->
        <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 ">
            <div class="tile-stats">
                <div class="icon">
                    <i class="fa fa-cubes"></i>
                </div>
                    <div class="count">{{ $ctc_count['total'] }}</div>
                    <h3>Total Orders</h3>
            </div>
        </div>
        <!--Animated-a Div Close-->

        <!--Animated-b Div Open-->
        <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 ">
            <div class="tile-stats">
                <div class="icon">
                    <i class="fa fa-truck"></i>
                </div>
                    <div class="count">{{$ctc_count['picked-up'] }}</div>
                    <h3> Picked Up From Store</h3>
            </div>
        </div>
        <!--Animated-b Div Close-->
        <!--Animated-b Div Open-->
        <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 ">
            <div class="tile-stats">
                <div class="icon">
                    <i class="fa fa-truck"></i>
                </div>
                <div class="count">{{$ctc_count['at-hub'] }}</div>
                <h3>At Hub</h3>
            </div>
        </div>
        <!--Animated-b Div Open-->

        <!--Animated-b Div Open-->
        <div class="animated flipInY col-lg-3 col-md-6 col-sm-12 ">
            <div class="tile-stats">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                    <div class="count">{{$ctc_count['at-store'] }}</div>
                    <h3> At Store Orders</h3>
            </div>
        </div>
    </div>
    <!--top_tiles Div Close-->

</div>