@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Detail Hutang</h1>

    <!-- Menampilkan Detail Hutang -->
    <div class="mb-3">
        <h3>Detail Hutang</h3>
        <p><strong>Nama Supplier:</strong> {{ $hutang->nama_supplier }}</p>
        <p><strong>Tanggal:</strong> {{ $hutang->tanggal }}</p>
        <p><strong>No Faktur:</strong> {{ $hutang->no_faktur }}</p>
        <p><strong>Jumlah:</strong> Rp {{ number_format($hutang->jumlah, 0, ',', '.') }}</p>
        <p><strong>Pembayaran:</strong> Rp {{ number_format($hutang->pembayaran, 0, ',', '.') }}</p>
        <p><strong>Kekurangan:</strong> Rp {{ number_format($hutang->kekurangan, 0, ',', '.') }}</p>
        <p><strong>Status:</strong> {{ $hutang->status }}</p>
    </div>

    <!-- Menampilkan Riwayat Perubahan -->
    <div class="mb-3">
        <h3>Riwayat Perubahan Hutang</h3>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Waktu Perubahan</th>
                    <th>Jumlah</th>
                    <th>Pembayaran</th>
                    <th>Kekurangan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hutang->hutangHistories as $history)
                    <tr>
                        <td>{{ $history->created_at->format('d-m-Y H:i:s') }}</td>
                        <td>Rp {{ number_format($history->jumlah, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($history->pembayaran, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($history->kekurangan, 0, ',', '.') }}</td>
                        <td>{{ $history->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Tombol Kembali -->
    <a href="{{ route('hutang.index', ['page' => $currentPage]) }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
