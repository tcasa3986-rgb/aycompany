<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    public function sucursales(): BelongsToMany
    {
        return $this->belongsToMany(Sucursal::class, 'sucursal_user')
            ->withPivot('es_predeterminada')
            ->withTimestamps();
    }

    public function getCurrentSucursalIdAttribute(): ?int
    {
        // Si hay una sucursal en sesión, usar esa. Si no, la predeterminada.
        if (session()->has('sucursal_id')) {
            return session('sucursal_id');
        }

        return $this->sucursales()->wherePivot('es_predeterminada', true)->first()?->id;
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'active',
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
            'active' => 'boolean',
        ];
    }
}
