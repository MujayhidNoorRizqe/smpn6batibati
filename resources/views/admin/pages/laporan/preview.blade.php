{{-- penjelasan: Halaman ini menampilkan preview laporan berdasarkan target dan periode yang dipilih. --}}
{{-- penjelasan: Dari halaman ini user bisa export CSV atau cetak/simpan PDF. --}}

@extends('admin.layouts.app')

@section('title', $report['title'])

@section('content')

@php
    $routeParams = array_merge([
        'jenis' => $jenis,
        'targetType' => $targetType,
        'targetId' => $targetId,
    ], request()->query());
@endphp

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>{{ $report['title'] }}</h1>
            <p>{{ $report['description'] }}</p>
        </div>

        <div class="header-actions">
            <a href="{{ route($routePrefix . '.laporan.periode', ['jenis' => $jenis, 'targetType' => $targetType, 'targetId' => $targetId]) }}" class="btn btn-outline">
                <i class="bi bi-arrow-left me-1"></i>
                Periode
            </a>

            <a href="{{ route($routePrefix . '.laporan.export.csv', $routeParams) }}" class="btn btn-success">
                <i class="bi bi-filetype-csv me-1"></i>
                Export CSV
            </a>

            <a href="{{ route($routePrefix . '.laporan.cetak', $routeParams) }}" target="_blank" class="btn btn-primary">
                <i class="bi bi-printer me-1"></i>
                Cetak / PDF
            </a>
        </div>
    </div>

    <div class="info-grid">
        <div>
            <span>Jenis Laporan</span>
            <strong>{{ $jenisLabel }}</strong>
        </div>

        <div>
            <span>Nama</span>
            <strong>{{ $targetTitle }}</strong>
        </div>

        <div>
            <span>Periode</span>
            <strong>{{ $report['periode']['label'] }}</strong>
        </div>

        <div>
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

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Preview Data</h2>
                <p>Data yang tampil mengikuti periode laporan yang dipilih.</p>
            </div>

            <span class="badge-count">
                {{ $report['data']->total() }} data
            </span>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        @foreach ($report['headers'] as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @forelse ($report['rows'] as $row)
                        <tr>
                            <td>{{ $report['data']->firstItem() + $loop->index }}</td>

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
        </div>

        <div class="pagination-wrapper">
            {{ $report['data']->links() }}
        </div>
    </div>
</div>

<style>
    .page-content {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .page-header-card,
    .card,
    .summary-card,
    .info-grid {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .page-header-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .page-header-card h1,
    .card h2 {
        margin: 0;
        color: #0f172a;
        font-weight: 900;
    }

    .page-header-card p,
    .card-header p {
        margin: 6px 0 0;
        color: #64748b;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .info-grid div {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-grid span,
    .summary-card span {
        color: #64748b;
        font-size: 13px;
    }

    .info-grid strong {
        color: #0f172a;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 14px;
    }

    .summary-card {
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .summary-card strong {
        color: #0f172a;
        font-size: 26px;
        line-height: 1;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
    }

    .badge-count {
        background: #dbeafe;
        color: #2563eb;
        border-radius: 8px;
        padding: 6px 10px;
        font-weight: 800;
        font-size: 13px;
        white-space: nowrap;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        border-bottom: 1px solid #e2e8f0;
        padding: 13px 12px;
        text-align: left;
        vertical-align: middle;
        white-space: nowrap;
    }

    .data-table th {
        background: #f8fafc;
        color: #0f172a;
        font-weight: 800;
    }

    .empty-row {
        text-align: center;
        color: #64748b;
        padding: 28px !important;
    }

    .pagination-wrapper {
        margin-top: 18px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        padding: 11px 18px;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .btn-success {
        background: #16a34a;
        color: #ffffff;
    }

    .btn-outline {
        background: #ffffff;
        color: #475569;
        border-color: #cbd5e1;
    }

    @media (max-width: 1100px) {
        .summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .page-header-card,
        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .summary-grid,
        .info-grid {
            grid-template-columns: 1fr;
        }

        .header-actions {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

@endsection
