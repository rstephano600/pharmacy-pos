<?php

namespace Database\Seeders;

use App\Models\Pharmacy;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PharmacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Method 1: Create specific, known pharmacies for testing
        $knownPharmacies = [
            [
                'name' => 'DODOMA - Central City Pharmacy',
                'license_number' => 'PH-CTR-001',
                'country' => 'Tanzani',
                'region' => 'Dodoma',
                'district' => 'Ihumwa',
                'location' => '123 Main Street, Dodoma, Tanzania',
                'working_hours' => 'Mon-Sun: 7:00 AM - 11:00 PM',
                'contact_phone' => '+255 0657 856 790',
                'contact_email' => 'info@centralpharmacy.com',
                'license_expiry' => '2026-12-31',
                'is_active' => true,
                // 'pharmacy_logo' will use the default
            ],
            [
                'name' => 'MWANZA - Green Hills Medicare',
                'license_number' => 'PH-GRN-002',
                'country' => 'Tanzania',
                'region' => 'Mwanza',
                'district' => 'Rockcity',
                'location' => 'Mwanza Street, Rockcity , Tanzania',
                'working_hours' => 'Mon-Fri: 8:00 AM - 9:00 PM, Sat: 9:00 AM - 6:00 PM',
                'contact_phone' => '+255 700 123 456',
                'contact_email' => 'mwanza@pharmacy.com',
                'license_expiry' => '2025-06-15',
                'is_active' => true,
            ],
            [
                'name' => 'KIGOMA - Green Hills Medicare',
                'license_number' => 'PH-GRN-003',
                'country' => 'Tanzania',
                'region' => 'Kigoma',
                'district' => 'Ujiji',
                'location' => '124 Ujiji, Kigoma, Tanzania',
                'working_hours' => 'Mon-Fri: 8:00 AM - 9:00 PM, Sat: 9:00 AM - 6:00 PM',
                'contact_phone' => '+255 700 123 457',
                'contact_email' => 'kigoma@greenhillsmedicare.com',
                'license_expiry' => '2025-06-15',
                'is_active' => true,
            ],
            [
                'name' => 'Green Hills Medicare',
                'license_number' => 'PH-GRN-002',
                'country' => 'Kenya',
                'region' => 'Nairobi',
                'district' => 'Westlands',
                'location' => 'Mama Ngina Street, Nairobi, Kenya',
                'working_hours' => 'Mon-Fri: 8:00 AM - 9:00 PM, Sat: 9:00 AM - 6:00 PM',
                'contact_phone' => '+256 700 123 458',
                'contact_email' => 'orders@greenhillsmedicare.com',
                'license_expiry' => '2025-06-15',
                'is_active' => true,
            ],
        ];

        foreach ($knownPharmacies as $pharmacyData) {
            Pharmacy::firstOrCreate(
                ['license_number' => $pharmacyData['license_number']], // Check for uniqueness
                $pharmacyData
            );
        }

        // Method 2: Use the Factory to create a large number of fake pharmacies
        // Uncomment the next line to generate 50 fake pharmacies
        // Pharmacy::factory()->count(50)->create();

        // Method 3: Create a few specific ones AND a few fake ones (recommended)
        Pharmacy::factory()->count(10)->create(); // Creates 10 additional fake pharmacies
    }
}