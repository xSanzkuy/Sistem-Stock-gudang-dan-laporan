@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Tambah Penjualan</h1>

    <!-- Tampilkan Error Jika Ada -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                    <input type="date" class="form-control" name="tanggal" id="tanggal" value="{{ old('tanggal') }}" required>
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
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Disc %</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="sales-details">
                @if (old('details'))
                    @foreach (old('details') as $index => $detail)
                        <tr>
                            <td>
                                <select name="details[{{ $index }}][produk_id]" class="form-select produk-select" data-index="{{ $index }}" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produk as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == $detail['produk_id'] ? 'selected' : '' }}>
                                            {{ $item->nama_barang }} ({{ $item->stok }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="details[{{ $index }}][qty]" class="form-control qty-input" data-index="{{ $index }}" max="0" value="{{ $detail['qty'] }}" required></td>
                            <td><input type="number" name="details[{{ $index }}][harga]" class="form-control" value="{{ $detail['harga'] }}" required></td>
                            <td><input type="number" name="details[{{ $index }}][diskon]" class="form-control" value="{{ $detail['diskon'] ?? 0 }}"></td>
                            <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>
                            <select name="details[0][produk_id]" class="form-select produk-select" data-index="0" required>
                                <option value="">Pilih Produk</option>
                                @foreach($produk as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_barang }} ({{ $item->stok }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="details[0][qty]" class="form-control qty-input" data-index="0" max="0" required></td>
                        <td><input type="number" name="details[0][harga]" class="form-control" required></td>
                        <td><input type="number" name="details[0][diskon]" class="form-control" value="0"></td>
                        <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
                    </tr>
                @endif
            </tbody>
        </table>
        <button type="button" class="btn btn-primary" id="add-row">Tambah Barang</button>

        <div class="mb-3 mt-3">
      
    <label for="pembayaran">Pembayaran</label>
    <input type="number" class="form-control" id="pembayaran" name="pembayaran" value="0" required>
</div>

            <label for="metode_pembayaran">Metode Pembayaran</label>
            <select name="metode_pembayaran" class="form-control" required>
                <option value="tunai" {{ old('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                <option value="kredit" {{ old('metode_pembayaran') == 'kredit' ? 'selected' : '' }}>Kredit</option>
            </select>
        </div>

        <!-- Submit -->
        <div class="mt-4">
            <button type="submit" class="btn btn-success">Simpan Penjualan</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = {{ old('details') ? count(old('details')) : 1 }};

    // Atur tanggal otomatis ke hari ini
    const today = new Date().toISOString().split('T')[0]; // Format ke 'YYYY-MM-DD'
    const tanggalInput = document.getElementById('tanggal');
    if (!tanggalInput.value) { // Jika belum ada nilai (saat pertama kali form dibuka)
        tanggalInput.value = today;
    }

    // Atur stok maksimal pada semua input qty saat halaman dimuat
    document.querySelectorAll('.produk-select').forEach(function (select) {
        const selectedOption = select.options[select.selectedIndex];
        const stok = parseInt(selectedOption.text.match(/\((\d+)\)/)?.[1] || 0);
        const index = select.getAttribute('data-index');
        const qtyInput = document.querySelector(`.qty-input[data-index="${index}"]`);
        qtyInput.setAttribute('max', stok);
        if (parseInt(qtyInput.value) > stok) {
            qtyInput.value = stok; // Sesuaikan nilai jika melebihi stok
        }
    });

    // Tambah baris barang
    document.getElementById('add-row').addEventListener('click', function () {
        const tbody = document.getElementById('sales-details');
        const newRow = `
            <tr>
                <td>
                    <select name="details[${rowIndex}][produk_id]" class="form-select produk-select" data-index="${rowIndex}" required>
                        <option value="">Pilih Produk</option>
                        @foreach($produk as $item)
                            <option value="{{ $item->id }}">{{ $item->nama_barang }} ({{ $item->stok }})</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="details[${rowIndex}][qty]" class="form-control qty-input" data-index="${rowIndex}" max="0" required></td>
                <td><input type="number" name="details[${rowIndex}][harga]" class="form-control" required></td>
                <td><input type="number" name="details[${rowIndex}][diskon]" class="form-control"></td>
                <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', newRow);
        rowIndex++;
    });

    // Hapus baris barang
    document.getElementById('sales-details').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });

    // Update stok maksimal saat produk dipilih
    document.getElementById('sales-details').addEventListener('change', function (e) {
        if (e.target.classList.contains('produk-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const stok = parseInt(selectedOption.text.match(/\((\d+)\)/)?.[1] || 0);
            const index = e.target.getAttribute('data-index');
            const qtyInput = document.querySelector(`.qty-input[data-index="${index}"]`);
            qtyInput.setAttribute('max', stok);
            if (parseInt(qtyInput.value) > stok) {
                qtyInput.value = stok; // Sesuaikan nilai jika melebihi stok
                alert(`Jumlah yang Anda masukkan melebihi stok. Nilai disesuaikan ke maksimal (${stok}).`);
            }
        }
    });

    // Validasi saat mengetik di input qty
    document.getElementById('sales-details').addEventListener('input', function (e) {
        if (e.target.classList.contains('qty-input')) {
            const max = parseInt(e.target.getAttribute('max'));
            if (parseInt(e.target.value) > max) {
                e.target.value = max; // Sesuaikan nilai jika melebihi stok
                alert(`Jumlah yang Anda masukkan melebihi stok. Nilai disesuaikan ke maksimal (${max}).`);
            }
        }
    });
});



</script>
@endsection
