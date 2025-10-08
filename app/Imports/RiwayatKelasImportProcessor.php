<?php

namespace App\Imports;

use Log;
use App\Models\Kelas;
use App\Models\Pegawai;
use App\Models\DataSiswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\RiwayatKelas;
use App\Models\StatusSiswa;
use App\Models\RiwayatKelasImportFailed;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RiwayatKelasImportProcessor implements ToCollection, WithHeadingRow
{
    protected $requiredHeaders = ['nis', 'kelas', 'walas'];

    public function collection(Collection $rows)
    {
        // 🟢 1. Validasi tahun ajaran & semester aktif
        $activeTahunAjaran = TahunAjaran::where('status', true)->first();
        $activeSemester = $activeTahunAjaran
            ? Semester::where('th_ajaran_id', $activeTahunAjaran->id)
                ->where('status', true)
                ->first()
            : null;

        if (!$activeTahunAjaran || !$activeSemester) {
            Notification::make()
                ->title('Gagal Impor Data Rombel')
                ->body('Tidak ada tahun ajaran atau semester aktif. Aktifkan terlebih dahulu.')
                ->danger()
                ->send();
            return;
        }

        // 🟢 2. Nonaktifkan semua riwayat kelas aktif dari tahun ajaran sebelumnya
        RiwayatKelas::where('status_aktif', true)
            ->where('tahun_ajaran_id', '<>', $activeTahunAjaran->id)
            ->update(['status_aktif' => false]);

        // 🟢 3. Validasi header
        $headers = array_keys($rows->first()->toArray());
        $missingHeaders = array_diff($this->requiredHeaders, $headers);
        if (!empty($missingHeaders)) {
            $quotedHeaders = array_map(fn($header) => "'$header'", $missingHeaders);
            Notification::make()
                ->title('Gagal Impor Data Rombel')
                ->body('Tidak dapat menemukan header ' . implode(', ', $quotedHeaders) . ' pada file Excel')
                ->danger()
                ->send();
            return;
        }

        // 🟢 4. Ambil data master (cache ke array agar cepat)
        $kelasMap = Kelas::select('id', 'nama_kelas')->get()->toArray();
        $pegawaiMap = Pegawai::select('id', 'nm_pegawai')->whereNotNull('nm_pegawai')->get()->toArray();
        $siswaMap = DataSiswa::select('id', 'nis', 'status_id')->get()->toArray();

        // 🟢 5. Siapkan array hasil validasi
        $validRows = [];
        $errors = [];

        foreach ($rows as $keyIndex => $row) {
            if (array_filter($row->toArray()) === []) continue;

            $index = $keyIndex + 2;
            $errorMessage = null;

            // 🟠 Validasi NIS
            $siswa_id = null;
            if (in_array($row['nis'], [null, '', '-', '#N/A'])) {
                $errorMessage = 'NIS tidak boleh kosong';
            } else {
                $siswaData = collect($siswaMap)->firstWhere('nis', trim($row['nis']));
                if (!$siswaData) {
                    $errorMessage = "NIS [{$row['nis']}] tidak ditemukan di database siswa";
                } else {
                    $siswa_id = $siswaData['id'];

                    // 🔵 Pastikan siswa berstatus aktif
                    $status = StatusSiswa::find($siswaData['status_id']);
                    if ($status && strtolower($status->status) !== 'aktif') {
                        $errorMessage = "Siswa dengan NIS [{$row['nis']}] tidak berstatus aktif";
                    }

                    // 🔵 Cek apakah sudah ada riwayat di tahun & semester aktif
                    $existingRiwayat = RiwayatKelas::where('data_siswa_id', $siswa_id)
                        ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                        ->where('semester_id', $activeSemester->id)
                        ->exists();
                    if ($existingRiwayat) {
                        $errorMessage = "Siswa dengan NIS [{$row['nis']}] sudah terdaftar di rombel untuk tahun ajaran dan semester ini";
                    }
                }
            }

            // 🟠 Validasi kelas
            $kelas_id = null;
            if (in_array($row['kelas'], [null, '', '-', '#N/A'])) {
                $errorMessage = ($errorMessage ? $errorMessage . ', ' : '') . 'Kelas tidak boleh kosong';
            } else {
                $kelas_id = $this->searchInArray($kelasMap, 'nama_kelas', trim($row['kelas']));
                if (!$kelas_id) {
                    $errorMessage = ($errorMessage ? $errorMessage . ', ' : '') . "Kelas [{$row['kelas']}] tidak ditemukan di database kelas";
                }
            }

            // 🟠 Validasi wali kelas
            $guru_id = null;
            if (in_array($row['walas'], [null, '', '-', '#N/A'])) {
                $errorMessage = ($errorMessage ? $errorMessage . ', ' : '') . 'Wali kelas tidak boleh kosong';
            } else {
                $guru_id = $this->searchInArray($pegawaiMap, 'nm_pegawai', trim($row['walas']));
                if (!$guru_id) {
                    $errorMessage = ($errorMessage ? $errorMessage . ', ' : '') . "Wali kelas [{$row['walas']}] tidak ditemukan di database pegawai";
                }
            }

            // 🔵 Kumpulkan hasil
            $rowData = [
                'data_siswa_id' => $siswa_id,
                'kelas_id' => $kelas_id,
                'pegawai_id' => $guru_id,
                'tahun_ajaran_id' => $activeTahunAjaran->id,
                'semester_id' => $activeSemester->id,
                'nis' => $row['nis'],
                'kelas' => $row['kelas'],
                'walas' => $row['walas'],
                'catatan_gagal' => $errorMessage ? "Error pada baris ke-$index: $errorMessage" : null,
            ];

            if ($errorMessage) {
                $errors[$index] = $rowData;
            } else {
                $validRows[$index] = $rowData;
            }
        }

        // 🟢 6. Tangani error (jika ada)
        if (!empty($errors)) {
            foreach ($errors as $rowData) {
                RiwayatKelasImportFailed::updateOrCreate(
                    [
                        'nis' => $rowData['nis'],
                        'tahun_ajaran_id' => $activeTahunAjaran->id,
                        'semester_id' => $activeSemester->id,
                    ],
                    $rowData
                );
            }

            Notification::make()
                ->title('Gagal Impor Data Rombel')
                ->body('Impor dibatalkan karena ada data error. Perbaiki data di Excel dan impor ulang.')
                ->danger()
                ->send();
            return;
        }

        // 🟢 7. Simpan semua data valid ke riwayat_kelas
        foreach ($validRows as $rowData) {
            RiwayatKelas::create([
                'data_siswa_id' => $rowData['data_siswa_id'],
                'kelas_id' => $rowData['kelas_id'],
                'pegawai_id' => $rowData['pegawai_id'],
                'tahun_ajaran_id' => $rowData['tahun_ajaran_id'],
                'semester_id' => $rowData['semester_id'],
                'status_aktif' => true,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 🟢 8. Notifikasi sukses
        Notification::make()
            ->title('SISTEM')
            ->body('Data rombel berhasil diimpor dan status aktif diperbarui')
            ->success()
            ->send();
    }

    protected function searchInArray(array $data, string $searchKey, string $searchValue): ?int
    {
        foreach ($data as $item) {
            if (isset($item[$searchKey]) && trim(strtolower($item[$searchKey])) === strtolower($searchValue)) {
                return $item['id'];
            }
        }
        return null;
    }
}
