@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Tambah Penjualan</h1>

    <!-- Tampilkan Error Jika Ada -->
    @if($errors->any())
    <div class="alert alert-danger">
        <strong>Terjadi kesalahan!</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

    <form action="{{ route('penjualan.store') }}" method="POST">
        @csrf
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="no_faktur" class="form-label">No Faktur</label>
                    <input type="text" class="form-control" name="no_faktur" id="no_faktur" value="{{ old('no_faktur') }}" required>
                </div>
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                </div>
                <div class="mb-3">
                    <label for="penerima" class="form-label">Kepada</label>
                    <input type="text" class="form-control" name="penerima" id="penerima" value="{{ old('penerima') }}" placeholder="Nama penerima" required>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <input type="text" class="form-control" name="alamat" id="alamat" value="{{ old('alamat') }}" placeholder="Alamat penerima">
                </div>
            </div>
        </div>

        <!-- Detail Penjualan -->
        <h4 class="mb-3">Detail Barang</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Disc %</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="sales-details">
                <tr>
                    <td>
                        <input type="text" name="details[0][kode]" class="form-control kode-barang-input" data-index="0" required>
                    </td>
                    <td>
                        <input type="text" name="details[0][nama_barang]" class="form-control nama-barang-input" data-index="0" readonly>
                    </td>
                    <td>
                        <input type="number" name="details[0][qty]" class="form-control qty-input" data-index="0" value="1" required>
                    </td>
                    <td>
                        <input type="number" name="details[0][harga]" class="form-control harga-input" data-index="0" value="0" required>
                    </td>
                    <td>
                        <input type="number" name="details[0][diskon]" class="form-control diskon-input" data-index="0" value="0">
                    </td>
                    <td>
                        <input type="text" name="details[0][subtotal]" class="form-control subtotal-input" data-index="0" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-row">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary mb-3" id="add-row">Tambah Barang</button>

        <!-- Total Harga -->
        <div class="mb-3">
            <label for="total_harga" class="form-label">Total Harga</label>
            <input type="text" id="total_harga" class="form-control" value="0" readonly>
        </div>

        <!-- Pembayaran -->
        <div class="mb-3">
            <label for="pembayaran" class="form-label">Pembayaran</label>
            <input type="number" name="pembayaran" id="pembayaran" class="form-control" value="{{ old('pembayaran', 0) }}" required>
        </div>
        <div class="mb-3">
            <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
            <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                <option value="tunai" {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                <option value="kredit" {{ old('metode_pembayaran') == 'kredit' ? 'selected' : '' }}>Kredit</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Simpan Penjualan</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = 1;

    // Fungsi menghitung subtotal
    function calculateSubtotal(row) {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const harga = parseFloat(row.querySelector('.harga-input').value) || 0;
        const diskon = parseFloat(row.querySelector('.diskon-input').value) || 0;

        const subtotal = qty * harga * (1 - diskon / 100);
        row.querySelector('.subtotal-input').value = subtotal.toFixed(0); // Tanpa desimal

        calculateTotalHarga();
    }

    // Fungsi menghitung total harga
    function calculateTotalHarga() {
        let total = 0;
        document.querySelectorAll('.subtotal-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });

         // Format angka ke dalam format Indonesia tanpa desimal
    document.getElementById('total_harga').value = total.toLocaleString('id-ID').replace(/,.*$/, '');
}

    // Event Listener untuk input perubahan
    document.getElementById('sales-details').addEventListener('input', function (e) {
        const row = e.target.closest('tr');
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('harga-input') || e.target.classList.contains('diskon-input')) {
            calculateSubtotal(row);
        }
    });

    // Tambah baris barang
    document.getElementById('add-row').addEventListener('click', function () {
        const tbody = document.getElementById('sales-details');
        const newRow = `
            <tr>
                <td>
                    <input type="text" name="details[${rowIndex}][kode]" class="form-control kode-barang-input" data-index="${rowIndex}" required>
                </td>
                <td>
                    <input type="text" name="details[${rowIndex}][nama_barang]" class="form-control nama-barang-input" data-index="${rowIndex}" readonly>
                </td>
                <td>
                    <input type="number" name="details[${rowIndex}][qty]" class="form-control qty-input" data-index="${rowIndex}" value="1" required>
                </td>
                <td>
                    <input type="number" name="details[${rowIndex}][harga]" class="form-control harga-input" data-index="${rowIndex}" value="0" required>
                </td>
                <td>
                    <input type="number" name="details[${rowIndex}][diskon]" class="form-control diskon-input" data-index="${rowIndex}" value="0">
                </td>
                <td>
                    <input type="text" name="details[${rowIndex}][subtotal]" class="form-control subtotal-input" data-index="${rowIndex}" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-row">Hapus</button>
                </td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', newRow);
        rowIndex++;
    });

    // Hapus baris barang
    document.getElementById('sales-details').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            calculateTotalHarga();
        }
    });

    // Hitung ulang subtotal saat halaman dimuat
    document.querySelectorAll('#sales-details tr').forEach(row => calculateSubtotal(row));
});
</script>
@endsection
