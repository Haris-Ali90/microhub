<?php

namespace App\Http\Controllers\Backend;

use App\Agreements;
use App\AgreementsUser;
use App\HubProcess;
use Illuminate\Http\Request;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MicroHubCookiesController extends Controller
{

    function getIndex(){

        //User Data to get the logged in details...
        $auth_user = Auth::user();

        $signed_agreement_data = AgreementsUser::where('user_id',$auth_user->id)->where('user_type','microhub')->whereNotNull('signed_at')->pluck('id');

        $agreement = Agreements::where('target','microhub')->get();

        if(count($signed_agreement_data)==0){
            return backend_view('cookies.cookies',compact('agreement'));
        }else{

            //This needs to be fixed
             return redirect('microhub/newprofile');
        }


    }


    //Posting the new permission request data in micro_hub_permission table as well...
    public function PostToAgreement(Request $request)
    {

        //User Data to get the logged in details...
        $auth_user = Auth::user();

        $posted_data = DB::table('agreements_user')->insert([
            'agreement_id' => $request->agreement_id,
            'user_id' => $auth_user->id,
            'user_type' => $request->user_type,
            'created_at'=>date("Y-m-d h:i:s"),
            'updated_at'=>date("Y-m-d h:i:s"),
            'signed_at'=>date("Y-m-d h:i:s"),
        ]);

        if($posted_data){
            return "Successfully Submit data to Agreements User";
        }else{
            return "Failed to Submit data in Agreements User";
        }


    }//Posting the new permission request data in micro_hub_permission table as well...

}
