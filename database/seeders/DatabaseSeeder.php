<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create([
            'name' => 'DukaFlow Super Admin',
            'email' => 'admin@dukaflow.test',
            'user_type' => User::TYPE_INTERNAL,
        ]);
        $superAdmin->assignRole('super_admin');

        $merchant = Merchant::create([
            'business_name' => 'Mama Amina Duka',
            'owner_name' => 'Amina Hassan',
            'phone' => '+255700000001',
            'email' => 'amina@example.com',
            'business_type' => 'Duka / mini-supermarket',
            'region' => 'Dar es Salaam',
            'city' => 'Kinondoni',
            'kyc_status' => Merchant::KYC_APPROVED,
            'reviewed_by' => $superAdmin->id,
            'reviewed_at' => now(),
        ]);

        $merchantOwner = User::factory()->create([
            'name' => 'Amina Hassan',
            'email' => 'owner@dukaflow.test',
            'user_type' => User::TYPE_MERCHANT,
            'merchant_id' => $merchant->id,
            'phone' => $merchant->phone,
        ]);
        $merchantOwner->assignRole('merchant_owner');
    }
}
