<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::all();
        foreach($users as $user){
            $user['role'] = $user->role->name;
        }
        return view('users.home')->with('users', $users);
    }

    public function update($id)
    {
        $user = User::find($id);
        if ($user == null) {
            return redirect('/');
        }
        $roles = Role::all();
        $selected_role = $user->role_id;
        
        return view('users.update', compact('user', 'roles', 'selected_role'));
    }

    public function edit(Request $request, $id)
    {
        $user = User::find($id);
        $user->role_id = $request->input('role_id');
        $user->save();
        return redirect('/users')->with('info', 'Update successfully!');
    }
}
