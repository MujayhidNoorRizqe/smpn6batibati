{{-- penjelasan: File ini adalah halaman tambah/edit jadwal per hari. --}}
{{-- penjelasan: Satu hari maksimal 6 pelajaran. --}}
{{-- penjelasan: Baris jadwal bisa ditambah dan dikurangi. --}}
{{-- penjelasan: Jika jadwal lama dikurangi dari form, jadwal lama akan dinonaktifkan agar riwayat data tetap aman. --}}

@extends('admin.layouts.app')

@section('title', 'Isi Jadwal Harian')

@section('content')

<div class="page-content">
    <div class="page-header-card">
        <div>
            <h1>Isi Jadwal {{ ucfirst($hari) }}</h1>
            <p>Kelas {{ $kelas->nama_kelas }} · Maksimal 6 pelajaran dalam satu hari.</p>
        </div>

        <a
            href="{{ route($routePrefix . '.jadwal-pelajaran.create-kelas', [
                'kelas' => $kelas->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester_id' => $semester->id,
            ]) }}"
            class="btn btn-outline"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke List Hari
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Data belum sesuai.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="info-grid">
        <div>
            <span>Kelas</span>
            <strong>{{ $kelas->nama_kelas }}</strong>
        </div>

        <div>
            <span>Hari</span>
            <strong>{{ ucfirst($hari) }}</strong>
        </div>

        <div>
            <span>Tahun Ajaran</span>
            <strong>{{ $tahunAjaran->nama_tahun_ajaran ?? $tahunAjaran->tahun_ajaran ?? '-' }}</strong>
        </div>

        <div>
            <span>Semester</span>
            <strong>{{ $semester->nama_semester_label ?? $semester->nama_semester ?? $semester->semester ?? '-' }}</strong>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h2>Form Jadwal Harian</h2>
                <p>Isi mata pelajaran, guru, dan jam pelajaran untuk hari {{ ucfirst($hari) }}.</p>
            </div>

            <button type="button" class="btn btn-outline-primary" id="addScheduleRow">
                <i class="bi bi-plus-circle me-1"></i>
                Tambah Baris
            </button>
        </div>

        <form
            method="POST"
            action="{{ route($routePrefix . '.jadwal-pelajaran.store-hari', [
                'kelas' => $kelas->id,
                'hari' => $hari,
            ]) }}"
            id="jadwalHariForm"
        >
            @csrf

            <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAjaran->id }}">
            <input type="hidden" name="semester_id" value="{{ $semester->id }}">

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th style="width: 140px;">Jam Mulai <span class="required">*</span></th>
                            <th style="width: 140px;">Jam Selesai <span class="required">*</span></th>
                            <th>Mata Pelajaran <span class="required">*</span></th>
                            <th>Guru <span class="required">*</span></th>
                            <th style="width: 140px;">Status <span class="required">*</span></th>
                            <th style="width: 80px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="scheduleRows">
                        @foreach ($jadwalForm as $index => $jadwal)
                            <tr class="schedule-row">
                                <td class="row-number">{{ $loop->iteration }}</td>

                                <td>
                                    <input
                                        type="hidden"
                                        name="jadwal[{{ $index }}][id]"
                                        value="{{ $jadwal['id'] ?? '' }}"
                                    >

                                    <input
                                        type="time"
                                        name="jadwal[{{ $index }}][jam_mulai]"
                                        class="form-control"
                                        value="{{ $jadwal['jam_mulai'] ?? '' }}"
                                        required
                                    >
                                </td>

                                <td>
                                    <input
                                        type="time"
                                        name="jadwal[{{ $index }}][jam_selesai]"
                                        class="form-control"
                                        value="{{ $jadwal['jam_selesai'] ?? '' }}"
                                        required
                                    >
                                </td>

                                <td>
                                    <select name="jadwal[{{ $index }}][mata_pelajaran_id]" class="form-select" required>
                                        <option value="">Pilih Mata Pelajaran</option>
                                        @foreach ($mataPelajarans as $mapel)
                                            <option value="{{ $mapel->id }}" @selected((string) ($jadwal['mata_pelajaran_id'] ?? '') === (string) $mapel->id)>
                                                {{ $mapel->nama_mapel }}
                                                @if ($mapel->kode_mapel)
                                                    - {{ $mapel->kode_mapel }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <select name="jadwal[{{ $index }}][guru_id]" class="form-select" required>
                                        <option value="">Pilih Guru</option>
                                        @foreach ($gurus as $guru)
                                            <option value="{{ $guru->id }}" @selected((string) ($jadwal['guru_id'] ?? '') === (string) $guru->id)>
                                                {{ $guru->nama_pegawai }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <select name="jadwal[{{ $index }}][status]" class="form-select" required>
                                        <option value="aktif" @selected(($jadwal['status'] ?? 'aktif') === 'aktif')>Aktif</option>
                                        <option value="nonaktif" @selected(($jadwal['status'] ?? '') === 'nonaktif')>Nonaktif</option>
                                    </select>
                                </td>

                                <td class="text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-footer">
                <div class="note">
                    <strong>Catatan:</strong> maksimal 6 pelajaran per hari. Jadwal yang dihapus dari form akan dinonaktifkan.
                </div>

                <div class="form-actions">
                    <a
                        href="{{ route($routePrefix . '.jadwal-pelajaran.create-kelas', [
                            'kelas' => $kelas->id,
                            'tahun_ajaran_id' => $tahunAjaran->id,
                            'semester_id' => $semester->id,
                        ]) }}"
                        class="btn btn-outline"
                        data-confirm="true"
                        data-confirm-message="Batalkan pengisian jadwal hari ini? Perubahan yang belum disimpan akan hilang."
                        data-confirm-yes="Ya, Batalkan"
                        data-confirm-yes-class="btn-danger"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        data-confirm="true"
                        data-confirm-message="Apakah Anda yakin ingin menyimpan jadwal hari {{ ucfirst($hari) }} untuk kelas {{ $kelas->nama_kelas }}?"
                        data-confirm-yes="Ya, Simpan"
                        data-confirm-yes-class="btn-primary"
                    >
                        <i class="bi bi-save me-1"></i>
                        Simpan Jadwal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<template id="scheduleRowTemplate">
    <tr class="schedule-row">
        <td class="row-number"></td>

        <td>
            <input type="hidden" data-name="jadwal[__INDEX__][id]" class="schedule-input" value="">

            <input
                type="time"
                data-name="jadwal[__INDEX__][jam_mulai]"
                class="form-control schedule-input"
                required
            >
        </td>

        <td>
            <input
                type="time"
                data-name="jadwal[__INDEX__][jam_selesai]"
                class="form-control schedule-input"
                required
            >
        </td>

        <td>
            <select data-name="jadwal[__INDEX__][mata_pelajaran_id]" class="form-select schedule-input" required>
                <option value="">Pilih Mata Pelajaran</option>
                @foreach ($mataPelajarans as $mapel)
                    <option value="{{ $mapel->id }}">
                        {{ $mapel->nama_mapel }}
                        @if ($mapel->kode_mapel)
                            - {{ $mapel->kode_mapel }}
                        @endif
                    </option>
                @endforeach
            </select>
        </td>

        <td>
            <select data-name="jadwal[__INDEX__][guru_id]" class="form-select schedule-input" required>
                <option value="">Pilih Guru</option>
                @foreach ($gurus as $guru)
                    <option value="{{ $guru->id }}">
                        {{ $guru->nama_pegawai }}
                    </option>
                @endforeach
            </select>
        </td>

        <td>
            <select data-name="jadwal[__INDEX__][status]" class="form-select schedule-input" required>
                <option value="aktif" selected>Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </td>

        <td class="text-end">
            <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const maxRows = 6;
        const addButton = document.getElementById('addScheduleRow');
        const rowsContainer = document.getElementById('scheduleRows');
        const template = document.getElementById('scheduleRowTemplate');

        function refreshRows() {
            const rows = rowsContainer.querySelectorAll('.schedule-row');

            rows.forEach(function (row, index) {
                const number = row.querySelector('.row-number');

                if (number) {
                    number.textContent = index + 1;
                }

                row.querySelectorAll('[name]').forEach(function (input) {
                    input.name = input.name.replace(/jadwal\[\d+\]/, 'jadwal[' + index + ']');
                });
            });

            if (addButton) {
                addButton.disabled = rows.length >= maxRows;
            }
        }

        function applyNamesToNewRow(row, index) {
            row.querySelectorAll('.schedule-input').forEach(function (input) {
                const name = input.getAttribute('data-name');

                if (name) {
                    input.setAttribute('name', name.replace('__INDEX__', index));
                    input.removeAttribute('data-name');
                }
            });
        }

        if (addButton && rowsContainer && template) {
            addButton.addEventListener('click', function () {
                const currentRows = rowsContainer.querySelectorAll('.schedule-row').length;

                if (currentRows >= maxRows) {
                    alert('Maksimal 6 pelajaran dalam satu hari.');
                    return;
                }

                const nextIndex = currentRows;
                const clone = template.content.cloneNode(true);
                const row = clone.querySelector('.schedule-row');

                applyNamesToNewRow(row, nextIndex);
                rowsContainer.appendChild(clone);

                refreshRows();
            });
        }

        if (rowsContainer) {
            rowsContainer.addEventListener('click', function (event) {
                const removeButton = event.target.closest('.remove-row');

                if (! removeButton) {
                    return;
                }

                const rows = rowsContainer.querySelectorAll('.schedule-row');

                if (rows.length <= 1) {
                    alert('Minimal harus ada satu baris jadwal.');
                    return;
                }

                removeButton.closest('.schedule-row').remove();
                refreshRows();
            });
        }

        refreshRows();
    });
