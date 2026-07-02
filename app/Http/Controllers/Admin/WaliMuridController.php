<?php

// penjelasan: File ini adalah controller untuk modul Data Wali Murid.
// penjelasan: Controller ini dipakai oleh Super Admin dan Admin.
// penjelasan: Controller ini mengatur daftar wali murid, tambah wali murid, detail wali murid, edit, update, dan aktif/nonaktif.
// penjelasan: File ini dipanggil dari route /super-admin/wali-murid dan /admin/wali-murid.
// penjelasan: Semua validasi memakai pesan Bahasa Indonesia agar selaras dengan UI global.
// penjelasan: Nama wali, hubungan, nomor WhatsApp, alamat, dan status wajib diisi.
// penjelasan: NIK, pekerjaan, dan nomor HP bersifat opsional.
// penjelasan: Nomor WhatsApp dinormalisasi otomatis menjadi format +62.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// penjelasan: Controller adalah class dasar bawaan Laravel untuk membuat controller.

use App\Models\WaliMurid;
// penjelasan: Model WaliMurid digunakan untuk mengambil, menyimpan, dan mengubah data pada tabel wali_murids.

use Illuminate\Http\Request;
// penjelasan: Request digunakan untuk mengambil data dari form tambah dan edit wali murid.

use Illuminate\Validation\Rule;
// penjelasan: Rule digunakan untuk validasi unique dan pilihan nilai tertentu.

class WaliMuridController extends Controller
{
    /**
     * penjelasan: Method routePrefix digunakan agar route bisa menyesuaikan role user yang sedang login.
     * penjelasan: Jika user login sebagai super_admin, maka prefix route adalah super-admin.
     * penjelasan: Jika user login sebagai admin, maka prefix route adalah admin.
     */
    private function routePrefix(): string
    {
        return auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';
    }

    /**
     * penjelasan: Method index digunakan untuk menampilkan daftar wali murid.
     * penjelasan: Method ini dipanggil oleh route GET /super-admin/wali-murid atau /admin/wali-murid.
     */
    public function index(Request $request)
    {
        $query = WaliMurid::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nama_wali', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%')
                    ->orWhere('no_hp', 'like', '%' . $search . '%')
                    ->orWhere('no_whatsapp', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('hubungan')) {
            $query->where('hubungan', $request->hubungan);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $waliMurids = $query->latest()->paginate(10)->withQueryString();

        $routePrefix = $this->routePrefix();

        return view('admin.pages.wali-murid.index', compact('waliMurids', 'routePrefix'));
    }

    /**
     * penjelasan: Method create digunakan untuk menampilkan form tambah wali murid.
     * penjelasan: Method ini dipanggil oleh route GET /super-admin/wali-murid/create atau /admin/wali-murid/create.
     */
    public function create()
    {
        $routePrefix = $this->routePrefix();

        return view('admin.pages.wali-murid.create', compact('routePrefix'));
    }

