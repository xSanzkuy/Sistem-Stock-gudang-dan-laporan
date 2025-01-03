<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use Illuminate\Http\Request;

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
            'status' => 'required|in:Lunas,Belum Lunas',
        ]);

        Piutang::create($request->all());

        return redirect()->route('piutang.index')->with('success', 'Data piutang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $piutang = Piutang::findOrFail($id);
        return view('piutang.edit', compact('piutang'));
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
        $piutang->update($request->all());

        return redirect()->route('piutang.index')->with('success', 'Data piutang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $piutang = Piutang::findOrFail($id);
        $piutang->delete();

        return redirect()->route('piutang.index')->with('success', 'Data piutang berhasil dihapus.');
    }
}
