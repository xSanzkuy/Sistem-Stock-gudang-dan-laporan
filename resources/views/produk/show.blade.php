@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Detail Produk: {{ $produk->nama_barang }}</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Informasi Produk</h5>
            <p><strong>Kode:</strong> {{ $produk->kode }}</p>
            <p><strong>Nama Barang:</strong> {{ $produk->nama_barang }}</p>
            <p><strong>Jenis:</strong> {{ $produk->jenis }}</p>
            <p><strong>Stok:</strong> {{ $produk->stok }}</p>
            <p><strong>Harga Beli:</strong> Rp {{ number_format($produk->harga_beli, 0, ',', '.') }}</p>
            <p><strong>Harga Jual:</strong> Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</p>
        </div>
    </div>

    <h5 class="mb-3">Riwayat Pembelian</h5>
    @if ($produk->pembelian->isEmpty())
        <p class="text-muted">Belum ada riwayat pembelian untuk produk ini.</p>
    @else
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>No Faktur</th>
                    <th>Supplier</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
    @forelse ($produk->itemPembelian as $item)
        <tr>
            <td>{{ $item->pembelian->no_faktur ?? '-' }}</td>
            <td>{{ $item->pembelian->supplier ?? '-' }}</td>
            <td>{{ $item->pembelian->tanggal ?? '-' }}</td>
            <td>{{ $item->qty ?? 0 }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center">Belum ada data pembelian untuk produk ini.</td>
        </tr>
    @endforelse
</tbody>


        </table>
    @endif

    <a href="{{ route('produk.index') }}" class="btn btn-secondary mt-3">Kembali</a>
</div>
@endsection
