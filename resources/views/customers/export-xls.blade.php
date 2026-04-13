<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    @verbatim
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Register Debitur</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    @endverbatim
    <style>
        td, th {
            mso-number-format: "\@";
            border: 1px solid #000;
            padding: 4px 8px;
            font-family: Arial, sans-serif;
            font-size: 11px;
            vertical-align: top;
        }
        th {
            background-color: #d1d5db;
            font-weight: bold;
            text-align: center;
        }
        .header-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            border: none;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="11" class="header-title">REGISTER DEBITUR</td>
        </tr>
        <tr>
            <td colspan="11" style="text-align: center; border: none;">Dicetak pada: {{ date('d/m/Y H:i') }}</td>
        </tr>
        <tr><td colspan="11" style="border: none;">&nbsp;</td></tr>

        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Nama</th>
                <th>Tipe</th>
                <th>No. Identitas</th>
                <th>No. Telp</th>
                <th>Pekerjaan</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>Alamat</th>
                <th>Tanggal Terdaftar</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($customers as $customer)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ ucfirst($customer->type) }}</td>
                    <td>{{ $customer->identity_number }}</td>
                    <td>{{ $customer->phone_number }}</td>
                    <td>{{ $customer->job ?? '-' }}</td>
                    <td>{{ $customer->pob }}</td>
                    <td>{{ \Carbon\Carbon::parse($customer->dob)->format('d/m/Y') }}</td>
                    <td>{{ $customer->address }}</td>
                    <td>{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
