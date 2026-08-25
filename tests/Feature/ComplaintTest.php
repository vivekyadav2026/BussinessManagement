<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Models\Organization;
use App\Models\Employee;
use App\Models\Complaint;
use App\Models\Role;
use App\Notifications\ComplaintAssignedNotification;

class ComplaintTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_and_assign_complaint()
    {
        Notification::fake();

        $org = Organization::create(['name' => 'Test Org']);
        
        $user = User::factory()->create(['organization_id' => $org->id]);
        $role = Role::create(['organization_id' => $org->id, 'name' => 'Admin']);
        $role->permissions()->create(['module' => 'complaints', 'name' => 'complaints.view', 'label' => 'View Complaints']);
        $role->permissions()->create(['module' => 'complaints', 'name' => 'complaints.create', 'label' => 'Create Complaints']);
        $role->permissions()->create(['module' => 'complaints', 'name' => 'complaints.manage', 'label' => 'Manage Complaints']);
        $user->roles()->attach($role);

        $assigneeUser = User::factory()->create(['organization_id' => $org->id]);
        $emp = Employee::create([
            'organization_id' => $org->id,
            'user_id' => $assigneeUser->id,
            'first_name' => 'Support',
            'last_name' => 'Agent',
        ]);

        $this->actingAs($user);
        
        // Create Complaint
        $response = $this->post(route('organization.complaints.store'), [
            'subject' => 'System Issue',
            'description' => 'The system is down.',
            'priority' => 'High',
            'assigned_to' => $emp->id,
        ]);

        $response->assertRedirect(route('organization.complaints.index'));
        
        $complaint = Complaint::first();
        $this->assertEquals('System Issue', $complaint->subject);
        $this->assertEquals('High', $complaint->priority);
        $this->assertEquals('Open', $complaint->status);
        $this->assertEquals($emp->id, $complaint->assigned_to);

        // Verify Notification was sent
        Notification::assertSentTo(
            [$assigneeUser], ComplaintAssignedNotification::class
        );
    }

    public function test_can_update_complaint_status_and_reassign()
    {
        Notification::fake();

        $org = Organization::create(['name' => 'Test Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        
        $role = Role::create(['organization_id' => $org->id, 'name' => 'Admin']);
        $role->permissions()->create(['module' => 'complaints', 'name' => 'complaints.view', 'label' => 'View Complaints']);
        $role->permissions()->create(['module' => 'complaints', 'name' => 'complaints.manage', 'label' => 'Manage Complaints']);
        $user->roles()->attach($role);

        $assigneeUser = User::factory()->create(['organization_id' => $org->id]);
        $emp2 = Employee::create([
            'organization_id' => $org->id,
            'user_id' => $assigneeUser->id,
            'first_name' => 'Tech',
            'last_name' => 'Guy',
        ]);

        $complaint = Complaint::create([
            'organization_id' => $org->id,
            'subject' => 'Printer broken',
            'description' => 'Paper jam',
            'priority' => 'Medium',
            'status' => 'Open',
        ]);

        $this->actingAs($user);

        $response = $this->put(route('organization.complaints.update', $complaint), [
            'status' => 'In Progress',
            'priority' => 'Urgent',
            'assigned_to' => $emp2->id,
        ]);

        $response->assertSessionHas('success');
        
        $complaint->refresh();
        $this->assertEquals('In Progress', $complaint->status);
        $this->assertEquals('Urgent', $complaint->priority);
        $this->assertEquals($emp2->id, $complaint->assigned_to);

        Notification::assertSentTo(
            [$assigneeUser], ComplaintAssignedNotification::class
        );
    }
}
