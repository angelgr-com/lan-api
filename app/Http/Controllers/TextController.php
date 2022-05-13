<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\Text;
use App\Models\Language;
use App\Http\Requests\StoreTextRequest;
use App\Http\Requests\UpdateTextRequest;
use App\Models\Author;
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
        try {
            $languages = Country::orderBy('name')
                         ->select('name as label', 'name as value')
                         ->get();
            return $languages;
        } catch (\Exception $exception) {
            Log::error('Retrieve of countries list failed. Error: '.$exception->getMessage());
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

    public function textsByCefr($level)
    {
        // Validate paramether before query the database
        if (preg_match("/^[A-Ca-c][1-2]/i", $level)) {
            $texts = DB::table('texts')
            ->select(
                'texts.text as text',
                'texts.difficulty as difficulty',
                'sources.author_id as author_id',
                'cefrs.level as cefr',
                'types.type as type',)
            ->where('level', '=', $level)
            ->leftJoin('sources', 'sources.id', '=', 'texts.source_id')
            ->leftJoin('cefrs', 'cefrs.id', '=', 'texts.cefr_id')
            ->leftJoin('types', 'types.id', '=', 'texts.type_id')
            ->paginate(1);

            return $texts;
        } else {
            return response()->json([
                'message' => 'Invalid parameter'
            ], 400);  
        }
    }

    public function authorName($id) {
        $author = Author::find($id);

        return $author->first_name .' '. $author->last_name;
    }

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
}
