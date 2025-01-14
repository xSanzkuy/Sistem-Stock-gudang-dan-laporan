<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use Illuminate\Http\Request;
use App\Models\PiutangHistory;


class PiutangController extends Controller
{
    public function index()
    {
        $piutang = Piutang::paginate(10); // Pagination with 10 items per page
        return view('piutang.index', compact('piutang'));
    }

    public function create()
    {
        return view('piutang.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nama_pelanggan' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'no_faktur' => 'required|string|max:255',
        'jumlah' => 'required|numeric|min:0',
        'pembayaran' => 'nullable|numeric|min:0',
    ]);

    // Hitung kekurangan
    $kekurangan = $request->jumlah - ($request->pembayaran ?? 0);

    // Tentukan status otomatis berdasarkan kekurangan
    $status = $kekurangan > 0 ? 'Belum Lunas' : 'Lunas';

    // Simpan data piutang
    Piutang::create([
        'nama_pelanggan' => $request->nama_pelanggan,
        'tanggal' => $request->tanggal,
        'no_faktur' => $request->no_faktur,
        'jumlah' => $request->jumlah,
        'pembayaran' => $request->pembayaran ?? 0,
        'kekurangan' => $kekurangan,
        'status' => $status, // Jika kekurangan > 0 maka status 'Belum Lunas', jika tidak maka 'Lunas'
    ]);
   
    // Redirect ke halaman sebelumnya
    return redirect()->route('piutang.index', ['page' => request('page')])
                     ->with('success', 'Data piutang berhasil ditambahkan.');
}



    

public function edit($id)
{
    $piutang = Piutang::findOrFail($id);
    $currentPage = request('page');

    return view('piutang.edit', compact('piutang', 'currentPage'));
}


    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'no_faktur' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'pembayaran' => 'nullable|numeric|min:0',
            'status' => 'required|in:Lunas,Belum Lunas',
        ]);
    
        $piutang = Piutang::findOrFail($id);
    
        // Menyimpan riwayat perubahan
        PiutangHistory::create([
            'piutang_id' => $piutang->id,
            'jumlah' => $piutang->jumlah,
            'pembayaran' => $piutang->pembayaran,
            'kekurangan' => $piutang->kekurangan,
            'status' => $piutang->status,
        ]);
    
        // Hitung kekurangan
        $kekurangan = $request->jumlah - ($request->pembayaran ?? 0);
    
        // Update data piutang
        $piutang->update([
            'nama_pelanggan' => $request->nama_pelanggan,
            'tanggal' => $request->tanggal,
            'no_faktur' => $request->no_faktur,
            'jumlah' => $request->jumlah,
            'pembayaran' => $request->pembayaran ?? 0,
            'kekurangan' => $kekurangan, // Perbarui kekurangan
            'status' => $kekurangan > 0 ? 'Belum Lunas' : 'Lunas', // Tentukan status berdasarkan kekurangan
        ]);
      // Redirect ke halaman sebelumnya
      return redirect()->route('piutang.index', ['page' => request('page')])
      ->with('success', 'Data piutang berhasil diperbarui.');
}
    
    

    public function destroy($id)
    {
        $piutang = Piutang::findOrFail($id);
        $piutang->delete();

        return redirect()->route('piutang.index', ['page' => request('page')])
        ->with('success', 'Data piutang berhasil dihapus.');
    }

    public function show($id)
{
    $piutang = Piutang::with('piutangHistories')->findOrFail($id);

    // Tangkap halaman sebelumnya dari query string
    $currentPage = request('page');

    return view('piutang.show', compact('piutang', 'currentPage'));
}
}
