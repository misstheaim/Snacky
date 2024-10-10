<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function roles() : BelongsTo 
    {
        return $this->belongsTo(Role::class, 'role', 'id');
    }

    public function snacks() :HasMany
    {
        return $this->hasMany(Snack::class);
    }


    public function isAdmin() :bool
    {
        return $this->roles()->select('role')->value('role') === "ADMIN";
    }

    public function isManager() :bool
    {
        return $this->roles()->select('role')->value('role') === "MANAGER";
    }

    public function isDev() :bool
    {
        return $this->roles()->select('role')->value('role') === "DEV";
    }
}
