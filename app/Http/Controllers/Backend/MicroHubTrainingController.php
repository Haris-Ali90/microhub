<?php
namespace App\Http\Controllers\Backend;

use App\Quiz;
use App\Zones;
use App\JCUser;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Trainings;
use App\JCAttemptedQuizDetail;
use App\OrderCategory;
use App\ZoneSchedule;
use App\PreferWorkTime;
use App\PreferWorkType;
use App\MicroHubRequest;
use App\JCTrainingSeen;
use App\JCAttemptedQuiz;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\TrainingRepositoryInterface;


class MicroHubTrainingController extends BackendController
{
    
    
    public function training()
    {
        $categories = OrderCategory::where('user_type','micro_hub')->get();
        return backend_view('profile.training',compact('categories'));
    }
      /**
     * Training Documents view
     *
     */
    public function getTrainingDocuments($category)
    {
        $category_id = base64_decode($category);
        $show_quiz = false;
        $training_documents = Trainings::where('order_category_id',$category_id)->where('user_type','micro_hub')
            ->whereIn('type',['application/pdf','document','image/png','image/jpeg'])->get();

        $training_compulsory = Trainings::where('order_category_id',$category_id)->where('is_compulsory',1)->pluck('id');

        $joey_training_seen = JCTrainingSeen::whereIn('training_id',$training_compulsory)->where('jc_users_id',auth()->user()->id)->get()->count();
        if(count($training_compulsory) >= 1 && count($training_compulsory) == $joey_training_seen)
        {
            $show_quiz = true;
        }
        $attempt = JCAttemptedQuiz::where('category_id', $category_id)->where('jc_users_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();

        if($attempt){
            if($attempt->is_passed == 1){
                $show_quiz =false;
            }
        }
        return backend_view('profile.training-documents',compact('training_documents','show_quiz','category_id'));
    }

    /**
     * Training videos view
     *
     */
    public function getTrainingVideos($category)
    {

        $category_id = base64_decode($category);
        $show_quiz = false;
        $training_videos = Trainings::where('order_category_id',$category_id)->where('type','video/mp4')->get();

        $training_compulsory = Trainings::where('order_category_id',$category_id)->where('is_compulsory',1)->pluck('id');

        $joey_training_seen = JCTrainingSeen::whereIn('training_id',$training_compulsory)->where('jc_users_id',auth()->user()->id)->get()->count();
        if(count($training_compulsory) >= 1 && count($training_compulsory) == $joey_training_seen)
        {
            $show_quiz = true;
        }
        $attempt = JCAttemptedQuiz::where('category_id', $category_id)->where('jc_users_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();
        if($attempt){
            if($attempt->is_passed == 1){
                $show_quiz =false;
            }
        }


        return backend_view('profile.training-videos',compact('training_videos','show_quiz','category_id'));
    }



    /**
     * Joey Training View
     *
     */
    public function trainingView($id)
    {
        JCTrainingSeen::create(['jc_users_id'=> auth()->user()->id, 'training_id'=>$id]);
        return $id;
    }

    /**
     * Training Quiz view
     *
     */
    public function getTrainingQuiz($category)
    {
        $category_fetch = $category;
        $category_id = base64_decode($category);
        $categoryname = OrderCategory::where('user_type','micro_hub')->where('id',$category_id)->first();
        $training_quiz = Quiz::where('user_type','micro_hub')->where('order_category_id',$category_id)->get();

        return backend_view('profile.training-quiz',compact('training_quiz','category_id','categoryname','category_fetch'));
    }


    /**
     * Training Quiz Submit
     *
     */
    public function getTrainingQuizSubmit(Request $request)
    {

        $training_quiz = Quiz::where('user_type','micro_hub')->where('order_category_id',$request->get('category_id'))->get();
        $correct_score = 0;
        foreach ($training_quiz as $quiz) {
            if ($quiz->correct_answer_id == $request->get('question' . $quiz->id)) {
                $correct_score = $correct_score + 1;
            }
        }
        $category = OrderCategory::where('user_type','micro_hub')->where('id', $request->get('category_id'))->first();

        $result = 0;
        if (count($training_quiz) != 0 and  intval(round(($correct_score/count($training_quiz))*100)) >= intval(round(($category->score/count($training_quiz))*100)) )
        {
                     $result = 1;
        }

        $joey_attempt_quiz = new JCAttemptedQuiz();
        $joey_attempt_quiz->category_id = $request->get('category_id');
        $joey_attempt_quiz->jc_users_id = auth()->user()->id;
        $joey_attempt_quiz->is_passed = $result;
        $joey_attempt_quiz->save();



        foreach ($training_quiz as $quiz) {
            $correct = 0;
            if ($quiz->correct_answer_id == $request->get('question' . $quiz->id)) {
                $correct= 1;
            }

            $joey_attempt_quiz_detail = new JCAttemptedQuizDetail();
            $joey_attempt_quiz_detail->jc_users_id = auth()->user()->id;
            $joey_attempt_quiz_detail->question_id = $quiz->id;
            $joey_attempt_quiz_detail->quiz_id = $joey_attempt_quiz->id;
            $joey_attempt_quiz_detail->answer_id = $request->get('question' . $quiz->id);
            $joey_attempt_quiz_detail->is_correct = $correct;
            $joey_attempt_quiz_detail->save();
        }

        if ($result == 1){

            return redirect('microhub/training-quiz-result/'.base64_encode($request->get('category_id')));

        }
        else
        {
            
            return redirect('microhub/training-quiz-result-failed/'.base64_encode($request->get('category_id')));
        }
    }


    /**
     * Training Quiz view
     *
     */
    public function getTrainingQuizResult($category)
    {
        $category_id = base64_decode($category);

        $categoryname = OrderCategory::where('user_type','micro_hub')->where('id',$category_id)->first();
        $joeyAttemptQuiz =JCAttemptedQuiz::where('category_id',$category_id)->where('jc_users_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();
        $correct_score = 0;
        $total_count = count($joeyAttemptQuiz->attemptDetail);
        foreach ($joeyAttemptQuiz->attemptDetail as $detail)
        {
            if ($detail->is_correct == 1) {
                $correct_score = $correct_score + 1;
            }
        }

        $percentage = intval(round(($correct_score/$total_count)*100));
        return backend_view('profile.training-quiz-result',compact('percentage','correct_score','total_count','joeyAttemptQuiz','categoryname','category_id'));
    }


    /**
     * Training Quiz view
     *
     */
    public function getTrainingQuizResultFailed($category)
    {
        $category_id = base64_decode($category);
        $categoryname = OrderCategory::where('user_type','micro_hub')->where('id',$category_id)->first();
        $joeyAttemptQuiz =JCAttemptedQuiz::where('category_id',$category_id)->where('jc_users_id', auth()->user()->id)->orderBy('created_at', 'desc')->first();
        $correct_score = 0;
        $total_count = count($joeyAttemptQuiz->attemptDetail);
        foreach ($joeyAttemptQuiz->attemptDetail as $detail)
        {
            if ($detail->is_correct == 1) {
                $correct_score = $correct_score + 1;
            }
        }
        $percentage = intval(round(($correct_score/$total_count)*100));
        return backend_view('profile.training-quiz-result-failed',compact('percentage','correct_score','total_count','joeyAttemptQuiz','category_id','categoryname'));
    }


}
