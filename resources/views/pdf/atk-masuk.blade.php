<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan ATK Masuk</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #333; padding: 6px; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Laporan ATK Masuk</h2>

    <p><strong>Tanggal:</strong> {{ $record->tanggal->format('d/m/Y') }}</p>
    <p><strong>Nomor Nota:</strong> {{ $record->nomor_nota }}</p>

    <h3>Detail Barang</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Harga Satuan</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $item)
                <tr>
                    <td>{{ $item->atk->nama_atk }}</td>
                    <td>{{ $item->qty }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align: left;">Total Bayar:</th>
                <th style="text-align: right;">Rp {{ number_format($grand_total, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
