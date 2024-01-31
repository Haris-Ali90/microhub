<?php

namespace App\Http\Controllers\Backend;

use App\AmazonEnteries;
use App\DeliveryProcessType;
use App\Package;
use App\PackageSubscription;
use App\PaymentLog;
use App\Post;
use Illuminate\Http\Request;
use App\Sprint;
use App\Http\Requests;
use App\Http\Controllers\Backend\BackendController;

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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Stripe;
//use Stripe\Stripe;
//use Stripe\Stripe;
use whereBetween;
use Carbon\Carbon;
use PDFlib;

use App\FinanceVendorCity;
use App\HubStore;
use App\TorontoEntries;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use App\HubProcess;
use App\AlertSystem;
use App\BrookerJoey;
use App\BrookerUser;
use App\CTCEntry;
use App\CtcVendor;
use App\CustomerRoutingTrackingId;
use App\FinanceVendorCityDetail;
use App\FlagHistory;
use App\Http\Traits\BasicModelFunctions;
use App\HubZones;
use App\Joey;
use App\JoeyRouteLocations;
use App\JoeyRoutes;
use App\MerchantIds;
use App\Setting;
use App\SprintTaskHistory;
use App\TrackingImageHistory;
use App\WarehouseJoeysCount;
use DateTime;
use DateTimeZone;


class MicroHubSettingController extends BackendController
{
    public $Global_Value;

    public function getAllDefinedPermissions(){
        //Query to fetch data with joins...
        $all_defined_permissions = DeliveryProcessType::whereNotNull('id')->get();

        return json_encode($all_defined_permissions);

    }//All Defined Permissions...


    //Get All Packages
    public function getAllPackages(){

        $main_package = Package::whereNull('deleted_at')->get();
        $all_package_detail = Package::join('package_detail','packages.id','=','package_detail.package_id')
            ->join('delivery_process_type','delivery_process_type.id','=','package_detail.service_id')
            ->whereNull('package_detail.deleted_at')
            ->whereNull('packages.deleted_at')
            ->get();

        $all_package['main_package'] = $main_package;
        $all_package['package_detail'] = $all_package_detail;

        return json_encode($all_package);
    }//All Packages...


    public function getRequestedPackage(){

        //User Data to get the logged in details...
        $auth_user = Auth::user();

        $all_requested_package = PackageSubscription::where('is_active',0)->where('hub_id',$auth_user->hub_id)
            ->pluck('package_id')
            ->toArray();

        return json_encode($all_requested_package);

    }//All Requested Permissions



    public function getRequestedPermissions(){

        //User Data to get the logged in details...
        $auth_user = Auth::user();

        $all_requested_permissions = HubProcess::whereNull('hub_process.is_active')->orWhere('hub_process.is_active', 0)->where('hub_process.hub_id',$auth_user->hub_id)
            ->join('delivery_process_type', 'hub_process.process_id', '=', 'delivery_process_type.id')
            ->get();

        return json_encode($all_requested_permissions);

    }//All Requested Permissions


    public function RequestNewPermission(Request $request)
    {

        $new_permission_request = HubProcess::create([
            'hub_id' => $request->hub_id,
            'process_id' => $request->process_id
        ]);

        $this->PostToMicroHubPermissons($new_permission_request->id);

//        return $new_permission_request->id;

    }//Request a new Permission


    public function RequestNewPackage(Request $request)
    {
        $auth_user = Auth::user();

        $Subscribed_package = PackageSubscription::whereNull('deleted_at')->where('hub_id',$auth_user->hub_id)->get();

        if(count($Subscribed_package) == 0){
            $new_package_request = PackageSubscription::create([
                'hub_id' => $request->hub_id,
                'package_id' => $request->package_id
            ]);
        }else{
            $new_package_request = PackageSubscription::where('hub_id',$request->hub_id)->update(['package_id'=>$request->package_id,'is_active'=>0]);
        }


        return $new_package_request;

    }//Request a new Package

    //Posting the new permission request data in micro_hub_permission table as well...
    public function PostToMicroHubPermissons($hubProcessId)
    {

        //User Data to get the logged in details...
        $auth_user = Auth::user();

        $new_permission_request = DB::table('micro_hub_permissions')->insert([
            'hub_process_id' => $hubProcessId,
            'micro_hub_user_id' => $auth_user->id
        ]);

        if($new_permission_request){
            return "Successfully Submit data to Micro Hub Permissions";
        }else{
            return "Failed to Submit data in Micro_Hub_Permission";
        }


    }//Posting the new permission request data in micro_hub_permission table as well...

