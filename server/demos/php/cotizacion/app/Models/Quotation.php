<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'quotation_number', 'issue_date', 'due_date', 'client_id',
        'currency', 'subtotal', 'discount_amount', 'tax_amount', 'total',
        'status', 'notes', 'terms',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date'   => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(QuotationDetail::class);
    }

    /**
     * Genera el próximo número de cotización con formato: PREFIX-YYYY-NNNN
     * Robusto: no depende de ids, usa la secuencia máxima del año actual.
     */
    public static function generateNumber(): string
    {
        $prefix = Setting::get('quotation_prefix', 'COT');
        $year   = date('Y');
        $pattern = "{$prefix}-{$year}-%";

        $last = static::where('quotation_number', 'like', $pattern)
                      ->orderByRaw('LENGTH(quotation_number) DESC, quotation_number DESC')
                      ->value('quotation_number');

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last);
            $seq   = ((int) end($parts)) + 1;
        }

        return "{$prefix}-{$year}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Borrador'  => 'badge-gray',
            'Emitida'   => 'badge-blue',
            'Aprobada'  => 'badge-green',
            'Rechazada' => 'badge-red',
            default     => 'badge-gray',
        };
    }

    public function getCurrencySymbolAttribute(): string
    {
        return match ($this->currency) {
            'USD' => '$',
            'EUR' => '€',
            default => 'S/',
        };
    }
}
