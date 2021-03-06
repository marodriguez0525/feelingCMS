<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use App\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use Input;
use Kamaln7\Toastr\Facades\Toastr;
use Response;
use Validator;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
     * Display a listing of the resource.
     */
    
    public function index()
    {
        $roles = Role::all();
        $title = trans('back.pages.roles');
        return view('back.roles.index', compact('roles', 'title'));
    }

    /*
     * Show the form for creating a new resource.
     */
    
    public function create()
    {
        $title = trans('back.pages.newRole');
        $permList = $this->permissionList();

        return view('back.roles.create', compact('title', 'permList'));
    }

    /*
     * Store a newly created resource in storage.
     */
    
    public function store(Request $request)
    {

        $title = trans('back.pages.newRole');
        // validate request, not working tho, sad boy
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name|min:3|max:10',
            'display_name' => 'required|min:3|max:30',
            'description' => 'required|min:3|max:250',
        ]);

        if ($validator->fails()) {

            Toastr::error(trans('messages.error.msg_validation'), $title);

            return redirect()
                ->route('roles.create')
                ->withErrors($validator)
                ->withInput();
        } else {

            $permissions = $request['permissions'];
            $role = Role::create($request->all());
            $role->savePermissions($permissions);

            Toastr::success(trans('messages.success.newRole'), $title);
            return redirect()->route('roles.index');
        }

    }

    /*
     * Display the specified resource.
     */
    
    public function show($id)
    {
        $title = trans('back.pages.roles');
        $role = Role::findOrNew($id);
        $users = $this->usersWithRole($role);
        $permList = $this->rolePermissionList($role);

        return view('back.roles.show', compact('role', 'permList', 'users', 'title'));
    }

    /*
     * Show the form for editing the specified resource.
     */

    public function edit($id)
    {
        $title = trans('back.pages.editRole');
        $role = Role::findOrNew($id);
        $permList = $this->rolePermissionList($role);

        return view('back.roles.edit', compact('role', 'permList', 'title'));
    }

    /*
     * Update the specified resource in storage.
     */
    
    public function update(Request $request, $id)
    {
        $title = trans('back.pages.editRole');
        $role = Role::findOrNew($id);

        // validate request

        $rules = [
            'name' => 'required|min:3|max:10|unique:roles,name,' . $id,
            'display_name' => 'required|min:3|max:30',
            'description' => 'required|min:3|max:250',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {

            Toastr::error(trans('messages.error.msg_validation'), $title);

            return redirect()
                ->route('roles.create')
                ->withErrors($validator)
                ->withInput();
        } else {

            $permissions = $request['permissions'];
            $role->update($request->all(), $id);
            $role->savePermissions($permissions);

            Toastr::success(trans('messages.success.updatedRole'), $title);
            return redirect()->route('roles.index');
        }
    }

    /*
     * Remove the specified resource from storage.
     */
    
    public function destroy($id)
    {
        $title = trans('back.pages.roles');
        $id = Input::get('roleId');

        $role = Role::findOrNew($id);
        $usersInRole = $this->usersWithRole($role);
        if(isset($usersInRole) && count($usersInRole) > 0){
            Toastr::error(trans('messages.error.deleteRole'), $title);

        }else {
            $role->delete();
            Toastr::success(trans('messages.success.deleteRole'), $title);
        }

        return Response::json(['success' => 'Success']);

    }

    private function usersWithRole($role)
    {

        $users = User::all()->filter(function ($item) use ($role) {
            return $item->hasRole($role->name);
        });

        return $users;
    }

    private function rolePermissionList($role)
    {

        $permList = Permission::all()->sortBy('name');
        foreach ($permList as $perm) {
            if ($role->hasPermission($perm->name)) {
                $perm->checked = true;
            }
        }
        $permList = $permList->groupBy('context')->sortBy('name');
        return $permList;
    }

    private function permissionList()
    {

        $permList = Permission::all()->sortBy('name');
        $permList = $permList->groupBy('context')->sortBy('name');
        return $permList;
    }
}
