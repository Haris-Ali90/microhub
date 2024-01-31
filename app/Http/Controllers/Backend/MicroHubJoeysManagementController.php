<?php

namespace App\Http\Controllers\Backend;

use App\Joey;
use App\MicroHubJoeyAssign;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class MicroHubJoeysManagementController extends BackendController
{

    public function statistics(Request $request)
    {

        // checking the request exsit
        $request_data = $request->all();
        $date = Carbon::now();
        $lastMonth =  $date->subMonth()->format('F');
        $auth_hub_id = isset(auth()->user()->hub_id) ? auth()->user()->hub_id : 0;

        if (!empty($request_data)) {

            if ($request_data['days'] == '3days') {
                $date = Carbon::now()->subDays(3)->format('Y-m-d');
                $currentDate = Carbon::now()->format('Y-m-d');

                $totalSignUps = Joey::whereNull('deleted_at')
                    ->whereBetween(\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), [$date, $currentDate])->count();

                $basicRegistration = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->where('joeys.on_duty', 1)
                    ->whereNull('joeys.deleted_at')
                    ->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$date, $currentDate])->count();

                $percentage = 0;
                if ($totalSignUps > 0 && $basicRegistration > 0) {
                    $percentage = round($basicRegistration / $totalSignUps * 100);
                } else {
                    $percentage = 1;
                }

                /**
                 * application submission calcultion
                 * */
                $totalApplicationSubmissionCount = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$date, $currentDate])
                    ->whereNull('joeys.deleted_at')
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->where('is_active', 1)
                    ->count();

                /**
                 * quiz passed
                 * */
                $totalQuizPassedCount = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->join('microhub_attempted_quiz', 'microhub_attempted_quiz.jc_users_id', '=', 'joeys.id')
                    ->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$date, $currentDate])
                    ->where('joeys.is_active', 1)
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->whereNull('joeys.deleted_at')
                    ->whereNull('microhub_attempted_quiz.deleted_at')
                    ->where('microhub_attempted_quiz.is_passed', 1)
                    ->count();

            }
            elseif ($request_data['days'] == 'lastweek') {


                $startOfCurrentWeek = Carbon::now()->startOfWeek();
                $startOfLastWeek = $startOfCurrentWeek->copy()->subDays(7)->format('Y-m-d');
                $currentDate = Carbon::now()->format('Y-m-d');


                $totalSignUps = Joey::whereNull('deleted_at')
                    ->whereBetween(\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), [$startOfLastWeek, $currentDate])->count();
                $basicRegistration = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->where('joeys.on_duty', 1)
                    ->whereNull('joeys.deleted_at')
                    ->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$startOfLastWeek, $currentDate])->count();


                $percentage = 0;
                if ($totalSignUps > 0 && $basicRegistration > 0) {
                    $percentage = round($basicRegistration / $totalSignUps * 100);
                } else {
                    $percentage = 1;
                }
                /**
                 * application submission calcultion
                 * */
                $totalApplicationSubmissionCount = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$startOfLastWeek, $currentDate])
                    ->whereNull('joeys.deleted_at')
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->where('is_active', 1)
                    ->count();

                /**
                 * quiz passed
                 * */
                $totalQuizPassedCount = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->join('microhub_attempted_quiz', 'microhub_attempted_quiz.jc_users_id', '=', 'joeys.id')
                    ->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$startOfLastWeek, $currentDate])
                    ->where('joeys.is_active', 1)
                    ->whereNull('joeys.deleted_at')
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->whereNull('microhub_attempted_quiz.deleted_at')
                    ->where('microhub_attempted_quiz.is_passed', 1)
                    ->count();

            }

            elseif ($request_data['days'] == '15days') {

                $date = Carbon::now()->subDays(15)->format('Y-m-d');
                $currentDate = Carbon::now()->format('Y-m-d');

                $totalSignUps = Joey::whereNull('deleted_at')
                    ->whereBetween(\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), [$date, $currentDate])->count();

                $basicRegistration = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->where('joeys.on_duty', 1)
                    ->whereNull('joeys.deleted_at')
                    ->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$date, $currentDate])->count();

                $percentage = 0;
                if ($totalSignUps > 0 && $basicRegistration > 0) {
                    $percentage = round($basicRegistration / $totalSignUps * 100);
                } else {
                    $percentage = 1;
                }
                /**
                 * application submission calcultion
                 * */
                $totalApplicationSubmissionCount = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$date, $currentDate])
                    ->whereNull('joeys.deleted_at')
                    ->where('joeys.is_active', 1)
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->count();

                /**
                 * quiz passed
                 * */
                $totalQuizPassedCount = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->join('microhub_attempted_quiz', 'microhub_attempted_quiz.jc_users_id', '=', 'joeys.id')
                    ->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$date, $currentDate])
                    ->where('joeys.is_active', 1)
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->whereNull('joeys.deleted_at')
                    ->whereNull('microhub_attempted_quiz.deleted_at')
                    ->where('microhub_attempted_quiz.is_passed', 1)
                    ->count();

            }

            elseif ($request_data['days'] == 'lastmonth') {

                $firstDayofPreviousMonth = Carbon::now()->startOfMonth()->subMonth()->toDateString();

                $lastDayofPreviousMonth = Carbon::now()->endOfMonth()->subMonth()->toDateString();


                $totalSignUps = Joey::whereNull('deleted_at')
                    ->whereBetween(\DB::raw("CONVERT_TZ(created_at,'UTC','America/Toronto')"), [$firstDayofPreviousMonth, $lastDayofPreviousMonth])->count();

                $basicRegistration = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->where('joeys.on_duty', 1)
                    ->whereNull('joeys.deleted_at')
                    ->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$firstDayofPreviousMonth, $lastDayofPreviousMonth])->count();

                $percentage = 0;
                if ($totalSignUps > 0 && $basicRegistration > 0) {
                    $percentage = round($basicRegistration / $totalSignUps * 100);
                } else {
                    $percentage = 1;
                }

                /**
                 * application submission calcultion
                 * */
                $totalApplicationSubmissionCount = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$firstDayofPreviousMonth, $lastDayofPreviousMonth])
                    ->whereNull('joeys.deleted_at')
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->where('joeys.is_active', 1)
                    ->count();

                /**
                 * quiz passed
                 * */
                $totalQuizPassedCount = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->join('microhub_attempted_quiz', 'microhub_attempted_quiz.jc_users_id', '=', 'joeys.id')
                    ->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$firstDayofPreviousMonth, $lastDayofPreviousMonth])
                    ->where('joeys.is_active', 1)
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->whereNull('joeys.deleted_at')
                    ->whereNull('microhub_attempted_quiz.deleted_at')
                    ->where('microhub_attempted_quiz.is_passed', 1)
                    ->count();


            }

            elseif ($request_data['days'] == 'all') {

                $totalSignUps = Joey::whereNull('deleted_at')->count();
                $basicRegistration = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->where('joeys.on_duty', 1)
                    ->whereNull('joeys.deleted_at')->count();

                if ($totalSignUps > 0 && $basicRegistration > 0) {
                    $percentage = round($basicRegistration / $totalSignUps * 100);
                } else {
                    $percentage = 1;
                }
                /**
                 * application submission calcultion
                 * */
                $totalApplicationSubmissionCount = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->where('joeys.is_active', 1)
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->whereNull('joeys.deleted_at')->count();

                /**
                 * quiz passed
                 * */
                $totalQuizPassedCount = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                    ->join('microhub_attempted_quiz', 'microhub_attempted_quiz.jc_users_id', '=', 'joeys.id')
                    ->where('joeys.is_active', 1)
                    ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                    ->whereNull('joeys.deleted_at')
                    ->whereNull('microhub_attempted_quiz.deleted_at')
                    ->where('microhub_attempted_quiz.is_passed', 1)
                    ->count();

            }
        } else {

            $totalSignUps = Joey::whereNull('deleted_at')->count();
            $basicRegistration = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                ->where('on_duty', 1)
                ->whereNull('joeys.deleted_at')
                ->count();

            if ($totalSignUps > 0 && $basicRegistration > 0) {
                $percentage = round($basicRegistration / $totalSignUps * 100);
            } else {
                $percentage = 1;
            }

            /**
             * application submission calcultion
             * */
            $totalApplicationSubmissionCount = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                ->where('joeys.is_active', 1)
                ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                ->whereNull('joeys.deleted_at')->count();

            /**
             * quiz passed
             * */
            $totalQuizPassedCount = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
                ->join('microhub_attempted_quiz', 'microhub_attempted_quiz.jc_users_id', '=', 'joeys.id')
                ->where('joeys.is_active', 1)
                ->where('microhub_joey_assign.hub_id', $auth_hub_id)
                ->whereNull('joeys.deleted_at')
                ->whereNull('microhub_attempted_quiz.deleted_at')
                ->where('microhub_attempted_quiz.is_passed', 1)
                ->count();

        }

        $assignJoeys = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
            ->whereNull('joeys.deleted_at')
            ->where('microhub_joey_assign.hub_id', $auth_hub_id)
            ->pluck('joeys.id')->toArray();

        $joeys_list = Joey::WhereNotIn('id', $assignJoeys)->whereNull('deleted_at')->where('is_active',1)->get();


        return backend_view('joeys_managment', compact('joeys_list','lastMonth','percentage', 'totalSignUps', 'totalQuizPassedCount', 'totalApplicationSubmissionCount'
            , 'basicRegistration'));
    }

    public function joeysList(){

        $auth_hub_id = isset(auth()->user()->hub_id) ? auth()->user()->hub_id : 0;
        $assignJoeys = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
            ->whereNull('joeys.deleted_at')
            ->where('microhub_joey_assign.hub_id', $auth_hub_id)
            ->pluck('joeys.id')->toArray();

        $joeys_list = Joey::WhereNotIn('id', $assignJoeys)->whereNull('deleted_at')->where('is_active',1)->get();

        return json_encode($joeys_list);
    }

    /**
     * basic registration
     */
    public function joeysOnDuty(Datatables $datatables, Request $request): JsonResponse
    {
        $data = $request->all();

        $auth_hub_id = isset(auth()->user()->hub_id) ? auth()->user()->hub_id : null;
        $currentDate = Carbon::now()->format('Y-m-d');
        $basicRegistration = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
            ->where('microhub_joey_assign.hub_id', $auth_hub_id)
            ->where('joeys.on_duty', 1)
            ->whereNull('joeys.deleted_at')
            ->select('joeys.id','joeys.first_name','joeys.last_name',DB::raw("CONCAT('joeys.first_name','joeys.last_name') AS full_name"),'joeys.address','joeys.email','joeys.phone');

        if ($data['days'] == '3days') {
            $date = Carbon::now()->subDays(3)->format('Y-m-d');
            $basicRegistration->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$date, $currentDate]);
        }
        elseif ($data['days'] == 'lastweek')
        {
            $startOfCurrentWeek = Carbon::now()->startOfWeek();
            $startOfLastWeek = $startOfCurrentWeek->copy()->subDays(7)->format('Y-m-d');
            $basicRegistration->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$startOfLastWeek, $currentDate]);
        }
        elseif ($data['days'] == '15days')
        {
            $date = Carbon::now()->subDays(15)->format('Y-m-d');
            $basicRegistration->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$date, $currentDate]);
        }
        elseif ($data['days'] == 'lastmonth')
        {
            $firstDayofPreviousMonth = Carbon::now()->startOfMonth()->subMonth()->toDateString();
            $lastDayofPreviousMonth = Carbon::now()->endOfMonth()->subMonth()->toDateString();
            $basicRegistration->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$firstDayofPreviousMonth, $lastDayofPreviousMonth]);
        }

        return $datatables->eloquent($basicRegistration)
            ->make(true);
    }

    /**
     * Application submission
     */
    public function totalApplicationSubmissionTable(Datatables $datatables, Request $request)
    {
        $auth_hub_id = isset(auth()->user()->hub_id) ? auth()->user()->hub_id : 0;
        $applicationsSubmission = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
            ->where('joeys.is_active', 1)
            ->where('microhub_joey_assign.hub_id', $auth_hub_id)
            ->whereNull('joeys.deleted_at')
            ->select('joeys.id','joeys.first_name','joeys.last_name',DB::raw("CONCAT('joeys.first_name','joeys.last_name') AS full_name"),'joeys.address','joeys.email','joeys.phone');
        $data = $request->all();
        $currentDate = Carbon::now()->format('Y-m-d');

        if ($data['days'] == '3days') {
            $date = Carbon::now()->subDays(3)->format('Y-m-d');
            $applicationsSubmission->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$date, $currentDate]);
        } elseif ($data['days'] == 'lastweek') {
            $startOfCurrentWeek = Carbon::now()->startOfWeek();
            $startOfLastWeek = $startOfCurrentWeek->copy()->subDays(7)->format('Y-m-d');
            $applicationsSubmission->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$startOfLastWeek, $currentDate]);
        } elseif ($data['days'] == '15days') {
            $date = Carbon::now()->subDays(15)->format('Y-m-d');
            $applicationsSubmission->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$date, $currentDate]);
        } elseif ($data['days'] == 'lastmonth') {
            $firstDayofPreviousMonth = Carbon::now()->startOfMonth()->subMonth()->toDateString();
            $lastDayofPreviousMonth = Carbon::now()->endOfMonth()->subMonth()->toDateString();
            $applicationsSubmission->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$firstDayofPreviousMonth, $lastDayofPreviousMonth]);
        }
        return $datatables->eloquent($applicationsSubmission)
            ->make(true);
    }

    /**
     * Quiz passed Record
     *
     */
    public function totalQuizPassedTable(Datatables $datatables, Request $request)
    {
        $auth_hub_id = isset(auth()->user()->hub_id) ? auth()->user()->hub_id : 0;
        $quizPassed = Joey::join('microhub_joey_assign','microhub_joey_assign.joey_id','=','joeys.id')
            ->join('microhub_attempted_quiz', 'microhub_attempted_quiz.jc_users_id', '=', 'joeys.id')
            ->where('joeys.is_active', 1)
            ->whereNull('joeys.deleted_at')
            ->where('microhub_joey_assign.hub_id', $auth_hub_id)
            ->whereNull('microhub_attempted_quiz.deleted_at')
            ->where('microhub_attempted_quiz.is_passed', 1)
            ->select('joeys.id','joeys.first_name','joeys.last_name',DB::raw("CONCAT('joeys.first_name','joeys.last_name') AS full_name"),'joeys.address','joeys.email','joeys.phone');
        $currentDate = Carbon::now()->format('Y-m-d');
        $data = $request->all();
        if ($data['days'] == '3days') {
            $date = Carbon::now()->subDays(3)->format('Y-m-d');
            $quizPassed->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$date, $currentDate]);
        } elseif ($data['days'] == 'lastweek') {
            $startOfCurrentWeek = Carbon::now()->startOfWeek();
            $startOfLastWeek = $startOfCurrentWeek->copy()->subDays(7)->format('Y-m-d');
            $quizPassed->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$startOfLastWeek, $currentDate]);
        } elseif ($data['days'] == '15days') {
            $date = Carbon::now()->subDays(15)->format('Y-m-d');
            $quizPassed->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$date, $currentDate]);
        } elseif ($data['days'] == 'lastmonth') {
            $firstDayofPreviousMonth = Carbon::now()->startOfMonth()->subMonth()->toDateString();
            $lastDayofPreviousMonth = Carbon::now()->endOfMonth()->subMonth()->toDateString();
            $quizPassed->whereBetween(\DB::raw("CONVERT_TZ(microhub_joey_assign.created_at,'UTC','America/Toronto')"), [$firstDayofPreviousMonth, $lastDayofPreviousMonth]);
        }

        return $datatables->eloquent($quizPassed)
            ->make(true);
    }

    public function addJoey(Request $request, MicroHubJoeyAssign $microHubJoeyAssign)
    {
        $data = $request->all();
        $auth_hub_id = isset(auth()->user()->hub_id) ? auth()->user()->hub_id : null;
        if(!empty($data['joeys']))
        {

            $addJoey = [
                'joey_id' => $data['joeys'],
                'hub_id' => $auth_hub_id,

            ];

            $microHubJoeyAssign->create($addJoey);

            session()->flash('alert-success', 'Joey Assign successfully!');
        }

        session()->flash('alert-success', 'Please select Joey!');
        return redirect( 'joeys');
    }

}
