@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Detail Piutang</h1>

    <!-- Menampilkan Detail Piutang -->
    <div class="mb-3">
        <h3>Detail Piutang</h3>
        <p><strong>Nama Pelanggan:</strong> {{ $piutang->nama_pelanggan }}</p>
        <p><strong>Tanggal:</strong> {{ $piutang->tanggal }}</p>
        <p><strong>No Faktur:</strong> {{ $piutang->no_faktur }}</p>
        <p><strong>Jumlah:</strong> {{ $piutang->jumlah }}</p>
        <p><strong>Pembayaran:</strong> {{ $piutang->pembayaran }}</p>
        <p><strong>Kekurangan:</strong> {{ $piutang->kekurangan }}</p>
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
                        <td>{{ $history->created_at }}</td>
                        <td>{{ $history->jumlah }}</td>
                        <td>{{ $history->pembayaran }}</td>
                        <td>{{ $history->kekurangan }}</td>
                        <td>{{ $history->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('piutang.index') }}" class="btn btn-primary">Kembali ke Daftar Piutang</a>
</div>
@endsection
