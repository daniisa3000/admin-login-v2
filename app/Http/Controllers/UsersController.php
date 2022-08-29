<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        if(Auth::user()->user_type) {
            $users = DB::table('users')
                        ->where('user_type', '=', 0)
                        ->orderBy('created_at', 'DESC');

            return view('users.index', compact('users'));
        }
        else {
            return redirect('/home');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Auth::user()->user_type) {
            return view('users.create');
        }
        else {
            return redirect('/home');
        }
    }


    public function store(Request $request)
    {
        if(Auth::user()->user_type) {

            $validatedData = $request->validate([
                'name' => 'required:max:40',
                'email' => 'required|unique:users|max:190',
                'password' => 'required|min:6',
                'role' => 'required|max:40',
            ]);
            
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            $role = new Role;
            $role->user_id = $user->id;
            $role->role = $request->role;
            $role->save();

            return redirect('/admin-panel')->with('msg_success', 'User Created Successfully');
            
        }
        else {
            return redirect('/home');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Auth::user()->user_type) {
            $user = User::find($id);
            return view('users.view', compact('user'));
        }
        else {
            return redirect('/home');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->user_type) {
            $user = User::findOrFail($id);
            $user_role = DB::table('roles')->where('user_id', '=', $id)->first();
            return view('users.edit', compact('user', 'user_role'));

        }
        else {
            return redirect('/home');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->user_type) {

            $validatedData = $request->validate([
                'name' => 'required:max:40',
                'email' => 'required|max:190',
                'password' => 'nullable|min:6|confirmed',
                'role' => 'required|max:40',
            ]);

            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            if($user->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            $role = Role::where('user_id', '=', $id)->firstOrFail();
            $role->role = $request->role;
            $role->permission = $request->permission;
            $role->save();

            return redirect('/admin-panel')->with('msg_success', 'User Updated Successfully');
        }
        else {
            return redirect('/home');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::user()->user_type) {
            $user = User::find($id);
            $role = Role::where('user_id', '=', $id)->firstOrFail();
            $role->delete();
            $user->delete();
            return redirect('/admin-panel')->with('msg_success', 'User Deleted Successfully');
        }
        else {
            return redirect('/home');
        }
    }
}