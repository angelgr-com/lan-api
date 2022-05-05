<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public function languages(){
        // A language can be learned by many users
        return $this->belongsToMany(Language::class, 'learn__users');
        // A language can be speaked by many users
        return $this->belongsToMany(Language::class, 'speak__users');
    }
}
