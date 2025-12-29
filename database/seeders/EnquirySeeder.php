<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enquiry;
use App\Models\Property;

class EnquirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get properties that are available for HM
        $properties = Property::where('status', 'available')
                            ->where('is_available_for_hm', true)
                            ->get();

        if ($properties->isEmpty()) {
            $this->command->info('No available properties found. Please run PropertySeeder first.');
            return;
        }

        // Create sample enquiries
        $enquiries = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@email.com',
                'phone' => '0401 234 567',
                'message' => 'Hi, I\'m interested in this accommodation. I\'m looking for a place that\'s wheelchair accessible and close to public transport. Could you please provide more information about the support services available?',
                'status' => 'pending',
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@email.com',
                'phone' => '0402 345 678',
                'message' => 'Hello, I would like to enquire about this property. I\'m a NDIS participant and looking for shared accommodation. What are the current housemates like?',
                'status' => 'tended',
                'provider_notes' => 'Called Michael back, very interested. Scheduled property viewing for next week.',
                'tended_at' => now()->subDays(2),
            ],
            [
                'name' => 'Emma Williams',
                'email' => 'emma.williams@email.com',
                'phone' => null,
                'message' => 'I\'m interested in this accommodation for my son who has autism. He needs a quiet environment and access to support workers. Is this property suitable?',
                'status' => 'pending',
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.brown@email.com',
                'phone' => '0403 456 789',
                'message' => 'Hi there, I\'m looking for accommodation in this area. I work part-time and need somewhere that allows pets. Does this property allow pets?',
                'status' => 'closed',
                'provider_notes' => 'Property doesn\'t allow pets. Referred to another property that does.',
                'tended_at' => now()->subDays(5),
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@email.com',
                'phone' => '0404 567 890',
                'message' => 'I\'m a support coordinator looking for accommodation for my client. They need 24/7 support and wheelchair access. Is this available?',
                'status' => 'tended',
                'provider_notes' => 'Met with Lisa and her client. Property is suitable, waiting for NDIS plan approval.',
                'tended_at' => now()->subDays(1),
            ],
            [
                'name' => 'James Taylor',
                'email' => 'james.taylor@email.com',
                'phone' => '0405 678 901',
                'message' => 'Hello, I\'m interested in this accommodation. I have a physical disability and need ground floor access. Can you confirm this is available?',
                'status' => 'pending',
            ],
        ];

        foreach ($enquiries as $enquiryData) {
            // Randomly assign to a property
            $property = $properties->random();
            
            Enquiry::create([
                'property_id' => $property->id,
                'name' => $enquiryData['name'],
                'email' => $enquiryData['email'],
                'phone' => $enquiryData['phone'],
                'message' => $enquiryData['message'],
                'status' => $enquiryData['status'],
                'provider_notes' => $enquiryData['provider_notes'] ?? null,
                'tended_at' => $enquiryData['tended_at'] ?? null,
                'created_at' => now()->subDays(rand(1, 30)), // Random creation date within last 30 days
            ]);
        }

        $this->command->info('Created ' . count($enquiries) . ' sample enquiries.');
    }
}