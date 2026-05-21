<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Interaccion extends Model
{
    protected $table = 'interacciones';

    protected $fillable = ['principio_a', 'principio_b', 'severidad', 'descripcion'];

    /**
     * Devuelve interacciones que afectan a alguno de los principios pasados.
     * Compara case-insensitive y por subcadena.
     *
     * @param  array<string> $principios
     * @return \Illuminate\Support\Collection<int, Interaccion>
     */
    public static function buscarEntre(array $principios): \Illuminate\Support\Collection
    {
        $principios = collect($principios)
            ->filter()
            ->map(fn ($p) => strtolower(trim($p)))
            ->unique()
            ->values()
            ->all();

        if (count($principios) < 2) {
            return collect();
        }

        return self::all()->filter(function ($i) use ($principios) {
            $a = strtolower($i->principio_a);
            $b = strtolower($i->principio_b);

            $matchA = collect($principios)->contains(fn ($p) => Str::contains($a, $p) || Str::contains($p, $a));
            $matchB = collect($principios)->contains(fn ($p) => Str::contains($b, $p) || Str::contains($p, $b));

            return $matchA && $matchB;
        })->values();
    }
}
