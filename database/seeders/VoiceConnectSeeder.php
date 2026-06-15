<?php

namespace Database\Seeders;

use App\Models\CoinPackage;
use App\Models\Gift;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VoiceConnectSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@voiceconnect.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::SuperAdmin,
                'is_profile_complete' => true,
                'email_verified_at' => now(),
            ]
        );

        $packages = [
            ['name' => 'Starter Pack', 'coins' => 100, 'price' => 49.00, 'sort_order' => 1],
            ['name' => 'Popular Pack', 'coins' => 500, 'price' => 199.00, 'discount_percent' => 10, 'sort_order' => 2],
            ['name' => 'Mega Pack', 'coins' => 1200, 'price' => 399.00, 'discount_percent' => 20, 'is_promotional' => true, 'sort_order' => 3],
        ];

        foreach ($packages as $package) {
            CoinPackage::query()->firstOrCreate(
                ['name' => $package['name']],
                $package,
            );
        }

        $gifts = [
            ['name' => 'Rose', 'slug' => 'rose', 'coin_cost' => 10, 'reward_value' => 5, 'sort_order' => 1],
            ['name' => 'Heart', 'slug' => 'heart', 'coin_cost' => 50, 'reward_value' => 25, 'sort_order' => 2],
            ['name' => 'Crown', 'slug' => 'crown', 'coin_cost' => 500, 'reward_value' => 250, 'sort_order' => 3],
        ];

        foreach ($gifts as $gift) {
            Gift::query()->firstOrCreate(
                ['slug' => $gift['slug']],
                $gift,
            );
        }
    }
}
