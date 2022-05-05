<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estext extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'text',
        'text_id',
    ];
}
