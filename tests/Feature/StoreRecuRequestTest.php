<?php

namespace Tests\Feature;

use App\Http\Requests\StoreRecuRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreRecuRequestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function valid_texte_brut_passes_validation()
    {
        $request = new StoreRecuRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'texte_brut' => 'Valid receipt text with enough characters',
        ], $rules);

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function texte_brut_less_than_10_characters_fails()
    {
        $request = new StoreRecuRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'texte_brut' => 'Short',
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('texte_brut', $validator->errors()->toArray());
    }

    #[Test]
    public function texte_brut_exceeding_10000_characters_fails()
    {
        $request = new StoreRecuRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'texte_brut' => str_repeat('a', 10001),
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('texte_brut', $validator->errors()->toArray());
    }

    #[Test]
    public function texte_brut_is_required()
    {
        $request = new StoreRecuRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('texte_brut', $validator->errors()->toArray());
    }
}
