<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use Illuminate\Http\Request;
use App\Models\PiutangHistory;


class PiutangController extends Controller
{
    public function index(Request $request)  
{  
    $query = Piutang::query();  

    // Filter pencarian berdasarkan Nama Pelanggan atau No Faktur  
    if ($request->has('search') && $request->search) {  
        $query->where(function($q) use ($request) {  
            $q->where('nama_pelanggan', 'like', '%' . $request->search . '%')  
              ->orWhere('no_faktur', 'like', '%' . $request->search . '%');  
        });  
    }  

    // Filter berdasarkan status  
    if ($request->has('status') && $request->status) {  
        $query->where('status', $request->status);  
    }  

    // Pagination 10 transaksi per halaman  
    $piutang = $query->paginate(10);  

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
            'jumlah' => 'required|string|min:0',
            'pembayaran' => 'nullable|string|min:0',
            'status' => 'required|in:Lunas,Belum Lunas',
        ]);
        // Hapus format titik sebelum menyimpan ke database
    $jumlah = str_replace('.', '', $request->jumlah);
    $pembayaran = str_replace('.', '', $request->pembayaran);

    
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
      $jumlah = str_replace('.', '', $request->jumlah); // Hapus titik dari jumlah
      $pembayaran = str_replace('.', '', $request->pembayaran); // Hapus titik dari pembayaran
      
      $kekurangan = $jumlah - ($pembayaran ?? 0);
          // Pastikan status tidak berubah menjadi 'Lunas' jika kekurangan masih ada
    $status = $kekurangan <= 0 ? 'Lunas' : 'Belum Lunas';

    // Update data piutang
    $piutang->update([
        'nama_pelanggan' => $request->nama_pelanggan,
        'tanggal' => $request->tanggal,
        'no_faktur' => $request->no_faktur,
        'jumlah' => $jumlah, // Simpan nilai tanpa format
        'pembayaran' => $pembayaran, // Simpan nilai tanpa format
        'kekurangan' => $kekurangan,
        'status' => $kekurangan > 0 ? 'Belum Lunas' : 'Lunas',
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
