<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser; // 1. Tambahkan ini
use Filament\Panel; // 2. Tambahkan ini

class User extends Authenticatable implements FilamentUser // 3. Tambahkan "implements FilamentUser"
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 4. Tambahkan 'role' agar bisa diisi
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 5. Letakkan fungsi canAccessPanel di sini
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
