<?php

namespace App\Http\Controllers;

use App\Models\Speak_User;
use App\Http\Requests\StoreSpeak_UserRequest;
use App\Http\Requests\UpdateSpeak_UserRequest;

class SpeakUserController extends Controller
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
     * @param  \App\Http\Requests\StoreSpeak_UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSpeak_UserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Speak_User  $speak_User
     * @return \Illuminate\Http\Response
     */
    public function show(Speak_User $speak_User)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Speak_User  $speak_User
     * @return \Illuminate\Http\Response
     */
    public function edit(Speak_User $speak_User)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSpeak_UserRequest  $request
     * @param  \App\Models\Speak_User  $speak_User
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSpeak_UserRequest $request, Speak_User $speak_User)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Speak_User  $speak_User
     * @return \Illuminate\Http\Response
     */
    public function destroy(Speak_User $speak_User)
    {
        //
    }
}