    /**
     * penjelasan: Method store digunakan untuk menyimpan data wali murid baru.
     * penjelasan: Method ini dipanggil oleh form tambah wali murid.
     */
    public function store(Request $request)
    {
        // penjelasan: Nomor WhatsApp dinormalisasi sebelum validasi.
        // penjelasan: Contoh input 081234567890 akan diubah menjadi +6281234567890.
        $request->merge([
            'no_whatsapp' => $this->formatWhatsappNumber($request->input('no_whatsapp')),
        ]);

        $validated = $request->validate([
            'nama_wali' => ['required', 'string', 'max:150'],
            'nik' => ['nullable', 'string', 'max:30', 'unique:wali_murids,nik'],
            'hubungan' => ['required', Rule::in(['ayah', 'ibu', 'wali'])],
            'pekerjaan' => ['nullable', 'string', 'max:150'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'no_whatsapp' => ['required', 'string', 'max:30', 'regex:/^\+62[0-9]{8,15}$/'],
            'alamat' => ['required', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        $validated = $this->normalizeFields($validated);

        WaliMurid::create($validated);

        return redirect()
            ->route($this->routePrefix() . '.wali-murid.index')
            ->with('success', 'Data wali murid berhasil ditambahkan.');
    }

    /**
     * penjelasan: Method show digunakan untuk menampilkan detail wali murid.
     * penjelasan: Parameter WaliMurid $waliMurid otomatis diisi Laravel berdasarkan id dari URL.
     */
    public function show(WaliMurid $waliMurid)
    {
        $routePrefix = $this->routePrefix();

        return view('admin.pages.wali-murid.show', compact('waliMurid', 'routePrefix'));
    }

    /**
     * penjelasan: Method edit digunakan untuk menampilkan form edit wali murid.
     */
    public function edit(WaliMurid $waliMurid)
    {
        $routePrefix = $this->routePrefix();

        return view('admin.pages.wali-murid.edit', compact('waliMurid', 'routePrefix'));
    }

    /**
     * penjelasan: Method update digunakan untuk menyimpan perubahan data wali murid.
     * penjelasan: Method ini dipanggil oleh form edit melalui route PUT /wali-murid/{waliMurid}.
     */
    public function update(Request $request, WaliMurid $waliMurid)
    {
        // penjelasan: Nomor WhatsApp dinormalisasi sebelum validasi.
        // penjelasan: Sistem menerima input +628, 628, 08, atau 8 lalu menyimpan sebagai +62.
        $request->merge([
            'no_whatsapp' => $this->formatWhatsappNumber($request->input('no_whatsapp')),
        ]);

        $validated = $request->validate([
            'nama_wali' => ['required', 'string', 'max:150'],
            'nik' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('wali_murids', 'nik')->ignore($waliMurid->id),
            ],
            'hubungan' => ['required', Rule::in(['ayah', 'ibu', 'wali'])],
            'pekerjaan' => ['nullable', 'string', 'max:150'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'no_whatsapp' => ['required', 'string', 'max:30', 'regex:/^\+62[0-9]{8,15}$/'],
            'alamat' => ['required', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], $this->validationMessages());

        $validated = $this->normalizeFields($validated);

        $waliMurid->update($validated);

        return redirect()
            ->route($this->routePrefix() . '.wali-murid.index')
            ->with('success', 'Data wali murid berhasil diperbarui.');
    }

    /**
     * penjelasan: Method toggleStatus digunakan untuk mengubah status wali murid aktif/nonaktif.
     * penjelasan: Data wali murid tidak dihapus permanen agar nanti relasi dengan murid tetap aman.
     */
    public function toggleStatus(WaliMurid $waliMurid)
    {
        $newStatus = $waliMurid->status === 'aktif' ? 'nonaktif' : 'aktif';

        $waliMurid->update([
            'status' => $newStatus,
        ]);

        $message = $newStatus === 'aktif'
            ? 'Wali murid berhasil diaktifkan.'
            : 'Wali murid berhasil dinonaktifkan.';

        return back()->with('success', $message);
    }

    /**
     * penjelasan: Method validationMessages menyimpan semua pesan validasi Bahasa Indonesia.
     * penjelasan: Pesan ini dipakai saat tambah dan edit wali murid.
     */
    private function validationMessages(): array
    {
        return [
            'nama_wali.required' => 'Nama wali wajib diisi.',
            'nama_wali.string' => 'Nama wali harus berupa teks.',
            'nama_wali.max' => 'Nama wali maksimal 150 karakter.',

            'nik.string' => 'NIK harus berupa teks.',
            'nik.max' => 'NIK maksimal 30 karakter.',
            'nik.unique' => 'NIK sudah digunakan oleh wali murid lain.',

            'hubungan.required' => 'Hubungan wajib dipilih.',
            'hubungan.in' => 'Hubungan yang dipilih tidak valid.',

            'pekerjaan.string' => 'Pekerjaan harus berupa teks.',
            'pekerjaan.max' => 'Pekerjaan maksimal 150 karakter.',

            'no_hp.string' => 'Nomor HP harus berupa teks.',
            'no_hp.max' => 'Nomor HP maksimal 30 karakter.',

            'no_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_whatsapp.string' => 'Nomor WhatsApp harus berupa teks.',
            'no_whatsapp.max' => 'Nomor WhatsApp maksimal 30 karakter.',
            'no_whatsapp.regex' => 'Nomor WhatsApp harus menggunakan format +62 dan diikuti nomor aktif, contoh +6281234567890.',

            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',

            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];
    }

    /**
     * penjelasan: Method normalizeFields membersihkan input sebelum disimpan.
     * penjelasan: Field opsional kosong akan disimpan sebagai null.
     */
    private function normalizeFields(array $validated): array
    {
        $nullableFields = [
            'nik',
            'pekerjaan',
            'no_hp',
        ];

        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        if (isset($validated['nama_wali'])) {
            $validated['nama_wali'] = trim($validated['nama_wali']);
        }

        if (isset($validated['nik']) && $validated['nik'] !== null) {
            $validated['nik'] = trim($validated['nik']);
        }

        if (isset($validated['hubungan'])) {
            $validated['hubungan'] = trim($validated['hubungan']);
        }

        if (isset($validated['pekerjaan']) && $validated['pekerjaan'] !== null) {
            $validated['pekerjaan'] = trim($validated['pekerjaan']);
        }

        if (isset($validated['no_hp']) && $validated['no_hp'] !== null) {
            $validated['no_hp'] = trim($validated['no_hp']);
        }

        if (isset($validated['no_whatsapp'])) {
            $validated['no_whatsapp'] = trim($validated['no_whatsapp']);
        }

        if (isset($validated['alamat'])) {
            $validated['alamat'] = trim($validated['alamat']);
        }

        return $validated;
    }

    /**
     * penjelasan: Method formatWhatsappNumber mengubah nomor WhatsApp menjadi format +62.
     * penjelasan: Contoh 081234567890 menjadi +6281234567890.
     * penjelasan: Contoh 6281234567890 menjadi +6281234567890.
     * penjelasan: Contoh 81234567890 menjadi +6281234567890.
     */
    private function formatWhatsappNumber(?string $value): string
    {
        $number = trim((string) $value);

        $number = str_replace([' ', '-', '.', '(', ')'], '', $number);

        if ($number === '') {
            return '';
        }

        $number = ltrim($number, '+');

        if (str_starts_with($number, '0')) {
            return '+62' . substr($number, 1);
        }

        if (str_starts_with($number, '62')) {
            return '+' . $number;
        }

        if (str_starts_with($number, '8')) {
            return '+62' . $number;
        }

        return '+' . $number;
    }
}
