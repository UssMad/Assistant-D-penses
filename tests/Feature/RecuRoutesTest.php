<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecuRoutesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_cannot_access_recus_index()
    {
        $this->get(route('recus.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_access_recus_create()
    {
        $this->get(route('recus.create'))->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_store_recus()
    {
        $this->post(route('recus.store'))->assertRedirect(route('login'));
    }

    #[Test]
    public function guest_cannot_access_depenses_index()
    {
        $this->get(route('depenses.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_user_can_access_recus_index()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('recus.index'))
            ->assertOk();
    }

    #[Test]
    public function authenticated_user_can_access_recus_create()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('recus.create'))
            ->assertOk();
    }
}
