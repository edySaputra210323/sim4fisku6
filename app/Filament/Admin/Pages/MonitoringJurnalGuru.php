<?php

namespace App\Filament\Admin\Pages;

use App\Models\JurnalGuru;
use App\Models\JadwalMengajar;
use App\Models\Pegawai;
use App\Models\Kelas;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class MonitoringJurnalGuru extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Monitoring Jurnal Guru';
    protected static ?string $title = 'Monitoring Jurnal Guru';
    protected static string $view = 'filament.admin.pages.monitoring-jurnal-guru';

    public $jadwalHariIni = [];
    public $tanggal;
    public $filterGuru;
    public $filterKelas;

    public function mount(): void
    {
        $this->tanggal = request('tanggal', now()->toDateString());
        $this->filterGuru = request('guru_id');
        $this->filterKelas = request('kelas_id');

        $this->loadData();
    }

    public function loadData(): void
{
    $tanggal = Carbon::parse($this->tanggal);

    // Ubah nama hari agar cocok dengan database (tanpa locale error)
    $hari = match (strtolower($tanggal->format('l'))) {
        'monday' => 'Senin',
        'tuesday' => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday' => 'Kamis',
        'friday' => 'Jumat',
        'saturday' => 'Sabtu',
        'sunday' => 'Minggu',
        default => '-',
    };

    // 🔹 Ambil jadwal berdasarkan hari
    $jadwalQuery = JadwalMengajar::with(['guru', 'kelas', 'mapel'])
        ->where('hari', $hari);

    if ($this->filterGuru) {
        $jadwalQuery->where('pegawai_id', $this->filterGuru);
    }

    if ($this->filterKelas) {
        $jadwalQuery->where('kelas_id', $this->filterKelas);
    }

    $jadwal = $jadwalQuery->get();

    // 🔹 Ambil jurnal guru pada tanggal tsb
    $jurnalHariIni = JurnalGuru::whereDate('tanggal', $tanggal)->get();

    // 🔹 Mapping data ke format untuk tabel
    $this->jadwalHariIni = $jadwal->map(function ($item) use ($jurnalHariIni) {
        $sudahIsi = $jurnalHariIni->contains(function ($jurnal) use ($item) {
            return $jurnal->pegawai_id == $item->pegawai_id
                && $jurnal->kelas_id == $item->kelas_id
                && $jurnal->mapel_id == $item->mapel_id;
        });

        return [
            'guru' => $item->guru?->nm_pegawai ?? '-',
            'kelas' => $item->kelas?->nama_kelas ?? '-',
            'mapel' => $item->mapel?->nama_mapel ?? '-',
            'jam_ke' => is_array($item->jam_ke) ? implode(', ', $item->jam_ke) : $item->jam_ke,
            'sudahIsi' => $sudahIsi,
        ];
    });
}

    public function getGuruListProperty()
    {
        return Pegawai::pluck('nm_pegawai', 'id');
    }

    public function getKelasListProperty()
    {
        return Kelas::pluck('nama_kelas', 'id');
    }
}
