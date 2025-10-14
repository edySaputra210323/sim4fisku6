<table>
    <thead>
        <tr>
            <th><strong>Tanggal</strong></th>
            <th><strong>Jam Ke</strong></th>
            <th><strong>Materi</strong></th>
            <th><strong>Kegiatan</strong></th>
            <th colspan="3" style="text-align:center"><strong>Kehadiran Peserta Didik</strong></th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th><strong>Sakit</strong></th>
            <th><strong>Izin</strong></th>
            <th><strong>Alpa</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
            <tr>
                <td>{{ $row['tanggal'] }}</td>
                <td>{{ $row['jam_ke'] }}</td>
                <td>{{ $row['materi'] }}</td>
                <td>{{ $row['kegiatan'] }}</td>
                <td>{{ $row['sakit'] ?: '-' }}</td>
                <td>{{ $row['izin'] ?: '-' }}</td>
                <td>{{ $row['alpa'] ?: '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
