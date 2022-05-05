<?php

namespace App\Http\Controllers;

use App\Models\Role_User;
use App\Http\Requests\StoreRole_UserRequest;
use App\Http\Requests\UpdateRole_UserRequest;

class RoleUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRole_UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRole_UserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role_User  $role_User
     * @return \Illuminate\Http\Response
     */
    public function show(Role_User $role_User)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role_User  $role_User
     * @return \Illuminate\Http\Response
     */
    public function edit(Role_User $role_User)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRole_UserRequest  $request
     * @param  \App\Models\Role_User  $role_User
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRole_UserRequest $request, Role_User $role_User)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role_User  $role_User
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role_User $role_User)
    {
        //
    }
}
