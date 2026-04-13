<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    @verbatim
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Register Pencairan</x:Name>
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
        .number {
            mso-number-format: "#,##0";
            text-align: right;
        }
        .decimal {
            mso-number-format: "#,##0.00";
            text-align: right;
        }
        .date-cell {
            mso-number-format: "dd\/mm\/yyyy";
            text-align: center;
        }
        .header-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            border: none;
        }
        .header-subtitle {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            border: none;
        }
        .total-row td, .total-row th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table>
        {{-- Title Rows --}}
        <tr>
            <td colspan="12" class="header-title">REGISTER PENCAIRAN KREDIT</td>
        </tr>
        <tr>
            <td colspan="12" class="header-subtitle">
                @if($viewMode === 'yearly')
                    Tahun: {{ date('Y', strtotime($filterMonth)) }}
                @elseif($viewMode === 'period')
                    Periode: {{ \Carbon\Carbon::parse($filterMonth . '-01')->translatedFormat('F Y') }} s/d {{ \Carbon\Carbon::parse($filterMonthEnd . '-01')->translatedFormat('F Y') }}
                @else
                    Bulan: {{ \Carbon\Carbon::parse($filterMonth . '-01')->translatedFormat('F Y') }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="12" class="header-subtitle" style="font-size: 9px; font-style: italic; color: #666; border: none;">
                Dicetak: {{ now()->format('d M Y H:i') }}
            </td>
        </tr>
        <tr><td colspan="12" style="border: none;">&nbsp;</td></tr>

        {{-- Table Header --}}
        <tr>
            <th>No</th>
            <th>Kode AO</th>
            <th>Nama AO</th>
            <th>No. SPK</th>
            <th>Tanggal Pencairan</th>
            <th>Nama Nasabah</th>
            <th>Alamat</th>
            <th>Jumlah (Rp)</th>
            <th>Jangka Waktu (Bln)</th>
            <th>Suku Bunga (%)</th>
            <th>Jenis Pinjaman</th>
            <th>Angsuran (Rp)</th>
            <th>Status</th>
        </tr>

        {{-- Data Rows --}}
        @php $no = 1; @endphp
        @foreach($disbursements as $item)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $item->user->code ?? '-' }}</td>
                <td>{{ $item->user->name ?? '-' }}</td>
                <td>{{ $item->nomor_spk ?? '-' }}</td>
                <td class="date-cell">{{ \Carbon\Carbon::parse($item->disbursement_date)->format('d/m/Y') }}</td>
                <td>{{ $item->customer_name }}</td>
                <td>{{ $item->address ?? '-' }}</td>
                <td class="number">{{ $item->amount }}</td>
                <td style="text-align: center;">{{ $item->jangka_waktu }}</td>
                <td class="decimal">{{ $item->suku_bunga }}</td>
                <td>{{ $item->jenis_pinjaman ?? '-' }}</td>
                <td class="number">{{ $item->angsuran }}</td>
                <td style="text-align: center;">{{ ucfirst($item->status ?? 'aktif') }}</td>
            </tr>
        @endforeach

        {{-- Empty row separator --}}
        <tr><td colspan="13" style="border: none;">&nbsp;</td></tr>

        {{-- Summary Row --}}
        <tr class="total-row">
            <th colspan="7" style="text-align: right;">TOTAL PENCAIRAN ({{ $disbursements->count() }} record)</th>
            <td class="number" style="font-weight: bold; background-color: #f3f4f6;">{{ $disbursements->sum('amount') }}</td>
            <td colspan="3" style="border: none;"></td>
            <td class="number" style="font-weight: bold; background-color: #f3f4f6;">{{ $disbursements->sum('angsuran') }}</td>
            <td style="border: none;"></td>
        </tr>
    </table>
</body>
</html>
