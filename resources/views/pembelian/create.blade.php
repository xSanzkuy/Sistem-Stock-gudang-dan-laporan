@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Tambah Pembelian</h1>

    <!-- Tampilkan Notifikasi Error -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pembelian.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="no_faktur">No Faktur</label>
            <input type="text" class="form-control" id="no_faktur" name="no_faktur" value="{{ old('no_faktur') }}" required>
        </div>
        <div class="mb-3">
            <label for="tanggal">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ old('tanggal') }}" required>
        </div>
        <div class="mb-3">
            <label for="supplier">Supplier</label>
            <input type="text" class="form-control" id="supplier" name="supplier" value="{{ old('supplier') }}" required>
        </div>

        <!-- Detail Pembelian -->
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Jenis</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Disc %</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="purchase-details">
                @if (old('details'))
                    @foreach (old('details') as $index => $detail)
                        <tr>
                            <td><input type="text" name="details[{{ $index }}][kode]" class="form-control" value="{{ $detail['kode'] }}" required></td>
                            <td><input type="text" name="details[{{ $index }}][jenis]" class="form-control" value="{{ $detail['jenis'] }}" required></td>
                            <td><input type="text" name="details[{{ $index }}][nama_barang]" class="form-control" value="{{ $detail['nama_barang'] }}" required></td>
                            <td><input type="number" name="details[{{ $index }}][qty]" class="form-control" value="{{ $detail['qty'] }}" required></td>
                            <td><input type="number" name="details[{{ $index }}][harga]" class="form-control" value="{{ $detail['harga'] }}" required></td>
                            <td><input type="number" name="details[{{ $index }}][diskon]" class="form-control" value="{{ $detail['diskon'] ?? 0 }}" required></td>
                            <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td><input type="text" name="details[0][kode]" class="form-control" required></td>
                        <td><input type="text" name="details[0][jenis]" class="form-control" required></td>
                        <td><input type="text" name="details[0][nama_barang]" class="form-control" required></td>
                        <td><input type="number" name="details[0][qty]" class="form-control" required></td>
                        <td><input type="number" name="details[0][harga]" class="form-control" required></td>
                        <td><input type="number" name="details[0][diskon]" class="form-control" value="0" required></td>
                        <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Input PPN Manual -->
        <div class="mb-3">
            <label for="ppn">PPN (%)</label>
            <input type="number" class="form-control" id="ppn" name="ppn" value="{{ old('ppn') }}" required>
        </div>

        <div class="mb-3">
            <label for="metode_pembayaran">Metode Pembayaran</label>
            <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" required>
                <option value="tunai" {{ old('metode_pembayaran') === 'tunai' ? 'selected' : '' }}>Tunai</option>
                <option value="kredit" {{ old('metode_pembayaran') === 'kredit' ? 'selected' : '' }}>Kredit</option>
            </select>
        </div>

        <button type="button" class="btn btn-primary" id="add-row">Tambah Barang</button>
        <button type="submit" class="btn btn-success">Simpan Pembelian</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Set tanggal otomatis ke hari ini
    const today = new Date().toISOString().split('T')[0];
    const tanggalInput = document.getElementById('tanggal');
    if (!tanggalInput.value) {
        tanggalInput.value = today; // Set nilai default hanya jika input kosong
    }

    // Menambahkan baris baru pada tabel
    let rowIndex = {{ old('details') ? count(old('details')) : 1 }};
    document.getElementById('add-row').addEventListener('click', function () {
        const tbody = document.getElementById('purchase-details');
        const newRow = `
            <tr>
                <td><input type="text" name="details[${rowIndex}][kode]" class="form-control" required></td>
                <td><input type="text" name="details[${rowIndex}][jenis]" class="form-control" required></td>
                <td><input type="text" name="details[${rowIndex}][nama_barang]" class="form-control" required></td>
                <td><input type="number" name="details[${rowIndex}][qty]" class="form-control" required></td>
                <td><input type="number" name="details[${rowIndex}][harga]" class="form-control" required></td>
                <td><input type="number" name="details[${rowIndex}][diskon]" class="form-control" value="0" required></td>
                <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', newRow);
        rowIndex++;
    });

    // Hapus baris dari tabel
    document.getElementById('purchase-details').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
  });
</script>
@endsection
