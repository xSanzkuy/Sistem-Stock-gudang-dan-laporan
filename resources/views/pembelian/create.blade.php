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
                <tr>
                    <td>
                        <select name="details[0][kode]" class="form-control select-kode">
                            <option value="">Pilih Kode Barang</option>
                            @foreach ($produk as $item)
                                <option value="{{ $item->kode }}" 
                                        data-jenis="{{ $item->jenis }}" 
                                        data-nama="{{ $item->nama_barang }}" 
                                        data-harga="{{ number_format($item->harga_beli, 0, '', '') }}">
                                    {{ $item->kode }} - {{ $item->nama_barang }} (Stok: {{ $item->stok }})
                                </option>
                            @endforeach
                        </select>
                        <input type="text" name="details[0][kode_manual]" class="form-control input-kode mt-2 d-none" placeholder="Kode Barang Baru">
                    </td>
                    <td><input type="text" name="details[0][jenis]" class="form-control" required></td>
                    <td><input type="text" name="details[0][nama_barang]" class="form-control nama-barang" required></td>
                    <td><input type="number" name="details[0][qty]" class="form-control qty" min="0" required></td>
                    <td><input type="number" name="details[0][harga]" class="form-control harga" min="0" required></td>
                    <td><input type="number" name="details[0][diskon]" class="form-control diskon" value="0" min="0" required></td>
                    <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
                </tr>
            </tbody>
        </table>

        <!-- Input PPN Manual -->
        <div class="mb-3">
            <label for="ppn">PPN (%)</label>
            <input type="number" class="form-control" id="ppn" name="ppn" value="{{ old('ppn') }}">
        </div>

        <div class="mb-3">
            <label for="pembayaran">Pembayaran (Rp)</label>
            <input type="number" class="form-control" id="pembayaran" name="pembayaran" value="{{ old('pembayaran') }}">
        </div>

        <!-- Total Harga -->
        <div class="mb-3">
            <label for="total_harga">Total Harga (Rp)</label>
            <input type="text" class="form-control" id="total_harga" name="total_harga" value="0" readonly>
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
    const today = new Date().toISOString().split('T')[0];
    const tanggalInput = document.getElementById('tanggal');
    if (!tanggalInput.value) {
        tanggalInput.value = today;
    }

    let rowIndex = 1;

    // Tambahkan baris baru
    document.getElementById('add-row').addEventListener('click', function () {
    const tbody = document.getElementById('purchase-details');
    const newRow = `
        <tr>
            <td>
                <select name="details[${rowIndex}][kode]" class="form-control select-kode">
                    <option value="">Pilih Kode Barang</option>
                    @foreach ($produk as $item)
                        <option value="{{ $item->kode }}" 
                                data-jenis="{{ $item->jenis }}" 
                                data-nama="{{ $item->nama_barang }}" 
                                data-harga="{{ $item->harga_beli }}">
                            {{ $item->kode }} - {{ $item->nama_barang }} (Stok: {{ $item->stok }})
                        </option>
                    @endforeach
                </select>
                <input type="text" name="details[${rowIndex}][kode_manual]" class="form-control input-kode mt-2" placeholder="Kode Barang Baru">
              </td>
                <td><input type="text" name="details[${rowIndex}][jenis]" class="form-control" required></td>
                <td><input type="text" name="details[${rowIndex}][nama_barang]" class="form-control nama-barang" required></td>
                <td><input type="number" name="details[${rowIndex}][qty]" class="form-control qty" min="0" required></td>
                <td><input type="number" name="details[${rowIndex}][harga]" class="form-control harga" min="0" required></td>
                <td><input type="number" name="details[${rowIndex}][diskon]" class="form-control diskon" value="0" min="0" required></td>
                <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', newRow);
        rowIndex++;
    });

    // Hapus baris
    document.getElementById('purchase-details').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            calculateTotal();
        }
    });

    // Perbarui nama barang saat dropdown berubah
    document.getElementById('purchase-details').addEventListener('change', function (e) {
    if (e.target.classList.contains('select-kode')) {
        const row = e.target.closest('tr');
        const selectedOption = e.target.selectedOptions[0];

        // Input yang akan diisi otomatis
        const jenisInput = row.querySelector('input[name^="details"][name$="[jenis]"]');
        const namaBarangInput = row.querySelector('input[name^="details"][name$="[nama_barang]"]');
        const hargaInput = row.querySelector('input[name^="details"][name$="[harga]"]');
        const kodeManualInput = row.querySelector('input[name^="details"][name$="[kode_manual]"]');

        if (selectedOption.value) {
            // Jika kode barang dipilih dari dropdown
            jenisInput.value = selectedOption.dataset.jenis || '';
            namaBarangInput.value = selectedOption.dataset.nama || '';
            hargaInput.value = selectedOption.dataset.harga || 0;
            kodeManualInput.value = ''; // Kosongkan input manual jika dropdown dipilih
            kodeManualInput.classList.add('d-none'); // Sembunyikan input manual
        } else {
            // Reset jika dropdown kosong
            jenisInput.value = '';
            namaBarangInput.value = '';
            hargaInput.value = 0;
            kodeManualInput.classList.remove('d-none'); // Tampilkan input manual
        }
    }
});

// Event listener untuk mengatur kembali input manual jika kode manual diisi
document.getElementById('purchase-details').addEventListener('input', function (e) {
    if (e.target.classList.contains('input-kode')) {
        const row = e.target.closest('tr');

        // Reset input otomatis jika kode barang manual diisi
        row.querySelector('select.select-kode').value = '';
        row.querySelector('input[name^="details"][name$="[jenis]"]').value = '';
        row.querySelector('input[name^="details"][name$="[nama_barang]"]').value = '';
        row.querySelector('input[name^="details"][name$="[harga]"]').value = 0;
    }
});

    // Toggle antara input manual dan dropdown
    document.getElementById('purchase-details').addEventListener('change', function (e) {
        if (e.target.classList.contains('select-kode')) {
            const inputKode = e.target.closest('td').querySelector('.input-kode');
            if (e.target.value === '') {
                inputKode.classList.remove('d-none');
            } else {
                inputKode.classList.add('d-none');
                inputKode.value = '';
            }
        }
    });

    // Hitung total harga
    document.getElementById('purchase-details').addEventListener('input', calculateTotal);
    document.getElementById('ppn').addEventListener('input', calculateTotal);

    function calculateTotal() {
        let subtotal = 0;

        document.querySelectorAll('#purchase-details tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty')?.value || 0);
            const harga = parseFloat(row.querySelector('.harga')?.value || 0);
            const diskon = parseFloat(row.querySelector('.diskon')?.value || 0);

            if (qty > 0 && harga > 0) {
                const diskonAmount = (harga * qty) * (diskon / 100);
                subtotal += (harga * qty) - diskonAmount;
            }
        });

        const ppn = parseFloat(document.getElementById('ppn').value || 0);
        const total = subtotal + (subtotal * (ppn / 100));
        document.getElementById('total_harga').value = total.toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }
});
</script>
@endsection
