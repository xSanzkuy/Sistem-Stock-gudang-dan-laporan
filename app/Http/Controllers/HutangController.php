<?php

namespace App\Http\Controllers;

use App\Models\Hutang;
use Illuminate\Http\Request;

class HutangController extends Controller
{
    public function index()
{
    $hutang = Hutang::paginate(10); // Menampilkan 10 data per halaman
    return view('hutang.index', compact('hutang'));
}

    public function bayarHutang(Request $request, $id)
    {
        $hutang = Hutang::findOrFail($id);
        $hutang->update([
            'status' => 'Lunas',
        ]);

        return redirect()->route('hutang.index')->with('success', 'Hutang berhasil dilunasi.');
    }

    public function create()
    {
        return view('hutang.create');
    }

    public function store(Request $request)
    {
        Hutang::create($request->all());
        return redirect()->route('hutang.index')->with('success', 'Hutang berhasil ditambahkan!');
    }

    public function edit(Hutang $hutang)
    {
        return view('hutang.edit', compact('hutang'));
    }

    public function update(Request $request, Hutang $hutang)
    {
        $hutang->update($request->all());
        return redirect()->route('hutang.index')->with('success', 'Hutang berhasil diperbarui!');
    }

    public function destroy(Hutang $hutang)
    {
        $hutang->delete();
        return redirect()->route('hutang.index')->with('success', 'Hutang berhasil dihapus!');
    }

    
}
