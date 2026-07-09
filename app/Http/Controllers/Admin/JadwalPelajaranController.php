<?php

// penjelasan: File ini adalah controller untuk Modul Jadwal Pelajaran.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Jadwal pelajaran dibuat per kelas.
// penjelasan: Alur tambah jadwal sekarang dibuat bertahap:
// penjelasan: 1. Pilih kelas.
// penjelasan: 2. Masuk halaman list hari Senin sampai Sabtu.
// penjelasan: 3. Pilih hari.
// penjelasan: 4. Isi jadwal per hari maksimal 6 pelajaran.
// penjelasan: 5. Simpan dan kembali ke halaman list hari kelas tersebut.
// penjelasan: Controller ini juga memvalidasi agar jadwal guru dan kelas tidak bentrok.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Pegawai;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class JadwalPelajaranController extends Controller
{
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    private function hariList(): array
    {
        return [
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu',
            'kamis' => 'Kamis',
            'jumat' => 'Jumat',
            'sabtu' => 'Sabtu',
        ];
    }

    private function urutanHari(?string $hari): int
    {
        return match ($hari) {
            'senin' => 1,
            'selasa' => 2,
            'rabu' => 3,
            'kamis' => 4,
            'jumat' => 5,
            'sabtu' => 6,
            default => 99,
        };
    }

    private function hariLabel(string $hari): string
    {
        return $this->hariList()[$hari] ?? '-';
    }

    private function tahunAjaranDefault()
    {
        return TahunAjaran::where('status', 'aktif')
            ->latest()
            ->first()
            ?? TahunAjaran::latest()->first();
    }

    private function semesterDefault(?int $tahunAjaranId = null)
    {
        $queryAktif = Semester::where('status', 'aktif');

        if ($tahunAjaranId) {
            $queryAktif->where('tahun_ajaran_id', $tahunAjaranId);
        }

        $semester = $queryAktif->latest()->first();

        if ($semester) {
            return $semester;
        }

        $querySemua = Semester::query();

        if ($tahunAjaranId) {
            $querySemua->where('tahun_ajaran_id', $tahunAjaranId);
        }

        return $querySemua->latest()->first();
    }

    private function periodeTerpilih(Request $request): array
    {
        $tahunAjaranDefault = $this->tahunAjaranDefault();

        $selectedTahunAjaranId = $request->filled('tahun_ajaran_id')
            ? (int) $request->tahun_ajaran_id
            : ($tahunAjaranDefault?->id);

        $semesterDefault = $this->semesterDefault($selectedTahunAjaranId ? (int) $selectedTahunAjaranId : null);

        $selectedSemesterId = $request->filled('semester_id')
            ? (int) $request->semester_id
            : ($semesterDefault?->id);

        return [
            $selectedTahunAjaranId,
            $selectedSemesterId,
        ];
    }

    public function index(Request $request)
    {
        $kelasList = Kelas::with('waliKelas')
            ->where('status', 'aktif')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $kelasPerTingkat = $kelasList->groupBy(function ($kelas) {
            return $kelas->tingkat ?: 'Lainnya';
        });

        $tahunAjarans = TahunAjaran::latest()->get();

        $semesters = Semester::with('tahunAjaran')
            ->latest()
            ->get();

        $selectedTahunAjaranId = $request->tahun_ajaran_id;
        $selectedSemesterId = $request->semester_id;
        $selectedStatus = $request->status;

        $kelasList = $kelasList->map(function ($kelas) use ($selectedTahunAjaranId, $selectedSemesterId, $selectedStatus) {
            $queryJadwal = JadwalPelajaran::where('kelas_id', $kelas->id);

            if ($selectedTahunAjaranId) {
                $queryJadwal->where('tahun_ajaran_id', $selectedTahunAjaranId);
            }

            if ($selectedSemesterId) {
                $queryJadwal->where('semester_id', $selectedSemesterId);
            }

            if ($selectedStatus) {
                $queryJadwal->where('status', $selectedStatus);
            }

            $kelas->total_jadwal = (clone $queryJadwal)->count();
            $kelas->total_jadwal_aktif = (clone $queryJadwal)->where('status', 'aktif')->count();
            $kelas->total_mapel = (clone $queryJadwal)->pluck('mata_pelajaran_id')->unique()->count();
            $kelas->total_guru = (clone $queryJadwal)->pluck('guru_id')->unique()->count();

            return $kelas;
        });

        $kelasPerTingkat = $kelasList->groupBy(function ($kelas) {
            return $kelas->tingkat ?: 'Lainnya';
        });

        $totalKelas = $kelasList->count();
        $totalJadwal = $kelasList->sum('total_jadwal');

        $routePrefix = $this->routePrefix();

        return view('admin.pages.jadwal-pelajaran.index', compact(
            'kelasPerTingkat',
            'tahunAjarans',
            'semesters',
            'selectedTahunAjaranId',
            'selectedSemesterId',
            'selectedStatus',
            'totalKelas',
            'totalJadwal',
            'routePrefix'
        ));
    }

    public function kelas(Request $request, Kelas $kelas)
    {
        $kelas->load('waliKelas');

        $tahunAjarans = TahunAjaran::latest()->get();

        $semesters = Semester::with('tahunAjaran')
            ->latest()
            ->get();

        [$selectedTahunAjaranId, $selectedSemesterId] = $this->periodeTerpilih($request);

        $selectedStatus = $request->status;

        $query = JadwalPelajaran::with([
                'tahunAjaran',
                'semester',
                'kelas',
                'mataPelajaran',
                'guru',
            ])
            ->where('kelas_id', $kelas->id);

        if ($selectedTahunAjaranId) {
            $query->where('tahun_ajaran_id', $selectedTahunAjaranId);
        }

        if ($selectedSemesterId) {
            $query->where('semester_id', $selectedSemesterId);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        $jadwalPelajarans = $query
            ->get()
            ->sortBy(function ($jadwal) {
                $hari = str_pad((string) $this->urutanHari($jadwal->hari), 2, '0', STR_PAD_LEFT);
                $jamMulai = $jadwal->jam_mulai ?? '99:99';

                return $hari . '|' . $jamMulai;
            })
            ->values();

        $jadwalPerHari = collect($this->hariList())->map(function ($label, $hari) use ($jadwalPelajarans) {
            $items = $jadwalPelajarans->where('hari', $hari)->values();

            return (object) [
                'hari' => $hari,
                'label' => $label,
                'jadwals' => $items,
                'total_jadwal' => $items->count(),
                'total_aktif' => $items->where('status', 'aktif')->count(),
            ];
        })->values();

        $tahunAjaranDipilih = $selectedTahunAjaranId
            ? TahunAjaran::find($selectedTahunAjaranId)
            : null;

        $semesterDipilih = $selectedSemesterId
            ? Semester::find($selectedSemesterId)
            : null;

        $routePrefix = $this->routePrefix();

        return view('admin.pages.jadwal-pelajaran.kelas', compact(
            'kelas',
            'tahunAjarans',
            'semesters',
            'selectedTahunAjaranId',
            'selectedSemesterId',
            'selectedStatus',
            'tahunAjaranDipilih',
            'semesterDipilih',
            'jadwalPerHari',
            'jadwalPelajarans',
            'routePrefix'
        ));
    }

    public function create(Request $request)
    {
        $kelasList = Kelas::with('waliKelas')
            ->where('status', 'aktif')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $kelasPerTingkat = $kelasList->groupBy(function ($kelas) {
            return $kelas->tingkat ?: 'Lainnya';
        });

        $routePrefix = $this->routePrefix();

        return view('admin.pages.jadwal-pelajaran.create', compact(
            'kelasList',
            'kelasPerTingkat',
            'routePrefix'
        ));
    }

    public function createKelas(Request $request, Kelas $kelas)
    {
        $kelas->load('waliKelas');

        [$selectedTahunAjaranId, $selectedSemesterId] = $this->periodeTerpilih($request);

        $tahunAjarans = TahunAjaran::where('status', 'aktif')
            ->latest()
            ->get();

        if ($tahunAjarans->isEmpty()) {
            $tahunAjarans = TahunAjaran::latest()->get();
        }

        $semesters = Semester::with('tahunAjaran')
            ->where(function ($query) use ($selectedTahunAjaranId) {
                $query->where('status', 'aktif');

                if ($selectedTahunAjaranId) {
                    $query->where('tahun_ajaran_id', $selectedTahunAjaranId);
                }
            })
            ->latest()
            ->get();

        if ($semesters->isEmpty()) {
            $semesters = Semester::with('tahunAjaran')
                ->when($selectedTahunAjaranId, function ($query) use ($selectedTahunAjaranId) {
                    $query->where('tahun_ajaran_id', $selectedTahunAjaranId);
                })
                ->latest()
                ->get();
        }

        $jadwalPerHari = collect($this->hariList())->map(function ($label, $hari) use ($kelas, $selectedTahunAjaranId, $selectedSemesterId) {
            $query = JadwalPelajaran::where('kelas_id', $kelas->id)
                ->where('hari', $hari);

            if ($selectedTahunAjaranId) {
                $query->where('tahun_ajaran_id', $selectedTahunAjaranId);
            }

            if ($selectedSemesterId) {
                $query->where('semester_id', $selectedSemesterId);
            }

            return (object) [
                'hari' => $hari,
                'label' => $label,
                'total_jadwal' => (clone $query)->count(),
                'total_aktif' => (clone $query)->where('status', 'aktif')->count(),
            ];
        })->values();

        $tahunAjaranDipilih = $selectedTahunAjaranId
            ? TahunAjaran::find($selectedTahunAjaranId)
            : null;

        $semesterDipilih = $selectedSemesterId
            ? Semester::find($selectedSemesterId)
            : null;

        $routePrefix = $this->routePrefix();

        return view('admin.pages.jadwal-pelajaran.create-kelas', compact(
            'kelas',
            'tahunAjarans',
            'semesters',
            'selectedTahunAjaranId',
            'selectedSemesterId',
            'tahunAjaranDipilih',
            'semesterDipilih',
            'jadwalPerHari',
            'routePrefix'
        ));
    }

    public function createHari(Request $request, Kelas $kelas, string $hari)
    {
        if (! array_key_exists($hari, $this->hariList())) {
            abort(404);
        }

        $kelas->load('waliKelas');

        $validated = $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
        ], [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'semester_id.required' => 'Semester wajib dipilih.',
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($validated['tahun_ajaran_id']);
        $semester = Semester::findOrFail($validated['semester_id']);

        $jadwalHariIni = JadwalPelajaran::with([
                'mataPelajaran',
                'guru',
            ])
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('semester_id', $semester->id)
            ->where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get();

        $mataPelajarans = MataPelajaran::where('status', 'aktif')
            ->orderBy('nama_mapel')
            ->get();

        $gurus = Pegawai::where('jenis_pegawai', 'guru')
            ->where('status', 'aktif')
            ->orderBy('nama_pegawai')
            ->get();

        $jadwalForm = old('jadwal');

        if (! $jadwalForm) {
            if ($jadwalHariIni->isNotEmpty()) {
                $jadwalForm = $jadwalHariIni->map(function ($jadwal) {
                    return [
                        'id' => $jadwal->id,
                        'jam_mulai' => $jadwal->jam_mulai_format,
                        'jam_selesai' => $jadwal->jam_selesai_format,
                        'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                        'guru_id' => $jadwal->guru_id,
                        'status' => $jadwal->status,
                    ];
                })->toArray();
            } else {
                $jadwalForm = [
                    [
                        'id' => '',
                        'jam_mulai' => '',
                        'jam_selesai' => '',
                        'mata_pelajaran_id' => '',
                        'guru_id' => '',
                        'status' => 'aktif',
                    ],
                ];
            }
        }

        $routePrefix = $this->routePrefix();

        return view('admin.pages.jadwal-pelajaran.create-hari', compact(
            'kelas',
            'hari',
            'tahunAjaran',
            'semester',
            'jadwalHariIni',
            'jadwalForm',
            'mataPelajarans',
            'gurus',
            'routePrefix'
        ));
    }

    public function store(Request $request)
    {
        return redirect()
            ->route($this->routePrefix() . '.jadwal-pelajaran.create')
            ->with('error', 'Gunakan alur baru: pilih kelas, pilih hari, lalu isi jadwal per hari.');
    }

    public function storeHari(Request $request, Kelas $kelas, string $hari)
    {
        if (! array_key_exists($hari, $this->hariList())) {
            abort(404);
        }

        $validated = $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'semester_id' => ['required', 'exists:semesters,id'],

            'jadwal' => ['required', 'array', 'min:1', 'max:6'],
            'jadwal.*.id' => ['nullable', 'integer', 'exists:jadwal_pelajarans,id'],
            'jadwal.*.jam_mulai' => ['required', 'date_format:H:i'],
            'jadwal.*.jam_selesai' => ['required', 'date_format:H:i'],
            'jadwal.*.mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'jadwal.*.guru_id' => ['required', 'exists:pegawais,id'],
            'jadwal.*.status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        $validated['kelas_id'] = $kelas->id;
        $validated['hari'] = $hari;

        $existingDayIds = JadwalPelajaran::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $validated['tahun_ajaran_id'])
            ->where('semester_id', $validated['semester_id'])
            ->where('hari', $hari)
            ->pluck('id')
            ->toArray();

        foreach ($validated['jadwal'] as $index => $jadwalItem) {
            $jadwalId = $jadwalItem['id'] ?? null;

            if ($jadwalId && ! in_array((int) $jadwalId, array_map('intval', $existingDayIds), true)) {
                throw ValidationException::withMessages([
                    'jadwal' => 'Baris jadwal ke-' . ($index + 1) . ': ID jadwal tidak sesuai dengan kelas, hari, tahun ajaran, dan semester yang sedang diedit.',
                ]);
            }

            $dataJadwal = [
                'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                'semester_id' => $validated['semester_id'],
                'kelas_id' => $kelas->id,
                'mata_pelajaran_id' => $jadwalItem['mata_pelajaran_id'],
                'guru_id' => $jadwalItem['guru_id'],
                'hari' => $hari,
                'jam_mulai' => $jadwalItem['jam_mulai'],
                'jam_selesai' => $jadwalItem['jam_selesai'],
                'status' => $jadwalItem['status'],
            ];

            $currentJadwal = $jadwalId
                ? JadwalPelajaran::find($jadwalId)
                : null;

            $this->validateTimeRange($dataJadwal, $index + 1);
            $this->validateSupportingData($dataJadwal, $index + 1);
            $this->validateNoScheduleConflict($dataJadwal, $currentJadwal, $index + 1, $existingDayIds);
        }

        $this->validateNoSubmittedScheduleConflict($validated);

        DB::transaction(function () use ($validated, $kelas, $hari, $existingDayIds) {
            $submittedIds = [];

            foreach ($validated['jadwal'] as $jadwalItem) {
                $data = [
                    'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                    'semester_id' => $validated['semester_id'],
                    'kelas_id' => $kelas->id,
                    'mata_pelajaran_id' => $jadwalItem['mata_pelajaran_id'],
                    'guru_id' => $jadwalItem['guru_id'],
                    'hari' => $hari,
                    'jam_mulai' => $jadwalItem['jam_mulai'],
                    'jam_selesai' => $jadwalItem['jam_selesai'],
                    'status' => $jadwalItem['status'],
                ];

                if (! empty($jadwalItem['id'])) {
                    $jadwal = JadwalPelajaran::findOrFail($jadwalItem['id']);
                    $jadwal->update($data);
                    $submittedIds[] = $jadwal->id;
                } else {
                    $jadwal = JadwalPelajaran::create($data);
                    $submittedIds[] = $jadwal->id;
                }
            }

            JadwalPelajaran::whereIn('id', $existingDayIds)
                ->whereNotIn('id', $submittedIds)
                ->update(['status' => 'nonaktif']);
        });

        return redirect()
            ->route($this->routePrefix() . '.jadwal-pelajaran.create-kelas', [
                'kelas' => $kelas->id,
                'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                'semester_id' => $validated['semester_id'],
            ])
            ->with('success', 'Jadwal hari ' . $this->hariLabel($hari) . ' kelas ' . $kelas->nama_kelas . ' berhasil disimpan.');
    }

    public function show(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->load([
            'tahunAjaran',
            'semester',
            'kelas',
            'mataPelajaran',
            'guru',
        ]);

        $routePrefix = $this->routePrefix();

        return view('admin.pages.jadwal-pelajaran.show', compact('jadwalPelajaran', 'routePrefix'));
    }

    public function edit(JadwalPelajaran $jadwalPelajaran)
    {
        $tahunAjarans = TahunAjaran::where(function ($query) use ($jadwalPelajaran) {
                $query->where('status', 'aktif')
                    ->orWhere('id', $jadwalPelajaran->tahun_ajaran_id);
            })
            ->latest()
            ->get();

        $semesters = Semester::with('tahunAjaran')
            ->where(function ($query) use ($jadwalPelajaran) {
                $query->where('status', 'aktif')
                    ->orWhere('id', $jadwalPelajaran->semester_id);
            })
            ->latest()
            ->get();

        $kelasList = Kelas::where(function ($query) use ($jadwalPelajaran) {
                $query->where('status', 'aktif')
                    ->orWhere('id', $jadwalPelajaran->kelas_id);
            })
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $mataPelajarans = MataPelajaran::where(function ($query) use ($jadwalPelajaran) {
                $query->where('status', 'aktif')
                    ->orWhere('id', $jadwalPelajaran->mata_pelajaran_id);
            })
            ->orderBy('nama_mapel')
            ->get();

        $gurus = Pegawai::where('jenis_pegawai', 'guru')
            ->where(function ($query) use ($jadwalPelajaran) {
                $query->where('status', 'aktif')
                    ->orWhere('id', $jadwalPelajaran->guru_id);
            })
            ->orderBy('nama_pegawai')
            ->get();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.jadwal-pelajaran.edit', compact(
            'jadwalPelajaran',
            'tahunAjarans',
            'semesters',
            'kelasList',
            'mataPelajarans',
            'gurus',
            'routePrefix'
        ));
    }

    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $validated = $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'guru_id' => ['required', 'exists:pegawais,id'],
            'hari' => ['required', Rule::in(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'])],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        $this->validateTimeRange($validated);
        $this->validateSupportingData($validated);
        $this->validateNoScheduleConflict($validated, $jadwalPelajaran);

        $jadwalPelajaran->update($validated);

        return redirect()
            ->route($this->routePrefix() . '.jadwal-pelajaran.kelas', [
                'kelas' => $validated['kelas_id'],
                'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                'semester_id' => $validated['semester_id'],
            ])
            ->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function toggleStatus(JadwalPelajaran $jadwalPelajaran)
    {
        if ($jadwalPelajaran->status === 'aktif') {
            $jadwalPelajaran->update(['status' => 'nonaktif']);

            return back()->with('success', 'Jadwal pelajaran berhasil dinonaktifkan.');
        }

        $validated = $jadwalPelajaran->only([
            'tahun_ajaran_id',
            'semester_id',
            'kelas_id',
            'mata_pelajaran_id',
            'guru_id',
            'hari',
            'jam_mulai',
            'jam_selesai',
            'status',
        ]);

        $validated['status'] = 'aktif';
        $validated['jam_mulai'] = substr($jadwalPelajaran->jam_mulai, 0, 5);
        $validated['jam_selesai'] = substr($jadwalPelajaran->jam_selesai, 0, 5);

        $this->validateTimeRange($validated);
        $this->validateSupportingData($validated);
        $this->validateNoScheduleConflict($validated, $jadwalPelajaran);

        $jadwalPelajaran->update(['status' => 'aktif']);

        return back()->with('success', 'Jadwal pelajaran berhasil diaktifkan.');
    }

    private function validationMessages(): array
    {
        return [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak valid.',

            'semester_id.required' => 'Semester wajib dipilih.',
            'semester_id.exists' => 'Semester tidak valid.',

            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas tidak valid.',

            'jadwal.required' => 'Minimal satu jadwal wajib diisi.',
            'jadwal.array' => 'Format jadwal tidak valid.',
            'jadwal.min' => 'Minimal satu jadwal wajib diisi.',
            'jadwal.max' => 'Maksimal 6 pelajaran dalam satu hari.',

            'jadwal.*.id.integer' => 'ID jadwal tidak valid.',
            'jadwal.*.id.exists' => 'ID jadwal tidak ditemukan.',

            'jadwal.*.mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'jadwal.*.mata_pelajaran_id.exists' => 'Mata pelajaran tidak valid.',

            'jadwal.*.guru_id.required' => 'Guru pengajar wajib dipilih.',
            'jadwal.*.guru_id.exists' => 'Guru pengajar tidak valid.',

            'jadwal.*.jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jadwal.*.jam_mulai.date_format' => 'Format jam mulai tidak valid.',

            'jadwal.*.jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jadwal.*.jam_selesai.date_format' => 'Format jam selesai tidak valid.',

            'jadwal.*.status.required' => 'Status wajib dipilih.',
            'jadwal.*.status.in' => 'Status yang dipilih tidak valid.',

            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'mata_pelajaran_id.exists' => 'Mata pelajaran tidak valid.',

            'guru_id.required' => 'Guru pengajar wajib dipilih.',
            'guru_id.exists' => 'Guru pengajar tidak valid.',

            'hari.required' => 'Hari wajib dipilih.',
            'hari.in' => 'Hari yang dipilih tidak valid.',

            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid.',

            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid.',

            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];
    }

    private function validateTimeRange(array $validated, ?int $baris = null): void
    {
        if ($validated['jam_selesai'] <= $validated['jam_mulai']) {
            $pesan = 'Jam selesai harus lebih besar dari jam mulai.';

            if ($baris) {
                $pesan = 'Baris jadwal ke-' . $baris . ': ' . $pesan;
            }

            throw ValidationException::withMessages([
                'jam_selesai' => $pesan,
            ]);
        }
    }

    private function validateSupportingData(array $validated, ?int $baris = null): void
    {
        if ($validated['status'] !== 'aktif') {
            return;
        }

        $prefix = $baris ? 'Baris jadwal ke-' . $baris . ': ' : '';

        $tahunAjaran = TahunAjaran::find($validated['tahun_ajaran_id']);

        if (! $tahunAjaran || $tahunAjaran->status !== 'aktif') {
            throw ValidationException::withMessages([
                'tahun_ajaran_id' => $prefix . 'Jadwal aktif harus memakai tahun ajaran yang aktif.',
            ]);
        }

        $semester = Semester::find($validated['semester_id']);

        if (! $semester || $semester->status !== 'aktif') {
            throw ValidationException::withMessages([
                'semester_id' => $prefix . 'Jadwal aktif harus memakai semester yang aktif.',
            ]);
        }

        if ((int) $semester->tahun_ajaran_id !== (int) $validated['tahun_ajaran_id']) {
            throw ValidationException::withMessages([
                'semester_id' => $prefix . 'Semester yang dipilih harus sesuai dengan tahun ajaran.',
            ]);
        }

        $kelasAktif = Kelas::where('id', $validated['kelas_id'])
            ->where('status', 'aktif')
            ->exists();

        if (! $kelasAktif) {
            throw ValidationException::withMessages([
                'kelas_id' => $prefix . 'Kelas yang dipilih harus berstatus aktif.',
            ]);
        }

        $mataPelajaranAktif = MataPelajaran::where('id', $validated['mata_pelajaran_id'])
            ->where('status', 'aktif')
            ->exists();

        if (! $mataPelajaranAktif) {
            throw ValidationException::withMessages([
                'mata_pelajaran_id' => $prefix . 'Mata pelajaran yang dipilih harus berstatus aktif.',
            ]);
        }

        $guruAktif = Pegawai::where('id', $validated['guru_id'])
            ->where('jenis_pegawai', 'guru')
            ->where('status', 'aktif')
            ->exists();

        if (! $guruAktif) {
            throw ValidationException::withMessages([
                'guru_id' => $prefix . 'Guru yang dipilih harus pegawai jenis guru dan berstatus aktif.',
            ]);
        }
    }

    private function validateNoScheduleConflict(
        array $validated,
        ?JadwalPelajaran $currentJadwal = null,
        ?int $baris = null,
        array $excludeIds = []
    ): void {
        if ($validated['status'] !== 'aktif') {
            return;
        }

        $prefix = $baris ? 'Baris jadwal ke-' . $baris . ': ' : '';

        $kelasConflictQuery = JadwalPelajaran::where('status', 'aktif')
            ->where('semester_id', $validated['semester_id'])
            ->where('kelas_id', $validated['kelas_id'])
            ->where('hari', $validated['hari'])
            ->where('jam_mulai', '<', $validated['jam_selesai'])
            ->where('jam_selesai', '>', $validated['jam_mulai']);

        if ($currentJadwal) {
            $kelasConflictQuery->where('id', '!=', $currentJadwal->id);
        }

        if (! empty($excludeIds)) {
            $kelasConflictQuery->whereNotIn('id', $excludeIds);
        }

        if ($kelasConflictQuery->exists()) {
            throw ValidationException::withMessages([
                'jam_mulai' => $prefix . 'Jadwal kelas bentrok dengan jadwal lain pada hari dan jam tersebut.',
            ]);
        }

        $guruConflictQuery = JadwalPelajaran::where('status', 'aktif')
            ->where('semester_id', $validated['semester_id'])
            ->where('guru_id', $validated['guru_id'])
            ->where('hari', $validated['hari'])
            ->where('jam_mulai', '<', $validated['jam_selesai'])
            ->where('jam_selesai', '>', $validated['jam_mulai']);

        if ($currentJadwal) {
            $guruConflictQuery->where('id', '!=', $currentJadwal->id);
        }

        if (! empty($excludeIds)) {
            $guruConflictQuery->whereNotIn('id', $excludeIds);
        }

        if ($guruConflictQuery->exists()) {
            throw ValidationException::withMessages([
                'guru_id' => $prefix . 'Guru sudah memiliki jadwal lain pada hari dan jam tersebut.',
            ]);
        }
    }

    private function validateNoSubmittedScheduleConflict(array $validated): void
    {
        $jadwals = collect($validated['jadwal'])
            ->values()
            ->map(function ($item) use ($validated) {
                return [
                    'semester_id' => $validated['semester_id'],
                    'kelas_id' => $validated['kelas_id'],
                    'guru_id' => $item['guru_id'],
                    'hari' => $validated['hari'],
                    'jam_mulai' => $item['jam_mulai'],
                    'jam_selesai' => $item['jam_selesai'],
                    'status' => $item['status'],
                ];
            })
            ->filter(function ($item) {
                return $item['status'] === 'aktif';
            })
            ->values();

        for ($i = 0; $i < $jadwals->count(); $i++) {
            for ($j = $i + 1; $j < $jadwals->count(); $j++) {
                $jadwalA = $jadwals[$i];
                $jadwalB = $jadwals[$j];

                if ($jadwalA['hari'] !== $jadwalB['hari']) {
                    continue;
                }

                $bentrokWaktu = $jadwalA['jam_mulai'] < $jadwalB['jam_selesai']
                    && $jadwalA['jam_selesai'] > $jadwalB['jam_mulai'];

                if (! $bentrokWaktu) {
                    continue;
                }

                throw ValidationException::withMessages([
                    'jadwal' => 'Jadwal yang disubmit bentrok pada baris ke-' . ($i + 1) . ' dan baris ke-' . ($j + 1) . '. Dalam satu kelas, jadwal pada hari dan jam yang sama tidak boleh bertabrakan.',
                ]);
            }
        }

        for ($i = 0; $i < $jadwals->count(); $i++) {
            for ($j = $i + 1; $j < $jadwals->count(); $j++) {
                $jadwalA = $jadwals[$i];
                $jadwalB = $jadwals[$j];

                if ((int) $jadwalA['guru_id'] !== (int) $jadwalB['guru_id']) {
                    continue;
                }

                if ($jadwalA['hari'] !== $jadwalB['hari']) {
                    continue;
                }

                $bentrokWaktu = $jadwalA['jam_mulai'] < $jadwalB['jam_selesai']
                    && $jadwalA['jam_selesai'] > $jadwalB['jam_mulai'];

                if (! $bentrokWaktu) {
                    continue;
                }

                throw ValidationException::withMessages([
                    'jadwal' => 'Guru pada baris ke-' . ($i + 1) . ' dan baris ke-' . ($j + 1) . ' memiliki jadwal yang bentrok.',
                ]);
            }
        }
    }
}
