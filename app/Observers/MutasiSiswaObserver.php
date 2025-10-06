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
            // simpan status lama
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

    /**
     * Handle sebelum soft delete.
     */
    public function deleting(MutasiSiswa $mutasiSiswa): void
    {
        $this->rollbackStatus($mutasiSiswa);
    }

    /**
     * Handle restore (jika data mutasi dikembalikan).
     */
    public function restoring(MutasiSiswa $mutasiSiswa): void
    {
        $this->syncStatus($mutasiSiswa);
    }

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

    private function rollbackStatus(MutasiSiswa $mutasiSiswa): void
    {
        $siswa = $mutasiSiswa->dataSiswa;
        if (! $siswa) return;

        if (! $mutasiSiswa->status_sebelum_id) return;

        // Jika mutasi KELUAR dihapus → kembalikan status sebelumnya, hapus tanggal_keluar
        if ($mutasiSiswa->tipe_mutasi === TipeMutasiEnum::KELUAR) {
            $siswa->update([
                'status_id'      => $mutasiSiswa->status_sebelum_id,
                'tanggal_keluar' => null,
            ]);
        }

        // Jika mutasi MASUK dihapus → tetap aktif, tapi reset tanggal_masuk
        if ($mutasiSiswa->tipe_mutasi === TipeMutasiEnum::MASUK) {
            $siswa->update([
                'status_id'     => $mutasiSiswa->status_sebelum_id,
                'tanggal_masuk' => null,
            ]);
        }
    }
}
