@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Tambah Produk</h1>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('produk.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="kode">Kode Produk</label>
            <input type="text" class="form-control" name="kode" required>
        </div>
        <div class="mb-3">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" class="form-control" name="nama_barang" required>
        </div>
        <div class="mb-3">
            <label for="jenis">Jenis</label>
            <input type="text" class="form-control" name="jenis" required>
        </div>
        <div class="mb-3">
            <label for="stok">Stok</label>
            <input type="number" class="form-control" name="stok" required>
        </div>
        <div class="mb-3">
            <label for="harga_beli">Harga Beli</label>
            <input type="number" class="form-control" name="harga_beli" required>
        </div>
        <div class="mb-3">
            <label for="harga_jual">Harga Jual</label>
            <input type="number" class="form-control" name="harga_jual" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan Produk</button>
    </form>
</div>
@endsection
