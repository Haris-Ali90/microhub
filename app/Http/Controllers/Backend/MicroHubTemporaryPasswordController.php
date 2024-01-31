<?php

namespace App\Http\Controllers\Backend;

use App\Claim;
use App\Classes\Fcm;
use App\DeliveryProcessType;
use App\HubProcess;
use App\Models\Zones;
use App\Models\JCUser;
use App\Models\ZoneSchedule;
use App\NewUser;
use App\TemporaryPassword;
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

class MicroHubTemporaryPasswordController extends BackendController
{



    public function getIndex()
    {
        return backend_view('auth.temporarypassword');
    }




    public function getGeneratedPassword(){

        //Mark:- Checking User Credential...
        $auth_user = Auth::user();
        //Query to fetch data with joins...


        if($auth_user->temporary_password){

            return json_encode($auth_user->temporary_password);
        }



    }//All Defined Permissions...


    public function postGeneratedPassword(Request $request)
    {

        $auth_user = Auth::user();
        $user_id = $auth_user->id;

        User::where('id', $user_id)->update([
            'temporary_password' => $request->password,
            'temporary_password_created' => date("y-m-d h:i:s"),
        ]);

        return redirect('microhub/temporarypassword');
    }
    

}
