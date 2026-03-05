<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Data Debitur - {{ $customer->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            background: #e5e7eb;
            /* Gray background for screen */
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.3;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }

        .sheet {
            background: white;
            width: 210mm;
            min-height: 297mm;
            padding: 10mm 15mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            margin: 0 auto;
            position: relative;
        }



        /* Mobile Fix */
        @media screen and (max-width: 768px) {
            body {
                display: block;
                overflow-x: auto;
                padding: 10px;
                background: #e5e7eb;
            }

            .sheet {
                margin: 0 auto;
            }

            /* Adjust button position for mobile */
            .fixed.top-4.right-4 {
                top: auto;
                bottom: 20px;
                right: 20px;
            }
        }

        @media print {
            body {
                background: none;
                display: block;
                padding: 0;
                margin: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .sheet {
                box-shadow: none;
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 10mm 15mm;
                page-break-after: always;
            }

            .no-print {
                display: none !important;
            }

            /* Ensure page breaks work correctly */
            .page-break {
                page-break-before: always;
            }
        }

        .header {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0 0;
        }

        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 13pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        td {
            vertical-align: top;
            padding: 3px 5px;
        }

        .label {
            width: 35%;
            font-weight: bold;
        }

        .colon {
            width: 2%;
            text-align: center;
        }

        .value {
            width: 63%;
        }

        .photo-box {
            width: 3cm;
            height: 4cm;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .signatures {
            margin-top: 50px;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            text-align: center;
            width: 40%;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid black;
        }

        .page-footer {
            position: absolute;
            bottom: 10mm;
            right: 15mm;
            text-align: right;
            font-size: 9pt;
            color: gray;
        }
    </style>
</head>

<body>
    <!-- Print Button (Hidden when printing) -->
    <div class="fixed top-4 right-4 no-print print:hidden z-50">
        <button onclick="window.print()"
            class="bg-blue-600 text-white px-4 py-2 rounded shadow-lg hover:bg-blue-700 font-sans transition-all transform hover:scale-105">
            Cetak (Print)
        </button>
        <button onclick="window.close()"
            class="bg-gray-500 text-white px-4 py-2 rounded shadow-lg hover:bg-gray-600 font-sans ml-2 transition-all transform hover:scale-105">
            Tutup
        </button>
    </div>

    <div class="sheet">
        <div class="header">
            <h1>PT. BPR Puriseger Sentosa</h1>
            <h2>DATA DEBITUR / NASABAH</h2>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="width: 75%;">
                <table>
                    <tr>
                        <td class="label">Nama Lengkap</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $customer->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Nasabah</td>
                        <td class="colon">:</td>
                        <td class="value">{{ ucfirst($customer->type) }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIK / No. Identitas</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $customer->identity_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tempat, Tanggal Lahir</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $customer->pob }},
                            {{ \Carbon\Carbon::parse($customer->dob)->format('d-m-Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Kelamin</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $customer->gender }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pekerjaan</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $customer->job ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status Perkawinan</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $customer->marital_status }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pendidikan Terakhir</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $customer->education }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nama Ibu Kandung</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $customer->mother_name }}</td>
                    </tr>
                </table>
            </div>
            <div class="photo-box">
                @if($customer->photo_path)
                    <img src="{{ route('media.customers', ['type' => 'photos', 'filename' => basename($customer->photo_path)]) }}" alt="Foto Debitur">
                @else
                    <span>Foto 3x4</span>
                @endif
            </div>
        </div>

        <div class="section-title">KONTAK DAN ALAMAT</div>
        <table>
            <tr>
                <td class="label">Nomor Telepon / HP</td>
                <td class="colon">:</td>
                <td class="value">{{ $customer->phone_number }}</td>
            </tr>
            <tr>
                <td class="label">Alamat Lengkap</td>
                <td class="colon">:</td>
                <td class="value">
                    {{ $customer->address }}<br>
                    Desa/Kel: {{ $customer->village }}, Kec: {{ $customer->district }}<br>
                    Kab/Kota: {{ $customer->regency }}, Prov: {{ $customer->province }}
                </td>
            </tr>
            <tr>
                <td class="label">Kontak Darurat</td>
                <td class="colon">:</td>
                <td class="value">{{ $customer->emergency_contact ?? '-' }}</td>
            </tr>
        </table>

        @if($customer->spouse_name)
            <div class="section-title">DATA PASANGAN</div>
            <table>
                <tr>
                    <td class="label">Nama Pasangan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $customer->spouse_name }}</td>
                </tr>
                <tr>
                    <td class="label">NIK Pasangan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $customer->spouse_identity_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">TTL Pasangan</td>
                    <td class="colon">:</td>
                    <td class="value">
                        {{ $customer->spouse_pob ?? '-' }},
                        {{ $customer->spouse_dob ? \Carbon\Carbon::parse($customer->spouse_dob)->format('d-m-Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Pekerjaan Pasangan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $customer->spouse_job ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Pendidikan Terakhir Pasangan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $customer->spouse_education ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Nomor Telepon Pasangan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $customer->spouse_notelp ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Hubungan</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $customer->spouse_relation ?? '-' }}</td>
                </tr>
            </table>
        @endif



        <!-- Page Break for Screen is handled by separate sheet, for print by CSS -->
        <div class="page-footer">
            Halaman 1 dari 2
        </div>
    </div>

    <!-- Second Sheet/Page for Map -->
    <!-- Add margin-top for screen separation -->
    <div class="sheet mt-8 print:mt-0 print:border-none">
        <div class="page-break"></div>

        <div class="section-title" style="margin-top: 0;">LOKASI DAN PETA</div>
        <table>
            <tr>
                <td class="label">Koordinat</td>
                <td class="colon">:</td>
                <td class="value">
                    Lat: {{ $customer->latitude ?? '-' }}, Long: {{ $customer->longitude ?? '-' }}
                </td>
            </tr>
        </table>

        @if($customer->location_image_path)
            <div style="margin-top: 10px; width: 100%; border: 1px solid #000; padding: 5px; position: relative;">
                <p style="margin: 0 0 5px 0; font-weight: bold; font-size: 10pt;">Peta Lokasi:</p>
                <div
                    style="width: 100%; height: 500px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                    <img src="{{ route('media.customers', ['type' => 'map', 'filename' => basename($customer->location_image_path)]) }}" alt="Peta Lokasi"
                        style="max-width: 100%; max-height: 100%; object-fit: contain;">
                </div>
                @if($customer->latitude && $customer->longitude)
                    <div style="margin-top: 5px; text-align: center;">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $customer->latitude }},{{ $customer->longitude }}"
                            target="_blank" style="color: black; text-decoration: none; font-size: 10pt;">
                            Google Maps:
                            https://www.google.com/maps/search/?api=1&query={{ $customer->latitude }},{{ $customer->longitude }}
                        </a>
                    </div>

                    <!-- QR Code Overlay -->
                    <div
                        style="position: absolute; top: 5px; right: 5px; background: white; padding: 5px; border: 1px solid #ccc;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode('https://www.google.com/maps/search/?api=1&query=' . $customer->latitude . ',' . $customer->longitude) }}"
                            alt="QR Code Lokasi" style="width: 80px; height: 80px;">
                    </div>
                @endif
            </div>
        @endif

        <div class="signatures">
            <div class="signature-box">
                <p>Diperiksa Oleh,</p>
                <div class="signature-line">
                    ( <b>{{ auth()->user()->name }} )</b> <br>
                </div>
            </div>
            <div class="signature-box">
                <p>Debitur,</p>
                <div class="signature-line">
                    ( <b>{{ $customer->name }}</b> ) <br>
                    
                </div>
            </div>
        </div>

        <div class="page-footer">
            Dicetak pada: {{ now()->format('d F Y H:i') }} <br>
            Halaman 2 dari 2
        </div>
    </div>

</body>

</html>