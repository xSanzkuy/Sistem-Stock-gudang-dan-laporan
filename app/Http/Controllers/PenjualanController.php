<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\ItemPenjualan;
use Illuminate\Http\Request;
use App\Models\LaporanKeuntungan;
use Illuminate\Support\Facades\DB; // Tambahkan ini
use App\Models\LaporanKeuntunganDetail;

class PenjualanController extends Controller
{
    public function index(Request $request)  
{  
    $query = Penjualan::with('details.produk');  

    // Filter pencarian berdasarkan No Faktur atau Penerima  
    if ($request->has('search') && $request->search) {  
        $query->where(function($q) use ($request) {  
            $q->where('no_faktur', 'like', '%' . $request->search . '%')  
              ->orWhere('penerima', 'like', '%' . $request->search . '%');  
        });  
    }  

    // Filter berdasarkan waktu  
    if ($request->has('filter')) {  
        $filter = $request->filter;  

        if ($filter == 'daily') {  
            $query->whereDate('tanggal', now());  
        } elseif ($filter == 'monthly') {  
            $query->whereMonth('tanggal', now()->month)  
                  ->whereYear('tanggal', now()->year);  
        } elseif ($filter == 'yearly') {  
            $query->whereYear('tanggal', now()->year);  
        }  
    }  

    // Pagination 10 transaksi per halaman  
    $penjualan = $query->paginate(10);  

    // Menghitung nama produk untuk setiap penjualan  
    $penjualan->each(function ($item) {  
        $produkList = [];  
        foreach ($item->details as $detail) {  
            $produkList[] = $detail->produk->nama_barang . ' (' . $detail->qty . ')';  
        }  
        $item->produk_nama = implode(', ', $produkList);  
    });  

    return view('penjualan.index', compact('penjualan'));  
}
    

