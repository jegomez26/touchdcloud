<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NdisBusinessSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('ndis_businesses')->insert([
            [
                'business_name' => 'Support Worker Co Pty Ltd',
                'abn' => '73608293910',
                'services_offered' => json_encode([
                    'Support Coordination',
                    'Assistance with Daily Living',
                    'Community Participation',
                    'Travel and Transport Assistance',
                    'Household Tasks'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'SDA Services Plus Australia',
                'abn' => '52170635927',
                'services_offered' => json_encode([
                    'Specialist Disability Accommodation (SDA)'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'i’ara Support Coordination',
                'abn' => '56613776793',
                'services_offered' => json_encode([
                    'Support Coordination',
                    'Specialist Disability Accommodation (SDA)',
                    'Supported Independent Living (SIL)',
                    'Assistive Technology',
                    'Therapeutic Supports'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Enable Life Care',
                'abn' => '71123456789',
                'services_offered' => json_encode([
                    'Plan Management',
                    'Therapeutic Supports',
                    'Employment Support'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'business_name' => 'Bright Futures Disability Services',
                'abn' => '60111222333',
                'services_offered' => json_encode([
                    'Early Childhood Supports',
                    'Assistance with Daily Living',
                    'Community Participation'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
