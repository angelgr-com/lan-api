<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role_User extends Model
{
    use Uuids, HasFactory;

    protected $fillable = [
        'role_id',
        'user_id',
    ];
}
