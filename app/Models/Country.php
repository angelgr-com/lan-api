<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use Uuids, HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
    ];
}
