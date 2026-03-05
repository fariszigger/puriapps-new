<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        session()->flash('success', 'Customer deleted successfully.');
    }

    public function render()
    {
        $query = Customer::query();

        $query->when(!empty($this->search), function (\Illuminate\Database\Eloquent\Builder $query) {
            $query->where(function (\Illuminate\Database\Eloquent\Builder $q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                        ->orWhere('address', 'like', '%' . $this->search . '%');
                }
                );
            });

       return view('livewire.customer-table', [
        'customers' => $query->orderBy('id', 'desc')->paginate($this->perPage),
    ]);
    }
}
