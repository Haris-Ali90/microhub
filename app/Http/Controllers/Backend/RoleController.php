<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Backend\UpdateRoleRequest;
use Illuminate\Http\Request;

use App\Roles;
use App\Permissions;
use App\User;
use App\Http\Requests\Backend\RoleRequest;


class RoleController extends BackendController
{

    private $RoleRepository;

    public function __construct()
    {
    }

    /**
     * Index action
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $Roles =  Roles::where('id', '!=' ,Permissions::SUPER_ADMIN_ROLE_ID)->where( 'type' , Roles::ROLE_TYPE_NAME)->orderBy('display_name','ASC')->get();

        return backend_view('Roles.role',compact(
            'Roles'
        ));
    }


    /**
     * Create action
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        return backend_view('Roles.roleadd');
    }

    /**
     * store action
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function store(RoleRequest $request)
    {
        $data = $request->except(
            [
                '_token',
                '_method',
            ]
        );


        /*createing inserting data*/
        $create = [
            'display_name' => $data['display_name'],
            'role_name' => SlugMaker($data['display_name']),
            'type' => Roles::ROLE_TYPE_NAME,
        ];

        /*inserting data*/
        /*$this->RoleRepository->create($create);*/
        Roles::create($create);

        /*return data */
        session()->flash('alert-success', 'Role has been created successfully!');
        return redirect()
            ->route('role.create');


    }

    /**
     * show action
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $role_id = base64_decode($id);
        $role = Roles::where(['id' => $role_id])->get();

        $role = $role[0];
        $permissions =  config('permissions');//Permissions::GetAllPermissions();
        $route_names = $role->Permissions->pluck('route_name')->toArray();
        //dd($permissions,$role);

        return backend_view('Roles.show',compact(
            'role',
            'route_names',
            'permissions'
        ));
    }


    /**
     * edit action
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $role_id = base64_decode($id);
        $role = Roles::find($role_id);


        // getting dashnboard card permissions

        return backend_view('Roles.roleedit',compact(
            'role'
        ));
    }


    /**
     * update action
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(UpdateRoleRequest $request,Roles $role)
    {
        /*getting all requests data*/
        $Postdata = $request->all();


        /*creating updating data*/
        $update_data = [
            'display_name' => $Postdata['display_name'],
            'role_name' => SlugMaker($Postdata['display_name']),
            'type' => Roles::ROLE_TYPE_NAME,
            /*'dashbaord_cards_rights' => $dashboard_cards_rights,*/
        ];


        /*updating data*/
        $role->update($update_data);
        /*return data */
        return redirect()
            ->route('role.index')
            ->with('success', 'Role updated successfully');

    }


    public function setPermissions(Roles $role)
    {
        // getting permissions
        $permissions_list = config('permissions');//Permissions::getAllPermissions();

        return backend_view('Roles.set-permissions',compact(
            'role',
            'permissions_list'
        ));
    }

    public function setPermissionsUpdate(Request $request,$role)
    {
        // now creating insert data of permissions
        $insert_permissions = [];

        $role_permissions = ($request->permissions != null) ? $request->permissions :[];

        foreach($role_permissions as $role_permission)
        {
            if(strpos($role_permission, '|') !== false)
            {
                foreach(explode('|',$role_permission) as $child_permission )
                {
                    $insert_permissions[] =['route_name'=> $child_permission, 'role_id'=>$role];
                }
            }
            else
            {
                $insert_permissions[] = ['route_name'=> $role_permission, 'role_id'=>$role];
            }

        }

        // deleting old data
        $delete = Permissions::where('role_id',$role)->delete();

        //inserting new data
        $crate_permissions = Permissions::insert($insert_permissions);

        /*return data */
        return redirect()
            ->route('role.index')
            ->with('success', 'Role permissions updated successfully');

    }


}
