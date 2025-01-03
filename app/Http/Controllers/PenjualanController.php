<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\ItemPenjualan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index(Request $request)
{
    $query = Penjualan::with('details.produk');

    if ($request->has('search') && $request->search) {
        $query->where('no_faktur', 'like', '%' . $request->search . '%')
              ->orWhere('penerima', 'like', '%' . $request->search . '%');
    }

    $penjualan = $query->paginate(10);

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
        $validated = $request->validate([
            'no_faktur' => 'required|unique:penjualan,no_faktur',
            'tanggal' => 'required|date',
            'penerima' => 'required|string',
            'alamat' => 'nullable|string',
            'metode_pembayaran' => 'required|in:kredit,tunai',
            'details' => 'required|array|min:1',
            'details.*.produk_id' => 'required|exists:produk,id',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.harga' => 'required|numeric|min:0',
            'details.*.diskon' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $subtotal = 0;
            $jumlahBarang = 0;

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

            foreach ($validated['details'] as $detail) {
                $jumlah = $detail['qty'] * $detail['harga'] * (1 - ($detail['diskon'] ?? 0) / 100);
                $subtotal += $jumlah;
                $jumlahBarang += $detail['qty'];

                $produk = Produk::findOrFail($detail['produk_id']);
                if ($produk->stok < $detail['qty']) {
                    return redirect()->back()->with('error', "Stok produk {$produk->nama_barang} tidak mencukupi.");
                }
                $produk->stok -= $detail['qty'];
                $produk->save();

                $penjualan->details()->create([
                    'produk_id' => $detail['produk_id'],
                    'qty' => $detail['qty'],
                    'harga' => $detail['harga'],
                    'diskon' => $detail['diskon'] ?? 0,
                    'jumlah' => $jumlah,
                ]);
            }

            $ppn = $subtotal * 0.1;
            $total_harga = $subtotal + $ppn;

            $penjualan->update([
                'subtotal' => $subtotal,
                'ppn' => $ppn,
                'total_harga' => $total_harga,
                'jumlah_barang' => $jumlahBarang,
            ]);

            \App\Models\Piutang::create([
                'nama_pelanggan' => $validated['penerima'],
                'tanggal' => $validated['tanggal'],
                'no_faktur' => $validated['no_faktur'],
                'jumlah' => $total_harga,
                'pembayaran' => $validated['metode_pembayaran'] === 'tunai' ? $total_harga : 0,
                'status' => $validated['metode_pembayaran'] === 'tunai' ? 'Lunas' : 'Belum Lunas',
            ]);
            session()->flash('success', 'Penjualan berhasil dibuat.');
            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil disimpan.');
        } catch (\Exception $e) {
            \Log::error('Error saat menyimpan penjualan', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan penjualan.');
        }
    }

    public function edit(Penjualan $penjualan)
    {
        try {
            $produk = Produk::all();
            return view('penjualan.edit', compact('penjualan', 'produk'));
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

            return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diperbarui.');
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
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus penjualan.');
        }
    }

    public function show(Penjualan $penjualan)
    {
        try {
            $penjualan->load('details.produk');
            return view('penjualan.show', compact('penjualan'));
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

}
