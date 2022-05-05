<?php

namespace App\Http\Controllers;

use App\Models\Learn_User;
use App\Http\Requests\StoreLearn_UserRequest;
use App\Http\Requests\UpdateLearn_UserRequest;

class LearnUserController extends Controller
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
     * @param  \App\Http\Requests\StoreLearn_UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLearn_UserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Learn_User  $learn_User
     * @return \Illuminate\Http\Response
     */
    public function show(Learn_User $learn_User)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Learn_User  $learn_User
     * @return \Illuminate\Http\Response
     */
    public function edit(Learn_User $learn_User)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLearn_UserRequest  $request
     * @param  \App\Models\Learn_User  $learn_User
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLearn_UserRequest $request, Learn_User $learn_User)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Learn_User  $learn_User
     * @return \Illuminate\Http\Response
     */
    public function destroy(Learn_User $learn_User)
    {
        //
    }
}
