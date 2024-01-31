<?php

namespace App\Http\Controllers\Backend;

use App\Classes\Client;
use App\CustomRoutingTrackingId;
use App\Hub;
use App\HubStore;
use App\JoeyRoute;
use App\JoeyRouteLocations;
use App\LogRoutes;
use App\MerchantIds;
use App\RoutingZones;
use App\SlotJob;
use App\Slots;
use App\Sprint;
use App\Task;
use App\ZonesTypes;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirstMileJobController extends BackendController
{

    public function getFirstMileJobList(Request $request){

        $hubId = auth()->user()->hub_id;
        $date=$request->get('date');
        $hub_id=$request->get('id');
        if(empty($date)){
            $date=date('Y-m-d');
        }
        $fistMileJobs = $this->getRoutificJob($date,$hubId);
//        dd($fistMileJobs);
        return backend_view('first_mile.job.list',compact('fistMileJobs','hubId'));
    }

    public function getRoutificJob($date,$id){

        $datas = SlotJob::whereNull('slots_jobs.deleted_at')
            ->where('slots_jobs.created_at','like',$date.'%')
            ->where('slots_jobs.hub_id','=',$id)
            ->where('slots_jobs.mile_type','=',1)
            ->get(['job_id','status','slots_jobs.id','engine']);

        return $datas;
    }

    public function createRouteForFirstMile($id){

        $url= "/jobs";

        $client = new Client($url);
        $client->setJobID($id);
        $apiResponse = $client->getJobResults();

        $job=SlotJob::where('job_id','=',$id)->first();

        if($apiResponse['status']=='finished'){

            $solution = $apiResponse['output']['solution'];

            if($apiResponse['output']['num_unserved'] > 0){
                return response()->json([
                    "status_code" => 400,
                    "status" => "Route Creation Error",
                    "output"=>$apiResponse['output']['num_unserved'] .' orders is un served'
                ]);
            }

            if(!empty($solution)){

                foreach ($solution as $key => $value){

                    if(count($value)>1){

                        $Route = new JoeyRoute();

                        //$Route->joey_id = $key;
                        $Route->date =$job->created_at;
                        $Route->hub = $job->hub_id;
                        $Route->zone = $job->zone_id;
                        if(isset($apiResponse['output']['total_working_time'])){
                            $Route->total_travel_time=$apiResponse['output']['total_working_time'];
                        }
                        else{
                            $Route->total_travel_time=0;
                        }
                        if(isset($apiResponse['output']['total_distance']))
                        {
                            $Route->total_distance=$apiResponse['output']['total_distance'];
                        }
                        else
                        {
                            $Route->total_distance=0;
                        }
                        $Route->mile_type = 1;
                        $Route->save();

                        $removeArray = array_slice($value, 1, -1);

                        for($i=0;$i<count($removeArray);$i++){

                                $routeLoc = new JoeyRouteLocations();
                                $routeLoc->route_id = $Route->id;
                                $routeLoc->ordinal = $i+1;
                                $routeLoc->task_id = $removeArray[$i]['location_id'];

                                if(isset($removeArray[$i]['distance']) && !empty($removeArray[$i]['distance'])){
                                    $routeLoc->distance = $removeArray[$i]['distance'];
                                }

                                if(isset($removeArray[$i]['arrival_time']) && !empty($removeArray[$i]['arrival_time'])){
                                    $routeLoc->arrival_time = $removeArray[$i]['arrival_time'];
                                    if(isset($removeArray[$i]['finish_time'])){
                                        $routeLoc->finish_time = $removeArray[$i]['finish_time'];
                                    }
                                }
                                $routeLoc->save();

//                            }
                        }
                    }
                }
                SlotJob::where('job_id','=',$job->job_id)->update(['status'=>$apiResponse['status']]);
                return response()->json([
                    "status_code" => 200,
                    "output"=> 'Route Create Successfully'
                ]);

            }
        }

        else{

            $error = new LogRoutes();
            $error->error = $job->job_id." is in ".$apiResponse['status'];
            $error->save();

            return back()->with('error','Routes creation is in process');
        }
    }

    // delete job of first mile
    public function deleteFirstMileJob(Request $request){

        SlotJob::where('id','=',$request->get('delete_id'))->update(['status'=>'finished','deleted_at'=>date('Y-m-d h:i:s')]);
        return redirect()->back()->with('success', 'job deleted successfully');
    }

}