@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Tambah Piutang</h1>

    <form action="{{ route('piutang.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nama_pelanggan">Nama Pelanggan</label>
            <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" required>
        </div>
        <div class="mb-3">
            <label for="tanggal">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
        </div>
        <div class="mb-3">
            <label for="no_faktur">No Faktur</label>
            <input type="text" class="form-control" id="no_faktur" name="no_faktur" required>
        </div>
        <div class="mb-3">
            <label for="jumlah">Jumlah</label>
            <input type="number" class="form-control" id="jumlah" name="jumlah" required>
        </div>
        <div class="mb-3">
            <label for="pembayaran">Pembayaran</label>
            <input type="number" class="form-control" id="pembayaran" name="pembayaran" value="0">
        </div>
        <div class="mb-3">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="Belum Lunas">Belum Lunas</option>
                <option value="Lunas">Lunas</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('piutang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
