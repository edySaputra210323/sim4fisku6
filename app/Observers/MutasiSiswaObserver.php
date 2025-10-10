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
            // Simpan status lama
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

    /**
     * Sinkronisasi status siswa dan riwayat kelas berdasarkan tipe mutasi.
     */
    private function syncStatus(MutasiSiswa $mutasiSiswa): void
    {
        $siswa = $mutasiSiswa->dataSiswa;
        if (! $siswa) {
            return;
        }

        // Catat waktu perubahan status
        $siswa->updateQuietly(['last_status_updated_at' => now()]);

        // 🔴 MUTASI KELUAR
        if ($mutasiSiswa->tipe_mutasi === TipeMutasiEnum::KELUAR) {
            $statusKeluar = StatusSiswa::whereRaw('LOWER(status) = ?', ['pindah'])->first();

            if ($statusKeluar) {
                $siswa->update([
                    'status_id'      => $statusKeluar->id,
                    'tanggal_keluar' => $mutasiSiswa->tanggal_mutasi,
                ]);
            }

            // Nonaktifkan semua riwayat kelas aktif siswa ini
            $siswa->riwayatKelas()
                ->where('status_aktif', true)
                ->update(['status_aktif' => false]);
        }

        // 🟢 MUTASI MASUK
        if ($mutasiSiswa->tipe_mutasi === TipeMutasiEnum::MASUK) {
            $statusAktif = StatusSiswa::whereRaw('LOWER(status) = ?', ['aktif'])->first();

            if ($statusAktif) {
                $siswa->update([
                    'status_id'     => $statusAktif->id,
                    'tanggal_masuk' => $mutasiSiswa->tanggal_mutasi,
                ]);
            }

            // Aktifkan kembali riwayat kelas terbaru (jika ada)
            $riwayatTerbaru = $siswa->riwayatKelas()->latest('id')->first();
            if ($riwayatTerbaru) {
                $riwayatTerbaru->update(['status_aktif' => true]);
            }
        }
    }

    /**
     * Rollback status jika mutasi dihapus / dibatalkan.
     */
    private function rollbackStatus(MutasiSiswa $mutasiSiswa): void
    {
        $siswa = $mutasiSiswa->dataSiswa;
        if (! $siswa || ! $mutasiSiswa->status_sebelum_id) {
            return;
        }

        // Catat waktu rollback status
        $siswa->updateQuietly(['last_status_updated_at' => now()]);

        // 🔄 Jika mutasi KELUAR dihapus → kembalikan status sebelumnya, aktifkan riwayat terakhir
        if ($mutasiSiswa->tipe_mutasi === TipeMutasiEnum::KELUAR) {
            $siswa->update([
                'status_id'      => $mutasiSiswa->status_sebelum_id,
                'tanggal_keluar' => null,
            ]);

            $riwayatTerbaru = $siswa->riwayatKelas()->latest('id')->first();
            if ($riwayatTerbaru) {
                $riwayatTerbaru->update(['status_aktif' => true]);
            }
        }

        // 🔄 Jika mutasi MASUK dihapus → nonaktifkan riwayat kelas aktif, kembalikan status sebelumnya
        if ($mutasiSiswa->tipe_mutasi === TipeMutasiEnum::MASUK) {
            $siswa->update([
                'status_id'     => $mutasiSiswa->status_sebelum_id,
                'tanggal_masuk' => null,
            ]);

            $siswa->riwayatKelas()
                ->where('status_aktif', true)
                ->update(['status_aktif' => false]);
        }
    }
}
