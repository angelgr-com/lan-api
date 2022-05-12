<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\Text;
use App\Models\Language;
use App\Http\Requests\StoreTextRequest;
use App\Http\Requests\UpdateTextRequest;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TextController extends Controller
{
    public function languagesList(){
        try {
            $languages = Language::orderBy('name')
            ->select('name as label', 'name as value')
            ->get();
            
            return $languages;
        } catch (\Exception $exception) {
            Log::error('Retrieve of languages list failed. Error: '.$exception->getMessage());
            return response()->json([
                'message' => 'Languages failed',
                'Error' => $exception->getMessage(),
                'Code' => $exception->getCode(),
                'File' => $exception->getFile(),
                'Line' => $exception->getLine(),
                'Trace' => $exception->getTrace(),
            ], 500);     
        }
    }

    public function countriesList(){
        $languages = Country::orderBy('name')
                        ->select('name as label', 'name as value')
                        ->get();
        return $languages;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $texts = DB::table('texts')
        ->select(
            'texts.text as text',
            'texts.difficulty as difficulty',
            'sources.author_id as author_id',
            'cefrs.level as cefr',
            'types.type as type',)
        ->leftJoin('sources', 'sources.id', '=', 'texts.source_id')
        ->leftJoin('cefrs', 'cefrs.id', '=', 'texts.cefr_id')
        ->leftJoin('types', 'types.id', '=', 'texts.type_id')
        ->get();

        return $texts;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //https://zenquotes.io/api/quotes
        // Instantiate the client class from GuzzleHttp\Client
        $client = new Client();
        $url = "https://zenquotes.io/api/quotes";


        $response = $client->request('GET', $url, [
            'verify'  => false,
        ]);

        $quotes = json_decode($response->getBody());

        return $quotes;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Text  $text
     * @return \Illuminate\Http\Response
     */
    public function show(Text $text)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Text  $text
     * @return \Illuminate\Http\Response
     */
    public function edit(Text $text)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTextRequest  $request
     * @param  \App\Models\Text  $text
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTextRequest $request, Text $text)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Text  $text
     * @return \Illuminate\Http\Response
     */
    public function destroy(Text $text)
    {
        //
    }
}
