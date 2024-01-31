@extends( 'backend.layouts.app' )
@section('title', 'Result')
<style>
    .left_col {
        display: none;
    }
    div#main_content_area {
        margin-left: 0px !important;
        border-left: 0px solid #ebe5e2 !important;
    }
</style>
@section('content')
    <!-- Content Area - [Start] -->
    <div id="main_content_area" class="page-training-result">
        <div class="section-training section-padding bg-white">
            <div class="container md">
                <div class="hgroup align-center">
                    <h2>{{$categoryname->name}}</h2>
                </div>

                <div class="steps_list_horizontal training_steps">
                    <ul class="no-list">
                        <li class="row_success col-4 col-md-4">
                            <div class="status"><i class="icofont-check"></i></div>
                            <div class="step_info">
                                <span class="title f15 semibold">Tutorials</span>
                            </div>
                        </li>
                        <li class="row_success col-4 col-md-4">
                            <div class="status"><i class="icofont-check"></i></div>
                            <div class="step_info">
                                <span class="title f15 semibold">Quiz Test</span>
                            </div>
                        </li>
                        <li class="row_active col-4 col-md-4">
                            <div class="status"><i class="icofont-check"></i></div>
                            <div class="step_info">
                                <span class="title f15 semibold">Results</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="quiz_result">
                    <div class="result_icon">
                        <img src="{{asset('images/success_icon.png')}}" height="100px"/>
                    </div>
                    <div class="hgroup seperator_center align-center mb-15">
                        <h2>Well done!</h2>
                        <p>Your next training is enabled now, please feel free to watch next</p>
                    </div>

                    <div class="hgroup seperator_center align-center">
                        <h4 class="bf-color mb-0">Your score</h4>
                        {{-- <p>Atleast {{intval(round($joeyAttemptQuiz->category->score))}} correct answere is required to pass this test</p> --}}
                    </div>

                    <div class="result_progress">
                        <div class="result_progress_bar">
                            @if($percentage > 0)
                            <div class="success_status_bar" style="width: <?php echo $percentage ?>%">{{$percentage}}%</div>
                            @endif
                            @if($percentage != 100)
                            <div class="failed_status_bar" style="width: <?php echo 100-$percentage ?>%">{{100-$percentage}}%</div>
                                @endif
                        </div>
                    </div>
                </div>

                <div class="result_numbers_wrap">
                    <div class="row align-items-center mb-align-center">
                        <div class="col-12 col-md-5 col-sm-5">
                            <p class="mb-5">You have successfully given</p>
                            <button type="button" class="btn btn-white btn-xs" data-toggle="modal" data-target=".check-answer-modal">Check answers</button>
                        </div>
                        <div class="col-12 col-md-7 col-sm-7">
                            <ul class="no-list result_numbers pull-right">
                                <li><span class="numbers success-color1">{{$correct_score}}</span> <span class="success-color1">answers</span></li>
                                <li><span class="numbers regular bc1-light f16">out of </span></li>
                                <li><span class="numbers">{{$total_count}}</span> <span>questions</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="btn-group align-center">
                    {{-- @if($header_menu) --}}
                        <a href="{{url('microhub/newtraining')}}" class="btn btn-primary btn-sm next-quiz-btn">Back to Home</a>
                    {{-- @else --}}
                        <a href="{{url('microhub/training-quiz/'.base64_encode($category_id))}}" class="btn btn-primary btn-sm next-quiz-btn">Back To Training</a>
                    {{-- @endif --}}
                </div>

                <div class="modal fade check-answer-modal custom-modal-style" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <button type="button" class="modal-close-btn" data-dismiss="modal"><i class="icofont-close-line"></i></button>
                            <div class="hgroup seperator_left">
                                <h2>Answers</h2>
                                <h4 class="bf-color regular lh-12">JoeyCo Introduction</h4>
                            </div>

                            <div class="quiz_questions_answers">
                                <ul class="no-list">
                                    @foreach($joeyAttemptQuiz->attemptDetail as $detail)
                                    <li class="flexbox flex-center jc-space-between">
                                        <div class="question_cnt">
                                            <h5>{{$detail->question->question}}</h5>
{{--                                            <p>{{if($detail->answer->answer){$detail->answer->answer;}}}</p>--}}
                                        </div>
                                        @if($detail->is_correct == 1)
                                            <div class="iscorrect_answer"><i class="icofont-check"></i></div>
                                            @else
                                            <div class="iscorrect_answer"><i class="icofont-close-line"></i></div>
                                        @endif

                                    </li>
                                    @endforeach

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
