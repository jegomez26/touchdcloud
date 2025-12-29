<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Provider;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first provider or create one if none exists
        $provider = Provider::first();
        if (!$provider) {
            $provider = Provider::create([
                'business_name' => 'Test Provider',
                'contact_email' => 'test@provider.com',
                'phone' => '1234567890',
                'address' => '123 Test Street',
                'suburb' => 'Test Suburb',
                'state' => 'VIC',
                'post_code' => '3000',
                'abn' => '12345678901',
                'ndis_registration_number' => 'NDIS123456',
                'status' => 'verified',
            ]);
        }

        // Create sample properties
        $properties = [
            [
                'provider_id' => $provider->id,
                'title' => 'Modern Home Oasis',
                'description' => 'A beautiful modern home designed with accessibility in mind. Features spacious rooms, wheelchair accessibility, and modern amenities.',
                'type' => 'High Physical Support',
                'address' => '123 Main Street',
                'suburb' => 'Richmond',
                'state' => 'VIC',
                'post_code' => '3121',
                'num_bedrooms' => 3,
                'num_bathrooms' => 2,
                'rent_per_week' => 450.00,
                'is_available_for_hm' => true,
                'amenities' => json_encode(['Wheelchair Accessible', 'Air Conditioning', 'Heating', 'Wi-Fi', 'Parking']),
                'photos' => json_encode([]),
                'status' => 'available',
                'total_vacancies' => 2,
                'current_occupancy' => 1,
            ],
            [
                'provider_id' => $provider->id,
                'title' => 'Serene Suburban Dwelling',
                'description' => 'A peaceful suburban home perfect for independent living. Close to amenities and public transport.',
                'type' => 'Improved Livability',
                'address' => '456 Oak Avenue',
                'suburb' => 'Toowoomba',
                'state' => 'QLD',
                'post_code' => '4350',
                'num_bedrooms' => 4,
                'num_bathrooms' => 3,
                'rent_per_week' => 380.00,
                'is_available_for_hm' => true,
                'amenities' => json_encode(['Garden Access', 'Pet Friendly', 'Laundry Facilities', 'Parking']),
                'photos' => json_encode([]),
                'status' => 'available',
                'total_vacancies' => 3,
                'current_occupancy' => 1,
            ],
            [
                'provider_id' => $provider->id,
                'title' => 'City View Apartment',
                'description' => 'Modern apartment with stunning city views. Fully accessible with elevator access.',
                'type' => 'Fully Accessible',
                'address' => '789 City Road',
                'suburb' => 'Parramatta',
                'state' => 'NSW',
                'post_code' => '2150',
                'num_bedrooms' => 2,
                'num_bathrooms' => 1,
                'rent_per_week' => 520.00,
                'is_available_for_hm' => true,
                'amenities' => json_encode(['Wheelchair Accessible', 'Air Conditioning', 'Furnished', 'Wi-Fi']),
                'photos' => json_encode([]),
                'status' => 'available',
                'total_vacancies' => 1,
                'current_occupancy' => 1,
            ],
            [
                'provider_id' => $provider->id,
                'title' => 'Coastal Retreat Villa',
                'description' => 'Beautiful coastal villa with ocean views. Perfect for those who love the beach lifestyle.',
                'type' => 'Robust',
                'address' => '321 Beach Road',
                'suburb' => 'Fremantle',
                'state' => 'WA',
                'post_code' => '6160',
                'num_bedrooms' => 3,
                'num_bathrooms' => 2,
                'rent_per_week' => 480.00,
                'is_available_for_hm' => true,
                'amenities' => json_encode(['Garden Access', 'Air Conditioning', 'Heating', 'Parking']),
                'photos' => json_encode([]),
                'status' => 'available',
                'total_vacancies' => 2,
                'current_occupancy' => 0,
            ],
        ];

        foreach ($properties as $propertyData) {
            Property::create($propertyData);
        }
    }
}
