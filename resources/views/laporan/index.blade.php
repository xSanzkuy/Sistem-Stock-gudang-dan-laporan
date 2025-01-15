@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Laporan Keuntungan ({{ ucfirst($periode) }})</h1>

    <!-- Form Filter -->
    <form method="GET" action="{{ route('laporan.index') }}" class="mb-4" id="filter-form">
    <input type="hidden" name="page" value="{{ request('page', 1) }}">
    <div class="form-group">
        <label for="periode">Periode</label>
        <select name="periode" id="periode" class="form-control" onchange="this.form.submit()">
            <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
            <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
            <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
        </select>
    </div>

    <div class="form-group mt-3">
        @if ($periode === 'harian')
            <label for="tanggal">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}" class="form-control" onchange="this.form.submit()">
        @elseif ($periode === 'bulanan')
            <label for="bulan">Bulan</label>
            <input type="month" name="bulan" id="bulan" value="{{ $bulan }}" class="form-control" onchange="this.form.submit()">
        @elseif ($periode === 'tahunan')
            <label for="tahun">Tahun</label>
            <input type="number" name="tahun" id="tahun" min="2000" max="{{ now()->year }}" value="{{ $tahun }}" class="form-control" onchange="this.form.submit()">
        @endif
    </div>
</form>



<!-- Rekap Keuntungan -->
<div class="alert alert-primary mt-4">
    <h5>Rekap Keuntungan</h5>
    <ul class="list-unstyled">
        <li>Total Modal: <strong>Rp {{ number_format($rekap['total_modal'], 0, ',', '.') }}</strong></li>
        <li>Total Penjualan: <strong>Rp {{ number_format($rekap['total_penjualan'], 0, ',', '.') }}</strong></li>
        <li>Total Keuntungan: <strong>Rp {{ number_format($rekap['total_keuntungan'], 0, ',', '.') }}</strong></li>
    </ul>
</div>


    <!-- Tabel Laporan Keuntungan -->
    <div id="laporan-tabel">
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Total Transaksi</th>
                    <th>Total Modal</th>
                    <th>Total Penjualan</th>
                    <th>Total Keuntungan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($laporan as $laporanItem)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($laporanItem->tanggal)->format('d M Y') }}</td>
                        <td>{{ number_format($laporanItem->total_transaksi, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($laporanItem->total_modal, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($laporanItem->total_penjualan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($laporanItem->total_keuntungan, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('laporan.detail', $laporanItem->id) }}" class="btn btn-info">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Navigasi Paginasi -->
        <div class="mt-4">
    {{ $laporan->links('pagination::bootstrap-5') }}
</div>

    </div>

    <!-- Tombol Ekspor (PDF) -->
    <div class="mt-4">
        <a href="{{ route('laporan.export_pdf', ['periode' => $periode, 'tanggal' => $tanggal]) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Ekspor Laporan (PDF)
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filter-form');
    const rekapKeuntungan = document.getElementById('rekap-keuntungan');
    const laporanTabel = document.getElementById('laporan-tabel');

    form.addEventListener('change', function () {
        const formData = new FormData(form);
        const queryParams = new URLSearchParams(formData).toString();

        fetch(`{{ route('laporan.filter') }}?${queryParams}`)
            .then(response => response.json())
            .then(data => {
                // Update Rekap Keuntungan
                rekapKeuntungan.innerHTML = `
                    <h5>Rekap Keuntungan</h5>
                    <ul class="list-unstyled">
                        <li>Total Modal: <strong>Rp ${data.rekap.total_modal.toLocaleString()}</strong></li>
                        <li>Total Penjualan: <strong>Rp ${data.rekap.total_penjualan.toLocaleString()}</strong></li>
                        <li>Total Keuntungan: <strong>Rp ${data.rekap.total_keuntungan.toLocaleString()}</strong></li>
                    </ul>
                `;

                // Update Tabel dan Paginasi
                laporanTabel.innerHTML = data.html;
            })
            .catch(error => console.error('Error:', error));
    });
});
</script>
@endsection
