<?php

namespace App\Observers;

use App\Models\StatusSiswa;
use App\Models\MutasiSiswa;
use App\Enums\TipeMutasiEnum;

class MutasiSiswaObserver
{
    public function created(MutasiSiswa $mutasiSiswa): void
    {
        if ($mutasiSiswa->dataSiswa) {
            // simpan status lama sebelum diubah
            $mutasiSiswa->updateQuietly([
                'status_sebelum_id' => $mutasiSiswa->dataSiswa->status_id,
            ]);
    
            $this->syncStatus($mutasiSiswa);
        }
    }

    public function updated(MutasiSiswa $mutasiSiswa): void
    {
        $this->syncStatus($mutasiSiswa);
    }

    // public function deleted(MutasiSiswa $mutasiSiswa): void
    // {
    //     Kalau status_sebelum_id tersedia, rollback ke sana
    // if ($mutasiSiswa->status_sebelum_id && $mutasiSiswa->dataSiswa) {
    //     $mutasiSiswa->dataSiswa()->update([
    //         'status_id' => $mutasiSiswa->status_sebelum_id,
    //         'tanggal_keluar' => null,
    //     ]);
    //     }
    // }

    // public function forceDeleted(MutasiSiswa $mutasiSiswa): void
    // {
    //     // Sama aja, jaga2 kalau ada force delete
    //     if ($mutasiSiswa->status_sebelum_id && $mutasiSiswa->dataSiswa) {
    //         $mutasiSiswa->dataSiswa()->update([
    //             'status_id' => $mutasiSiswa->status_sebelum_id,
    //             'tanggal_keluar' => null,
    //         ]);
    //     }
    // }

    private function syncStatus(MutasiSiswa $mutasiSiswa): void
    {
        if (! $mutasiSiswa->dataSiswa) {
            return;
        }

        // Jika mutasi = KELUAR
        if ($mutasiSiswa->tipe_mutasi === TipeMutasiEnum::KELUAR) {
            $statusKeluar = StatusSiswa::whereRaw('LOWER(status) = ?', ['pindah'])->first();

            if ($statusKeluar) {
                $mutasiSiswa->dataSiswa()->update([
                    'status_id'      => $statusKeluar->id,
                    'tanggal_keluar' => $mutasiSiswa->tanggal_mutasi,
                ]);
            }
        }

        // Jika mutasi = MASUK
        if ($mutasiSiswa->tipe_mutasi === TipeMutasiEnum::MASUK) {
            $statusAktif = StatusSiswa::whereRaw('LOWER(status) = ?', ['aktif'])->first();

            if ($statusAktif) {
                $mutasiSiswa->dataSiswa()->update([
                    'status_id'     => $statusAktif->id,
                    'tanggal_masuk' => $mutasiSiswa->tanggal_mutasi,
                ]);
            }
        }
    }
}
