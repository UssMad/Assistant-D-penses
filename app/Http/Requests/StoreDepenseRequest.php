<?php

namespace App\Http\Requests;

use App\Enums\CategorieDepense;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recu_id' => [
                'required',
                'exists:recus,id',
                function ($attribute, $value, $fail) {
                    if (!auth()->user()->recus()->where('id', $value)->exists()) {
                        $fail('Le reçu sélectionné ne vous appartient pas.');
                    }
                },
            ],
            'libelle' => ['required', 'string', 'max:255'],
            'quantite' => ['required', 'integer', 'min:1'],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
            'categorie' => ['required', Rule::enum(CategorieDepense::class)],
        ];
    }
}
