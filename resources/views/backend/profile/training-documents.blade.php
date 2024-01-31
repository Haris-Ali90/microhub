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

.nav-md .container.body .col-md-3.left_col {
    display: none !important;
}
.video_frame {
    position: relative;
    overflow: hidden;
    border: solid 3px #fff;
    -webkit-border-radius: 10px;
    -khtml-border-radius: 10px;
    -moz-border-radius: 10px;
    -ms-border-radius: 10px;
    -o-border-radius: 10px;
    border-radius: 10px;
    -webkit-box-shadow: 0 3px 7px rgb(180 164 155 / 60%);
    -khtml-box-shadow: 0 3px 7px rgba(180, 164, 155, 0.6);
    -moz-box-shadow: 0 3px 7px rgba(180, 164, 155, 0.6);
    -ms-box-shadow: 0 3px 7px rgba(180, 164, 155, 0.6);
    -o-box-shadow: 0 3px 7px rgba(180, 164, 155, 0.6);
    box-shadow: 0 3px 7px rgb(180 164 155 / 60%);
}
.mainBody {
    float: left;
    width: 100%;
    background: #fff;
    padding-top: 100px;
    height: 80vh;
}
.video_frame video {
    width: 100%;
    height: 348px;
    display: block;
}
.training_video {
    float: none;
    width: 660px;
    text-align: center;
    margin: 0 auto;
}
.training_items_list {
    float: left;
    width: 100%;
    text-align: left;
    background: #fff;
}
.training_items_list h4 {
    font-family: "Poppins", sans-serif !important;
    font-weight: 600;
    color: #e46d29 !important;
    margin-top: 0;
    margin-bottom: 14px;
    position: relative;
    line-height: 1.5em;
    text-align: left;
}
.item_row.flexbox.flex-center.jc-space-between.videoSwitchBtnWrap {
    padding: 20px 0;
    border-top: solid 1px #eae6e4;
    align-items: center;
}
.flexbox {
    display: flex !important;
}
.item_thumb {
    position: relative;
    overflow: hidden;
    border: solid 3px #fff;
    background: no-repeat;
    background-size: cover;
    width: 96px;
    height: 68px;
    -webkit-box-shadow: 0 0 0 1px transparent;
    -moz-box-shadow: 0 0 0 1px transparent;
    -ms-box-shadow: 0 0 0 1px transparent;
    -o-box-shadow: 0 0 0 1px transparent;
    box-shadow: 0 0 0 1px transparent;
    -webkit-border-radius: 10px;
    -khtml-border-radius: 10px;
    -moz-border-radius: 10px;
    -ms-border-radius: 10px;
    -o-border-radius: 10px;
    border-radius: 10px;
}
.item_info {
    float: left;
    width: 68%;
    padding-left: 10px;
    height: 50px;
    color: #000;
    font-size: 14px;
}
h4.h5.nomargin {
    color: #b4a49b !important;
    font-size: 17px;
    font-weight: 600;
    margin: 0;
}
.item_actions a.btn.btn-basecolor1.btn-sm.view_document {
    background: #b4a49b;
    width: 100% !important;
    padding: 6px;
    font-size: 18px;
    color: #fff;
}
.item_actions {
    width: 20%;
}
.steps_list_horizontal {
    margin-bottom: 35px;
    width: 660px;
    margin: 0 auto;
}
.hgroup.align-center {
    width: 660px;
    margin: 0 auto;
    margin-bottom: 30px;
}
.hgroup.align-center h2 {
    font-family: "Poppins", sans-serif !important;
    font-weight: 600;
    color: #e46d29 !important;
    margin-top: 0;
    margin-bottom: 14px;
    position: relative;
    line-height: 1.5em;
    font-size: 32px;
}
ul.no-list {
    display: -webkit-box;
    display: -moz-box;
    display: box;
    display: -webkit-flex;
    display: -moz-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: start;
    -moz-box-align: start;
    box-align: start;
    -webkit-align-items: flex-start;
    -moz-align-items: flex-start;
    -ms-align-items: flex-start;
    -o-align-items: flex-start;
    align-items: flex-start;
    -ms-flex-align: start;
    padding: 0;
}
.steps_list_horizontal .status {
    padding: 0;
    position: relative;
}
.steps_list_horizontal li {
    text-align: center;
}
.steps_list_horizontal .status:before {
    content: "";
    display: block;
    background: #f6f2ef;
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 1px;
}
.steps_list_horizontal li.row_success .status .icofont-check {
    color: #fff;
    font-size: 29px;
}
.steps_list li.row_success .status .icofont-check, .steps_list_horizontal li.row_success .status .icofont-check, .page-training-quiz .quiz_progress .progress_status_bar {
    background-color: #bddc06;
    background-image: -webkit-linear-gradient(left, #bddc06, 50%, #81c400);
    background-image: linear-gradient(to right,#bddc06, 50%, #81c400);
}
.icofont-check:before {
    content: "\eed8";
}
i.icofont-check {
    position: relative;
    margin: 0 auto;
    color: #b4a49b;
    z-index: 1;
    display: block;
    background: #f6f2ef;
    text-align: center;
    font-size: 22px;
    width: 32px;
    height: 32px;
    line-height: 32px;
    -webkit-border-radius: 100%;
    -khtml-border-radius: 100%;
    -moz-border-radius: 100%;
    -ms-border-radius: 100%;
    -o-border-radius: 100%;
    border-radius: 100%;
}
ul.no-list li {
    display: block;
}
.steps_list_horizontal li.row_success .step_info .title {
    color: #e46d29;
}
.steps_list_horizontal li.row_active .status .icofont-check:before {
    content: "\ea67";
}
.steps_list_horizontal li.row_active .status .icofont-check {
    background: #443404;
    color: #fff;
    -webkit-transform: scale(0.7);
    -khtml-transform: scale(0.7);
    -moz-transform: scale(0.7);
    -ms-transform: scale(0.7);
    -o-transform: scale(0.7);
    transform: scale(0.7);
}
.steps_list_horizontal li.row_active .step_info .title {
    color: #443404;
}
.steps_list_horizontal .status:before {
    content: "";
    display: block;
    background: #f6f2ef;
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 1px;
}
.steps_list_horizontal li.row_disabled .status .icofont-check {
    -webkit-transform: scale(0.5);
    -khtml-transform: scale(0.5);
    -moz-transform: scale(0.5);
    -ms-transform: scale(0.5);
    -o-transform: scale(0.5);
    transform: scale(0.5);
}

    .steps_list_horizontal .status [class*="icofont-"] {
        position: relative;
        margin: 0 auto;
        color: #b4a49b;
        z-index: 1;
        display: block;
        background: #f6f2ef;
        text-align: center;
        font-size: 22px;
        width: 32px;
        height: 32px;
        line-height: 32px;
        -webkit-border-radius: 100%;
        -khtml-border-radius: 100%;
        -moz-border-radius: 100%;
        -ms-border-radius: 100%;
        -o-border-radius: 100%;
        border-radius: 100%;
    }
    .btn:not(:disabled):not(.disabled) {
        cursor: pointer;
    }


body .hgroup .back_button {
    position: absolute;
    top: 5px;
    left: 0;
}
.btn.btn-xs {
    padding: 0.55rem 16px 0.52rem;
}
.btn.btn-primary, .btn.btn-primary.disabled, .btn.btn-basecolor1, .btn.btn-basecolor1.disabled, .btn.btn-basecolor3, .btn.btn-basecolor3.disabled, .btn.btn-secondary, .btn.btn-secondary.disabled, .btn.btn-bc1light, .btn.btn-bc1light.disabled, .btn.btn-bc1lightest, .btn.btn-bc1lightest.disabled, .btn.btn-grd-1, .btn.btn-grd-1.disabled, .btn.btn-default, .btn.btn-default.disabled, .btn.btn-border, .btn.btn-border.disabled, .btn.btn-white, .btn.btn-white.disabled, .btn.btn-success, .btn.btn-success.disabled {
    padding-top: 1rem;
    padding-bottom: 0.75rem;
}
.btn.btn-xs {
    font-size: 16px;
    font-weight: 600;
    -webkit-border-radius: 6px;
    -khtml-border-radius: 6px;
    -moz-border-radius: 6px;
    -ms-border-radius: 6px;
    -o-border-radius: 6px;
    border-radius: 6px;
}
.btn.btn-bc1lightest, .btn.btn-bc1lightest.disabled {
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
.btn:not(:disabled):not(.disabled) {
    cursor: pointer;
    width: 20%;
}
.hgroup.align-center {
    display: flex;
    flex-direction: row-reverse;
    justify-content: space-between;
    align-items: center;
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
    <div class="mainBody">
        <div class="hgroup align-center">
            <h2>JoeyCo Introduction</h2>
            <a href="{{backend_url('microhub/newtraining/')}}" class="back_button btn btn-bc1lightest no-shadow btn-xs"><i class="icofont-rounded-left"></i> Back</a>

        </div>
        <div class="steps_list_horizontal training_steps">
            <ul class="no-list">
                <li class="row_success col-4 col-md-4">
                    <div class="status"><i class="icofont-check"></i></div>
                    <div class="step_info">
                        <span class="title f15 semibold">Tutorials</span>
                    </div>
                </li>
                <li class="row_active col-4 col-md-4">
                    <div class="status"><i class="icofont-check"></i></div>
                    <div class="step_info">
                        <span class="title f15 semibold">Quiz Test</span>
                    </div>
                </li>
                <li class="row_disabled col-4 col-md-4">
                    <div class="status"><i class="icofont-check"></i></div>
                    <div class="step_info">
                        <span class="title f15 semibold">Results</span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="training_video">
        <div class="training_items_list">
            <h4><?php echo count($training_documents); ?> Tutorials</h4>
    @foreach($training_documents as $training)
                <?php $done = '';
                $result = \App\JCTrainingSeen::where('training_id', $training->id)->where('jc_users_id', auth()->user()->id)->first(); ?>
                @if($result)
                    <?php $done = 'done'; ?>
                @endif
            <div class="item_row {{$done}} flexbox flex-center jc-space-between">
                <div class="item_icon">
                    <span class="sprite-30x39-document">
                </div>
                <div class="item_info">
                    <h4 class="h5 nomargin">{{$training->title}}</h4>
                </div>
                <div class="item_actions ">
                    @if($result)
                    <a href="https://onboarding.joeyco.com/download-file?fileData={{$training->url}}"  target="_blank" class="btn btn-bc1light btn-sm"><i class="icofont-check icon-scale-16"></i> Done</a>
                    @else
                        <a href="https://onboarding.joeyco.com/download-file?fileData={{$training->url}}" data-id="{{$training->id}}" target="_blank"  class="btn btn-basecolor1 btn-sm view_document" ><i class="icofont-eye-alt"></i> Read</a>
                        @endif
                </div>
            </div>
            @endforeach

        </div>
    </div>
    </div>
    @if($show_quiz)
        <div class="quiz_btn_full">
            <div class="container md">
                <a href="{{url('/microhub/training-quiz/'.base64_encode($category_id))}}" class="btn btn-primary btn-lg full-w start_quiz_btn"><span class="light">Ready for quiz?</span> Lets Start <i class="icofont-rounded-right"></i></a>
            </div>
        </div>
        @endif


    </div>
    <!-- /#page-wrapper -->


    <script>
        $(document).ready(function(){
            $('.view_document').click(function(e){
                console.log('test');
                //e.preventDefault();
                var id = $(this).attr("data-id");
                $.ajax({
                    type: "get",
                    url: "<?php echo URL::to('/microhub/training-view'); ?>"+"/"+id,
                    data: {},
                    success: function (data) {
                        //location.reload();
                    },
                    error:function (error) {
                        //console.log(error)
                    }

                });
            });
        });
    </script>

@endsection