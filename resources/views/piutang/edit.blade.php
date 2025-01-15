@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Edit Piutang</h1>

    <form action="{{ route('piutang.update', ['piutang' => $piutang->id, 'page' => $currentPage]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nama_pelanggan">Nama Pelanggan</label>
            <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" value="{{ $piutang->nama_pelanggan }}" required>
        </div>
        <div class="mb-3">
            <label for="tanggal">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ $piutang->tanggal }}" required>
        </div>
        <div class="mb-3">
            <label for="no_faktur">No Faktur</label>
            <input type="text" class="form-control" id="no_faktur" name="no_faktur" value="{{ $piutang->no_faktur }}" required>
        </div>
        <div class="mb-3">
            <label for="jumlah">Jumlah</label>
            <input type="text" class="form-control" id="jumlah" name="jumlah" 
                   value="{{ number_format($piutang->jumlah, 0, ',', '.') }}" 
                   onfocus="removeFormatting(this)" 
                   onblur="addFormatting(this)" 
                   required>
        </div>
        <div class="mb-3">
            <label for="pembayaran">Pembayaran</label>
            <input type="text" class="form-control" id="pembayaran" name="pembayaran" 
                   value="{{ number_format($piutang->pembayaran, 0, ',', '.') }}" 
                   onfocus="removeFormatting(this)" 
                   onblur="addFormatting(this)" 
                   required>
        </div>
        <div class="mb-3">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="Belum Lunas" {{ $piutang->status === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="Lunas" {{ $piutang->status === 'Lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="{{ route('piutang.index', ['page' => $currentPage]) }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Fungsi untuk menghapus format angka
    function removeFormatting(input) {
        const value = input.value.replace(/\./g, ''); // Hilangkan titik
        input.value = value; // Set nilai tanpa format
    }

    // Fungsi untuk menambahkan format angka
    function addFormatting(input) {
        const value = parseFloat(input.value.replace(/\./g, '')) || 0; // Konversi ke angka
        input.value = value.toLocaleString('id-ID'); // Format angka dengan locale Indonesia
    }
</script>
@endsection
