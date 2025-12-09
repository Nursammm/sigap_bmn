<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    public const ROLE_ADMIN     = 'admin';
    public const ROLE_PENGELOLA = 'pengelola';
    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_PENGELOLA,
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPengelola(): bool
    {
        return $this->role === self::ROLE_PENGELOLA;
    }

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
        'password' => 'hashed',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    public function getProfilePhotoUrlAttribute(): string
    {
        $path = $this->profile_photo_path ?? $this->avatar ?? null;

        if ($path) {
            if (Str::startsWith($path, ['http://', 'https://'])) {
                return $path;
            }

            // Prefer public disk; fallback to asset() if file not found on disk
            if (Storage::disk('public')->exists($path)) {
                return Storage::url($path);
            }

            return asset($path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'User') . '&background=08376B&color=fff';
    }
}
