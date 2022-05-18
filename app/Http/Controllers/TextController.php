<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTextRequest;
use App\Http\Requests\UpdateTextRequest;
use GuzzleHttp\Client;
use App\Models\Author;
use App\Models\Country;
use App\Models\Language;
use App\Models\Text;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class TextController extends Controller
{
    public function getAll()
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

    public function getTextById($id) {
        // Validate UUID parameter before query the database
        if (preg_match("/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/i", $id)) {
            $text = DB::table('texts')->where('id', '=', $id)->value('text');

            if($text !== null) {
                return response()->json([
                    'text' => $text
                ], 400);
            } else {
                return response()->json([
                    'message' => 'Invalid id'
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Invalid parameter'
            ], 400);  
        }
    }

    public function textsByCefr($level)
    {
        // Validate CEFR level parameter before query the database
        if (preg_match("/^[A-Ca-c][1-2]/i", $level)) {
            $texts = DB::table('texts')
            ->select(
                'texts.id as id',
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

    public function retrieveCorrectTranslation($textId)
    {
        // Validate UUID before query the database
        if (preg_match("/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/i", $textId)) {
            $esText = DB::table('estexts')
            ->where('text_id', '=', $textId)
            ->value('text');

            if($esText !== null) {
                return response()->json([
                    'esText' => $esText
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Invalid id'
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Invalid parameter'
            ], 400);  
        }
    }

    public function saveUserTranslation(Request $request) {
        // Validate language parameter
        if($request->language === 'English' | $request->language === 'Spanish' ) {
            $translation = new Translation();

            $language_id = DB::table('languages')
                           ->where('name', '=', $request->language)
                           ->value('id');
            $user_id = User::where('id', '=', auth('api')->user()->id)->value('id');

            // Find translation in database
            if($request->language === 'English') {
                $databaseText = DB::table('texts')
                ->where('id', '=', $request->text_id)
                ->value('text');
            }
            if($request->language === 'Spanish') {
                $databaseText = DB::table('estexts')
                ->where('text_id', '=', $request->text_id)
                ->value('text');
            }

            // Prepare received translation by checking
            // that words are separated by only one space
            $request->text = preg_replace("/\s+/", " ", $request->text);
            // Removes punctuation
            $request->text = preg_replace("/[,;\'\".]/", " ", $request->text);
            $userTranslationArray = explode(" ", $request->text);

            // Prepare database text by checking
            // that words are separated by only one space
            $databaseText = preg_replace("/\s+/", " ", $databaseText);
            // Removes punctuation
            $databaseText = preg_replace("/[,;\'\".]/", " ", $databaseText);
            $databaseTextArray = explode(" ", $databaseText);

            $hits = 0;

            // Save the minimum array length to avoid out of index when comparing arrays
            $length = min(
                count($databaseTextArray), count($userTranslationArray)
            );

            // Count how many coincidences between user translation and correct translation
            for($i=0; $i<$length; $i++) {
                if($databaseTextArray[$i] === $userTranslationArray[$i]) {
                    $hits++;
                }
            }
            
            // Save hit ratio
            $hit_rate = round($hits / count($databaseTextArray), 2);

            // Save translation to database
            $translation->hit_rate = $hit_rate;
            $translation->text = $request->text;
            $translation->user_id = $user_id;
            $translation->text_id = $request->text_id;
            $translation->language_id = $language_id;
            $translation->save();

            return response()->json([
                'translation' => $translation,
                'userTranslationArray' => $userTranslationArray,
                '$request->language' => $request->language,
                'databaseText' => $databaseText,
                'databaseTextArray' => $databaseTextArray,
            ], 200); 

        } else {
            return response()->json([
                'message' => 'Invalid parameter'
            ], 400); 
        }
    }

    public function authorFullName($id) {
        // Validate paramether before query the database
        if (preg_match("/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/i", $id)) {
            // Find author by id
            $author = Author::find($id);

            // If author id exists get the full name
            if($author !== null) {
                return response()->json([
                    'author' => $author->first_name .' '. $author->last_name
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Invalid id'
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Invalid parameter'
            ], 400);  
        }
    }

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

    public function zenquotes()
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
