@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Edit Pembelian</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="no_faktur">No Faktur</label>
            <input type="text" class="form-control" name="no_faktur" value="{{ $pembelian->no_faktur }}" required>
        </div>
        <div class="mb-3">
            <label for="tanggal">Tanggal</label>
            <input type="date" class="form-control" name="tanggal" value="{{ $pembelian->tanggal }}" required>
        </div>
        <div class="mb-3">
            <label for="supplier">Supplier</label>
            <input type="text" class="form-control" name="supplier" value="{{ $pembelian->supplier }}" required>
        </div>
        <div class="mb-3">
            <label for="metode_pembayaran">Metode Pembayaran</label>
            <select name="metode_pembayaran" class="form-select" required>
                <option value="tunai" {{ $pembelian->metode_pembayaran === 'tunai' ? 'selected' : '' }}>Tunai</option>
                <option value="kredit" {{ $pembelian->metode_pembayaran === 'kredit' ? 'selected' : '' }}>Kredit</option>
            </select>
        </div>

        <!-- Detail Pembelian -->
        <h4 class="mb-3">Detail Pembelian</h4>
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
            <tbody id="purchase-details">
                @foreach($pembelian->details as $index => $detail)
                <tr>
                    <td>
                        <select name="details[{{ $index }}][produk_id]" class="form-select" required>
                            <option value="">Pilih Produk</option>
                            @foreach($produk as $item)
                                <option value="{{ $item->id }}" {{ $item->id == $detail->produk_id ? 'selected' : '' }}>
                                    {{ $item->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="details[{{ $index }}][qty]" class="form-control" value="{{ $detail->qty }}" required></td>
                    <td><input type="number" name="details[{{ $index }}][harga]" class="form-control" value="{{ $detail->harga }}" required></td>
                    <td><input type="number" name="details[{{ $index }}][diskon]" class="form-control" value="{{ $detail->diskon }}"></td>
                    <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tombol Tambah dan Simpan -->
        <div class="d-flex justify-content-between mt-3">
            <button type="button" class="btn btn-primary" id="add-row">Tambah Barang</button>
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = {{ $pembelian->details->count() }};
    
    document.getElementById('add-row').addEventListener('click', function () {
        const tbody = document.getElementById('purchase-details');
        const newRow = `
            <tr>
                <td>
                    <select name="details[${rowIndex}][produk_id]" class="form-select" required>
                        <option value="">Pilih Produk</option>
                        @foreach($produk as $item)
                            <option value="{{ $item->id }}">{{ $item->nama_barang }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="details[${rowIndex}][qty]" class="form-control" required></td>
                <td><input type="number" name="details[${rowIndex}][harga]" class="form-control" required></td>
                <td><input type="number" name="details[${rowIndex}][diskon]" class="form-control"></td>
                <td><button type="button" class="btn btn-danger remove-row">Hapus</button></td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', newRow);
        rowIndex++;
    });

    document.getElementById('purchase-details').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endsection
