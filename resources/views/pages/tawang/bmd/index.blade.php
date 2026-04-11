@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Penggunaan BMD & BAST - {{ ucfirst($lokasi) }}</h6>
        
        <div class="d-flex">
            <form action="{{ route('lokasi.bmd.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" 
                           placeholder="Cari pemakai/barang..." 
                           name="search" id="autoSearch" list="searchList"
                           value="{{ request('search') }}" autocomplete="off">
                    <datalist id="searchList"></datalist>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>

            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.bmd.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary"><i class="fas fa-plus fa-sm"></i> Tambah Data</a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="{{ route('lokasi.bmd.print', ['lokasi' => $lokasi]) }}" target="_blank"><i class="fas fa-print fa-fw mr-2"></i>Cetak Laporan</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0" style="font-size: 0.75rem; color: #000;">
                <thead class="thead-light text-center">
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Nama Barang</th>
                        <th rowspan="2">Kode Barang</th>
                        <th rowspan="2">Lokasi Penggunaan</th>
                        <th colspan="2">Data Pemakai</th>
                        <th colspan="2">Dokumen BAST</th>
                        <th rowspan="2">Status Pajak</th>
                        <th rowspan="2">Aksi</th>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <th>Identitas</th>
                        <th>Nomor</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bmds as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="font-weight-bold">{{ $item->peralatan->nama_barang ?? '-' }}</td>
                        <td>{{ $item->peralatan_kode }}</td>
                        <td>{{ $item->alamat_penggunaan }}</td>
                        <td>{{ $item->pemakai_nama }}</td>
                        <td><small>{{ $item->pemakai_identitas }}</small></td>
                        <td>{{ $item->bast_nomor ?? '-' }}</td>
                        <td class="text-center">
                            @if($item->bast_file)
                                <a href="{{ asset('storage/' . $item->bast_file) }}" target="_blank" class="btn btn-sm btn-info btn-circle"><i class="fas fa-file-pdf"></i></a>
                            @else - @endif
                        </td>
                        <td class="text-center">
                            @if($item->tanggal_pajak)
                                <span class="badge badge-success">Terdata</span>
                            @else
                                <span class="badge badge-light text-muted">Bukan Kendaraan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('lokasi.bmd.edit', [$lokasi, $item->id]) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('lokasi.bmd.destroy', [$lokasi, $item->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data pemakaian ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-4">Belum ada data penggunaan aset.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $bmds->links() }}</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('autoSearch');
        const list = document.getElementById('searchList');

        input.addEventListener('focus', function() {
            let history = JSON.parse(localStorage.getItem('search_history_bmd')) || [];
            list.innerHTML = '';
            history.forEach(item => {
                let option = document.createElement('option');
                option.value = item;
                option.label = '(Riwayat)';
                list.appendChild(option);
            });
        });

        input.closest('form').addEventListener('submit', function() {
            let query = input.value.trim();
            if (query.length > 0) {
                let history = JSON.parse(localStorage.getItem('search_history_bmd')) || [];
                if (!history.includes(query)) {
                    history.unshift(query);
                    localStorage.setItem('search_history_bmd', JSON.stringify(history.slice(0, 5)));
                }
            }
        });

        input.addEventListener('input', function() {
            const query = this.value;
            if (query.length < 2) return; 
            fetch(`/{{ $lokasi }}/bmd/autocomplete?term=${query}`).then(res => res.json()).then(data => {
                data.forEach(item => {
                    let option = document.createElement('option');
                    option.value = item.value; // Nama pemakai
                    option.label = item.label; // Info Lengkap
                    list.appendChild(option);
                });
            });
        });
    });
</script>
@endsection