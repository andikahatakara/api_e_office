<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar',
        'nip',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'avatar_url',
        'full_name',
    ];

    /**
     * Set attribute avatar_url
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
    */
    protected function avatarUrl() : Attribute
    {
        return new Attribute(
            get: fn () => asset(Storage::url($this->attributes['avatar']))
        );
    }

    /**
     * Set attribute full_name
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
    */
    protected function fullName() : Attribute
    {
        return new Attribute(
            get: fn () => $this->attributes['first_name'].' '.$this->attributes['last_name']
        );
    }

    /**
     * Get employee of user
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
    */
    public function employee() : HasOne
    {
        return $this->hasOne(Employee::class, 'user_id');
    }
}
