@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Daftar Piutang</h1>

    <!-- Notifikasi Berhasil -->
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
            });
        </script>
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
            <th>Kekurangan</th>
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
                <td>{{ number_format($item->kekurangan, 0, ',', '.') }}</td>
                <td>{{ $item->status }}</td>
                <td>
                <a href="{{ route('piutang.show', ['id' => $item->id, 'page' => request('page')]) }}" class="btn btn-info btn-sm">Riwayat</a>

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
                <td colspan="8" class="text-center">Tidak ada data piutang.</td>
            </tr>
        @endforelse
    </tbody>
</table>


    <div class="d-flex justify-content-center mt-4">
        {{ $piutang->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Event Listener untuk Tombol Hapus
    document.querySelectorAll('.btn-delete').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Data piutang atas nama ${name} akan dihapus secara permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`form-delete-${id}`).submit();
                }
            });
        });
    });
});
</script>
@endsection
