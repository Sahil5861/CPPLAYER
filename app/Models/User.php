<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function setPasswordAttribute($value)
    {
       $this->attributes['password'] = bcrypt($value);
    }

    public static function generateLoginPin() {
        $number = mt_rand(1000000000, 9999999999); // better than rand()

        // call the same function if the barcode exists already
        if (self::loginPinExists($number)) {
            return self::generateLoginPin();
        }

        // otherwise, it's valid and can be used
        return $number;
    }

    public static function generateLoginAppPin() {
        $number = mt_rand(10000000, 99999999); // better than rand()

        return $number;
    }

    public static function generateOver18Pin() {
        $number = mt_rand(10000000, 99999999); // better than rand()

        // call the same function if the barcode exists already
        // if (self::loginPinExists($number)) {
        //     return self::generateLoginPin();
        // }

        // otherwise, it's valid and can be used
        return $number;
    }

    public static function loginPinExists($number) {
        // query the database and return a boolean
        // for instance, it might look like this in Laravel
        return User::whereLoginPin($number)->exists();
    }
}
