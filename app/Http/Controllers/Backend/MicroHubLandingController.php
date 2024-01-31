<?php

namespace App\Http\Controllers\Backend;

use App\AmazonEnteries;
use App\Http\Controllers\Backend\BackendController;
use App\Post;
use Illuminate\Http\Request;
use App\Sprint;
use App\Http\Requests;
use App\User;
use App\Teachers;
use App\Institute;
use App\Amazon;
use App\Amazon_count;
use App\Ctc;
use App\Ctc_count;
use App\CoursesRequest;
use date;
use DB;
use Illuminate\Support\Facades\Auth;
use whereBetween;
use Carbon\Carbon;
use PDFlib;

class MicroHubLandingController extends BackendController
{


    /**
     * Get Montreal ,Ottawa ,Ctc dashboard count and graph
     */

    //Mark:- This is a by default Page, It will appear when no permission in allowed to user -- Daniyal Khan
    public function getIndex(Request $request)
    {
        $auth_user = Auth::user();
        $pass = $auth_user->password;

        if($auth_user->is_active == 0){
            return backend_view('auth.newpassword',compact('pass'));
        }else{
            return backend_view('landing');
        }
    }



}
