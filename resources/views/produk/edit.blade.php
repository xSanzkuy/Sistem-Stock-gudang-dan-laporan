@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Edit Produk</h1>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('produk.update', $produk->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="kode">Kode Produk</label>
            <input type="text" class="form-control" name="kode" value="{{ old('kode', $produk->kode) }}" required>
        </div>

        <div class="mb-3">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" class="form-control" name="nama_barang" value="{{ old('nama_barang', $produk->nama_barang) }}" required>
        </div>

        <div class="mb-3">
            <label for="jenis">Jenis</label>
            <select name="jenis" class="form-select" required>
                <option value="">Pilih Jenis</option>
                @foreach($jenisProduk as $jenis)
                    <option value="{{ $jenis }}" {{ old('jenis', $produk->jenis) == $jenis ? 'selected' : '' }}>
                        {{ $jenis }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="stok">Stok</label>
            <input type="number" class="form-control" name="stok" value="{{ old('stok', $produk->stok) }}" required>
        </div>

        <div class="mb-3">
            <label for="harga_beli">Harga Beli</label>
            <input type="number" class="form-control" name="harga_beli" value="{{ old('harga_beli', $produk->harga_beli) }}" required>
        </div>

        <div class="mb-3">
            <label for="harga_jual">Harga Jual</label>
            <input type="number" class="form-control" name="harga_jual" value="{{ old('harga_jual', $produk->harga_jual) }}" required>
        </div>

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="{{ route('produk.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
