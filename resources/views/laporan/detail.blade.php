@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Laporan Keuntungan</h1>
    <table class="table table-bordered">
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
            <tr>
                <td>{{ $laporan->tanggal }}</td>
                <td>{{ number_format($laporan->total_transaksi, 0) }}</td>
                <td>Rp {{ number_format($laporan->total_modal, 0) }}</td>
                <td>Rp {{ number_format($laporan->total_penjualan, 0) }}</td>
                <td>Rp {{ number_format($laporan->total_keuntungan, 0) }}</td>
            </tr>
        </tbody>
    </table>

    <h3>Detail Keuntungan</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Qty</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Keuntungan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan->details as $detail)
                <tr>
                    <td>{{ $detail->nama_produk }}</td>
                    <td>{{ $detail->qty }}</td>
                    <td>Rp {{ number_format($detail->harga_beli, 2) }}</td>
                    <td>Rp {{ number_format($detail->harga_jual, 2) }}</td>
                    <td>Rp {{ number_format($detail->keuntungan, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Tidak ada detail keuntungan untuk laporan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
