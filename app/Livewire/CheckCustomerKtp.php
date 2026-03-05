<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;

class CheckCustomerKtp extends Component
{
    public $no_id;

    public function mount($no_id = null)
    {
        $this->no_id = $no_id;
    }

    public function updatedNoId($value)
    {
        // Ensure only numbers are processed if needed, but requirements say "16 digit ktp number"
        // Let's just check length and existence.

        if (strlen($value) === 16) {
            $existingCustomer = Customer::where('identity_number', $value)->first();

            if ($existingCustomer) {
                $this->dispatch('swal:modal', [
                    'icon' => 'warning',
                    'title' => 'Caution',
                    'text' => 'No KTP terdaftar atas nama ' . $existingCustomer->name,
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.check-customer-ktp');
    }
}
