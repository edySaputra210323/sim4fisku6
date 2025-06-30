<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\DataSiswa;
use App\Models\Transport;
use App\Models\JarakTempuh;
use App\Models\StatusSiswa;
use App\Models\SiswaImportFailed;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImportProcessor implements ToCollection, WithHeadingRow
{
    // Header wajib di Excel
    protected $requiredHeaders = [
        'nama_siswa',
        'nis',
        'nisn',
        'jarak_tempuh',
        'transport',
        'status',
        'angkatan',
        'tanggal_masuk',
    ];

    public function collection(Collection $rows)
    {
        // Validasi header Excel
        $headers = array_keys($rows->first()->toArray());
        $missingHeaders = array_diff($this->requiredHeaders, $headers);

        // Jika ada header yang hilang
        if (!empty($missingHeaders)) {
            $quotedHeaders = array_map(fn($header) => "'$header'", $missingHeaders);
            Notification::make()
                ->title('Gagal Impor Data Siswa')
                ->body('Tidak dapat menemukan header ' . implode(', ', $quotedHeaders) . ' pada file Excel')
                ->danger()
                ->send();
            return;
        }

        // Ambil data master
        $jarakTempuhMap = JarakTempuh::select('id', 'nama_jarak_tempuh')->get()->toArray();
        $transportMap = Transport::select('id', 'nama_transport')->get()->toArray();
        $statusMap = StatusSiswa::select('id', 'status')->get()->toArray();

        foreach ($rows as $keyIndex => $row) {
            // Lewati baris kosong
            if (array_filter($row->toArray()) === []) {
                continue;
            }

            $index = $keyIndex + 2; // Nomor baris (dimulai dari 2 karena header di baris 1)
            $errorMessage = null;

            // Validasi jarak_tempuh
            $jarak_tempuh_id = null;
            if (!in_array($row['jarak_tempuh'], [null, '', '-', '#N/A'])) {
                $jarak_tempuh_id = $this->searchInArray($jarakTempuhMap, 'nama_jarak_tempuh', $row['jarak_tempuh']);
                if (!$jarak_tempuh_id) {
                    $errorMessage = 'Jarak tempuh [' . $row['jarak_tempuh'] . '] tidak ditemukan di DATA MASTER';
                }
            } else {
                $errorMessage = 'Jarak tempuh tidak boleh kosong';
            }

            // Validasi transport
            $transport_id = null;
            if (!in_array($row['transport'], [null, '', '-', '#N/A'])) {
                $transport_id = $this->searchInArray($transportMap, 'nama_transport', $row['transport']);
                if (!$transport_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Transport [' . $row['transport'] . '] tidak ditemukan di DATA MASTER';
                }
            } else {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Transport tidak boleh kosong';
            }

            // Validasi status
            $status_id = null;
            if (!in_array($row['status'], [null, '', '-', '#N/A'])) {
                $status_id = $this->searchInArray($statusMap, 'status', $row['status']);
                if (!$status_id) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'Status [' . $row['status'] . '] tidak ditemukan di DATA MASTER';
                }
            } else {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'Status tidak boleh kosong';
            }

            // Validasi NISN (unik)
            if (in_array($row['nisn'], [null, '', '-', '#N/A'])) {
                if ($errorMessage) $errorMessage .= ", ";
                $errorMessage .= 'NISN tidak boleh kosong';
            } else {
                $nisnExists = DataSiswa::query()
                    ->where('nisn', $row['nisn'])
                    ->where('nama_siswa', '!=', $row['nama_siswa'])
                    ->first();
                if ($nisnExists) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'NISN [' . $row['nisn'] . '] sudah ada di database, dimiliki oleh: ' . $nisnExists->nama_siswa;
                }
            }

            // Validasi NIS (opsional, tapi jika ada harus unik)
            if (!in_array($row['nis'], [null, '', '-', '#N/A'])) {
                $nisExists = DataSiswa::query()
                    ->where('nis', $row['nis'])
                    ->where('nama_siswa', '!=', $row['nama_siswa'])
                    ->first();
                if ($nisExists) {
                    if ($errorMessage) $errorMessage .= ", ";
                    $errorMessage .= 'NIS [' . $row['nis'] . '] sudah ada di database, dimiliki oleh: ' . $nisExists->nama_siswa;
                }
            }

           // Validasi dan konversi tanggal_masuk
           $tanggal_masuk = null;
           if (in_array($row['tanggal_masuk'], [null, '', '-', '#N/A'])) {
               if ($errorMessage) $errorMessage .= ", ";
               $errorMessage .= 'Tanggal masuk tidak boleh kosong';
           } else {
               // Bersihkan input
               $tanggal_masuk_raw = trim($row['tanggal_masuk']);
               // Coba beberapa format tanggal
               $formats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d'];
               $parsed = false;

               foreach ($formats as $format) {
                   try {
                       $tanggal_masuk = Carbon::createFromFormat($format, $tanggal_masuk_raw)->format('Y-m-d');
                       $parsed = true;
                       break;
                   } catch (\Exception $e) {
                       // Lanjutkan ke format berikutnya
                   }
               }

               // Jika tidak ada format yang cocok
               if (!$parsed) {
                   if ($errorMessage) $errorMessage .= ", ";
                   $errorMessage .= 'Format tanggal masuk [' . $tanggal_masuk_raw . '] tidak valid, harus d/m/Y (contoh: 28/10/1993)';
               }
           }

           // Validasi dan konversi tanggal_lahir (opsional)
           $tanggal_lahir = null;
           if (!in_array($row['tanggal_lahir'], [null, '', '-', '#N/A'])) {
               // Bersihkan input
               $tanggal_lahir_raw = trim($row['tanggal_lahir']);
               // Coba beberapa format tanggal
               $formats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d'];
               $parsed = false;

               foreach ($formats as $format) {
                   try {
                       $tanggal_lahir = Carbon::createFromFormat($format, $tanggal_lahir_raw)->format('Y-m-d');
                       $parsed = true;
                       break;
                   } catch (\Exception $e) {
                       // Lanjutkan ke format berikutnya
                   }
               }

               // Jika tidak ada format yang cocok
               if (!$parsed) {
                   if ($errorMessage) $errorMessage .= ", ";
                   $errorMessage .= 'Format tanggal lahir [' . $tanggal_lahir_raw . '] tidak valid, harus d/m/Y (contoh: 28/10/1993)';
               }
           }


            // Jika ada error, simpan ke SiswaImportFailed
            if ($errorMessage) {
                $errorMessage = "Error pada baris ke-$index: " . $errorMessage;
                SiswaImportFailed::updateOrCreate(
                    ['nisn' => $row['nisn']],
                    [
                        'nama_siswa' => $row['nama_siswa'],
                        'nis' => $row['nis'],
                        'no_hp' => $row['no_hp'] ?? null,
                        'email' => $row['email'] ?? null,
                        'agama' => $row['agama'] ?? null,
                        'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
                        'tempat_lahir' => $row['tempat_lahir'] ?? null,
                        'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                        'alamat' => $row['alamat'] ?? null,
                        'jarak_tempuh_id' => $jarak_tempuh_id,
                        'transport_id' => $transport_id,
                        'angkatan' => $row['angkatan'],
                        'tanggal_masuk' => $row['tanggal_masuk'],
                        'status_id' => $status_id,
                        'nm_ayah' => $row['nm_ayah'] ?? null,
                        'nm_ibu' => $row['nm_ibu'] ?? null,
                        'ditambah_oleh' => auth()->id(),
                        'catatan_gagal' => $errorMessage,
                    ]
                );

                Notification::make()
                    ->title('SISTEM')
                    ->body('Ada sebagian atau semua data gagal diimpor')
                    ->danger()
                    ->send();
            } else {
                // Hapus data lama di SiswaImportFailed
                SiswaImportFailed::where('nisn', $row['nisn'])->delete();

                // Simpan atau perbarui data ke DataSiswa
                DataSiswa::updateOrCreate(
                    ['nisn' => $row['nisn']],
                    [
                        'nama_siswa' => $row['nama_siswa'],
                        'nis' => $row['nis'],
                        'no_hp' => $row['no_hp'] ?? null,
                        'email' => $row['email'] ?? null,
                        'agama' => $row['agama'] ?? null,
                        'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
                        'tempat_lahir' => $row['tempat_lahir'] ?? null,
                        'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                        'alamat' => $row['alamat'] ?? null,
                        'jarak_tempuh_id' => $jarak_tempuh_id,
                        'transport_id' => $transport_id,
                        'angkatan' => $row['angkatan'],
                        'tanggal_masuk' => $row['tanggal_masuk'],
                        'status_id' => $status_id,
                        'nm_ayah' => $row['nm_ayah'] ?? null,
                        'nm_ibu' => $row['nm_ibu'] ?? null,
                        'user_id' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                Notification::make()
                    ->title('SISTEM')
                    ->body('Data berhasil diimpor')
                    ->success()
                    ->send();
            }
        }
    }

    protected function searchInArray(array $data, string $searchKey, string $searchValue): ?int
    {
        foreach ($data as $item) {
            if ($item[$searchKey] === $searchValue) {
                return $item['id'];
            }
        }
        return null;
    }
}