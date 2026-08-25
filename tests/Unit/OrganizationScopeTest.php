<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_see_users_in_their_own_organization()
    {
        $org1 = Organization::create(['name' => 'Org 1']);
        $org2 = Organization::create(['name' => 'Org 2']);

        $user1 = User::factory()->create(['organization_id' => $org1->id]);
        $user2 = User::factory()->create(['organization_id' => $org2->id]);

        $this->actingAs($user1);

        // When user 1 queries users, they should only see themselves
        $users = User::all();

        $this->assertCount(1, $users);
        $this->assertEquals($user1->id, $users->first()->id);
    }
}
