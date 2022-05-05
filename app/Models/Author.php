<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
    ];

    // An author can have many texts
    public function authors(){
        return $this->belongsToMany(Author::class, 'author__texts');
    }
}
