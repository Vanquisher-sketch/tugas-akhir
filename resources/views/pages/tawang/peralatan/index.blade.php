@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data Peralatan & Mesin (KIB B) - {{ ucfirst($lokasi) }}</h6>
        
        <div class="d-flex">
            {{-- REVISI: Search dengan Autocomplete & History --}}
            <form action="{{ route('lokasi.peralatan.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" 
                           placeholder="Cari nama/merk/nopol..." 
                           name="search" id="autoSearch" list="searchList"
                           value="{{ request('search') }}" autocomplete="off">
                    <datalist id="searchList"></datalist>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>

            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.peralatan.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle fa-sm text-white-50"></i> Tambah Data
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="{{ route('lokasi.peralatan.print', ['lokasi' => $lokasi]) }}" target="_blank">
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
            <table class="table table-bordered table-hover" width="100%" cellspacing="0" style="font-size: 0.7rem; color: #000;">
                <thead class="thead-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle">No</th>
                        <th rowspan="2" class="align-middle">Kode Barang</th>
                        <th rowspan="2" class="align-middle">Nama Barang</th>
                        <th rowspan="2" class="align-middle">NIBR</th>
                        <th rowspan="2" class="align-middle">Merk/Tipe</th>
                        <th colspan="3" class="align-middle">Identitas Kendaraan</th>
                        <th rowspan="2" class="align-middle">Lokasi</th>
                        <th rowspan="2" class="align-middle">Nilai (Rp)</th>
                        <th rowspan="2" class="align-middle">Tgl Perolehan</th>
                        <th rowspan="2" class="align-middle">Aksi</th>
                    </tr>
                    <tr>
                        <th>Polisi</th>
                        <th>Rangka</th>
                        <th>BPKB</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataPeralatan as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + $dataPeralatan->firstItem() - 1 }}</td>
                        <td class="font-weight-bold text-primary">{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->nibr }}</td>
                        <td>{{ $item->merk_tipe }}</td>
                        <td class="text-center"><span class="badge badge-dark">{{ $item->nomor_polisi ?? '-' }}</span></td>
                        <td><small>{{ $item->nomor_rangka ?? '-' }}</small></td>
                        <td><small>{{ $item->nomor_bpkb ?? '-' }}</small></td>
                        <td>{{ $item->Lok }}</td>
                        <td class="text-right font-weight-bold">{{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">
                            {{-- REVISI: Menggunakan kode_barang --}}
                            <a href="{{ route('lokasi.peralatan.edit', ['lokasi' => $lokasi, 'peralatan' => $item->kode_barang]) }}" class="btn btn-sm btn-warning mb-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('lokasi.peralatan.destroy', ['lokasi' => $lokasi, 'peralatan' => $item->kode_barang]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus aset ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-1"><i class="fas fa-trash"></i></button>
                            </form> 
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-5 text-gray-500">Data peralatan tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $dataPeralatan->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</div>

{{-- SCRIPT AUTOCOMPLETE & HISTORY --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('autoSearch');
        const list = document.getElementById('searchList');

        // Riwayat
        input.addEventListener('focus', function() {
            let history = JSON.parse(localStorage.getItem('search_history_peralatan')) || [];
            list.innerHTML = '';
            history.forEach(item => {
                let option = document.createElement('option');
                option.value = item;
                option.label = '(Riwayat)';
                list.appendChild(option);
            });
        });

        // Simpan Riwayat
        input.closest('form').addEventListener('submit', function() {
            let query = input.value.trim();
            if (query.length > 0) {
                let history = JSON.parse(localStorage.getItem('search_history_peralatan')) || [];
                if (!history.includes(query)) {
                    history.unshift(query);
                    localStorage.setItem('search_history_peralatan', JSON.stringify(history.slice(0, 5)));
                }
            }
        });

        // Saran Database
        input.addEventListener('input', function() {
            const query = this.value;
            if (query.length < 2) return; 
            const url = `/{{ $lokasi }}/peralatan/autocomplete?term=${query}`;
            fetch(url).then(res => res.json()).then(data => {
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