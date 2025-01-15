@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Edit Hutang</h1>

    <form action="{{ route('hutang.update', ['hutang' => $hutang->id, 'page' => $currentPage]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nama_supplier" class="form-label">Nama Supplier</label>
            <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" value="{{ $hutang->nama_supplier }}" required>
        </div>
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" 
                   value="{{ $hutang->tanggal->format('Y-m-d') }}" required>
        </div>
        <div class="mb-3">
            <label for="no_faktur" class="form-label">No Faktur</label>
            <input type="text" class="form-control" id="no_faktur" name="no_faktur" value="{{ $hutang->no_faktur }}" required>
        </div>
        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah</label>
            <input type="text" class="form-control" id="jumlah" name="jumlah"
                   value="{{ number_format($hutang->jumlah, 0, ',', '.') }}"
                   onfocus="removeFormatting(this)"
                   onblur="addFormatting(this)"
                   required>
        </div>
        <div class="mb-3">
            <label for="pembayaran" class="form-label">Pembayaran</label>
            <input type="text" class="form-control" id="pembayaran" name="pembayaran"
                   value="{{ number_format($hutang->pembayaran, 0, ',', '.') }}"
                   onfocus="removeFormatting(this)"
                   onblur="addFormatting(this)">
        </div>
        <div class="mb-3">
            <label for="jatuh_tempo" class="form-label">Jatuh Tempo</label>
            <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo" 
                   value="{{ $hutang->jatuh_tempo->format('Y-m-d') }}" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="Belum Lunas" {{ $hutang->status === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="Lunas" {{ $hutang->status === 'Lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="{{ route('hutang.index', ['page' => $currentPage]) }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function removeFormatting(input) {
        const value = input.value.replace(/\./g, ''); // Hilangkan titik
        input.value = value; // Set nilai tanpa format
    }

    function addFormatting(input) {
        const value = parseFloat(input.value.replace(/\./g, '')) || 0; // Konversi ke angka
        input.value = value.toLocaleString('id-ID'); // Format angka dengan locale Indonesia
    }
</script>
@endsection
