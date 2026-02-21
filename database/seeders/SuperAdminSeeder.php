<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds using raw SQL.
     *
     * @return void
     */
    public function run(): void
    {
        // Get SuperAdmin role ID
        $superAdminRoleId = DB::selectOne("SELECT id FROM roles WHERE name = 'SuperAdmin' LIMIT 1");
        
        if (!$superAdminRoleId) {
            $this->command->error('SuperAdmin role not found. Please run RolesSeeder first.');
            return;
        }

        $superAdminRoleId = $superAdminRoleId->id;
        $email = 'superadmin@example.com';
        $password = Hash::make('password');

        // Check if SuperAdmin already exists
        $existingUser = DB::selectOne("SELECT id FROM users WHERE email = ? LIMIT 1", [$email]);

        if ($existingUser) {
            $this->command->info('SuperAdmin user already exists.');
            return;
        }

        // Create system company for SuperAdmin if it doesn't exist
        $systemCompany = DB::table('companies')->where('name', 'System')->first();
        
        if (!$systemCompany) {
            $systemCompanyId = DB::table('companies')->insertGetId([
                'name' => 'System',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $systemCompanyId = $systemCompany->id;
        }

        // Create SuperAdmin user using raw SQL
        $userId = DB::table('users')->insertGetId([
            'name' => 'Super Admin',
            'email' => $email,
            'password' => $password,
            'company_id' => $systemCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign SuperAdmin role using raw SQL
        DB::table('user_company_role')->insert([
            'user_id' => $userId,
            'company_id' => $systemCompanyId,
            'role_id' => $superAdminRoleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('SuperAdmin created successfully.');
        $this->command->info('Email: ' . $email);
        $this->command->info('Password: password');
    }
}
