<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'text',
    	'difficulty',
        'source_id',
        'cefr_id',
    	'type_id',
    ];
}
