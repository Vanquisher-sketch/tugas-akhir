@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data Inventaris Tanah (KIB A) - {{ ucfirst($lokasi) }}</h6>
        
        <div class="d-flex">
            {{-- REVISI: Input Search dengan Autocomplete --}}
            <form action="{{ route('lokasi.tanah.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" 
                           placeholder="Cari nama/kode/alamat..." 
                           name="search" id="autoSearch" list="searchList"
                           value="{{ request('search') }}" autocomplete="off">
                    <datalist id="searchList"></datalist>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>

            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.tanah.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle fa-sm text-white-50"></i> Tambah Data
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="{{ route('lokasi.tanah.print', ['lokasi' => $lokasi]) }}" target="_blank">
                        <i class="fas fa-print fa-fw mr-2 text-gray-400"></i>Cetak Data
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0" style="font-size: 0.75rem; color: #000;">
                <thead class="thead-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle">No.</th>
                        <th rowspan="2" class="align-middle">Kode Barang</th>
                        <th rowspan="2" class="align-middle">Nama Barang</th>
                        <th rowspan="2" class="align-middle">Register</th>
                        <th rowspan="2" class="align-middle">Luas (M2)</th>
                        <th rowspan="2" class="align-middle">Alamat/Lokasi</th>
                        <th colspan="4" class="align-middle">Bukti Kepemilikan</th>
                        <th rowspan="2" class="align-middle">Nilai Perolehan (Rp)</th>
                        <th rowspan="2" class="align-middle">Tgl Perolehan</th>
                        <th rowspan="2" class="align-middle">Status</th>
                        <th rowspan="2" class="align-middle" style="min-width: 100px;">Aksi</th>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>Pemilik</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataTanah as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration + $dataTanah->firstItem() - 1 }}</td>
                            <td class="font-weight-bold text-primary">{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->nomor_register ?? '-' }}</td>
                            <td class="text-center">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td>{{ $item->Lok }}</td>
                            <td>{{ $item->bukti_nama ?? '-' }}</td>
                            <td>{{ $item->bukti_nomor ?? '-' }}</td>
                            <td class="text-center">{{ $item->bukti_tanggal ? \Carbon\Carbon::parse($item->bukti_tanggal)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->nama_kepemilikan_dokumen ?? '-' }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $item->status_penggunaan }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('lokasi.tanah.edit', ['lokasi' => $lokasi, 'tanah' => $item->kode_barang]) }}" class="btn btn-sm btn-warning mb-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('lokasi.tanah.destroy', ['lokasi' => $lokasi, 'tanah' => $item->kode_barang]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data tanah ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mb-1"><i class="fas fa-trash"></i></button>
                                </form> 
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center py-4 text-gray-500">
                                <i class="fas fa-search fa-2x mb-3"></i><br>
                                Data tanah tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $dataTanah->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</div>

{{-- SCRIPT AUTOCOMPLETE UNIVERSAL --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('autoSearch');
        const list = document.getElementById('searchList');

        // --- 1. TAMPILKAN RIWAYAT SAAT KOLOM DIKLIK ---
        input.addEventListener('focus', function() {
            let history = JSON.parse(localStorage.getItem('search_history_tanah')) || [];
            list.innerHTML = '';
            history.forEach(item => {
                let option = document.createElement('option');
                option.value = item;
                option.label = '(Riwayat)'; // Penanda bahwa ini data lama
                list.appendChild(option);
            });
        });

        // --- 2. SIMPAN KE RIWAYAT SAAT FORM DI-SUBMIT ---
        input.closest('form').addEventListener('submit', function() {
            let query = input.value.trim();
            if (query.length > 0) {
                let history = JSON.parse(localStorage.getItem('search_history_tanah')) || [];
                // Jangan simpan kalau sudah ada (biar gak duplikat)
                if (!history.includes(query)) {
                    history.unshift(query); // Tambah ke urutan paling atas
                    history = history.slice(0, 5); // Simpan 5 riwayat terakhir saja
                    localStorage.setItem('search_history_tanah', JSON.stringify(history));
                }
            }
        });

        // --- 3. AUTOCOMPLETE (SARAN DARI DATABASE) ---
        input.addEventListener('input', function() {
            const query = this.value;
            if (query.length < 2) return; 

            const segment = window.location.pathname.split('/')[2];
            const url = `/{{ $lokasi }}/${segment}/autocomplete?term=${query}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Jangan hapus semua (biar riwayat tetap ada di bawahnya)
                    data.forEach(item => {
                        let option = document.createElement('option');
                        option.value = item.label;
                        list.appendChild(option);
                    });
                });
        });
    });
</script>
@endsection