</script>

<style>
    .page-content {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .page-header-card,
    .card,
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
    }

    .page-header-card p,
    .card-header p {
        margin: 6px 0 0;
        color: #64748b;
    }

    .alert {
        border-radius: 12px;
        padding: 14px 16px;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
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

    .info-grid span {
        color: #64748b;
        font-size: 13px;
    }

    .info-grid strong {
        color: #0f172a;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
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
        padding: 12px;
        text-align: left;
        vertical-align: middle;
    }

    .data-table th {
        background: #f8fafc;
        color: #0f172a;
        font-weight: 800;
    }

    .required {
        color: #ef4444;
    }

    .form-control,
    .form-select {
        width: 100%;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        padding: 10px 11px;
        font-size: 14px;
        outline: none;
        background: #ffffff;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .form-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-top: 18px;
    }

    .note {
        color: #64748b;
        font-size: 13px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
    }

    .text-end {
        text-align: right;
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

    .btn-sm {
        padding: 8px 12px;
        font-size: 13px;
    }

    .btn-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .btn-outline {
        background: #ffffff;
        color: #475569;
        border-color: #cbd5e1;
    }

    .btn-outline-primary {
        background: #ffffff;
        color: #2563eb;
        border-color: #2563eb;
    }

    .btn-outline-danger {
        background: #ffffff;
        color: #dc2626;
        border-color: #dc2626;
    }

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .mt-2 {
        margin-top: 8px;
    }

    @media (max-width: 1100px) {
        .info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 800px) {
        .page-header-card,
        .card-header,
        .form-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            width: 100%;
            flex-direction: column;
        }
    }
</style>

@endsection
