<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use Uuids, HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'title',
    	'chapter',
    	'paragraph',
        'url',
        'author_id',
    ];

    // A source can have many authors
    public function sources(){
        return $this->belongsToMany(Source::class, 'author__sources');
    }
}
