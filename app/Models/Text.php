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
        'author_id',
        'cefr_id',
    	'difficulty_id',
    	'type_id',
    ];

    // A text can have many authors
    public function texts(){
        return $this->belongsToMany(Text::class, 'author__texts');
    }
}
