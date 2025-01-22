<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Produk;
use App\Models\ItemPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Hutang;


class PembelianController extends Controller
{
    public function index(Request $request)  
    {  
        $query = Pembelian::query();  
    
        // Filter pencarian berdasarkan No Faktur atau Supplier  
        if ($request->has('search') && $request->search) {  
            $query->where(function($q) use ($request) {  
                $q->where('no_faktur', 'like', '%' . $request->search . '%')  
                  ->orWhere('supplier', 'like', '%' . $request->search . '%');  
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
        $pembelian = $query->paginate(10);  
    
        return view('pembelian.index', compact('pembelian'));  
    }
    

    public function create()
    {
        try {
            $produk = Produk::all();
            return view('pembelian.create', compact('produk'));
        } catch (\Exception $e) {
            Log::error('Error fetching product data for create: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memuat data produk.']);
        }
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'no_faktur' => 'required|unique:pembelian,no_faktur',
        'tanggal' => 'required|date',
        'supplier' => 'required|string|max:255',
        'metode_pembayaran' => 'required|in:kredit,tunai',
        'ppn' => 'nullable|numeric|min:0',
        'pembayaran' => 'nullable|numeric|min:0',
        'details' => 'required|array|min:1',
        'details.*.kode' => 'nullable|string|max:255|required_without:details.*.kode_manual',
        'details.*.kode_manual' => 'nullable|string|max:255|required_without:details.*.kode',
        'details.*.jenis' => 'required|string|max:255',
        'details.*.nama_barang' => 'required|string|max:255',
        'details.*.qty' => 'required|integer|min:1',
        'details.*.harga' => 'required|numeric|min:0',
        'details.*.diskon' => 'nullable|numeric|min:0|max:100',
    ]);    

    DB::beginTransaction();

    try {
        $subtotal = 0;
        $pembayaranAwal = $validated['pembayaran'] ?? 0;

        // Simpan data pembelian utama
        $pembelian = Pembelian::create([
            'no_faktur' => $validated['no_faktur'],
            'tanggal' => $validated['tanggal'],
            'supplier' => $validated['supplier'],
            'subtotal' => 0, // Akan diperbarui nanti
            'ppn' => 0,
            'total_harga' => 0,
        ]);

        // Iterasi untuk menyimpan detail pembelian dan memperbarui stok produk
        foreach ($validated['details'] as $detail) {
            $kodeBarang = $detail['kode'] ?? $detail['kode_manual'];
        
            if (!$kodeBarang) {
                throw new \Exception("Kode barang tidak valid untuk salah satu detail pembelian.");
            }
        
            // Cek apakah produk sudah ada berdasarkan kode
            $produk = Produk::where('kode', $kodeBarang)->first();
        
            if ($produk) {
                // Jika produk sudah ada, perbarui stok
                $produk->stok += $detail['qty'];
                $produk->save();
            } else {
                // Jika produk belum ada, buat produk baru
                $produk = Produk::create([
                    'kode' => $kodeBarang,
                    'jenis' => $detail['jenis'],
                    'nama_barang' => $detail['nama_barang'],
                    'stok' => $detail['qty'],
                    'harga_beli' => $detail['harga'],
                    'harga_jual' => $detail['harga'] * 1.2, // markup 20%
                ]);
            }

            // Hitung subtotal untuk detail ini
            $jumlah = ($detail['qty'] * $detail['harga']) - (($detail['qty'] * $detail['harga']) * ($detail['diskon'] / 100));

            // Validasi jumlah tidak boleh negatif
            if ($jumlah < 0) {
                throw new \Exception("Jumlah tidak valid untuk produk: {$detail['nama_barang']}");
            }

            $subtotal += $jumlah;

            // Simpan detail pembelian
            $pembelian->details()->create([
                'produk_id' => $produk->id,
                'qty' => $detail['qty'],
                'harga' => $detail['harga'],
                'diskon' => $detail['diskon'] ?? 0,
                'jumlah' => $jumlah,
            ]);
        }

        // Hitung PPN dan Total Harga
        $ppnValue = $validated['ppn'] / 100; // Konversi ke desimal
        $ppn = $subtotal * $ppnValue;
        $totalHarga = $subtotal + $ppn;

        // Perbarui subtotal, PPN, dan total harga di pembelian
        $pembelian->update([
            'subtotal' => $subtotal,
            'ppn' => $ppn,
            'total_harga' => $totalHarga,
        ]);

        // Proses hutang berdasarkan metode pembayaran
        $statusHutang = $validated['metode_pembayaran'] === 'tunai' ? 'Lunas' : 'Belum Lunas';
        $sisaHutang = $totalHarga - $pembayaranAwal;

        Hutang::create([
            'nama_supplier' => $validated['supplier'],
            'tanggal' => $validated['tanggal'],
            'no_faktur' => $validated['no_faktur'],
            'jumlah' => $totalHarga,
            'pembayaran' => $pembayaranAwal,
            'kekurangan' => $sisaHutang,
            'jatuh_tempo' => $validated['metode_pembayaran'] === 'kredit' ? now()->addDays(30) : now(),
            'status' => $sisaHutang > 0 ? 'Belum Lunas' : 'Lunas',
        ]);

        DB::commit();

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil disimpan.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error saat menyimpan pembelian: ' . $e->getMessage());

        return redirect()->back()->withErrors([
            'error' => 'Terjadi kesalahan saat menyimpan pembelian: ' . $e->getMessage(),
        ]);
    }
}

    

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'no_faktur' => 'required|unique:pembelian,no_faktur,' . $id,
            'tanggal' => 'required|date',
            'supplier' => 'required|string',
            'ppn' => 'required|numeric|min:0', // Validasi untuk PPN
            'details' => 'required|array',
            'details.*.produk_id' => 'required|exists:produk,id',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.harga' => 'required|numeric|min:0',
            'details.*.diskon' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $pembelian = Pembelian::findOrFail($id);

