<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Uuids, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'country_id',
        'profile_picture',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'email_verified_at',
        'is_admin',
        'password',
        'remember_token',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function users(){
        // One user can have many roles
        return $this->belongsToMany(User::class, 'role_users');
        // One user is able to review many translations
        return $this->belongsToMany(User::class, 'translation__users');
        // One user is able to learn many languages
        return $this->belongsToMany(User::class, 'learn__users');
        // One user is able to speak many languages
        return $this->belongsToMany(User::class, 'speak__users');
    }
}
