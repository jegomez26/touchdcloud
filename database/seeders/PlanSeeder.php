<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter Plan',
                'slug' => 'starter',
                'description' => 'Perfect for small providers getting started with participant matching.',
                'monthly_price' => 299.00,
                'yearly_price' => 2988.00,
                'participant_profile_limit' => 3,
                'accommodation_listing_limit' => 0,
                'features' => [
                    'Up to 3 participant profiles',
                    'Basic matching features',
                    'Email support',
                    'Monthly profile refresh'
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Growth Plan',
                'slug' => 'growth',
                'description' => 'Ideal for growing providers who need more capacity and accommodation listings.',
                'monthly_price' => 599.00,
                'yearly_price' => 5988.00,
                'participant_profile_limit' => 10,
                'accommodation_listing_limit' => 0,
                'features' => [
                    'Up to 10 participant profiles',
                    'Advanced matching filters',
                    'Priority support',
                    'Monthly profile refresh',
                    '14-day free trial'
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium Plan',
                'slug' => 'premium',
                'description' => 'For established providers who need maximum capacity and features.',
                'monthly_price' => 799.00,
                'yearly_price' => 7990.00,
                'participant_profile_limit' => 20,
                'accommodation_listing_limit' => 10,
                'features' => [
                    'Up to 20 participant profiles',
                    'Up to 10 accommodation listings',
                    'Advanced matching filters',
                    'Dedicated account manager',
                    'Custom onboarding',
                    'Featured placement',
                    '14-day free trial'
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            Plan::create($planData);
        }
    }
}