            // Kembalikan stok lama sebelum menghapus
            foreach ($pembelian->details as $detail) {
                $produk = Produk::find($detail->produk_id);
                if ($produk) {
                    $produk->stok -= $detail->qty;
                    $produk->save();
                }
            }

            $pembelian->details()->delete();

            $subtotal = 0;

            foreach ($validated['details'] as $detail) {
                $produk = Produk::findOrFail($detail['produk_id']);

                // Update harga produk
                $produk->harga_beli = $detail['harga'];
                $produk->stok += $detail['qty'];
                $produk->save();

                $jumlah = ($detail['qty'] * $detail['harga']) - (($detail['qty'] * $detail['harga']) * ($detail['diskon'] / 100));
                $subtotal += $jumlah;

                $pembelian->details()->create([
                    'produk_id' => $detail['produk_id'],
                    'qty' => $detail['qty'],
                    'harga' => $detail['harga'],
                    'diskon' => $detail['diskon'] ?? 0,
                    'jumlah' => $jumlah,
                ]);
            }

            // Gunakan PPN dari input manual
            $ppnValue = $validated['ppn'] / 100; // Konversi ke desimal
            $ppn = $subtotal * $ppnValue;
            $totalHarga = $subtotal + $ppn;

            $pembelian->update([
                'no_faktur' => $validated['no_faktur'],
                'tanggal' => $validated['tanggal'],
                'supplier' => $validated['supplier'],
                'subtotal' => $subtotal,
                'ppn' => $ppn, // Simpan nilai PPN
                'total_harga' => $totalHarga,
            ]);

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pembelian: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat mengupdate pembelian.']);
        }
    }

    public function destroy(Pembelian $pembelian)
    {
        DB::beginTransaction();

        try {
            foreach ($pembelian->details as $detail) {
                $produk = Produk::find($detail->produk_id);
                if ($produk) {
                    $produk->stok -= $detail->qty;
                    $produk->save();
                }
            }

            $pembelian->details()->delete();
            $pembelian->delete();

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pembelian: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus pembelian.']);
        }
    }

    public function show($id)
    {
        try {
            $pembelian = Pembelian::with('details.produk')->findOrFail($id);
            return view('pembelian.show', compact('pembelian'));
        } catch (\Exception $e) {
            Log::error('Error fetching pembelian data for show: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memuat data pembelian.']);
        }
    }
}   