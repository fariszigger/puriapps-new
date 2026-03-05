<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::firstOrCreate(
            ['email' => 'admin@bprpuri.com'],
            [
                'username' => 'admin',
                'name' => 'Faris Muhammad',
                'password' => Hash::make('admin1234'),
                'role' => 'Admin',
                'code' => 'FR',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );

        // User AO AR
        User::firstOrCreate(
            ['email' => 'ao_ar@bprpuri.com'],
            [
                'username' => 'ar1234',
                'name' => 'Moch. Arif Priyadi',
                'password' => Hash::make('ar123456*'),
                'role' => 'Kabag',
                'code' => 'AR',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );

        // User AO BW
        User::firstOrCreate(
            ['email' => 'ao_bw@bprpuri.com'],
            [
                'username' => 'bw777',
                'name' => 'Kohar Hari Wibowo',
                'password' => Hash::make('bw123456*'),
                'role' => 'AO',
                'code' => 'BW',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );

        // User AO KS
        User::firstOrCreate(
            ['email' => 'ao_ks@bprpuri.com'],
            [
                'username' => 'ks4467',
                'name' => 'Kusmargianto',
                'password' => Hash::make('123123ks*'),
                'role' => 'AO',
                'code' => 'KS',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );

        // User AO SH
        User::firstOrCreate(
            ['email' => 'ao_sh@bprpuri.com'],
            [
                'username' => 'sh999',
                'name' => 'Slamet Hariyadi',
                'password' => Hash::make('999sh*'),
                'role' => 'AO',
                'code' => 'SH',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );

        // User AO MA
        User::firstOrCreate(
            ['email' => 'ao_ma@bprpuri.com'],
            [
                'username' => 'asrory06',
                'name' => 'Mohammad Asrory',
                'password' => Hash::make('ma123123*'),
                'role' => 'AO',
                'code' => 'MA',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );

        // User AO AP
        User::firstOrCreate(
            ['email' => 'ao_ap@bprpuri.com'],
            [
                'username' => 'ap_mojosari1',
                'name' => 'Anggradia Pratama',
                'password' => Hash::make('mojosari123*'),
                'role' => 'AO',
                'code' => 'AP',
                'office_branch' => 'Kantor Kas Mojosari',
                'status' => 'active',
            ]
        );

        // User AO HS
        User::firstOrCreate(
            ['email' => 'ao_hs@bprpuri.com'],
            [
                'username' => 'hendrik111*',
                'name' => 'Hendrik Sulistiono',
                'password' => Hash::make('hendrik_mojosari1*'),
                'role' => 'AO',
                'code' => 'HS',
                'office_branch' => 'Kantor Kas Mojosari',
                'status' => 'active',
            ]
        );

        // User AO GI
        User::firstOrCreate(
            ['email' => 'ao_gi@bprpuri.com'],
            [
                'username' => 'gi8765*',
                'name' => 'Gilang Isbiantoro Putra',
                'password' => Hash::make('gi7777*'),
                'role' => 'AO',
                'code' => 'GI',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );

        // User AO RN
        User::firstOrCreate(
            ['email' => 'ao_rn@bprpuri.com'],
            [
                'username' => 'reno555*',
                'name' => 'Alfilreno Keysha Bima Muhammad',
                'password' => Hash::make('12309876*'),
                'role' => 'AO',
                'code' => 'RN',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );
    }
}
