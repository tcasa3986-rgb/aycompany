<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = true;

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_rol');
    }
}

