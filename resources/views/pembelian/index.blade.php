@extends('layouts.app')  

@section('content')  
<div class="container mt-5">  
    <h1 class="mb-4">Daftar Pembelian</h1>  

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

    <!-- Form Pencarian dan Filter -->  
    <form action="{{ route('pembelian.index') }}" method="GET" class="mb-4 d-flex align-items-center">  
        <input type="text" name="search" class="form-control me-2" placeholder="Cari No Faktur atau Supplier" value="{{ request('search') }}">  
        <select name="filter" class="form-select me-2">  
            <option value="daily" {{ request('filter') == 'daily' ? 'selected' : '' }}>Harian</option>  
            <option value="monthly" {{ request('filter') == 'monthly' ? 'selected' : '' }}>Bulanan</option>  
            <option value="yearly" {{ request('filter') == 'yearly' ? 'selected' : '' }}>Tahunan</option>  
        </select>  
        <button class="btn btn-primary me-2" type="submit">Cari</button>  
        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary">Reset</a>  
    </form>  

    <!-- Tombol Tambah Pembelian -->  
    <a href="{{ route('pembelian.create') }}" class="btn btn-primary mb-3">Tambah Pembelian</a>  

    <!-- Tabel Pembelian -->  
    <table class="table table-bordered">  
        <thead class="table-dark">  
            <tr>  
                <th>No Faktur</th>  
                <th>Tanggal</th>  
                <th>Supplier</th>  
                <th>Total Harga</th>  
                <th>Status</th>  
                <th>Aksi</th>  
            </tr>  
        </thead>  
        <tbody>  
            @forelse($pembelian as $item)  
                <tr>  
                    <td>{{ $item->no_faktur }}</td>  
                    <td>{{ $item->tanggal }}</td>  
                    <td>{{ $item->supplier }}</td>  
                    <td>{{ number_format($item->total_harga, 0, ',', '.') }}</td>  
                    <td>{{ $item->status }}</td>  
                    <td>  
                        <a href="{{ route('pembelian.show', $item->id) }}" class="btn btn-info btn-sm">Detail</a>  
                        <a href="{{ route('pembelian.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>  
                        <form action="{{ route('pembelian.destroy', $item->id) }}" method="POST" style="display:inline;">  
                            @csrf  
                            @method('DELETE')  
                            <button type="button" class="btn btn-danger btn-sm delete-button" data-name="{{ $item->no_faktur }}">Hapus</button>  
                        </form>  
                    </td>  
                </tr>  
            @empty  
                <tr>  
                    <td colspan="6" class="text-center">Tidak ada data pembelian.</td>  
                </tr>  
            @endforelse  
        </tbody>  
    </table>  

    <!-- Pagination -->  
    <div class="d-flex justify-content-center mt-4">  
        {{ $pembelian->links('pagination::bootstrap-5') }}  
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
            const form = this.closest('form');  

            Swal.fire({  
                title: `Apakah Anda yakin?`,  
                text: `Pembelian dengan No Faktur ${detailName} akan dihapus!`,  
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