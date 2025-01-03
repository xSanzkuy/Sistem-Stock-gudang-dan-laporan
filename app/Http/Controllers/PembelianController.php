<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Produk;
use App\Models\ItemPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::query();
    
        // Filter pencarian berdasarkan No Faktur atau Supplier
        if ($request->has('search') && $request->search) {
            $query->where('no_faktur', 'like', '%' . $request->search . '%')
                  ->orWhere('supplier', 'like', '%' . $request->search . '%');
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
            'supplier' => 'required|string',
            'metode_pembayaran' => 'required|in:kredit,tunai',
            'details' => 'required|array|min:1',
            'details.*.kode' => 'required|string',
            'details.*.jenis' => 'required|string',
            'details.*.nama_barang' => 'required|string',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.harga' => 'required|numeric|min:0',
            'details.*.diskon' => 'nullable|numeric|min:0|max:100',
        ]);
    
        \DB::beginTransaction();
    
        try {
            $subtotal = 0;
    
            foreach ($validated['details'] as $detail) {
                // Cari atau buat produk berdasarkan kode, jenis, dan nama_barang
                $produk = Produk::firstOrCreate(
                    ['kode' => $detail['kode'], 'jenis' => $detail['jenis'], 'nama_barang' => $detail['nama_barang']],
                    [
                        'stok' => 0,
                        'harga_beli' => $detail['harga'],
                        'harga_jual' => $detail['harga'] * 1.2, // markup 20%
                    ]
                );
    
                // Update stok produk
                $produk->stok += $detail['qty'];
                $produk->save();
    
                // Hitung subtotal
                $jumlah = ($detail['qty'] * $detail['harga']) - (($detail['qty'] * $detail['harga']) * ($detail['diskon'] / 100));
                if ($jumlah < 0) {
                    throw new \Exception("Jumlah tidak valid untuk produk: {$detail['nama_barang']}");
                }
    
                $subtotal += $jumlah;
            }
    
            // Simpan data pembelian
            $pembelian = Pembelian::create([
                'no_faktur' => $validated['no_faktur'],
                'tanggal' => $validated['tanggal'],
                'supplier' => $validated['supplier'],
                'subtotal' => $subtotal,
                'ppn' => $subtotal * 0.1, // PPN 10%
                'total_harga' => $subtotal + ($subtotal * 0.1),
            ]);
    
            foreach ($validated['details'] as $detail) {
                $pembelian->details()->create([
                    'produk_id' => $produk->id,
                    'qty' => $detail['qty'],
                    'harga' => $detail['harga'],
                    'diskon' => $detail['diskon'] ?? 0,
                    'jumlah' => ($detail['qty'] * $detail['harga']) - (($detail['qty'] * $detail['harga']) * ($detail['diskon'] / 100)),
                ]);
            }
    
            // Tambahkan data hutang jika pembayaran kredit
            \App\Models\Hutang::create([
                'nama_supplier' => $validated['supplier'],
                'tanggal' => $validated['tanggal'],
                'no_faktur' => $validated['no_faktur'],
                'jumlah' => $subtotal + ($subtotal * 0.1),
                'jatuh_tempo' => $validated['metode_pembayaran'] === 'kredit' ? now()->addDays(30) : now(),
                'status' => $validated['metode_pembayaran'] === 'kredit' ? 'Belum Lunas' : 'Lunas',
            ]);
    
            \DB::commit();
    
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil disimpan.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error saat menyimpan pembelian: ' . $e->getMessage());
    
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

            $pembelian->update([
                'no_faktur' => $validated['no_faktur'],
                'tanggal' => $validated['tanggal'],
                'supplier' => $validated['supplier'],
                'subtotal' => $subtotal,
                'ppn' => $subtotal * 0.1,
                'total_harga' => $subtotal + ($subtotal * 0.1),
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
