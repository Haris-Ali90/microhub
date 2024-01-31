<?php

namespace App\Http\Controllers\Backend;

use App\AmazonEnteries;
use App\Http\Controllers\Backend\BackendController;
use App\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Sprint;
use App\Http\Requests;
use App\User;
use App\Teachers;
use App\Institute;
use App\Amazon;
use Illuminate\Support\Facades\Hash;
use App\Amazon_count;
use App\Ctc;
use App\Ctc_count;
use App\CoursesRequest;
use date;
use DB;
use whereBetween;
use Carbon\Carbon;
use PDFlib;

class MicroHubChangePassword extends BackendController
{


    /**
     * Get Montreal ,Ottawa ,Ctc dashboard count and graph
     */


    public function getNewPassword()
    {

        $auth_user = Auth::user();
        $pass = $auth_user->password;

        if($auth_user->is_active == 0){
            return backend_view('auth.newpassword',compact('pass'));
        }else{
            return redirect('microhub/cookies');
        }

    }

    //Updating two fields password and is_active in Dashboard_users Table...
    public function passwordUpdate(Request $request)
    {

        $updated_new_password = Hash::make($request->newpassword);
        //User Data to get the logged in details...
        $auth_user = Auth::user();


        $updating_password_first_time = DB::table('dashboard_users')->where('id', '=', $auth_user->id)->update(['is_active' => 1,'password' => $updated_new_password]);

        if($updating_password_first_time){
            return redirect('microhub/cookies');
        }else{
            return "Failed to Update Password !";
        }


    }//Updating two fields password and is_active in Dashboard_users Table...

}
