<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that SuperAdmin cannot invite an Admin in a new company.
     *
     * @return void
         */
    public function test_superadmin_cannot_invite_admin_in_new_company(): void
    {
        $superAdminRole = Role::create(['name' => 'SuperAdmin']);
        $adminRole = Role::create(['name' => 'Admin']);
        $systemCompany = Company::create(['name' => 'System']);
        
        $superAdmin = User::factory()->create(['company_id' => $systemCompany->id]);
        $superAdmin->roles()->attach($superAdminRole->id, ['company_id' => $systemCompany->id]);

        $this->actingAs($superAdmin);
        $response = $this->post('/invitations', [
            'email' => 'admin@example.com',
            'role_id' => $adminRole->id,
        ]);

        $response->assertSessionHasErrors(['error']);
    }

    /**
     * Test that Admin cannot invite another Admin or Member in their own company.
     *
     * @return void
         */
    public function test_admin_cannot_invite_admin_or_member_in_own_company(): void
    {
        $adminRole = Role::create(['name' => 'Admin']);
        $memberRole = Role::create(['name' => 'Member']);
        $company = Company::factory()->create();

        $admin = User::factory()->create(['company_id' => $company->id]);
        $admin->roles()->attach($adminRole->id, ['company_id' => $company->id]);

        $this->actingAs($admin);

        // Try to invite Admin
        $response = $this->post('/invitations', [
            'email' => 'admin2@example.com',
            'role_id' => $adminRole->id,
        ]);
        $response->assertSessionHasErrors(['error']);

        // Try to invite Member
        $response = $this->post('/invitations', [
            'email' => 'member@example.com',
            'role_id' => $memberRole->id,
        ]);
        $response->assertSessionHasErrors(['error']);
    }
}