    // payment gateway view
    public function servicePaymentView($hubId, $ProcessId)
    {
        return backend_view('setting.payment', compact('hubId', 'ProcessId'));
    }

    public function handleonlinepay(Request $request){

        $input = $request->all();
        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $charge = Stripe\Charge::create ([
                "amount" => 100*1000,
                "currency" => "PKR",
                "source" => $request->stripeToken,
                "description" => "Test payment from microHub"
            ]);

            if($charge['status'] == 'succeeded'){
                PaymentLog::create([
                    'hub_id' => $input['hub_id'],
                    'process_id' => $input['process_id'],
                    'charge_id'=>$charge->id,
                    'amount'=> 10,
                    'plan'=> 'Basic',
                    'status'=> $charge['status'],
                    'currency' => $charge['currency'],
                    'description' => $charge['description'],
                    'card_no'=> $input['card_no'],
                    'exp_year' => $input['ccExpiryYear'],
                    'exp_month' => $input['ccExpiryMonth'],
                    'cvc' => $input['cvvNumber']
                ]);
            }

            Session::flash('success', 'Payment successful!');

            return back();

        } catch (\Exception $ex) {
            return response()->json([
                'message' => $ex,
                'state' => 'error'
            ]);
        }

    }

    public function handleonlinepay2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_no' => 'required',
            'ccExpiryMonth' => 'required',
            'ccExpiryYear' => 'required',
            'cvvNumber' => 'required',
            //'amount' => 'required',
        ]);
        $input = $request->all();
        if ($validator->passes()) {
            $input = array_except($input,array('_token'));
//            $stripe = Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));Stripe\

//            dd($stripe);
            try {
                $stripe = new \Stripe\StripeClient(
                    'sk_test_51MYldaF11wLAqJTo3eYldCQ52bqZQJu3jd4WoEEaD3C22Fs4SI18yO1Gv7vwYaEY6bzM2F4FulEvpQUkpxX9h4w000SQFpS2Q4'
                );

//                $token = $stripe->tokens->create([
//                    'card' => [
//                        'number' => $request->get('card_no'),
//                        'exp_month' => $request->get('ccExpiryMonth'),
//                        'exp_year' => $request->get('ccExpiryYear'),
//                        'cvc' => $request->get('cvvNumber'),
//                    ],
//                ]);

//                dd($token);

                if (!isset($token['id'])) {
                    return redirect()->back();
                }
                $charge = $stripe->charges()->create([
                    'card' => $token['id'],
                    'currency' => 'USD',
                    'amount' => 20.49,
                    'description' => 'wallet',
                ]);

                dd($charge);

                if($charge['status'] == 'succeeded') {
                    echo "<pre>";
                    print_r($charge);exit();
                    return redirect()->route('addmoney.paymentstripe');
                } else {
                    Session::put('error','Money not add in wallet!!');
                    return redirect()->route('addmoney.paymentstripe');
                }
            } catch (Exception $e) {
                Session::put('error',$e->getMessage());
                return redirect()->route('addmoney.paymentstripe');
            } catch(\Cartalyst\Stripe\Exception\CardErrorException $e) {
                Session::put('error',$e->getMessage());
                return redirect()->route('addmoney.paywithstripe');
            } catch(\Cartalyst\Stripe\Exception\MissingParameterException $e) {
                Session::put('error',$e->getMessage());
                return redirect()->route('addmoney.paymentstripe');
            }
        }
    }

    /**
     * Get Montreal ,Ottawa ,Ctc dashboard count and graph
     */
    public function getIndex(Request $request)
    {
        $auth_user = Auth::user();

        $all_requested_permissions = HubProcess::whereNull('hub_process.is_active')
            ->orWhere('hub_process.is_active', 0)
            ->where('hub_process.hub_id',$auth_user->hub_id)
            ->join('delivery_process_type', 'hub_process.process_id', '=', 'delivery_process_type.id')
            ->get();

        $all_requested_package = PackageSubscription::where('is_active',0)->where('hub_id',$auth_user->hub_id)
            ->pluck('package_id')
            ->toArray();

        $is_requested = $this->checkPackageRequest(0);
        $is_subscribed = $this->checkPackageRequest(1);

        return backend_view('setting',compact('all_requested_permissions','all_requested_package','is_subscribed','is_requested'));

    }//Get Index Page with Requested Permission

    public function checkPackageRequest($flag){

        $auth_user = Auth::user();

        $data = PackageSubscription::where('hub_id',$auth_user->hub_id)->whereNull('deleted_at')->where('is_active',$flag)->first();
        return $data;
    }


}
