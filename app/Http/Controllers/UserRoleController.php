<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Common\UserType;
use App\Models\Roles;
use App\Models\Task;
use App\Models\TaskRoleMapping;
use App\Models\User;
use App\Models\UserRoleMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserRoleController extends Controller
{
    public function index(){

        $role = Roles::where('status', Status::ACTIVE)
        ->where('delete_status', Status::UNDELETE)
        ->orderBy('created_at', 'desc')
        ->get();

        $task = Task::all();

        return view('role.roles', compact('role', 'task'));
    }

    public function store(Request $request){

        $request->validate([
            'name'          => 'required',
            'description'   => 'required',
        ]);

        $role               = new Roles();
        $role->name         = $request->name;
        $role->description  = $request->description;
        $role->save();

        session()->flash('success', 'Role has been created');
        return redirect()->route('role.index');
    }

    public function edit($id){

        $role = Roles::where('id', $id)
        ->first();

        $task = Task::with(['TaskRole' => function($a) use($role){
            $a->where('role_id', $role->id);
        }])
        ->get();

        $task_role = TaskRoleMapping::where('role_id', $role->id)
        ->get();

        return view('role.edit_role', compact('role', 'task', 'task_role'));
    }

    public function update(Request $request){

        $request->validate([
            'name'          => 'required',
            'description'   => 'required',
        ]);

        Roles::where('id', $request->id)
        ->update([
            'name'          => $request->name,
            'description'   => $request->description,
        ]);

        session()->flash('success', 'Role has been updated');
        return redirect()->route('role.index');
    }

    public function delete($id){

        Roles::where('id', $id)
        ->update([
            'delete_status'          => Status::DELETE,
        ]);

        session()->flash('success', 'Role has been updated');
        return redirect()->route('role.index');
    }

    public function adminIndex(){

        $user = User::where('type', UserType::SUBADMIN)
        ->get();

        return view();
    }

    public function taskRoleStore(Request $request){
        
        $request->validate([
            'role'      => 'required',
        ]);

        if($request->task){

            foreach($request->task as $row){

                $task           = new TaskRoleMapping();
                $task->role_id  = $request->role;
                $task->task_id  = $row;
                $task->save();
            }

            session()->flash('success', 'Task has been maped to role');
            return redirect()->route('role.index');
        }
        else{

            session()->flash('error', 'Please seletc any one of the task');
            return redirect()->route('role.index');
        }
    }

    public function taskRoleUpdate(Request $request, $id){

        if($request->task){

            TaskRoleMapping::where('role_id', $id)
            ->delete();

            foreach($request->task as $row){

                $task           = new TaskRoleMapping();
                $task->role_id  = $id;
                $task->task_id  = $row;
                $task->save();
            }

            session()->flash('success', 'Task role been updated');
            return redirect()->route('role.index');
        }
        else{

            session()->flash('error', 'Please seletc any one of the task');
            return redirect()->route('role.index');
        }
    }

    public function adminUserIndex(){

        $user = User::where('type', UserType::SUBADMIN)
        ->where('delete_status', Status::UNDELETE)
        ->get();


        return view('role.user', compact('user'));
    }

    public function adminUserCreate(){
        
        $role = Roles::where('status', Status::ACTIVE)
        ->where('delete_status', Status::UNDELETE)
        ->get();

        return view('role.create_user', compact('role'));
    }

    public function adminUserStore(Request $request){
        
        $request->validate([
            'role'      => 'required',
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'required|unique:users,phone',
            'password'  => 'required',
            // 'address'   => 'required',
        ]);

        if($request->status){
            $status = 1;
        }
        else{
            $status = 0;
        }

        $user           = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->phone    = $request->phone;
        $user->password = Hash::make($request->password);
        $user->type     = UserType::SUBADMIN;
        $user->status   = $status;
        $user->save();

        $role           = new UserRoleMapping();
        $role->user_id  = $user->id;
        $role->role_id  = $request->role;
        $role->save();

        session()->flash('success', 'User has been created');
        return redirect()->route('admin_user.index');
    }

    public function adminUserView($id){

        $user = User::where('id', $id)
        ->first();
        
        return view('role.user_details', compact('user'));
    }

    public function adminUserEdit($id){

        $role = Roles::where('status', Status::ACTIVE)
        ->where('delete_status', Status::UNDELETE)
        ->get();

        $user = User::where('id', $id)
        ->first();

        return view('role.edit_user', compact('user', 'role'));
    }

    public function adminUserUpdate(Request $request, $id){

        $request->validate([
            'role'  => 'required',
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,'.$id.',id',
            'phone' => 'required|unique:users,phone,'.$id.',id',
        ]);

        if($request->password){

            $password = Hash::make($request->password);
        }
        else{

            $user = User::where('id', $id)
            ->first();

            $password = $user->password;
        }

        if($request->status){
            $status = 1;
        }
        else{
            $status = 0;
        }

        User::where('id', $id)
        ->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => $password,
            'status'    => $status,
        ]);

        UserRoleMapping::where('user_id', $id)
        ->update([
            'role_id' => $request->role,
        ]);

        session()->flash('success', 'User has been updated');
        return redirect()->route('admin_user.index');
    }

    public function userDelete($id){

        User::where('id', $id)
        ->update([
            'delete_status'     => Status::DELETE,
        ]);

        session()->flash('success', 'User has been deleted');
        return redirect()->route('admin_user.index');

    }
}
