@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Daftar Hutang</h1>
    <a href="{{ route('hutang.create') }}" class="btn btn-primary mb-3">Tambah Hutang</a>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Nama Supplier</th>
                <th>Tanggal</th>
                <th>No Faktur</th>
                <th>Jumlah</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($hutang as $item)
                <tr>
                    <td>{{ $item->nama_supplier }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->no_faktur }}</td>
                    <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td>{{ $item->jatuh_tempo }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                        <a href="{{ route('hutang.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('hutang.destroy', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data hutang.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tampilkan tautan pagination -->
    <div class="d-flex justify-content-center mt-4">
    {{ $hutang->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
