<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SupportCategory;

class SupportCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technical Issues',
                'slug' => 'technical-issues',
                'description' => 'Problems with the platform, bugs, and technical difficulties',
                'color' => '#EF4444',
                'icon' => 'fas fa-bug',
                'sort_order' => 1,
            ],
            [
                'name' => 'Account & Billing',
                'slug' => 'account-billing',
                'description' => 'Account management, subscription, and billing questions',
                'color' => '#3B82F6',
                'icon' => 'fas fa-credit-card',
                'sort_order' => 2,
            ],
            [
                'name' => 'Feature Requests',
                'slug' => 'feature-requests',
                'description' => 'Suggestions for new features and improvements',
                'color' => '#10B981',
                'icon' => 'fas fa-lightbulb',
                'sort_order' => 3,
            ],
            [
                'name' => 'General Inquiries',
                'slug' => 'general-inquiries',
                'description' => 'General questions and information requests',
                'color' => '#6B7280',
                'icon' => 'fas fa-question-circle',
                'sort_order' => 4,
            ],
            [
                'name' => 'Provider Support',
                'slug' => 'provider-support',
                'description' => 'Support specifically for service providers',
                'color' => '#8B5CF6',
                'icon' => 'fas fa-hospital',
                'sort_order' => 5,
            ],
            [
                'name' => 'Participant Support',
                'slug' => 'participant-support',
                'description' => 'Support specifically for NDIS participants',
                'color' => '#F59E0B',
                'icon' => 'fas fa-user-heart',
                'sort_order' => 6,
            ],
            [
                'name' => 'Support Coordinator Help',
                'slug' => 'support-coordinator-help',
                'description' => 'Support for NDIS support coordinators',
                'color' => '#EC4899',
                'icon' => 'fas fa-user-tie',
                'sort_order' => 7,
            ],
            [
                'name' => 'Complaints',
                'slug' => 'complaints',
                'description' => 'Formal complaints and grievances',
                'color' => '#DC2626',
                'icon' => 'fas fa-exclamation-triangle',
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $categoryData) {
            SupportCategory::create($categoryData);
            $this->command->info("Created category: {$categoryData['name']}");
        }

        $this->command->info('Support categories seeded successfully!');
    }
}
