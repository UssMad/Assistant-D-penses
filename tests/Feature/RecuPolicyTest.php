<?php

namespace Tests\Feature;

use App\Models\Recu;
use App\Models\User;
use App\Policies\RecuPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecuPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_view_own_recu()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        $policy = new RecuPolicy();

        $this->assertTrue($policy->view($user, $recu));
    }

    #[Test]
    public function user_cannot_view_others_recu()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $owner->id]);

        $policy = new RecuPolicy();

        $this->assertFalse($policy->view($other, $recu));
    }

    #[Test]
    public function user_can_update_own_recu()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        $policy = new RecuPolicy();

        $this->assertTrue($policy->update($user, $recu));
    }

    #[Test]
    public function user_cannot_update_others_recu()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $owner->id]);

        $policy = new RecuPolicy();

        $this->assertFalse($policy->update($other, $recu));
    }

    #[Test]
    public function user_can_delete_own_recu()
    {
        $user = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $user->id]);

        $policy = new RecuPolicy();

        $this->assertTrue($policy->delete($user, $recu));
    }

    #[Test]
    public function user_cannot_delete_others_recu()
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $recu = Recu::factory()->create(['user_id' => $owner->id]);

        $policy = new RecuPolicy();

        $this->assertFalse($policy->delete($other, $recu));
    }

    #[Test]
    public function any_authenticated_user_can_create()
    {
        $user = User::factory()->create();

        $policy = new RecuPolicy();

        $this->assertTrue($policy->create($user));
    }
}