    public function create()
    {
        try {
            $produk = Produk::all();
            return view('penjualan.create', compact('produk'));
        } catch (\Exception $e) {
            \Log::error('Error saat membuka halaman tambah penjualan', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuka halaman tambah penjualan.');
        }
    }

    public function store(Request $request)
    {
        // Validasi data input dengan pesan error custom
        $messages = [
            'no_faktur.required' => 'Nomor faktur harus diisi.',
            'no_faktur.unique' => 'Nomor faktur sudah terdaftar.',
            'tanggal.required' => 'Tanggal harus diisi.',
            'penerima.required' => 'Nama penerima harus diisi.',
            'details.*.kode.required' => 'Kode barang harus diisi.',
            'details.*.kode.exists' => 'Kode barang tidak ditemukan.',
            'details.*.qty.required' => 'Kuantitas barang harus diisi.',
            'details.*.qty.min' => 'Kuantitas barang minimal adalah 1.',
            'details.*.harga.required' => 'Harga barang harus diisi.',
            'details.*.harga.min' => 'Harga barang minimal adalah 0.',
        ];
    
        $validated = $request->validate([
            'no_faktur' => 'required|string|max:255|unique:penjualan,no_faktur',
            'tanggal' => 'required|date',
            'penerima' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'metode_pembayaran' => 'required|in:kredit,tunai',
            'ppn' => 'nullable|numeric|min:0', 
            'details' => 'required|array',
            'details.*.kode' => 'required|string|exists:produk,kode',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.harga' => 'required|numeric|min:0',
            'details.*.diskon' => 'nullable|numeric|min:0|max:100',
            'pembayaran' => 'nullable|numeric|min:0',
        ], $messages);
    
        try {
            $subtotal = 0;
            $jumlahBarang = 0;
            $totalKeuntungan = 0;
    
            // Buat penjualan baru
            $penjualan = Penjualan::create([
                'no_faktur' => $validated['no_faktur'],
                'tanggal' => $validated['tanggal'],
                'penerima' => $validated['penerima'],
                'alamat' => $validated['alamat'],
                'subtotal' => 0,
                'ppn' => 0,
                'total_harga' => 0,
                'jumlah_barang' => 0,
            ]);
    
            $details = []; // Menyimpan detail penjualan
    
            // Proses setiap detail penjualan
            foreach ($validated['details'] as $detail) {
                $produk = Produk::where('kode', $detail['kode'])->firstOrFail();
    
                // Validasi stok produk
                if ($produk->stok < $detail['qty']) {
                    return redirect()->back()
                        ->withErrors(["Stok produk {$produk->nama_barang} tidak mencukupi. Stok tersedia: {$produk->stok}"])
                        ->withInput();
                }
    
                // Hitung subtotal, modal, dan keuntungan
                $hargaPokok = $produk->harga_beli;
                $jumlah = $detail['qty'] * $detail['harga'] * (1 - ($detail['diskon'] ?? 0) / 100);
                $keuntungan = ($detail['harga'] - $hargaPokok) * $detail['qty'];
    
                $subtotal += $jumlah;
                $jumlahBarang += $detail['qty'];
                $totalKeuntungan += $keuntungan;
    
                // Kurangi stok produk
                $produk->stok -= $detail['qty'];
                $produk->save();
    
                // Tambahkan ke detail array
                $details[] = [
                    'produk_id' => $produk->id,
                    'qty' => $detail['qty'],
                    'harga' => $detail['harga'],
                    'diskon' => $detail['diskon'] ?? 0,
                    'jumlah' => $jumlah,
                ];
            }
    
            // Simpan detail penjualan
            $penjualan->details()->createMany($details);
    
            // Hitung PPN dan total harga
            $ppnValue = $validated['ppn'] / 100; // Konversi ke desimal
            $ppn = $subtotal * $ppnValue;
            $totalHarga = $subtotal + $ppn;

    
            // Update subtotal, PPN, dan total harga di penjualan
            $penjualan->update([
                'subtotal' => $subtotal,
                'ppn' => $ppn,
                'total_harga' => $totalHarga,
                'jumlah_barang' => $jumlahBarang,
            ]);
    
            // Simpan laporan keuntungan
            $laporan = LaporanKeuntungan::create([
                'tanggal' => $penjualan->tanggal,
                'total_transaksi' => 1,
                'total_modal' => $subtotal - $totalKeuntungan,
                'total_penjualan' => $subtotal,
                'total_keuntungan' => $totalKeuntungan,
            ]);
    
            // Simpan detail laporan keuntungan
            foreach ($details as $detail) {
                $produk = Produk::findOrFail($detail['produk_id']);
    
                LaporanKeuntunganDetail::create([
                    'laporan_keuntungan_id' => $laporan->id,
                    'produk_id' => $produk->id,
                    'nama_produk' => $produk->nama_barang,
                    'qty' => $detail['qty'],
                    'harga_beli' => $produk->harga_beli,
                    'harga_jual' => $detail['harga'],
                    'keuntungan' => ($detail['harga'] - $produk->harga_beli) * $detail['qty'],
                ]);
            }
    
            // Proses pembayaran
            $pembayaran = $validated['pembayaran'] ?? 0;
            $kekurangan = $totalHarga - $pembayaran;
    
            // Tambahkan data piutang
            \App\Models\Piutang::create([
                'nama_pelanggan' => $validated['penerima'],
                'tanggal' => $validated['tanggal'],
                'no_faktur' => $validated['no_faktur'],
                'jumlah' => $totalHarga,
                'pembayaran' => $pembayaran,
                'kekurangan' => $kekurangan,
                'status' => $pembayaran >= $totalHarga ? 'Lunas' : 'Belum Lunas',
            ]);
    
            // Hitung halaman tempat data baru berada
            $totalRecords = Penjualan::where('id', '<=', $penjualan->id)->count();
            $perPage = 10;
            $currentPage = ceil($totalRecords / $perPage);

     // Commit transaction jika semua proses berhasil
     DB::commit();

            // Redirect ke halaman dengan data baru
            return redirect()->route('penjualan.index', ['page' => $currentPage])
                ->with('success', 'Penjualan berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan pada database jika terjadi kesalahan
            \Log::error('Error saat menyimpan penjualan', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan penjualan.')->withInput();
        }
    }
    




public function edit(Penjualan $penjualan, Request $request)
{
    try {
        $produk = Produk::all();
        $currentPage = $request->input('page');
        return view('penjualan.edit', compact('penjualan', 'produk', 'currentPage'));
    } catch (\Exception $e) {
        \Log::error('Error saat membuka halaman edit penjualan', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Terjadi kesalahan saat membuka halaman edit penjualan.');
    }
}


    public function update(Request $request, Penjualan $penjualan)
    {
        $validated = $request->validate([
            'no_faktur' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'penerima' => 'required|string|max:255',
            'metode_pembayaran' => 'required|in:kredit,tunai',
            'details' => 'required|array',
            'details.*.produk_id' => 'required|exists:produk,id',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.harga' => 'required|numeric|min:0',
            'details.*.diskon' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $penjualan->details()->delete();

            $subtotal = 0;
            foreach ($validated['details'] as $detail) {
                $jumlah = $detail['qty'] * $detail['harga'] * (1 - ($detail['diskon'] ?? 0) / 100);
                $subtotal += $jumlah;

                $penjualan->details()->create([
                    'produk_id' => $detail['produk_id'],
                    'qty' => $detail['qty'],
                    'harga' => $detail['harga'],
                    'diskon' => $detail['diskon'] ?? 0,
                    'jumlah' => $jumlah,
                ]);

                $produk = Produk::findOrFail($detail['produk_id']);
                $produk->stok -= $detail['qty'];
                $produk->save();
            }

            $ppn = $subtotal * 0.1;
            $total_harga = $subtotal + $ppn;

            $penjualan->update([
                'subtotal' => $subtotal,
                'ppn' => $ppn,
                'total_harga' => $total_harga,
            ]);

            $piutang = \App\Models\Piutang::where('no_faktur', $penjualan->no_faktur)->first();
            if ($piutang) {
                $piutang->update([
                    'jumlah' => $total_harga,
                    'pembayaran' => $validated['metode_pembayaran'] === 'tunai' ? $total_harga : 0,
                    'status' => $validated['metode_pembayaran'] === 'tunai' ? 'Lunas' : 'Belum Lunas',
                ]);
            }

            // Ambil halaman saat ini dari query string
        $currentPage = $request->input('page', 1);

        // Redirect ke halaman yang sama
        return redirect()->route('penjualan.index', ['page' => $currentPage])
                         ->with('success', 'Penjualan berhasil diperbarui!');
    } catch (\Exception $e) {
        \Log::error('Error saat memperbarui penjualan', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui penjualan.');
    }
}

    public function destroy(Penjualan $penjualan)
    {
        try {
            $penjualan->details()->delete();
            $penjualan->delete();
            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error saat menghapus penjualan', ['error' => $e->getMessage()]);
            return redirect()->route('penjualan.index', ['page' => $request->input('page')])
            ->with('success', 'Penjualan berhasil dihapus!');
        }
    }

    public function show(Penjualan $penjualan, Request $request)
{
    try {
        $penjualan->load('details.produk');
        $currentPage = $request->input('page');
        return view('penjualan.show', compact('penjualan', 'currentPage'));
    } catch (\Exception $e) {
        \Log::error('Error saat melihat detail penjualan', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Terjadi kesalahan saat melihat detail penjualan.');
    }
}


    public function print($id)
{
    $penjualan = Penjualan::with('details.produk')->findOrFail($id);

    return view('penjualan.print', compact('penjualan'));
}

// Tambahkan di PenjualanController
public function getByKode(Request $request)
{
    $kode = $request->input('kode');

    $produk = Produk::where('kode', $kode)->first();

    if ($produk) {
        return response()->json([
            'success' => true,
            'data' => [
                'nama_barang' => $produk->nama_barang,
                'harga_jual' => $produk->harga_jual,
            ]
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Produk tidak ditemukan'
    ]);
}

}
