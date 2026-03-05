<?php

namespace Database\Seeders;

use App\Models\Collateral;
use App\Models\Customer;
use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Database\Seeder;

class CollateralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create a Test User (AO) if not exists
        $user = User::firstOrCreate(
            ['email' => 'ao_test@example.com'],
            [
                'name' => 'AO Test User',
                'username' => 'aotest',
                'password' => bcrypt('password'),
                'role' => 'ao',
            ]
        );

        // 2. Create a Test Customer
        $customer = Customer::create([
            'name' => 'Budi Santoso (Collateral Test)',
            'type' => 'Perorangan',
            'identity_number' => '320101' . rand(1000000000, 9999999999), 
            'phone_number' => '081234567890',
            'address' => 'Jl. Merdeka No. 45, Jakarta',
            'pob' => 'Jakarta',
            'dob' => '1980-01-01',
            'gender' => 'Laki-laki',
            'marital_status' => 'Menikah',
            'mother_name' => 'Siti Aminah',
            'relation' => 'Nasabah Lama',
        ]);

        // 3. Create a Dummy Evaluation for this Customer
        $evaluation = Evaluation::create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'application_id' => 'EVAL/TEST/' . date('Y') . '/' . rand(1000, 9999),
            'evaluation_date' => now(),
            'loan_scheme' => 'Kredit Modal Kerja',
            'loan_type' => 'Installment',
            'loan_purpose' => 'Modal Usaha',
            'loan_amount' => 100000000, 
            'installment_proposed_term' => 12,
            'installment_proposed_rate' => 1.5,
            'customer_profile' => 'Pedagang',
            'economic_sector' => 'Perdagangan',
            'economic_sector_code' => 'P01',
            'employment_status' => 'Wiraswasta',
        ]);

        $evaluation2 = Evaluation::create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'application_id' => 'EVAL/TEST/' . date('Y') . '/' . rand(1000, 9999),
            'evaluation_date' => now()->subMonths(6),
            'loan_scheme' => 'Kredit Investasi',
            'loan_type' => 'Installment',
            'loan_purpose' => 'Pembelian Alat',
            'loan_amount' => 50000000, 
            'installment_proposed_term' => 24,
            'installment_proposed_rate' => 1.2,
            'customer_profile' => 'Pedagang',
            'economic_sector' => 'Perdagangan',
            'economic_sector_code' => 'P01',
            'employment_status' => 'Wiraswasta',
        ]);

        // 4. Seed 10 Certificates
        for ($i = 0; $i < 10; $i++) {
            Collateral::create([
                'evaluation_id' => $evaluation2->id, // Attach to the older evaluation
                'type' => 'certificate',
                'owner_name' => $customer->name,
                'proof_type' => 'SHM',
                'proof_number' => '12.03.05.02.1.' . rand(10000, 99999),
                'market_value' => rand(500, 2000) * 1000000, // 500jt - 2M
                'bank_value' => rand(400, 1500) * 1000000,
                'location_address' => 'Jl. Raya Bogor KM ' . rand(20, 50),
                'property_surface_area' => rand(60, 200),
                'property_building_area' => rand(36, 150),
                'latitude' => '-6.' . rand(100000, 999999),
                'longitude' => '106.' . rand(100000, 999999),
            ]);
        }

        // 5. Seed 10 Vehicles
        $brands = ['Honda', 'Toyota', 'Suzuki', 'Yamaha', 'Kawasaki'];
        $models = ['Beat', 'Vario', 'Avanza', 'Xenia', 'NMax', 'PCX', 'Jazz'];
        
        for ($i = 0; $i < 10; $i++) {
            $brand = $brands[array_rand($brands)];
            $model = $models[array_rand($models)];
            
            Collateral::create([
                'evaluation_id' => $evaluation2->id,
                'type' => 'vehicle',
                'owner_name' => $customer->name,
                'proof_type' => 'BPKB',
                'proof_number' => 'M-' . rand(1000000, 9999999),
                'market_value' => rand(10, 150) * 1000000, // 10jt - 150jt
                'bank_value' => rand(8, 120) * 1000000,
                'vehicle_brand' => $brand,
                'vehicle_year' => rand(2015, 2024),
                'vehicle_plate_number' => 'B ' . rand(1000, 9999) . ' ' . chr(rand(65, 90)) . chr(rand(65, 90)),
                'vehicle_color' => ['Hitam', 'Putih', 'Merah', 'Biru', 'Silver'][rand(0, 4)],
                'vehicle_frame_number' => 'MH1' . strtoupper(substr(md5(rand()), 0, 14)),
                'vehicle_engine_number' => 'J' . strtoupper(substr(md5(rand()), 0, 10)),
                'location_address' => $customer->address,
            ]);
        }
    }
}
