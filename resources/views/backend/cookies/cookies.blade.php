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

<style>
    .col-md-3.left_col {
        display: none !important;
    }
    .checkbox {
        display: flex;
        align-items: center;
        font-size: 2rem;
        cursor: pointer;
        width: 30rem;
        font-size: 15px;
    }
    .checkbox a {
        text-decoration: underline;
    }
    .checkbox input {
        display: none;
    }
    .checkbox input:checked ~ .checkbox__mark svg {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    .checkbox__mark {
        margin-right: 0.5rem;
        position: relative;
        width: 2rem;
        height: 2rem;
        border: 0.2rem solid #000;
        float: left;
    }
    .checkbox__mark svg {
        position: absolute;

        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.5);
        width: 80%;
        height: auto;
        transition: all ease 0.2s;
        opacity: 0;
    }
    .checkbox--disabled {
        color: grey;
        pointer-events: none;
    }
    .checkbox--disabled .checkbox__mark {
        border-color: grey;
    }
    .checkbox--disabled .checkbox__mark {
        border-color: grey;
    }
    .left_col {
        display: none !important;
    }
    .page-content-wrapper.cutomMainbox_us {
        margin-left: 0 !important;
    }
    .container.xs {
        max-width: 592px;
    }
    body .hgroup {
        margin-bottom: 30px;
        position: relative;
    }
    body .h1, body .h2, body .h3, body .h4, body .h5, body .h6, body h1, body h2, body h3, body h4, body h5, body h6 {
        font-family: "Poppins", sans-serif !important;
        font-weight: 600;
        color: #e46d29 !important;
        margin-top: 0;
        margin-bottom: 14px;
        position: relative;
        line-height: 1.5em;
    }
    body .hgroup[class*="seperator_"]:after {
        content: "";
        display: block;
        height: 1px;
        background: #eae6e4;
        max-width: 40px;
        left: 0;
        right: 0;
        margin: 0 auto;
    }

    .trainings_list {
        width: 70%;
        margin: 0 auto;
    }
    aside#right_content {
        padding-top: 70px;
    }
    .row {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    .page-trainings .trainings_list .training_box {
        border: solid 1px #eae6e4;
        margin-bottom: 30px;
        overflow: hidden;
        background: #fff;
        -webkit-border-radius: 10px;
        -khtml-border-radius: 10px;
        -moz-border-radius: 10px;
        -ms-border-radius: 10px;
        -o-border-radius: 10px;
        border-radius: 10px;
        -webkit-box-shadow: 0 1px 4px #eae6e4;
        -khtml-box-shadow: 0 1px 4px #eae6e4;
        -moz-box-shadow: 0 1px 4px #eae6e4;
        -ms-box-shadow: 0 1px 4px #eae6e4;
        -o-box-shadow: 0 1px 4px #eae6e4;
        box-shadow: 0 1px 4px #eae6e4;
    }
    .page-trainings .trainings_list .training_box .row1 {
        padding: 20px;
    } */
    .page-trainings .trainings_list .training_box .info_wrap {
        position: relative;
        padding-right: 100px;
        display: -webkit-box;
        display: -moz-box;
        display: box;
        display: -webkit-flex;
        display: -moz-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -moz-box-align: center;
        box-align: center;
        -webkit-align-items: center;
        -moz-align-items: center;
        -ms-align-items: center;
        -o-align-items: center;
        align-items: center;
        -ms-flex-align: center;
    }
    .page-trainings .trainings_list .training_box .info_wrap .index span {
        display: block;
        background: #f6f2ef;
        color: #e46d29;
        font-weight: 600;
        -webkit-border-radius: 100%;
        -khtml-border-radius: 100%;
        -moz-border-radius: 100%;
        -ms-border-radius: 100%;
        -o-border-radius: 100%;
        border-radius: 100%;
        width: 53px;
        height: 53px;
    }
    .page-trainings .trainings_list .training_box .info_wrap .info {
        padding-left: 15px;
    }
    body .h1, body .h2, body .h3, body .h4, body .h5, body .h6, body h1, body h2, body h3, body h4, body h5, body h6 {
        font-family: "Poppins", sans-serif !important;
        font-weight: 600;
        color: #e46d29 !important;
        margin-top: 0;
        margin-bottom: 14px;
        position: relative;
        line-height: 1.5em;
    }
    .bc1-light {
        color: #b4a49b !important;
    }
    .page-trainings .trainings_list .training_box .info_wrap .status_training {
        position: absolute;
        top: 8px;
        right: 0;
    }
    .page-trainings .trainings_list .training_box .info_wrap .status_training .btn {
        display: -webkit-box;
        display: -moz-box;
        display: box;
        display: -webkit-flex;
        display: -moz-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -moz-box-align: center;
        box-align: center;
        -webkit-align-items: center;
        -moz-align-items: center;
        -ms-align-items: center;
        -o-align-items: center;
        align-items: center;
        -ms-flex-align: center;
    }
    [class^="icofont-"], [class*=" icofont-"] {
        font-family: 'IcoFont' !important;
        speak: none;
        font-style: normal;
        font-weight: normal;
        font-variant: normal;
        text-transform: none;
        white-space: nowrap;
        word-wrap: normal;
        direction: ltr;
        line-height: 1;
        -webkit-font-feature-settings: "liga";
        -webkit-font-smoothing: antialiased;
    }
    .flexbox.flex-center {
        -webkit-box-align: center;
        -moz-box-align: center;
        box-align: center;
        -webkit-align-items: center;
        -moz-align-items: center;
        -ms-align-items: center;
        -o-align-items: center;
        align-items: center;
        -ms-flex-align: center;
    }
    .page-trainings .trainings_list .training_box .row_actions li {
        flex: 0 0 50%;
        -webkit-flex: 0 0 50%;
        -ms-flex: 0 0 50%;
    }
    .flexbox.flex-center.jc-center.f18 {
        display: flex;
        background: #f6f2ef;
        color: #e46d29;
        font-weight: 600;
        -webkit-border-radius: 100%;
        -khtml-border-radius: 100%;
        -moz-border-radius: 100%;
        -ms-border-radius: 100%;
        -o-border-radius: 100%;
        border-radius: 100%;
        width: 53px;
        height: 53px;
        display: flex;
        justify-content: center;
    }
    body .hgroup h2 {
        font-size: 32px;
        text-align: center;
    }
    body .hgroup p {
        font-size: 16px;
        text-align: center;
    }
    .status_training {
        background: #f6f2ef;
        color: #e46d29;
        border: solid 1px #f6f2ef;
        -webkit-box-shadow: 0 2px 8px #e7ddd5;
        -khtml-box-shadow: 0 2px 8px #e7ddd5;
        -moz-box-shadow: 0 2px 8px #e7ddd5;
        -ms-box-shadow: 0 2px 8px #e7ddd5;
        -o-box-shadow: 0 2px 8px #e7ddd5;
        box-shadow: 0 2px 8px #e7ddd5;
    }
    .info h4.h5 {
        font-size: 17px;
        margin: 0;
    }
    .info {
        padding-left: 15px;
    }
    .status_training {
        position: absolute;
        top: 8px;
        right: 15px;
    }
    .page-content-wrapper.cutomMainbox_us {
        background: #fff;
    }
    .btn [class*="sprite-25-icon-"] {
        margin-right: 8px;
    }

    .sprite-25-icon-video {
        background-position: 0 -107px;
    }
    [class*="sprite-25-icon-"] {
        width: 25px;
        height: 25px;
        display: inline-block;
        vertical-align: middle;
    }
    [class*="sprite-"] {
        background: url(../images/sprite.png) no-repeat;
        background-size: 1050px auto;
        display: block;
    }
    .d-sm-block {
        display: block !important;
    }
    .training_box {
        border: solid 1px #eae6e4;
        margin-bottom: 30px;
        overflow: hidden;
        background: #f6f2ef;
        box-shadow: 0 1px 4px #eae6e4;
        border-radius: 10px;
    }
    ul.no-list.flexbox.flex-center {
        display: grid;
        grid-gap: 0;
        grid-template-columns: 50% 50%;
        padding: 0;
        margin: 0;
    }
    ul.no-list.flexbox.flex-center li:first-child a.btn {
        padding-left: 40px;
        border-right: 1px solid #e66c292e;
    }
    .info_wrap {
        display: flex;
        align-items: center;
        align-content: center;
        position: relative;
        padding-right: 100px;
        display: -webkit-box;
        display: -moz-box;
        display: box;
        display: -webkit-flex;
        display: -moz-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -moz-box-align: center;
        box-align: center;
        -webkit-align-items: center;
        -moz-align-items: center;
        -ms-align-items: center;
        -o-align-items: center;
        align-items: center;
        background: #fff;
        padding: 10px 0 5px 20px;
        justify-content: flex-start;
    }
    .btn {
        border-radius: 3px !important;
        font-size: 16px !important;
        margin: 0 !important;
        font-weight: 600 !important;
    }
    li {
        display: block;
    }
    .btn [class*="sprite-25-icon-"] {
        margin-right: 8px;
    }
    .sprite-25-icon-video {
        background-position: 0 -107px;
    }
    [class*="sprite-25-icon-"] {
        width: 25px;
        height: 25px;
        display: inline-block;
        vertical-align: middle;
    }
    [class*="sprite-"] {
        background: url(../images/sprite.png) no-repeat;
        background-size: 1050px auto;
        display: block;
    }
    a.cookies {
        width: 200px;
        display: table;
        margin: 0 auto;
        background: #e46d29;
        color: #fff;
        height: 44px;
        line-height: 39px;
        text-align: center;
        border-radius: 8px;
        margin-top: 50px;
    }
    ul.no-list.flexbox.flex-center li .btn {
        border-radius: 3px;
        display: flex;
        align-items: center;
        padding: 10px;
    }
    ul.no-list.flexbox.flex-center li .btn {
        border-radius: 3px;
        display: flex;
        align-items: center;
        font-size: 16px;
        font-weight: 600;
        color: #f2752e;
    }
    span.sprite-25-icon-video.d-none.d-sm-block {
        background-position: 0 -107px;
    }
    span.sprite-25-icon-book.d-none.d-sm-block {
        background-position: -25px -107px;
    }
    .btn.btn-bc1lightest:not(.no-hover):hover {
        background: #f0eae5;
    }

    .checkbox {
        display: flex;
        align-items: center;
        font-size: 2rem;
        cursor: pointer;
        width: 30rem;
        font-size: 15px;
    }
    .checkbox a {
        text-decoration: underline;
    }
    .checkbox input {
        display: none;
    }
    .checkbox input:checked ~ .checkbox__mark svg {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    .checkbox__mark {
        margin-right: 0.5rem;
        position: relative;
        width: 2rem;
        height: 2rem;
        border: 0.2rem solid #000;
        float: left;
    }
    .checkbox__mark svg {
        position: absolute;

        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.5);
        width: 80%;
        height: auto;
        transition: all ease 0.2s;
        opacity: 0;
    }
    .checkbox--disabled {
        color: grey;
        pointer-events: none;
    }
    .checkbox--disabled .checkbox__mark {
        border-color: grey;
    }
    .checkbox--disabled .checkbox__mark {
        border-color: grey;
    }
</style>
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


    @foreach($agreement as $data)
{!! $data->copy !!}
    @endforeach

    <!-- /#page-wrapper -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>


    {{-- My Ajax call -- By Daniyal Khan --}}
    <script>
<?php
    foreach ($agreement as $data){
        $id = $data->id;
    }
?>
var id = <?php echo json_encode($id);?>
        // Main Ajax Call For Setting Permission...
        $(document).ready(function(){

            $(document).on("click","#checkbox-3",function() {

                $(document).on("click",".cookies",function() {
                    AcceptAgreementRequestSent();
                });
            });

            //Main Function to get the permissions...
            function AcceptAgreementRequestSent(){

                //Submitting Data in Micro_hub_Permissions Table...
                $.ajax({
                    url: '../microhub/PostToAgreement',
                    type:'POST',
                    data:{
                        "agreement_id": id,
                        "user_type":"microhub",
                    },
                    success: function(response){
                        window.location.reload();
                    },
                    error: function (error) {
                        console.log(error);
                    }
                })//Submitting Data in Micro_hub_Permissions Table End here...

            }


        });

    </script>
@endsection
