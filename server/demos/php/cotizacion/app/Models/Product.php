<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'unit'];

    public function quotationDetails(): HasMany
    {
        return $this->hasMany(QuotationDetail::class);
    }
}
