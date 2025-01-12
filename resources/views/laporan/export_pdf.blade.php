<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuntungan</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Laporan Keuntungan</h1>
    <p>Periode: {{ $periode }}</p>
    <p>Tanggal: {{ $tanggal }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Total Transaksi</th>
                <th>Total Modal</th>
                <th>Total Penjualan</th>
                <th>Total Keuntungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan as $item)
                <tr>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ number_format($item->total_transaksi, 0, ',', '.') }}</td>
<td>{{ number_format($item->total_modal, 0, ',', '.') }}</td>
<td>{{ number_format($item->total_penjualan, 0, ',', '.') }}</td>
<td>{{ number_format($item->total_keuntungan, 0, ',', '.') }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
