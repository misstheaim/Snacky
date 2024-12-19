<?php

namespace App\Models;

use App\TwoFA\HasTwoFALoginCustom;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Solutionforest\FilamentEmail2fa\Interfaces\RequireTwoFALogin;
use Solutionforest\FilamentEmail2fa\Trait\HasTwoFALogin;

/**
 * @property-read int $id
 */
class User extends Authenticatable implements FilamentUser, RequireTwoFALogin, HasAvatar
{
    use HasFactory, HasTwoFALogin, HasTwoFALoginCustom, Notifiable {
        HasTwoFALoginCustom::isTwoFaVerfied insteadof HasTwoFALogin;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (! is_null($this->avatar)) {
            return config('app.s3-url') . $this->avatar;
        }
        return null;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
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

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function roles(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role', 'id');
    }

    public function snacks(): HasMany
    {
        return $this->hasMany(Snack::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function snacksApprovedByUser(): BelongsToMany
    {
        return $this->belongsToMany(Snack::class, 'snack_approved_by_user')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->roles()->select('role')->value('role') === config('app.admin_role');
    }

    public function isManager(): bool
    {
        return $this->roles()->select('role')->value('role') === config('app.manager_role');
    }

    public function isDev(): bool
    {
        return $this->roles()->select('role')->value('role') === config('app.dev_role');
    }
}
