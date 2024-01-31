<?php

namespace App\Http\Controllers\Backend;

use App\Claim;
use App\Classes\Fcm;
use App\HubRequest;
use App\Models\Zones;
use App\JCUser;
use App\Models\ZoneSchedule;
use App\OrderCategory;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use App\UserDevice;
use App\UserNotification;
use Illuminate\Http\Request;
use App\Models\PreferWorkTime;
use App\Models\PreferWorkType;
use App\Models\MicroHubRequest;
use Illuminate\Support\Facades\DB;
//use App\Http\Controllers\BackendController;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\UserRepositoryInterface;

class MicroHubNewProfileController extends BackendController
{
   
    public function getIndex()
    {

        $user = Auth::user();


        if($user->userType == 'user'){
            return redirect('microhub/training-newdocuments');
        }

        $jc_user = DB::table('jc_users')->where('email_address',$user->email)->get();

        foreach ($jc_user as $data){
            $jc_user_data = $data->id;
            $jc_user_address = $data->user_address;
            $jc_user_phone = $data->user_phone;

        }

        $hub_request = HubRequest::where('jc_user_id',$jc_user_data)->get();
        foreach ($hub_request as $data){
            $hub_user_data = $data->own_joeys;
        }

        return backend_view('new-profile.profile',compact('user','jc_user','hub_user_data','jc_user_address','jc_user_phone'));
  }


    public function training()
    {

        $categories = OrderCategory::where('user_type','micro_hub')->get();

        return backend_view('new-profile.training',compact('categories'));

    }
    public function profileUpdate(Request $request)
    {

        $user = Auth::user();
        $user_id = $user->id;
        $post = $request->all();

        User::where('id', $user_id)->update(['full_name' => $post['full_name'], 'phone' => $post['phone_no'], 'address' => $post['search_input'],'location_longitude' => $post['longitude'], 'location_latitude' => $post['latitude']]);
        JCUser::where('email_address', $user->email)->update(['user_address' => $post['search_input2'], 'user_phone' => $post['jc_phone']]);


        $jc_user_data = JCUser::where('email_address', $user->email)->get();

        foreach ($jc_user_data as $data){
            $jc_user_id = $data->id;
        }

        HubRequest::where('jc_user_id', $jc_user_id)->update(['own_joeys' => $post['own_joeys']]);
            return redirect('microhub/training-newdocuments');


    }
    

}
