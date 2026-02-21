<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Role;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that Admin and Member cannot create short URLs.
     *
     * @return void
     */
    public function test_admin_and_member_cannot_create_short_urls(): void
    {
        $company = Company::factory()->create();
        $adminRole = Role::create(['name' => 'Admin']);
        $memberRole = Role::create(['name' => 'Member']);

        // Test Admin cannot create
        $admin = User::factory()->create(['company_id' => $company->id]);
        $admin->roles()->attach($adminRole->id, ['company_id' => $company->id]);

        $this->actingAs($admin);
        $response = $this->get('/short-urls/create');
        $response->assertStatus(403);

        $response = $this->post('/short-urls', ['original_url' => 'https://example.com']);
        $response->assertStatus(403);

        // Test Member cannot create
        $member = User::factory()->create(['company_id' => $company->id]);
        $member->roles()->attach($memberRole->id, ['company_id' => $company->id]);

        $this->actingAs($member);
        $response = $this->get('/short-urls/create');
        $response->assertStatus(403);

        $response = $this->post('/short-urls', ['original_url' => 'https://example.com']);
        $response->assertStatus(403);
    }

    /**
     * Test that SuperAdmin cannot create short URLs.
     *
     * @return void
         */
    public function test_superadmin_cannot_create_short_urls(): void
    {
        $superAdminRole = Role::create(['name' => 'SuperAdmin']);
        $systemCompany = Company::create(['name' => 'System']);
        
        $superAdmin = User::factory()->create(['company_id' => $systemCompany->id]);
        $superAdmin->roles()->attach($superAdminRole->id, ['company_id' => $systemCompany->id]);

        $this->actingAs($superAdmin);
        $response = $this->get('/short-urls/create');
        $response->assertStatus(403);

        $response = $this->post('/short-urls', ['original_url' => 'https://example.com']);
        $response->assertStatus(403);
    }

    /**
     * Test that SuperAdmin cannot see the list of all short URLs.
     *
     * @return void
     */
    public function test_superadmin_cannot_see_short_urls_list(): void
    {
        $superAdminRole = Role::create(['name' => 'SuperAdmin']);
        $systemCompany = Company::create(['name' => 'System']);
        
        $superAdmin = User::factory()->create(['company_id' => $systemCompany->id]);
        $superAdmin->roles()->attach($superAdminRole->id, ['company_id' => $systemCompany->id]);

        $this->actingAs($superAdmin);
        $response = $this->get('/short-urls');
        $response->assertStatus(403);
    }

    /**
     * Test that Admin can only see short URLs not created in their own company.
     *
     * @return void
     */
    public function test_admin_can_only_see_short_urls_not_in_own_company(): void
    {
        $adminRole = Role::create(['name' => 'Admin']);
        $company1 = Company::factory()->create(['name' => 'Company 1']);
        $company2 = Company::factory()->create(['name' => 'Company 2']);

        $admin = User::factory()->create(['company_id' => $company1->id]);
        $admin->roles()->attach($adminRole->id, ['company_id' => $company1->id]);

        $user1 = User::factory()->create(['company_id' => $company1->id]);
        $user2 = User::factory()->create(['company_id' => $company2->id]);

        ShortUrl::create([
            'short_code' => 'test1',
            'original_url' => 'https://example.com/1',
            'user_id' => $user1->id,
            'company_id' => $company1->id,
        ]);

        ShortUrl::create([
            'short_code' => 'test2',
            'original_url' => 'https://example.com/2',
            'user_id' => $user2->id,
            'company_id' => $company2->id,
        ]);

        $this->actingAs($admin);
        $response = $this->get('/short-urls');
        $response->assertStatus(200);
        $response->assertDontSee('test1');
        $response->assertSee('test2');
    }

    /**
     * Test that Member can only see short URLs not created by themselves.
     *
     * @return void
     */
    public function test_member_can_only_see_short_urls_not_created_by_themselves(): void
    {
        $memberRole = Role::create(['name' => 'Member']);
        $company = Company::factory()->create();

        $member = User::factory()->create(['company_id' => $company->id]);
        $member->roles()->attach($memberRole->id, ['company_id' => $company->id]);

        $otherUser = User::factory()->create(['company_id' => $company->id]);

        ShortUrl::create([
            'short_code' => 'test1',
            'original_url' => 'https://example.com/1',
            'user_id' => $member->id,
            'company_id' => $company->id,
        ]);

        ShortUrl::create([
            'short_code' => 'test2',
            'original_url' => 'https://example.com/2',
            'user_id' => $otherUser->id,
            'company_id' => $company->id,
        ]);

        $this->actingAs($member);
        $response = $this->get('/short-urls');
        $response->assertStatus(200);
        $response->assertDontSee('test1');
        $response->assertSee('test2');
    }

    /**
     * Test that short URLs are not publicly resolvable and redirect to original URL.
     *
     * @return void
     */
    public function test_short_urls_are_not_publicly_resolvable(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $shortUrl = ShortUrl::create([
            'short_code' => 'test123',
            'original_url' => 'https://example.com',
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);

        // Test without authentication - should fail
        $response = $this->get('/s/test123');
        $response->assertStatus(403);

        // Test with authentication - should redirect
        $this->actingAs($user);
        $response = $this->get('/s/test123');
        $response->assertRedirect('https://example.com');
    }
}
