<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'hit_rate',
        'text',
    	'user_id',
    	'text_id',
    	'language_id',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    // A translation can be reviewed by many users
    public function translations() {
        return $this->belongsToMany(Translation::class, 'translation_users');
    }
}
