<?php

namespace App\Http\Controllers;

use App\Models\LaporanKeuntungan;
use App\Models\LaporanKeuntunganDetail;
use App\Models\Produk;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use App\Exports\LaporanKeuntunganExport;

class LaporanKeuntunganController extends Controller
{
    /**
     * Menyimpan laporan keuntungan per transaksi.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'items' => 'required|array',
                'items.*.produk_id' => 'required|exists:produk,id', // Pastikan produk_id ada di database
                'items.*.qty' => 'required|integer|min:1',
                'items.*.harga_jual' => 'required|numeric|min:0',
            ]);
    
            $totalModal = 0;
            $totalPenjualan = 0;
            $totalKeuntungan = 0;
    
            // Perhitungan total untuk laporan
            foreach ($request->items as $item) {
                $produk = Produk::find($item['produk_id']);
    
                if (!$produk) {
                    \Log::warning("Produk dengan ID {$item['produk_id']} tidak ditemukan.");
                    continue; // Lewati item yang tidak valid
                }
    
                $modal = $produk->harga_beli * $item['qty'];
                $penjualan = $item['harga_jual'] * $item['qty'];
                $keuntungan = $penjualan - $modal;
    
                $totalModal += $modal;
                $totalPenjualan += $penjualan;
                $totalKeuntungan += $keuntungan;
            }
    
            // Buat laporan keuntungan utama
            $laporan = LaporanKeuntungan::create([
                'tanggal' => $request->tanggal,
                'total_transaksi' => count($request->items),
                'total_modal' => $totalModal,
                'total_penjualan' => $totalPenjualan,
                'total_keuntungan' => $totalKeuntungan,
            ]);
    
            if (!$laporan) {
                \Log::error('Gagal membuat entri laporan keuntungan.');
                return response()->json(['message' => 'Gagal membuat laporan keuntungan'], 500);
            }
    
            // Buat detail laporan keuntungan
            foreach ($request->items as $item) {
                $produk = Produk::find($item['produk_id']);
    
                if (!$produk) {
                    \Log::warning("Produk dengan ID {$item['produk_id']} tidak ditemukan.");
                    continue; // Lewati item yang tidak valid
                }
    
                LaporanKeuntunganDetail::create([
                    'laporan_keuntungan_id' => $laporan->id,
                    'produk_id' => $produk->id,
                    'nama_produk' => $produk->nama_barang,
                    'qty' => $item['qty'],
                    'harga_beli' => $produk->harga_beli,
                    'harga_jual' => $item['harga_jual'],
                    'keuntungan' => ($item['harga_jual'] - $produk->harga_beli) * $item['qty'],
                ]);
            }
    
            \Log::info('Laporan dan detail berhasil disimpan.', $laporan->toArray());
            return response()->json(['message' => 'Laporan berhasil disimpan!', 'data' => $laporan], 201);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan laporan.', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Gagal menyimpan laporan', 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Menampilkan laporan keuntungan berdasarkan periode.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
{
    $periode = $request->input('periode', 'bulanan');
    $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
    $bulan = $request->input('bulan', now()->format('Y-m'));
    $tahun = $request->input('tahun', now()->year);

    // Query dasar untuk data laporan
    $query = LaporanKeuntungan::query();

    // Filter berdasarkan periode
    if ($periode === 'harian') {
        $query->whereDate('tanggal', Carbon::parse($tanggal));
    } elseif ($periode === 'bulanan') {
        $query->whereYear('tanggal', Carbon::parse($bulan)->year)
              ->whereMonth('tanggal', Carbon::parse($bulan)->month);
    } elseif ($periode === 'tahunan') {
        $query->whereYear('tanggal', $tahun);
    }

    // Data untuk tabel (dengan pagination)
    $laporan = $query->with('details')->paginate(10)->appends($request->query());

    // Hitung rekap keuntungan (tanpa pagination)
    $rekapQuery = LaporanKeuntungan::query();

    if ($periode === 'harian') {
        $rekapQuery->whereDate('tanggal', Carbon::parse($tanggal));
    } elseif ($periode === 'bulanan') {
        $rekapQuery->whereYear('tanggal', Carbon::parse($bulan)->year)
                   ->whereMonth('tanggal', Carbon::parse($bulan)->month);
    } elseif ($periode === 'tahunan') {
        $rekapQuery->whereYear('tanggal', $tahun);
    }

    $rekap = [
        'total_modal' => $rekapQuery->sum('total_modal'),
        'total_penjualan' => $rekapQuery->sum('total_penjualan'),
        'total_keuntungan' => $rekapQuery->sum('total_keuntungan'),
    ];

    return view('laporan.index', compact('laporan', 'periode', 'tanggal', 'bulan', 'tahun', 'rekap'));
}


    

    /**
     * Mengekspor laporan ke PDF.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportPDF(Request $request)
    {
        $periode = $request->input('periode');
        $tanggal = $request->input('tanggal');

        $laporan = $this->getLaporanByPeriode($periode, $tanggal);

        $pdf = PDF::loadView('laporan.export_pdf', compact('laporan', 'periode', 'tanggal'));
        return $pdf->download('laporan-keuntungan.pdf');
    }

    /**
     * Mendapatkan laporan berdasarkan periode dan tanggal.
     *
     * @param string $periode
     * @param string $tanggal
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getLaporanByPeriode($periode, $tanggal)
    {
        $query = LaporanKeuntungan::query();

        if ($periode === 'harian') {
            $query->whereDate('tanggal', Carbon::parse($tanggal));
        } elseif ($periode === 'bulanan') {
            $query->whereYear('tanggal', Carbon::parse($tanggal)->year)
                  ->whereMonth('tanggal', Carbon::parse($tanggal)->month);
        } elseif ($periode === 'tahunan') {
            $query->whereYear('tanggal', Carbon::parse($tanggal)->year);
        }

        return $query->with('details.produk')->get(); // Pastikan relasi produk dimuat
    }

    /**
     * Menampilkan detail laporan keuntungan.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function detail(Request $request, $id)
{
    try {
        $laporan = LaporanKeuntungan::with('details.produk')->findOrFail($id);

        if ($laporan->details->isEmpty()) {
            \Log::warning("Tidak ada detail untuk laporan ID {$id}");
        }

        // Tangkap semua query parameter
        $queryParams = $request->query();

        return view('laporan.detail', compact('laporan', 'queryParams'));
    } catch (\Exception $e) {
        \Log::error('Error saat membuka detail laporan keuntungan', ['error' => $e->getMessage()]);
        return redirect()->back()->with('error', 'Terjadi kesalahan saat membuka detail laporan.');
    }
}

    
    public function filter(Request $request)
    {
        $periode = $request->input('periode', 'bulanan');
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $tahun = $request->input('tahun', now()->year);
    
        // Query dasar untuk data laporan
        $query = LaporanKeuntungan::query();
    
        if ($periode === 'harian') {
            $query->whereDate('tanggal', Carbon::parse($tanggal));
        } elseif ($periode === 'bulanan') {
            $query->whereYear('tanggal', Carbon::parse($bulan)->year)
                  ->whereMonth('tanggal', Carbon::parse($bulan)->month);
        } elseif ($periode === 'tahunan') {
            $query->whereYear('tanggal', $tahun);
        }
    
        // Data untuk tabel (dengan pagination)
        $laporan = $query->with('details')->paginate(10)->appends($request->query());
    
        // Hitung rekap keuntungan
        $rekap = [
            'total_modal' => $query->sum('total_modal'),
            'total_penjualan' => $query->sum('total_penjualan'),
            'total_keuntungan' => $query->sum('total_keuntungan'),
        ];
    
        return response()->json([
            'rekap' => $rekap,
            'html' => view('laporan.partials.tabel', compact('laporan'))->render(),
        ]);
    }
}    