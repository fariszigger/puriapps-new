<?php

namespace Tests\Feature;

use App\Livewire\CheckCustomerKtp;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CheckCustomerKtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_successfully()
    {
        Livewire::test(CheckCustomerKtp::class)
            ->assertStatus(200);
    }

    public function test_it_does_not_alert_if_ktp_is_not_16_digits()
    {
        Livewire::test(CheckCustomerKtp::class)
            ->set('no_id', '12345')
            ->assertNotDispatched('swal:modal');
    }

    public function test_it_alerts_if_ktp_exists()
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'John Doe',
            'type' => 'Perorangan',
            'identity_number' => '1234567890123456',
            'phone_number' => '08123456789',
            'pob' => 'Jakarta',
            'dob' => '1990-01-01',
            'address' => 'Jl. Test',
            'user_id' => $user->id,
        ]);

        Livewire::test(CheckCustomerKtp::class)
            ->set('no_id', '1234567890123456')
            ->assertDispatched('swal:modal');
    }

    public function test_it_does_not_alert_if_ktp_does_not_exist()
    {
        Livewire::test(CheckCustomerKtp::class)
            ->set('no_id', '1616161616161616')
            ->assertNotDispatched('swal:modal');
    }
}
