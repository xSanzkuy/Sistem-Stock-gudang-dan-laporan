<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
       public function index(Request $request)
    {
        // Ambil semua jenis produk untuk dropdown filter
        $jenisProduk = Produk::select('jenis')->distinct()->pluck('jenis');
    
        // Query produk dengan filter dan search
        $produk = Produk::query();
    
        // Filter berdasarkan jenis produk
        if ($request->filled('jenis')) {
            $produk->where('jenis', $request->jenis);
        }
    
        // Search berdasarkan nama barang atau kode produk
        if ($request->filled('search')) {
            $produk->where(function ($query) use ($request) {
                $query->where('nama_barang', 'like', '%' . $request->search . '%')
                      ->orWhere('kode', 'like', '%' . $request->search . '%');
            });
        }
    
        // Paginate hasil query
        $produk = $produk->paginate(10); // Pagination dengan 10 item per halaman
    
        return view('produk.index', compact('produk', 'jenisProduk'));
    }   

    public function create()
    {
        return view('produk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ]);
    
        // Format angka untuk menghapus koma dan desimal
        $data = $request->all();
        $data['harga_beli'] = intval($data['harga_beli']);
        $data['harga_jual'] = intval($data['harga_jual']);
    
        // Simpan produk
        Produk::create($data);
    
        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan!');
    }
    

    public function edit(Produk $produk)
{
    // Ambil semua jenis produk untuk dropdown
    $jenisProduk = Produk::select('jenis')->distinct()->pluck('jenis');

    return view('produk.edit', compact('produk', 'jenisProduk'));
}


public function update(Request $request, Produk $produk)
{
    $request->validate([
        'kode' => 'required|string|max:255',
        'nama_barang' => 'required|string|max:255',
        'jenis' => 'required|string|max:255',
        'stok' => 'required|integer|min:0',
        'harga_beli' => 'required|numeric|min:0',
        'harga_jual' => 'required|numeric|min:0',
    ]);

    // Format angka untuk menghapus koma dan desimal
    $data = $request->all();
    $data['harga_beli'] = intval($data['harga_beli']);
    $data['harga_jual'] = intval($data['harga_jual']);

    // Update produk
    $produk->update($data);

    return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui!');
}


    public function destroy(Produk $produk)
    {
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function show($id)
{
    $produk = Produk::with(['pembelian.details.pembelian'])->findOrFail($id);

    return view('produk.show', compact('produk'));
}

}
