<?php

namespace App\Http\Controllers;

use App\Models\Cefr;
use App\Http\Requests\StoreCefrRequest;
use App\Http\Requests\UpdateCefrRequest;

class CefrController extends Controller
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
     * @param  \App\Http\Requests\StoreCefrRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCefrRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cefr  $cefr
     * @return \Illuminate\Http\Response
     */
    public function show(Cefr $cefr)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cefr  $cefr
     * @return \Illuminate\Http\Response
     */
    public function edit(Cefr $cefr)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCefrRequest  $request
     * @param  \App\Models\Cefr  $cefr
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCefrRequest $request, Cefr $cefr)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cefr  $cefr
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cefr $cefr)
    {
        //
    }
}
