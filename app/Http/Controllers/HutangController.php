<?php

namespace App\Http\Controllers;

use App\Models\Hutang;
use Illuminate\Http\Request;
use App\Models\HutangHistory;

class HutangController extends Controller
{
    public function index()
    {
        $hutang = Hutang::paginate(10); // Pagination with 10 items per page
        return view('hutang.index', compact('hutang'));
    }

    public function create()
    {
        return view('hutang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'no_faktur' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'pembayaran' => 'nullable|numeric|min:0',
            'jatuh_tempo' => 'required|date',
        ]);

        // Hitung kekurangan
        $jumlah = str_replace('.', '', $request->jumlah);
        $pembayaran = str_replace('.', '', $request->pembayaran ?? 0);
        $kekurangan = $jumlah - $pembayaran;

        // Tentukan status otomatis berdasarkan kekurangan
        $status = $kekurangan > 0 ? 'Belum Lunas' : 'Lunas';

        // Simpan data hutang
        Hutang::create([
            'nama_supplier' => $request->nama_supplier,
            'tanggal' => $request->tanggal,
            'no_faktur' => $request->no_faktur,
            'jumlah' => $jumlah,
            'pembayaran' => $pembayaran,
            'kekurangan' => $kekurangan,
            'jatuh_tempo' => $request->jatuh_tempo,
            'status' => $status,
        ]);

        // Redirect ke halaman sebelumnya
        return redirect()->route('hutang.index', ['page' => request('page')])
            ->with('success', 'Data hutang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $hutang = Hutang::findOrFail($id);
        $currentPage = request('page'); // Tangkap halaman saat ini
        return view('hutang.edit', compact('hutang', 'currentPage'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'nama_supplier' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'no_faktur' => 'required|string|max:255',
        'jumlah' => 'required|string|min:0',
        'pembayaran' => 'nullable|string|min:0',
        'jatuh_tempo' => 'required|date',
        'status' => 'required|in:Lunas,Belum Lunas',
    ]);

    $hutang = Hutang::findOrFail($id);

    // Simpan data sebelum perubahan untuk riwayat
    HutangHistory::create([
        'hutang_id' => $hutang->id,
        'jumlah' => $hutang->jumlah,
        'pembayaran' => $hutang->pembayaran,
        'kekurangan' => $hutang->kekurangan,
        'status' => $hutang->status,
        'created_at' => now(),
    ]);

    // Hapus format titik pada jumlah dan pembayaran sebelum diproses
    $jumlah = str_replace('.', '', $request->jumlah);
    $pembayaran = str_replace('.', '', $request->pembayaran ?? 0);

    // Hitung kekurangan baru
    $kekurangan = $jumlah - $pembayaran;

    // Tentukan status berdasarkan kekurangan
    $status = $kekurangan > 0 ? 'Belum Lunas' : 'Lunas';

    // Update data hutang
    $hutang->update([
        'nama_supplier' => $request->nama_supplier,
        'tanggal' => $request->tanggal,
        'no_faktur' => $request->no_faktur,
        'jumlah' => $jumlah,
        'pembayaran' => $pembayaran,
        'kekurangan' => $kekurangan,
        'jatuh_tempo' => $request->jatuh_tempo,
        'status' => $status,
    ]);

    // Redirect ke halaman sebelumnya
    return redirect()->route('hutang.index', ['page' => request('page')])
        ->with('success', 'Data hutang berhasil diperbarui.');
}



    public function destroy($id)
    {
        $hutang = Hutang::findOrFail($id);
        $hutang->delete();

        return redirect()->route('hutang.index', ['page' => request('page')])
            ->with('success', 'Data hutang berhasil dihapus.');
    }

    public function show($id)
    {
        $hutang = Hutang::with('hutangHistories')->findOrFail($id);

        // Tangkap halaman sebelumnya dari query string
        $currentPage = request('page');

        return view('hutang.show', compact('hutang', 'currentPage'));
    }
}
