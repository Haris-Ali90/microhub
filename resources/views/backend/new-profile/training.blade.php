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
    }

    *

    /
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
    <div class="page-content-wrapper cutomMainbox_us" style="min-height:1100px !important">
        <section class="progress_sec" style="padding-top: 50px">
            <div class="container">
                <div role="progressbar" aria-valuenow="67" aria-valuemin="0" aria-valuemax="100"
                     style="--value: 100"></div>
            </div>
        </section>
        <aside id="right_content" class="col-12 col-lg-12">
            <div class="inner">
                <div class="container xs">

                    <div class="hgroup align-center seperator_center">
                        <h2>Trainings</h2>
                        <p>Please get trained in the following categories and pass the quiz to start taking your first
                            order.</p>
                    </div>
                </div>
                <div class="container">
                    <div class="trainings_list">
                        <div class="row">
                            <?php $i = 1;
                            ?>
                            @foreach($categories as $category)
                                <?php $disable_class = '';
                                $disable_button = '';?>
                                @if($category->type == 'special')
                                    <?php
                                    $disable_class = 'disabled_box';
                                    $disable_button = 'inactiveLink';
                                    $sprint_count = \App\SprintSprint::where('joey_id', auth()->user()->id)->get();
                                    if (count($sprint_count) >= $category->order_count) {
                                        $disable_class = '';
                                        $disable_button = '';
                                    }?>
                                @endif
                                <?php
                                $show_quiz = false;
                                $training_compulsory = \App\Trainings::where('user_type', 'micro_hub')->where('order_category_id', $category->id)->where('is_compulsory', 1)->pluck('id');
                                $joey_training_seen = \App\JCTrainingSeen::whereIn('training_id', $training_compulsory)->where('jc_users_id', auth()->user()->id)->get()->count();
                                if (count($training_compulsory) >= 1 && count($training_compulsory) == $joey_training_seen) {
                                    $show_quiz = true;
                                }
                                ?>
                                <div class="col-md-6">
                                    <div class="training_box {{$disable_class}}">
                                        <div class="row1">
                                            <div class="info_wrap">
                                                <div class="index"><span
                                                            class="flexbox flex-center jc-center f18">{{$i}}</span>
                                                </div>
                                                <div class="info">
                                                    <h4 class="h5">{{$category->name}}</h4>
                                                    <div class="tutorials_count bc1-light">{{$category->trainings->count()}}
                                                        Tutorials
                                                    </div>
                                                </div>
                                                <div class="status_training">
                                                    <?php $result = \App\JCAttemptedQuiz::where('category_id', $category->id)->where('jc_users_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();?>
                                                    @if($result)
                                                        @if($result->is_passed == 1)
                                                            <div class="status_passed btn btn-bc1lightest no-shadow btn-sm cursor-default no-hover">
                                                                <i class="icofont-check icon-scale-16"></i> Passed
                                                            </div>
                                                        @else
                                                            @if($show_quiz)
                                                                <a href="{{url('microhub/training-quiz/'.base64_encode($category->id))}}"
                                                                   class="status_passed btn btn-basecolor1 no-shadow btn-sm ">Start
                                                                    Quiz</a>
                                                            @endif
                                                        @endif
                                                    @else
                                                        @if($show_quiz)
                                                            <a href="{{url('microhub/training-quiz/'.base64_encode($category->id))}}"
                                                               class="status_passed btn btn-basecolor1 no-shadow btn-sm ">Start
                                                                Quiz</a>
                                                        @endif
                                                    @endif


                                                </div>
                                            </div>
                                        </div>
                                        <div class="row_actions">
                                            <ul class="no-list flexbox flex-center">
                                                <li><a href="{{url('microhub/training/'.base64_encode($category->id))}}"
                                                       class="btn btn-bc1lightest no-radius full-w no-shadow {{$disable_button}}"><span
                                                                class="sprite-25-icon-video d-none d-sm-block"></span>Watch
                                                        Videos</a></li>
                                                <li>
                                                    <a href="{{url('microhub/training-documents/'.base64_encode($category->id))}}"
                                                       class="btn btn-bc1lightest no-radius full-w no-shadow {{$disable_button}}"><span
                                                                class="sprite-25-icon-book d-none d-sm-block"></span>Read
                                                        Documents</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <?php $i = $i + 1; ?>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </div>
    <!-- /#page-wrapper -->

@endsection