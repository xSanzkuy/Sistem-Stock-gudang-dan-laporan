@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Daftar Produk</h1>

    <!-- Notifikasi -->
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

    <!-- Filter dan Search -->
    <div class="d-flex mb-3">
        <form action="{{ route('produk.index') }}" method="GET" class="me-3">
            <div class="input-group">
                <select name="jenis" class="form-select">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisProduk as $jenis)
                        <option value="{{ $jenis }}" {{ request('jenis') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>

        <form action="{{ route('produk.index') }}" method="GET" class="flex-grow-1">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari Produk..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-secondary">Cari</button>
            </div>
        </form>
    </div>

    <!-- Tabel Produk -->
    <a href="{{ route('produk.create') }}" class="btn btn-primary mb-3">Tambah Produk</a>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Stok</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produk as $item)
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->jenis }}</td>
                    <td>{{ $item->stok }}</td>
                    <td>{{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('produk.show', $item->id) }}" class="btn btn-info btn-sm mb-1">Detail</a>
                        <a href="{{ route('produk.edit', $item->id) }}" class="btn btn-warning btn-sm mb-1">Edit</a>
                        <form action="{{ route('produk.destroy', $item->id) }}" method="POST" style="display:inline;" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger btn-sm delete-button" data-name="Produk {{ $item->nama_barang }}">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data produk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
    {{ $produk->links('pagination::bootstrap-5') }}
</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-button');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const detailName = this.getAttribute('data-name');
            const form = this.closest('.delete-form');

            Swal.fire({
                title: `Apakah Anda yakin?`,
                text: `${detailName} akan dihapus!`,
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
