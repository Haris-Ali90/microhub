
{{--@extends( 'backend.layouts.app' )--}}

@section('title', 'Training Quiz')

@section('JSLibraries')
    <script src="{{ backend_asset('js/quiz.js')}}"></script>
   <style>
       .left_col {
           display: none;
       }
       div#main_content_area {
           margin-left: 0px;
           border-left: 0px solid #ebe5e2;
       }
   </style>


@endsection
@section('content')
    <!-- Content Area - [Start] -->
    <div id="main_content_area" class="page-training-quiz">
        <div class="section-training section-padding bg-white">
            <div class="container md">
                <div class="hgroup align-center">
                    <h2>{{$categoryname->name}}</h2>
                    <a href="{{backend_url('microhub/training/')}}" class="back_button btn btn-bc1lightest no-shadow btn-xs"><i class="icofont-rounded-left"></i> Back</a>
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

                <div class="quiz_progress">
                    <div class="progress_bar">
                        <div class="progress_status_bar" style="width: 0%;"></div>
                    </div>

                    <div class="question_numbers"><span>0/{{count($training_quiz)}}</span></div>
                </div>

                <form method="POST" action="{{backend_url('microhub/training-quiz-submit/'.$category_fetch)}}" id="step1-form" novalidate class="needs-validation">

                    <input type="hidden" name="category_id" value="{{$category_id}}" >
                    <div class="quiz_question_list">
                        <?php $question_no= 1; ?>

                        @foreach($training_quiz as $quiz)

                            <?php $answer_no= 1;?>
                            <div class="quiz_question">
                                <h4 class="align-center">{{$quiz->question}}</h4>
                                @if($quiz->image)

                                    <div class="question_img align-center">
                                        <img src="{{$quiz->image}}" alt="">
                                    </div>
                                @endif
                                <div novalidate>
                                    <div class="form-group">
                                        <div class="custom-control-inline-wrap row no-gutter">

                                            @foreach($quiz->quizAnswers as $answer)
                                                <div class="text-center custom-radio style2 form-radio custom-control-inline full-w">
                                                    <input class="form-radio-input" type="radio" name="question{{$quiz->id}}" id="q{{$question_no}}answer{{$answer_no}}" value="{{$answer->id}}">
                                                    <label class="form-radio-label full-w" for="q{{$question_no}}answer{{$answer_no}}">
                                                        <span class="dp-block value">{{$answer->answer}}</span>
                                                        @if($answer->image)
                                                            <img src="{{$answer->image}}" alt="" class="mt-5">
                                                        @endif
                                                    </label>
                                                </div>
                                                <?php $answer_no= $answer_no + 1; ?>
                                            @endforeach
                                        </div>
                                    </div>
                                        <div class="btn-group align-center">
                                            <button type="submit" class="btn btn-primary btn-lg next-quiz-btn">Next</button>
                                        </div>
                                </div>
                            </div>
                            <?php $question_no = $question_no + 1;?>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
