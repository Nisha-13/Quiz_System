<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The account has not confirmed its email address yet.
     */
    public const STATUS_UNVERIFIED = 1;

    /**
     * The account confirmed its email address and may sign in.
     */
    public const STATUS_VERIFIED = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Every quiz attempt made by this user.
     */
    public function records()
    {
        return $this->hasMany(Record::class);
    }

    /**
     * Whether the account has been verified and may therefore sign in.
     */
    public function isVerified(): bool
    {
        return (int) $this->active === self::STATUS_VERIFIED;
    }
}
