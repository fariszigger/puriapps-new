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
        $admin = User::firstOrCreate(
            ['email' => 'admin@bprpuri.com'],
            [
                'username' => 'admin',
                'name' => 'Faris Muhammad',
                'password' => Hash::make('admin1234'),
                'code' => 'FR',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );
        $admin->assignRole('Admin');

        // User AO AR
        $ao_ar = User::firstOrCreate(
            ['email' => 'ao_ar@bprpuri.com'],
            [
                'username' => 'ar1234',
                'name' => 'Moch. Arif Priyadi',
                'password' => Hash::make('ar123456*'),
                'code' => 'AR',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );
        $ao_ar->assignRole('Kabag');

        // User AO BW
        $ao_bw = User::firstOrCreate(
            ['email' => 'ao_bw@bprpuri.com'],
            [
                'username' => 'bw777',
                'name' => 'Kohar Hari Wibowo',
                'password' => Hash::make('bw123456*'),
                'code' => 'BW',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );
        $ao_bw->assignRole('AO');

        // User AO KS
        $ao_ks = User::firstOrCreate(
            ['email' => 'ao_ks@bprpuri.com'],
            [
                'username' => 'ks4467',
                'name' => 'Kusmargianto',
                'password' => Hash::make('123123ks*'),
                'code' => 'KS',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );
        $ao_ks->assignRole('AO');

        // User AO SH
        $ao_sh = User::firstOrCreate(
            ['email' => 'ao_sh@bprpuri.com'],
            [
                'username' => 'sh999',
                'name' => 'Slamet Hariyadi',
                'password' => Hash::make('999sh*'),
                'code' => 'SH',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );
        $ao_sh->assignRole('AO');

        // User AO MA
        $ao_ma = User::firstOrCreate(
            ['email' => 'ao_ma@bprpuri.com'],
            [
                'username' => 'asrory06',
                'name' => 'Mohammad Asrory',
                'password' => Hash::make('ma123123*'),
                'code' => 'MA',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );
        $ao_ma->assignRole('AO');

        // User AO AP
        $ao_ap = User::firstOrCreate(
            ['email' => 'ao_ap@bprpuri.com'],
            [
                'username' => 'ap_mojosari1',
                'name' => 'Anggradia Pratama',
                'password' => Hash::make('mojosari123*'),
                'code' => 'AP',
                'office_branch' => 'Kantor Kas Mojosari',
                'status' => 'active',
            ]
        );
        $ao_ap->assignRole('AO');

        // User AO HS
        $ao_hs = User::firstOrCreate(
            ['email' => 'ao_hs@bprpuri.com'],
            [
                'username' => 'hendrik111*',
                'name' => 'Hendrik Sulistiono',
                'password' => Hash::make('hendrik_mojosari1*'),
                'code' => 'HS',
                'office_branch' => 'Kantor Kas Mojosari',
                'status' => 'active',
            ]
        );
        $ao_hs->assignRole('AO');

        // User AO GI
        $ao_gi = User::firstOrCreate(
            ['email' => 'ao_gi@bprpuri.com'],
            [
                'username' => 'gi8765*',
                'name' => 'Gilang Isbiantoro Putra',
                'password' => Hash::make('gi7777*'),
                'code' => 'GI',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );
        $ao_gi->assignRole('AO');

        // User AO RN
        $ao_rn = User::firstOrCreate(
            ['email' => 'ao_rn@bprpuri.com'],
            [
                'username' => 'reno555*',
                'name' => 'Alfilreno Keysha Bima Muhammad',
                'password' => Hash::make('12309876*'),
                'code' => 'RN',
                'office_branch' => 'Kantor Pusat',
                'status' => 'active',
            ]
        );
        $ao_rn->assignRole('AO');
    }
}
