---
description: How to create a new index (listing) page following the existing patterns in puriapps-new
---

# Create a New Index Page

Follow the patterns established by `customers.index`, `evaluations.index`, and `customer-visits.index`.

## Files to Create/Modify

### 1. Model (`app/Models/{ModelName}.php`)
- Create an Eloquent model if it doesn't exist.
- Add relationships (`belongsTo`, `hasMany`, etc.), `$fillable`, `$casts`, optional `SoftDeletes`.

### 2. Controller (`app/Http/Controllers/{ModelName}Controller.php`)
- Standard resource controller with `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.
- `index` method returns the Blade view (the Livewire table handles data fetching).
- Apply Spatie permission checks using `$this->authorize()` or `@can` in views.
- For role-scoped data, check `auth()->user()->can('view all data')` — if false, scope to `user_id`.

### 3. Livewire Table Component (`app/Livewire/{ModelName}Table.php`)
Follow the pattern from `CustomerVisitTable.php`:

```php
<?php

namespace App\Livewire;

use App\Models\{ModelName};
use Livewire\Component;
use Livewire\WithPagination;

class {ModelName}Table extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function delete($id)
    {
        ${model} = {ModelName}::findOrFail($id);
        ${model}->delete();
        session()->flash('success', '{Label} berhasil dihapus.');
    }

    public function render()
    {
        $query = {ModelName}::with([/* relationships */]);

        // Role scoping: AO sees only their own
        if (!auth()->user()->can('view all data')) {
            $query->where('user_id', auth()->id());
        }

        $query->when(!empty($this->search), function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
                // Add more searchable fields as needed
            });
        });

        return view('livewire.{model-name}-table', [
            '{items}' => $query->orderBy('id', 'desc')->paginate($this->perPage),
        ]);
    }
}
```

### 4. Livewire Table Blade (`resources/views/livewire/{model-name}-table.blade.php`)
- Include search input, per-page selector, table with columns, pagination.
- Use `wire:model.live.debounce.300ms="search"` for search.
- Use `wire:model.live="perPage"` for per-page selector.
- Action buttons: Edit, Delete (with SweetAlert `confirmDelete()`).
- Use `@can('edit {resource}')`, `@can('delete {resource}')` for permission gating.

### 5. Index Blade View (`resources/views/{model-name}/index.blade.php`)
Follow the pattern:

```blade
@extends('layouts.dashboard')

@section('title', 'Daftar {Label}')

@section('breadcrumb-items')
    <li class="inline-flex items-center">
        <div class="flex items-center">
            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 9 4-4-4-4" />
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Daftar {Label}</span>
        </div>
    </li>
@endsection

@section('content')
    <div class="w-full p-8 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl mt-8 mb-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Daftar {Label}</h1>
            <div class="flex items-center gap-2">
                @can('create {resource}')
                    <a href="{{ route('{route-prefix}.create') }}"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 transition-all shadow-lg hover:shadow-blue-500/30">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah {Label}
                    </a>
                @endcan
            </div>
        </div>

        <livewire:{livewire-component-name} />
    </div>
@endsection
```

### 6. Routes (`routes/web.php`)
Add inside the `middleware(['authentication'])` group:

```php
Route::resource('{route-prefix}', \App\Http\Controllers\{ModelName}Controller::class);
```

### 7. Dashboard Card (`resources/views/dashboard.blade.php`)
Add a card in the `<!-- Cards Grid -->` section following this pattern:

```blade
<a href="{{ route('{route-prefix}.index') }}"
    class="block p-6 bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl hover:bg-white/50 transition-all duration-300 transform hover:-translate-y-1 group">
    <div class="flex items-center justify-between mb-4">
        <h5 class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-{color}-700 transition-colors">
            Daftar {Label}</h5>
        <div class="p-3 bg-{color}-100/50 rounded-full text-{color}-600 group-hover:bg-{color}-200/50 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="{svg-icon-path}"></path>
            </svg>
        </div>
    </div>
    <p class="font-normal text-gray-700">Lihat dan tinjau {label lowercase}.</p>
</a>
```

### 8. Permissions (Spatie)
Create permissions for the new resource and assign to roles:

```php
// In a migration or seeder:
$permissions = ['view {resource}', 'create {resource}', 'edit {resource}', 'delete {resource}'];
foreach ($permissions as $p) {
    Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
}
// Assign to roles as needed
```

## Design Notes
- **Layout**: All pages extend `layouts.dashboard`.
- **Glass effect**: Use `bg-white/40 backdrop-blur-md rounded-xl border border-white/50 shadow-xl` on the main container.
- **Buttons**: Blue primary (`bg-blue-700`), with shadow hover effect (`shadow-lg hover:shadow-blue-500/30`).
- **SweetAlert**: Use `Swal.fire()` for delete/restore confirmations.
- **Direksi role**: Hide create/edit/delete buttons for read-only roles.
- **AO scoping**: AOs only see their own data (`where('user_id', auth()->id())`), admins/kabag see all.
