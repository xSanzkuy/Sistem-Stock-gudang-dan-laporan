@extends('layouts.app')

@section('content')

    <!-- Menampilkan Detail Piutang -->
    <div class="container mt-5">
    <h1 class="text-center mb-4">Detail Piutang</h1>

    <!-- Menampilkan Detail Piutang -->
    <div class="mb-3">
        <h3>Detail Piutang</h3>
        <p><strong>Nama Pelanggan:</strong> {{ $piutang->nama_pelanggan }}</p>
        <p><strong>Tanggal:</strong> {{ $piutang->tanggal }}</p>
        <p><strong>No Faktur:</strong> {{ $piutang->no_faktur }}</p>
        <p><strong>Jumlah:</strong> Rp {{ number_format($piutang->jumlah, 0, ',', '.') }}</p>
        <p><strong>Pembayaran:</strong> Rp {{ number_format($piutang->pembayaran, 0, ',', '.') }}</p>
        <p><strong>Kekurangan:</strong> Rp {{ number_format($piutang->kekurangan, 0, ',', '.') }}</p>
        <p><strong>Status:</strong> {{ $piutang->status }}</p>
    </div>

    <!-- Menampilkan Riwayat Perubahan -->
    <div class="mb-3">
        <h3>Riwayat Perubahan Piutang</h3>
        <table class="table table-bordered">
    <thead>
        <tr>
            <th>Waktu Perubahan</th>
            <th>Jumlah</th>
            <th>Pembayaran</th>
            <th>Kekurangan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($piutang->piutangHistories as $history)
            <tr>
                <td>{{ \Carbon\Carbon::parse($history->created_at)->format('d-m-Y H:i:s') }}</td>
                <td>Rp {{ number_format($history->jumlah, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($history->pembayaran, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($history->kekurangan, 0, ',', '.') }}</td>
                <td>{{ $history->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

    </div>

    <a href="{{ route('piutang.index', ['page' => $currentPage]) }}" class="btn btn-secondary">Kembali</a>
</div>

@endsection
