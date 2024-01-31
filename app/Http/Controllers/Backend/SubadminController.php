<?php

namespace App\Http\Controllers\Backend;

use App\DeliveryProcessType;
use App\FinanceVendorCity;
use App\Http\Requests\Backend\StoreSubadminRequest;
use App\HubProcess;
use App\HubRequest;
use App\JCUser;
use App\MicroHubPermission;
use App\Permissions;
use App\Roles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use App\Http\Requests\Backend\ChangepwdRequest;
use Hash;
use Illuminate\Foundation\Auth\ResetsPasswords;

class SubadminController extends BackendController
{
    use ResetsPasswords;

    public function getIndex()
    {

        return backend_view('subadmin.index');
    }

    public function subAdminList(Datatables $datatables, Request $request)
    {
        $hub_id = isset(auth()->user()->hub_id) ? auth()->user()->hub_id : 0;
        $query = User::where(['role_id' => User::ROLE_ADMIN])->where('hub_id',$hub_id)->where('email','!=',Auth::user()->email);
        return $datatables->eloquent($query)
            ->setRowId(static function ($record) {
                return $record->id;
            })
            ->editColumn('status', static function ($record) {
                return backend_view('subadmin.status', compact('record') );
            })
            ->editColumn('profile_picture', static function ($record) {
                if (isset($record->profile_picture)) {
                    return '<img onClick="ShowLightBox(this);" style = "width:50px;height:50px" src = "' . $record->profile_picture . '" />';
                } else {
                    return '';
                }
            })
            ->addColumn('action', static function ($record) {
                return backend_view('subadmin.action', compact('record'));
            })
            ->make(true);
    }

    public function add()
    {

        $hub_id = isset(auth()->user()->hub_id) ? auth()->user()->hub_id : 0;
        $hubProcess = HubProcess::where('hub_id', $hub_id)->where('is_active', 1)->whereNull('deleted_at')->pluck('process_id')->toArray();
        $deliveryProcessType = DeliveryProcessType::whereIn('id',$hubProcess)->whereNull('deleted_at')->get();


        $hubs = FinanceVendorCity::where('deleted_at', null)->get();
        return backend_view( 'subadmin.add', compact(
            'deliveryProcessType','hubs') );
    }

    public function create(StoreSubadminRequest $request,User $user)
    {
        $post=$request->all();
        $dashboardUserCheck= \App\User::where('email', $post['email'])->where('role_id',5)->get();
        if(count($dashboardUserCheck)>0){
            return redirect('subadmin/add');

        }else{

            $postData = $request->all();
            $hub_id = isset(auth()->user()->hub_id) ? auth()->user()->hub_id : 0;
            $updateRecord = [
                'full_name' => $postData['full_name'],
                'email' => $postData['email'],
                'phone' => $postData['phone'],
                'address' => $postData['address'],
                'type'=> isset($postData['type']) ? $postData['type'] : '',
                'role_id' => User::ROLE_ADMIN,
                'micro_sub_admin' => 1,
                'status' => 1,
                'hub_id' => $hub_id,

            ];

            if ( $request->has('password') && $request->get('password', '') != '' ) {
                $updateRecord['password'] = \Hash::make( $postData['password'] );
            }

            if ($request->hasFile('profile_picture')) {
                $imageName = \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();
                $path = public_path(Config::get('constants.front.dir.profilePicPath'));

                $request->file('profile_picture')->move($path, $imageName);
                $updateRecord['profile_picture'] = url('/').'/images/profile_images/'.$imageName;
            }
            else{
                $imageName="default.png";
                $updateRecord['profile_picture'] = url('/').'/images/profile_images/'.$imageName;
            }

            $userId = $user->create( $updateRecord );
            $userProcess = HubProcess::where('hub_id',$hub_id)->whereIn('process_id',$request->hubPermission)->where('is_active',1)->pluck('id')->toArray();

            // now creating microhub permission
            foreach ($userProcess as $index => $delivery_process_id) {
                // creating hub process
                $HubProcessPermissionCreate = [
                    'micro_hub_user_id' => $userId->id,
                    'hub_process_id' => $delivery_process_id
                ];

                $create_data = DB::table('micro_hub_permissions')
                    ->insert(['micro_hub_user_id'=> $userId->id,'hub_process_id' =>  $delivery_process_id]);

            }

            session()->flash('alert-success', 'Sub Admin has been created successfully!');

            $token = hash('ripemd160',uniqid(rand(),true));
            DB::table('password_resets')
                ->insert(['email'=> $postData['email'],'role_id' =>  User::ROLE_ADMIN,'token' => $token]);

            $email = base64_encode ($postData['email']);

            return redirect( 'subadmins' . $user->id );
        }


    }

