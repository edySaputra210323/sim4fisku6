<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusPengajuanEnum: string implements HasLabel, HasColor, HasIcon
{
    // Dibuat Pegawai
    case DRAFT = 'draft';

    // Proses approval
    case PENDING_KS = 'pending_ks';
    case PENDING_SDM = 'pending_sdm';

    // Penolakan
    case REJECTED_KS = 'rejected_ks';
    case REJECTED_SDM = 'rejected_sdm';

    // KS memulangkan form setelah SDM menolak
    case RETURNED_TO_PEGAWAI = 'returned_to_pegawai';

    // Persetujuan
    case APPROVED_KS = 'approved_ks';
    case APPROVED_SDM = 'approved_sdm';

    // Final
    case FINAL = 'final';

    /* ---------------- LABEL ---------------- */

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT                 => 'Draft (Dibuat Pegawai)',
            self::PENDING_KS            => 'Menunggu Kepala Sekolah',
            self::PENDING_SDM           => 'Menunggu SDM',
            self::REJECTED_KS           => 'Ditolak Kepala Sekolah',
            self::REJECTED_SDM          => 'Ditolak SDM',
            self::RETURNED_TO_PEGAWAI   => 'Dikembalikan ke Pegawai',
            self::APPROVED_KS           => 'Disetujui Kepala Sekolah',
            self::APPROVED_SDM          => 'Disetujui SDM',
            self::FINAL                 => 'Selesai / Final',
        };
    }

    /* ---------------- WARNA ---------------- */

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT                 => 'gray',
            self::PENDING_KS            => 'warning',
            self::PENDING_SDM           => 'warning',

            self::REJECTED_KS           => 'danger',
            self::REJECTED_SDM          => 'danger',
            self::RETURNED_TO_PEGAWAI   => 'danger',

            self::APPROVED_KS           => 'success',
            self::APPROVED_SDM          => 'success',
            self::FINAL                 => 'success',
        };
    }

    /* ---------------- ICON ---------------- */

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DRAFT                 => 'heroicon-m-pencil',

            self::PENDING_KS            => 'heroicon-m-clock',
            self::PENDING_SDM           => 'heroicon-m-clock',

            self::REJECTED_KS           => 'heroicon-m-x-circle',
            self::REJECTED_SDM          => 'heroicon-m-x-circle',
            self::RETURNED_TO_PEGAWAI   => 'heroicon-m-arrow-uturn-left',

            self::APPROVED_KS           => 'heroicon-m-check-circle',
            self::APPROVED_SDM          => 'heroicon-m-check-circle',
            self::FINAL                 => 'heroicon-m-check-badge',
        };
    }
}
