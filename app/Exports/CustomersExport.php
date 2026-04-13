<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Customer::with('user')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Tipe',
            'No. Identitas',
            'No. Telp',
            'Pekerjaan',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat',
            'AO Pendamping',
            'Tanggal Terdaftar',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            ucfirst($customer->type),
            $customer->identity_number,
            $customer->phone_number,
            $customer->job ?? '-',
            $customer->pob,
            $customer->dob,
            $customer->address,
            $customer->user->name ?? '-',
            $customer->created_at->format('d/m/Y H:i'),
        ];
    }
}
