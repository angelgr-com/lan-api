<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author_Text extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'author_id',
        'text_id',
    ];
}
