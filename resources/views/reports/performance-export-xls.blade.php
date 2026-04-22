<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Kunjungan</th>
                <th>Jam Kunjungan</th>
                <th>Nama Petugas</th>
                <th>Nama Nasabah</th>
                <th>Alamat</th>
                <th>Kol</th>
                <th>Hasil Penagihan</th>
                <th>Jumlah Bayar (Penagihan Langsung)</th>
                <th>Tanggal Janji Bayar</th>
                <th>Status Janji Terpenuhi</th>
                <th>Tanggal Realisasi Bayar</th>
                <th>Jumlah Realisasi Bayar</th>
                <th>Kondisi Saat Ini</th>
                <th>Rencana Penyelesaian</th>
                <th>Latitude</th>
                <th>Longitude</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visits as $visit)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $visit->created_at->format('Y-m-d') }}</td>
                    <td>{{ $visit->created_at->format('H:i:s') }}</td>
                    <td>{{ $visit->user->name ?? 'N/A' }} ({{ $visit->user->code ?? 'N/A' }})</td>
                    <td>{{ $visit->customer->name ?? $visit->customer_name }}</td>
                    <td>{{ $visit->customer->address ?? $visit->address }}</td>
                    <td>{{ $visit->kolektibilitas }}</td>
                    <td>{{ $visit->hasil_penagihan }}</td>
                    <td>{{ $visit->jumlah_bayar }}</td>
                    <td>{{ $visit->tanggal_janji_bayar }}</td>
                    <td>{{ $visit->janji_bayar_fulfilled ? 'Ya' : 'Tidak' }}</td>
                    <td>{{ $visit->janji_bayar_fulfilled_at }}</td>
                    <td>{{ $visit->jumlah_bayar_fulfilled }}</td>
                    <td>{{ strip_tags($visit->kondisi_saat_ini) }}</td>
                    <td>{{ strip_tags($visit->rencana_penyelesaian) }}</td>
                    <td>{{ $visit->latitude }}</td>
                    <td>{{ $visit->longitude }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>