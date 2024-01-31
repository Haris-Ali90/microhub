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


class MicroHubFinancialController extends BackendController
{
    
    
    public function getSummary()
    {
        return backend_view('financial.financial_summary');
    }

    public function getInformation()
    {
        return backend_view('financial.financial_information');
    }


}
