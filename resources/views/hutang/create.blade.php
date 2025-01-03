@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Tambah Hutang</h1>
    <form action="{{ route('hutang.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nama_supplier">Nama Supplier</label>
            <input type="text" class="form-control" name="nama_supplier" required>
        </div>
        <div class="mb-3">
            <label for="tanggal">Tanggal</label>
            <input type="date" class="form-control" name="tanggal" required>
        </div>
        <div class="mb-3">
            <label for="no_faktur">No Faktur</label>
            <input type="text" class="form-control" name="no_faktur" required>
        </div>
        <div class="mb-3">
            <label for="jumlah">Jumlah</label>
            <input type="number" class="form-control" name="jumlah" required>
        </div>
        <div class="mb-3">
            <label for="jatuh_tempo">Jatuh Tempo</label>
            <input type="date" class="form-control" name="jatuh_tempo" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
