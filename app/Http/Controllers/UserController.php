<?php

namespace App\Http\Controllers;

use App\Models\Showroom;
use App\Models\User;
use App\Models\UserShowroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    protected string $permission_for = 'user';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');

        return Inertia::render('User/Index', [
            'users' => User::with('roles')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->hasPermission('create');

        return Inertia::render('User/Create', [
            'roles' => Role::all(),
            'showrooms' => Showroom::withoutGlobalScopes()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->hasPermission('create');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:255|unique:users',
            'salary' => 'nullable|numeric',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'salary' => $request->salary,
            'password' => Hash::make($request->password),
        ]);

        $user->syncRoles($request->roles);
        $user->showroom()->sync($request->showrooms);

        return Redirect::route('user.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        $this->hasPermission('update');
        $user = User::findOrFail($id);
        $roles = Role::all();
        $showrooms = Showroom::withoutGlobalScopes()->get();
        $user_roles = $user->roles->pluck('id')->all();
        $user_showrooms = UserShowroom::where('user_id', $user->id)->pluck('showroom_id')->all();

        return Inertia::render('User/Edit', [
            'user' => $user,
            'roles' => $roles,
            'user_roles' => $user_roles,
            'user_showrooms' => $user_showrooms,
            'showrooms' => $showrooms,
        ]);
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
//        return $request->all();
        // check permission
        $this->hasPermission('update');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:255|unique:users,phone,' . $id,
            'salary' => 'nullable|numeric',
        ]);

        // update user
        $user = User::findOrFail($id);
        $user->update($data);

        // has password
        if ($request->password) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // set user roles
        $user->syncRoles($request->roles);
        $user->showroom()->sync($request->showrooms);

        return redirect()->route('user.index')->withSuccess('User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // check permission
        $this->hasPermission('delete');

        // delete user
        User::findOrFail($id)->delete();

        return redirect()->route('user.index')->withSuccess('User deleted successfully');
    }
}
