@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">FAKTUR PEMBELIAN</h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <p><strong>No Faktur:</strong> {{ $pembelian->no_faktur }}</p>
            <p><strong>Tanggal:</strong> {{ $pembelian->tanggal }}</p>
        </div>
        <div class="col-md-6 text-end">
            <p><strong>Dari:</strong> {{ $pembelian->supplier }}</p>
            <p><strong>Alamat:</strong> {{ $pembelian->alamat ?? '__________________________' }}</p>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Qty</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Disc %</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
    @foreach($pembelian->details as $detail)
        <tr>
            <td>{{ $detail->qty }}</td>
            <td>{{ $detail->produk->nama_barang ?? 'Produk tidak ditemukan' }}</td>
            <td>{{ number_format($detail->harga, 0, ',', '.') }}</td>
            <td>{{ number_format($detail->diskon, 0, ',', '.') }}</td>
            <td>{{ number_format($detail->jumlah, 0, ',', '.') }}</td>
        </tr>
    @endforeach
</tbody>

        <tfoot>
            <tr>
                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                <td>{{ number_format($pembelian->subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-end"><strong>PPN:</strong></td>
                <td>{{ number_format($pembelian->ppn, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                <td>{{ number_format($pembelian->total_harga, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="row mt-5">
        <div class="col-md-6 text-center">
            <p><strong>Hormat Kami,</strong></p>
            <br><br><br>
            <p>_________________________</p>
        </div>
        <div class="col-md-6 text-center">
            <p><strong>Supplier,</strong></p>
            <br><br><br>
            <p>_________________________</p>
        </div>
    </div>

    <div class="text-center mt-4 no-print">
        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">Kembali</a>
        <button class="btn btn-primary" onclick="window.print()">Cetak</button>
    </div>
</div>

<style>
    @media print {
        /* Sembunyikan elemen navigasi dan tombol */
        .no-print, .navbar, .sidebar {
            display: none !important;
        }

        /* Atur ukuran halaman cetak untuk A5 */
        @page {
            size: A5 portrait;
            margin: 10mm;
        }

        /* Margin untuk elemen cetak */
        body {
            margin: 0;
            padding: 0;
            font-size: 12px;
        }

        .container {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        h1 {
            font-size: 18px;
        }

        p {
            font-size: 12px;
            margin: 0;
        }

        .row:last-child {
            margin-bottom: 30px;
        }

        .text-end {
            text-align: right;
        }

        /* Atur jarak tanda tangan */
        .row.mt-5 .col-md-6 {
            display: inline-block;
            width: 45%;
            vertical-align: top;
        }
    }
</style>
@endsection
