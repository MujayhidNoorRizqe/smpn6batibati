{{-- penjelasan: Halaman cetak laporan. --}}
{{-- penjelasan: Halaman ini bisa dicetak atau disimpan sebagai PDF dari fitur print browser. --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $report['title'] }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 24px;
            color: #111827;
            background: #ffffff;
        }

        .print-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 18px;
        }

        .btn {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #111827;
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            text-decoration: none;
        }

        .btn-primary {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }

        .report-header {
            text-align: center;
            border-bottom: 3px solid #111827;
            padding-bottom: 16px;
            margin-bottom: 18px;
        }

        .report-header h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .report-header p {
            margin: 6px 0 0;
            color: #475569;
        }

        .report-info {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }

        .info-box,
        .summary-card {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px;
        }

        .info-box span,
        .summary-card span {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .info-box strong {
            font-size: 14px;
            color: #111827;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }

        .summary-card strong {
            font-size: 18px;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            font-weight: 700;
        }

        .empty-row {
            text-align: center;
            color: #64748b;
            padding: 20px;
        }

        .signature-area {
            margin-top: 36px;
            display: flex;
            justify-content: flex-end;
        }

        .signature-box {
            width: 260px;
            text-align: center;
        }

        .signature-space {
            height: 80px;
        }

        @media print {
            body {
                margin: 12mm;
            }

            .print-actions {
                display: none;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>

    <div class="print-actions">
        <button onclick="window.print()" class="btn btn-primary">
            Cetak / Simpan PDF
        </button>

        <button onclick="window.close()" class="btn">
            Tutup
        </button>
    </div>

    <div class="report-header">
        <h1>{{ $report['title'] }}</h1>
        <p>Sistem Informasi Akademik SMPN 6 Bati-Bati</p>
    </div>

    <div class="report-info">
        <div class="info-box">
            <span>Jenis Laporan</span>
            <strong>{{ $jenisLabel }}</strong>
        </div>

        <div class="info-box">
            <span>Nama</span>
            <strong>{{ $targetTitle }}</strong>
        </div>

        <div class="info-box">
            <span>Periode</span>
            <strong>{{ $report['periode']['label'] }}</strong>
        </div>

        <div class="info-box">
            <span>Detail Periode</span>
            <strong>{{ $report['periode']['nama'] }}</strong>
        </div>
    </div>

    <div class="summary-grid">
        @foreach ($report['summary'] as $summary)
            <div class="summary-card">
                <span>{{ $summary['label'] }}</span>
                <strong>{{ $summary['value'] }}</strong>
            </div>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                @foreach ($report['headers'] as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @forelse ($report['rows'] as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    @foreach ($row as $column)
                        <td>{{ $column }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($report['headers']) + 1 }}" class="empty-row">
                        Data laporan belum tersedia untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-area">
        <div class="signature-box">
            <p>Bati-Bati, {{ now()->format('d-m-Y') }}</p>
            <p>Mengetahui,</p>
            <div class="signature-space"></div>
            <strong>Kepala Sekolah</strong>
        </div>
    </div>

</body>
</html>
