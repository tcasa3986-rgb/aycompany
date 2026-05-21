<?php

namespace App\Traits;

use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait SucursalContext
{
    protected static function bootSucursalContext()
    {
        static::creating(function ($model) {
            if (Auth::check() && ! $model->sucursal_id) {
                $model->sucursal_id = Auth::user()->current_sucursal_id;
            }
        });

        static::addGlobalScope('sucursal', function (Builder $builder) {
            if (Auth::check()) {
                $sucursalId = Auth::user()->current_sucursal_id;
                if ($sucursalId) {
                    $builder->where($builder->getQuery()->from . '.sucursal_id', $sucursalId);
                }
            }
        });
    }

    public function sucursal()
    {
        return $this->belongsTo(\App\Models\Sucursal::class);
    }
}
