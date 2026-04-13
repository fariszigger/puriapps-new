<!DOCTYPE html>
<html>
<head>
    <title>Register Debitur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 20px;
            font-style: italic;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0; text-transform: uppercase;">Register Debitur</h2>
        <p style="margin: 5px 0;">Dicetak pada: {{ date('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama</th>
                <th style="width: 60px;">Tipe</th>
                <th style="width: 100px;">No. Identitas</th>
                <th style="width: 80px;">No. Telp</th>
                <th>Pekerjaan</th>
                <th>Alamat</th>
                <th>AO Pendamping</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $index => $customer)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ ucfirst($customer->type) }}</td>
                    <td>{{ $customer->identity_number }}</td>
                    <td>{{ $customer->phone_number }}</td>
                    <td>{{ $customer->job ?? '-' }}</td>
                    <td>{{ $customer->address }}</td>
                    <td>{{ $customer->user->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Debitur: {{ $customers->count() }} orang</p>
    </div>
</body>
</html>
