<?php

// penjelasan: Controller ini digunakan oleh guru dan staff.
// penjelasan: Guru/staff dapat membuat, melihat, mengedit, dan membatalkan pengajuan dinas, sakit, dan izin.
// penjelasan: Pengajuan hanya bisa diedit/dibatalkan selama status masih menunggu.
// penjelasan: Dinas dan sakit wajib upload bukti file/foto.
// penjelasan: Izin hanya wajib menyertakan alasan.

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\PengajuanAbsensiPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PengajuanAbsensiPegawaiController extends Controller
{
    private function routePrefix(): string
    {
        return auth()->user()->role === 'guru' ? 'guru' : 'staff';
    }

    private function currentPegawai()
    {
        $pegawai = auth()->user()->pegawai;

        if (! $pegawai) {
            abort(403, 'Akun ini belum terhubung dengan data pegawai.');
        }

        return $pegawai;
    }

    public function index(Request $request)
    {
        $pegawai = $this->currentPegawai();

        $query = PengajuanAbsensiPegawai::where('pegawai_id', $pegawai->id);

        if ($request->filled('jenis_pengajuan')) {
            $query->where('jenis_pengajuan', $request->jenis_pengajuan);
        }

        if ($request->filled('status_pengajuan')) {
            $query->where('status_pengajuan', $request->status_pengajuan);
        }

        $pengajuans = $query->latest()->paginate(10)->withQueryString();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.pengajuan-absensi-pegawai.index', compact('pengajuans', 'routePrefix'));
    }

    public function create()
    {
        $routePrefix = $this->routePrefix();

        return view('admin.pages.pengajuan-absensi-pegawai.create', compact('routePrefix'));
    }

    public function store(Request $request)
    {
        $pegawai = $this->currentPegawai();

        $validated = $this->validatePengajuan($request);

        $this->validateNoOverlappingSubmission($pegawai->id, $validated);

        if ($request->hasFile('bukti_file')) {
            $validated['bukti_file'] = $request->file('bukti_file')->store('bukti-absensi-pegawai', 'public');
        }

        $validated = $this->normalizeFields($validated);
        $validated['pegawai_id'] = $pegawai->id;
        $validated['status_pengajuan'] = 'menunggu';

        PengajuanAbsensiPegawai::create($validated);

        return redirect()
            ->route($this->routePrefix() . '.pengajuan-absensi-pegawai.index')
            ->with('success', 'Pengajuan absensi berhasil dikirim dan menunggu persetujuan admin.');
    }

    public function show(PengajuanAbsensiPegawai $pengajuanAbsensiPegawai)
    {
        $this->authorizeOwner($pengajuanAbsensiPegawai);

        $routePrefix = $this->routePrefix();

        return view('admin.pages.pengajuan-absensi-pegawai.show', compact('pengajuanAbsensiPegawai', 'routePrefix'));
    }

    public function edit(PengajuanAbsensiPegawai $pengajuanAbsensiPegawai)
    {
        $this->authorizeOwner($pengajuanAbsensiPegawai);
        $this->ensureMenunggu($pengajuanAbsensiPegawai);

        $routePrefix = $this->routePrefix();

        return view('admin.pages.pengajuan-absensi-pegawai.edit', compact('pengajuanAbsensiPegawai', 'routePrefix'));
    }

    public function update(Request $request, PengajuanAbsensiPegawai $pengajuanAbsensiPegawai)
    {
        $pegawai = $this->currentPegawai();

        $this->authorizeOwner($pengajuanAbsensiPegawai);
        $this->ensureMenunggu($pengajuanAbsensiPegawai);

        $validated = $this->validatePengajuan($request, $pengajuanAbsensiPegawai);

        $this->validateNoOverlappingSubmission($pegawai->id, $validated, $pengajuanAbsensiPegawai);

        if ($request->hasFile('bukti_file')) {
            if ($pengajuanAbsensiPegawai->bukti_file && Storage::disk('public')->exists($pengajuanAbsensiPegawai->bukti_file)) {
                Storage::disk('public')->delete($pengajuanAbsensiPegawai->bukti_file);
            }

            $validated['bukti_file'] = $request->file('bukti_file')->store('bukti-absensi-pegawai', 'public');
        }

        $validated = $this->normalizeFields($validated);

        $pengajuanAbsensiPegawai->update($validated);

        return redirect()
            ->route($this->routePrefix() . '.pengajuan-absensi-pegawai.index')
            ->with('success', 'Pengajuan absensi berhasil diperbarui.');
    }

    public function cancel(PengajuanAbsensiPegawai $pengajuanAbsensiPegawai)
    {
        $this->authorizeOwner($pengajuanAbsensiPegawai);

        if (! $pengajuanAbsensiPegawai->isMenunggu()) {
            return back()->withErrors(['pengajuan' => 'Pengajuan tidak dapat dibatalkan karena sudah diproses.']);
        }

        $pengajuanAbsensiPegawai->update([
            'status_pengajuan' => 'dibatalkan',
        ]);

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    private function validatePengajuan(Request $request, ?PengajuanAbsensiPegawai $pengajuan = null): array
    {
        $jenisPengajuan = $request->input('jenis_pengajuan');
        $hasExistingBukti = $pengajuan && $pengajuan->hasBuktiFile();

        return $request->validate([
            'jenis_pengajuan' => ['required', Rule::in(['dinas', 'sakit', 'izin'])],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],

            'judul_pengajuan' => [
                Rule::requiredIf($jenisPengajuan === 'dinas'),
                'nullable',
                'string',
                'max:150',
            ],

            'lokasi_kegiatan' => [
                Rule::requiredIf($jenisPengajuan === 'dinas'),
                'nullable',
                'string',
                'max:150',
            ],

            'alasan' => ['required', 'string'],

            'bukti_file' => [
                Rule::requiredIf(in_array($jenisPengajuan, ['dinas', 'sakit'], true) && ! $hasExistingBukti),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx',
                'max:4096',
            ],
        ], $this->validationMessages());
    }

    private function validationMessages(): array
    {
        return [
            'jenis_pengajuan.required' => 'Jenis pengajuan wajib dipilih.',
            'jenis_pengajuan.in' => 'Jenis pengajuan tidak valid.',

            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Tanggal mulai tidak valid.',

            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date' => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.',

            'judul_pengajuan.required' => 'Judul kegiatan dinas wajib diisi.',
            'judul_pengajuan.string' => 'Judul kegiatan harus berupa teks.',
            'judul_pengajuan.max' => 'Judul kegiatan maksimal 150 karakter.',

            'lokasi_kegiatan.required' => 'Lokasi kegiatan dinas wajib diisi.',
            'lokasi_kegiatan.string' => 'Lokasi kegiatan harus berupa teks.',
            'lokasi_kegiatan.max' => 'Lokasi kegiatan maksimal 150 karakter.',

            'alasan.required' => 'Alasan wajib diisi.',
            'alasan.string' => 'Alasan harus berupa teks.',

            'bukti_file.required' => 'Bukti wajib diupload untuk pengajuan dinas atau sakit.',
            'bukti_file.file' => 'Bukti harus berupa file.',
            'bukti_file.mimes' => 'Bukti harus berupa jpg, jpeg, png, webp, pdf, doc, atau docx.',
            'bukti_file.max' => 'Ukuran bukti maksimal 4 MB.',
        ];
    }

    private function normalizeFields(array $validated): array
    {
        foreach (['judul_pengajuan', 'lokasi_kegiatan'] as $field) {
            if (array_key_exists($field, $validated)) {
                $validated[$field] = trim((string) $validated[$field]) === ''
                    ? null
                    : trim($validated[$field]);
            }
        }

        if (isset($validated['alasan'])) {
            $validated['alasan'] = trim($validated['alasan']);
        }

        return $validated;
    }

    private function authorizeOwner(PengajuanAbsensiPegawai $pengajuan): void
    {
        $pegawai = $this->currentPegawai();

        if ((int) $pengajuan->pegawai_id !== (int) $pegawai->id) {
            abort(403);
        }
    }

    private function ensureMenunggu(PengajuanAbsensiPegawai $pengajuan): void
    {
        if (! $pengajuan->isMenunggu()) {
            back()
                ->withErrors(['pengajuan' => 'Pengajuan tidak dapat diubah karena sudah diproses.'])
                ->throwResponse();
        }
    }

    private function validateNoOverlappingSubmission(int|string $pegawaiId, array $validated, ?PengajuanAbsensiPegawai $currentPengajuan = null): void
    {
        $query = PengajuanAbsensiPegawai::where('pegawai_id', $pegawaiId)
            ->whereIn('status_pengajuan', ['menunggu', 'disetujui'])
            ->where('tanggal_mulai', '<=', $validated['tanggal_selesai'])
            ->where('tanggal_selesai', '>=', $validated['tanggal_mulai']);

        if ($currentPengajuan) {
            $query->where('id', '!=', $currentPengajuan->id);
        }

        if ($query->exists()) {
            back()
                ->withErrors(['tanggal_mulai' => 'Sudah ada pengajuan menunggu atau disetujui pada rentang tanggal tersebut.'])
                ->withInput()
                ->throwResponse();
        }
    }
}
