@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Daftar Penjualan</h1>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    <!-- Notifikasi Error -->
    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    <!-- Form Pencarian -->
    <form action="{{ route('penjualan.index') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari No Faktur atau Penerima..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Cari</button>
        </div>
    </form>

    <!-- Tabel Penjualan -->
    <a href="{{ route('penjualan.create') }}" class="btn btn-primary mb-3">Tambah Penjualan</a>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Penerima</th>
                <th>Nama Barang</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penjualan as $item)
                <tr>
                    <td>{{ $item->no_faktur }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->penerima }}</td>
                    <td>{{ $item->produk_nama }}</td>
                    <td>{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    <td>
                        <!-- Tombol Detail -->
                        <a href="{{ route('penjualan.show', ['penjualan' => $item->id, 'page' => request('page')]) }}" class="btn btn-info btn-sm">Detail</a>
                        
                        <!-- Tombol Edit -->
                        <a href="{{ route('penjualan.edit', ['penjualan' => $item->id, 'page' => request('page')]) }}" class="btn btn-warning btn-sm">Edit</a>
                        
                        <!-- Tombol Hapus -->
                        <form action="{{ route('penjualan.destroy', ['penjualan' => $item->id, 'page' => request('page')]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm delete-button" data-name="{{ $item->no_faktur }}">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data penjualan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $penjualan->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Event handler untuk tombol hapus dengan konfirmasi
    const deleteButtons = document.querySelectorAll('.delete-button');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const detailName = this.getAttribute('data-name');
            const form = this.closest('form');

            Swal.fire({
                title: `Apakah Anda yakin?`,
                text: `Penjualan dengan No Faktur ${detailName} akan dihapus!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
