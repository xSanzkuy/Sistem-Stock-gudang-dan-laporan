@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Daftar Piutang</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <!-- Form Pencarian -->
        <form action="{{ route('piutang.index') }}" method="GET" class="d-flex">
            <input type="text" name="search" class="form-control me-2" placeholder="Cari Pelanggan atau Faktur..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary">Cari</button>
        </form>

        <!-- Tombol Tambah -->
        <a href="{{ route('piutang.create') }}" class="btn btn-primary">Tambah Piutang</a>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nama Pelanggan</th>
                <th>Tanggal</th>
                <th>No Faktur</th>
                <th>Jumlah</th>
                <th>Pembayaran</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($piutang as $item)
                <tr>
                    <td>{{ $item->nama_pelanggan }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->no_faktur }}</td>
                    <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->pembayaran, 0, ',', '.') }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        <a href="{{ route('piutang.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('piutang.destroy', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data piutang.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-4">
        {{ $piutang->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
