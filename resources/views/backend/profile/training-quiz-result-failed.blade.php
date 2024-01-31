@extends( 'backend.layouts.app' )
@section('title', 'Result')
@section('content')
    <!-- Content Area - [Start] -->
    <div id="main_content_area" class="page-training-result">
        <div class="section-training section-padding bg-white">
            <div class="container md">
                <div class="hgroup align-center">
                    <h2>{{$categoryname->name}}</h2>
                    <a href="trainings.php" class="back_button btn btn-bc1lightest no-shadow btn-xs"><i class="icofont-rounded-left"></i> Back</a>
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
                            <div class="status"><i class="icofont-exclamation-tringle"></i></div>
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
                        <img src="{{asset('assets/front/assets/images/failed_joey.png')}}"/>
                    </div>
                    <div class="hgroup seperator_center align-center mb-15">
                        <h2 class="color-bright-red">Oh... It’s wrong</h2>
                        <p>Please go back to videos / documents and get more training.	</p>
                    </div>

                    <div class="hgroup seperator_center align-center">
                        <h4 class="bf-color mb-0">Your score</h4>
                        <p>Atleast {{intval(round($joeyAttemptQuiz->category->score))}} correct answer is required to pass this test.</p>
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
                <div class="mt-20">
                    <div class="row align-items-center">
                        <div class="col-12 col-sm-6 mb-align-center mb-10">
                            <a href="{{url('microhub/newtraining')}}" class="none bold"><i class="icofont-rounded-left"></i> Back To Training</a>
                        </div>
                        <div class="col-12 col-sm-6 mb-align-center align-right">
                            <a href="{{url('microhub/training-quiz/'.base64_encode($category_id))}}" class="btn btn-sm btn-primary next-quiz-btn">Retry Test</a>
                        </div>
                    </div>
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
{{--                                            <p>{{$detail->answer->answer}}</p>--}}
                                        </div>
                                        {{--<div class="iscorrect_answer"><i class="icofont-check"></i></div>--}}
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
