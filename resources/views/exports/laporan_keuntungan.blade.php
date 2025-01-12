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
        @foreach ($laporan as $data)
            <tr>
                <td>{{ $data->tanggal }}</td>
                <td>{{ $data->total_transaksi }}</td>
                <td>{{ $data->total_modal }}</td>
                <td>{{ $data->total_penjualan }}</td>
                <td>{{ $data->total_keuntungan }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
