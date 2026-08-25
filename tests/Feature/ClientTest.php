<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Organization;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_user_can_view_own_organization_clients()
    {
        $org1 = Organization::create(['name' => 'Org 1']);
        $user1 = User::factory()->create(['organization_id' => $org1->id]);
        $role1 = Role::create(['name' => 'Organization Admin', 'organization_id' => $org1->id]);
        $user1->roles()->attach($role1);

        $org2 = Organization::create(['name' => 'Org 2']);
        $user2 = User::factory()->create(['organization_id' => $org2->id]);
        
        $client1 = Client::create(['organization_id' => $org1->id, 'name' => 'John Doe']);
        $client2 = Client::create(['organization_id' => $org2->id, 'name' => 'Jane Smith']);

        $response = $this->actingAs($user1)->get(route('organization.clients.index'));
        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
    }

    public function test_api_search_returns_json_results()
    {
        $org1 = Organization::create(['name' => 'Org 1']);
        $user1 = User::factory()->create(['organization_id' => $org1->id]);
        $role1 = Role::create(['name' => 'Organization Admin', 'organization_id' => $org1->id]);
        $user1->roles()->attach($role1);

        Client::create(['organization_id' => $org1->id, 'name' => 'Alpha Corp', 'phone' => '123456']);
        Client::create(['organization_id' => $org1->id, 'name' => 'Beta Inc', 'phone' => '987654']);

        $response = $this->actingAs($user1)->getJson(route('organization.clients.search', ['q' => 'Alpha']));
        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals('Alpha Corp', $data[0]['name']);
    }

    public function test_can_crud_client()
    {
        $org = Organization::create(['name' => 'Org 1']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['name' => 'Organization Admin', 'organization_id' => $org->id]);
        $user->roles()->attach($role);

        $this->actingAs($user);

        // Create
        $response = $this->post(route('organization.clients.store'), [
            'name' => 'New Client',
            'email' => 'client@test.com',
            'phone' => '1234567890'
        ]);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('clients', ['name' => 'New Client']);
        
        $client = Client::where('name', 'New Client')->first();

        // Update
        $this->put(route('organization.clients.update', $client), [
            'name' => 'Updated Client',
            'email' => 'client@test.com',
            'phone' => '1234567890'
        ]);
        $this->assertDatabaseHas('clients', ['name' => 'Updated Client']);
    }
}
