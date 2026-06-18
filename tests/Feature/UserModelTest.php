<?php

namespace Tests\Feature;

use App\Models\Depenses;
use App\Models\Recu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_has_many_recus()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->recus->contains($recu));
    }

    #[Test]
    public function user_has_many_depenses_through_recus()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);
        $depense = Depenses::factory()->create(['recu_id' => $recu->id]);

        $this->assertTrue($user->depenses->contains($depense));
    }

    #[Test]
    public function user_only_sees_own_recus()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Recu::factory()->create(['user_id' => $user->id]);
        Recu::factory()->count(2)->create(['user_id' => $other->id]);

        $this->assertCount(1, $user->recus);
    }
}
