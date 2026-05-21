<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = ['name', 'document_number', 'email', 'phone', 'address'];

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }
}
