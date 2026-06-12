<?php

namespace App\Models;

use App\Enums\CategorieDepense;
use Database\Factories\DepensesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depenses extends Model
{
    /** @use HasFactory<DepensesFactory> */
    use HasFactory;

    protected $fillable = [
        'recu_id',
        'libelle',
        'quantite',
        'prix_unitaire',
        'categorie',
    ];

    protected function casts(): array
    {
        return [
            'categorie' => CategorieDepense::class,
        ];
    }

    public function recu(): BelongsTo
    {
        return $this->belongsTo(Recu::class);
    }
}