    public function active(User $record)
    {
        $record->activate();
        return redirect()
            ->route('sub-admin.index')
            ->with('success', 'Sub Admin has been Active successfully!');
    }

    public function inactive(User $record)
    {
        $record->deactivate();
        return redirect()
            ->route('sub-admin.index')
            ->with('success', 'Sub Admin has been Inactive successfully!');
    }

    public function edit($id)
    {
        $sub_id = base64_decode($id);
        $user = User::find($sub_id);
        $hub_id = isset(auth()->user()->hub_id) ? auth()->user()->hub_id : 0;
        $hubProcess = HubProcess::where('hub_id', $hub_id)->where('is_active', 1)->whereNull('deleted_at')->get();


        $havePermission = MicroHubPermission::where('micro_hub_user_id',$sub_id)->whereNull('deleted_at')->pluck('hub_process_id')->toArray();
        $selectedPermission = implode(',',$havePermission);

        return backend_view( 'subadmin.edit', compact('hubProcess','user','selectedPermission') );
    }


    public function update(Request $request, User $user)
    {
        $this->validate($request,[
            'full_name'  => 'required|max:255',
            'email'      => 'required|email|max:255',
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $postData = $request->all();
//        dd($postData);


        $postData['type'] = ($request->has('type')) ? $postData['type'] : '';

        $updateRecord = [
            'full_name' => $postData['full_name'],
            'email' => $postData['email'],
            'phone' => $postData['phone'],
            'type'=> $postData['type'],
            'address' => $postData['address'],

        ];

        if ( $request->has('password') && $request->get('password', '') != '' ) {
            $updateRecord['password'] = \Hash::make( $postData['password'] );
        }

        if($file = $request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture') ;

            $imageName = $user->id . '-' . \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();

            $path = public_path().'/images/profile_images' ;

            $file->move($path,$imageName);
            $updateRecord['profile_picture'] = url('/').'/images/profile_images/'.$imageName ;
        }

        $user->update($updateRecord);

        $permissionUpdate= [

            'updated_at' =>  Carbon::now(),
        ];
        MicroHubPermission::where('micro_hub_user_id',$user->id)->update($permissionUpdate);

        $userProcess = HubProcess::where('hub_id',$user->hub_id)->whereIn('id',$request->hubPermission)->where('is_active',1)->pluck('id')->toArray();

        // now creating microhub permission
        foreach ($userProcess as $index => $delivery_process_id) {
            // creating hub process
            $HubProcessPermissionCreate = [
                'micro_hub_user_id' => $user->id,
                'hub_process_id' => $delivery_process_id
            ];

            $already_assigned_permission = MicroHubPermission::where('micro_hub_user_id',$user->id)->where('hub_process_id',$delivery_process_id)->pluck('id')->toArray();
            if(count($already_assigned_permission) == 0){
                $create_data = DB::table('micro_hub_permissions')
                    ->insert(['micro_hub_user_id'=> $user->id,'hub_process_id' =>  $delivery_process_id]);
            }

        }


        session()->flash('alert-success', 'Subadmin has been updated successfully!');
        return redirect( 'subadmins');
    }

    public function destroy(User $user)
    {
        $userId = $user->id;
        $data = $user->delete();
        session()->flash('alert-success', 'Sub Admin has been deleted successfully!');

        return redirect( 'subadmins' );
    }

    public function profile($id)
    {
        $sub_id = base64_decode($id);
        $users = User::where(['id' => $sub_id])->get();


        foreach ($users as $data){
            $user_data = $data;
        }

        $hub_process_data = HubProcess::where('hub_id',$user_data->hub_id)->pluck('process_id')->toArray();


        $roles_assigned = array();

        foreach ($hub_process_data as $data){
            $data1 = DeliveryProcessType::where('id',$data)->get();
            foreach ($data1 as $x){
                array_push($roles_assigned,$x->process_title);
            }
        }

        $roles_assigned = implode(' | ',$roles_assigned);

        $users = $users[0];
        $rights = explode(',',$users->rights);
        return backend_view( 'subadmin.profile', compact('users','rights','roles_assigned') );
    }

    public function getChangePwd()
    {
        return backend_view( 'changepwd');
    }

    public function changepwd(ChangepwdRequest $request)
    {

        $postData = $request->all();

        /*dd($password);*/
        $password=$postData['old_pwd'];
        $admin=User::where('email',auth()->user()->email)->where('role_id',2)->first();
        $hashpwd=$admin['password'];
        if (Hash::check($password, $hashpwd))
        {
            if ( $request->has('new_pwd') && $request->get('new_pwd', '') != '' ) {
                $postData['new_pwd'] = \Hash::make( $postData['new_pwd'] );
                $newpwd=$postData['new_pwd'];
                User::where('email',auth()->user()->email)->where('role_id',2)->first()->update(['password' => $newpwd]);
                session()->flash('alert-success', 'Password has been change successfully!');
                return redirect( 'changepwd');
            }
        }
        else{

            session()->flash('alert-danger', 'Old password not Match!');
            return redirect( 'changepwd');

        }



    }

    public function adminedit($id){
        $sub_id = base64_decode($id);
        $user = User::find($sub_id);

        $permissions = explode(',',$user->permissions);
        $rights = explode(',',$user->rights);
        return backend_view( 'subadmin.adminedit', compact('user','permissions','rights') );

    }


    public function adminupdate(Request $request, User $user)
    {
        $this->validate($request,[
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'full_name'  => 'required|max:255',
            'email'      => 'required|email|max:255',
        ]);
        $postData = $request->all();
        $updateRecord = [
            'full_name' => $postData['full_name'],
            'email' => $postData['email'],
            'phone' => $postData['phone'],
            'address' => $postData['address'],

        ];
        $rights = implode(',', $postData['rights']);
        $updateRecord['rights'] = $rights;
        $permissions = implode(',', $postData['permissions']);
        $updateRecord['permissions'] = $permissions;
        if ( $request->has('password') && $request->get('password', '') != '' ) {
            $updateRecord['password'] = \Hash::make( $postData['password'] );
        }

        if($file = $request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture') ;

            $imageName = $user->id . '-' . \Illuminate\Support\Str::random(12) . '.' . $request->file('profile_picture')->getClientOriginalExtension();

            $path = public_path().'/images/profile_images' ;

            $file->move($path,$imageName);
            $updateRecord['profile_picture'] = url('/').'/public/images/profile_images/'.$imageName ;
        }


        $user->update( $postData );

        session()->flash('alert-success', 'Admin has been updated successfully!');
        return redirect( 'adminedit/'.base64_encode(auth()->user()->id));
    }


    public function accountSecurityEdit($id)
    {
        $sub_id = base64_decode($id);
        $user = User::find($sub_id);
        return backend_view( 'subadmin.security', compact('user') );
    }

    public function accountSecurityUpdate(Request $request, User $user)
    {
        $this->validate($request,[
            'is_email' => 'required',
        ]);
        $postData = $request->all();
        $updateRecord = [
            'is_email' => isset($postData['is_email'])? 1: 0,
            'is_scan' => isset($postData['is_scan'])? 1: 0,
        ];

        $user->update( $updateRecord );

        session()->flash('alert-success', 'Account Security has been updated successfully!');
        return redirect( 'account/security/edit/'.base64_encode(auth()->user()->id));
    }
}
