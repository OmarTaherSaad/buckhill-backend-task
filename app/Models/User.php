<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Auth\JwtToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'is_admin',
        'email',
        'password',
        'avatar',
        'address',
        'phone_number',
        'is_marketing',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password'      => 'hashed',
        'is_admin'      => 'boolean',
        'is_marketing'  => 'boolean',
    ];

    #region Relationships

    public function tokens()
    {
        return $this->hasMany(JwtToken::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    #endregion

    public function getJWTClaim()
    {
        return $this->uuid;
    }

    public function deleteRelated()
    {
        $this->deleteTokens();
    }

    public function deleteTokens()
    {
        $this->tokens()->delete();
        $this->orders()->delete();
    }
}
