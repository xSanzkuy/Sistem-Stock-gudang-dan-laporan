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
                <th>Pembayaran</th>
                <th>Kekurangan</th>
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
                    <td>{{ number_format($item->pembayaran, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->kekurangan, 0, ',', '.') }}</td>
                    <td>{{ $item->jatuh_tempo }}</td>
                    <td>{{ $item->status }}</td>
                    <td>
                    <a href="{{ route('hutang.show', ['id' => $item->id, 'page' => request('page')]) }}" class="btn btn-info btn-sm">Riwayat</a>
                    <a href="{{ route('hutang.edit', ['id' => $item->id, 'page' => request('page')]) }}" class="btn btn-warning btn-sm">Edit</a>

                        <!-- Tombol Hapus -->
                        <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $item->id }}" data-name="{{ $item->nama_supplier }}">Hapus</button>

                        <!-- Form Hapus -->
                        <form id="form-delete-{{ $item->id }}" action="{{ route('hutang.destroy', $item->id) }}" method="POST" style="display:none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data hutang.</td>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Pastikan SweetAlert2 terdeteksi
    if (typeof Swal === 'undefined') {
        console.error("SweetAlert2 (Swal) tidak terdeteksi! Pastikan library terpasang dengan benar.");
        return;
    }

    // Event Listener untuk Tombol Hapus
    document.querySelectorAll('.btn-delete').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Hutang dengan nama supplier ${name} akan dihapus secara permanen!`,
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
