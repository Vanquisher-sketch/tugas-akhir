@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Barang Rusak Berat - {{ ucfirst($lokasi) }}</h6>
        
        <div class="d-flex">
            <form action="{{ route('lokasi.rusak.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" 
                           placeholder="Cari ID/Nama/Nopol..." 
                           name="search" id="autoSearch" list="searchList"
                           value="{{ request('search') }}" autocomplete="off">
                    <datalist id="searchList"></datalist>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>

            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.rusak.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle fa-sm text-white-50"></i> Tambah Data
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="{{ route('lokasi.rusak.print', ['lokasi' => $lokasi]) }}" target="_blank">
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
            <table class="table table-bordered table-hover" width="100%" cellspacing="0" style="font-size: 0.8rem; color: #000;">
                <thead class="thead-light text-center">
                    <tr>
                        <th>No</th>
                        <th>No. ID Pemda</th>
                        <th>Nama Barang</th>
                        <th>Spesifikasi</th>
                        <th>No. Polisi</th>
                        <th>Thn Perolehan</th>
                        <th>Harga (Rp)</th>
                        <th>Kondisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataRusak as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + $dataRusak->firstItem() - 1 }}</td>
                        <td class="font-weight-bold text-danger">{{ $item->no_id_pemda }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->spesifikasi }}</td>
                        <td class="text-center">{{ $item->no_polisi ?? '-' }}</td>
                        <td class="text-center">{{ $item->tahun_perolehan }}</td>
                        <td class="text-right">{{ number_format($item->harga_perolehan, 0, ',', '.') }}</td>
                        <td class="text-center"><span class="badge badge-danger">{{ $item->kondisi }}</span></td>
                        <td class="text-center">
                            {{-- REVISI: link menggunakan no_id_pemda --}}
                            <a href="{{ route('lokasi.rusak.edit', ['lokasi' => $lokasi, 'rusak' => $item->no_id_pemda]) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('lokasi.rusak.destroy', ['lokasi' => $lokasi, 'rusak' => $item->no_id_pemda]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form> 
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-4">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $dataRusak->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('autoSearch');
        const list = document.getElementById('searchList');

        input.addEventListener('focus', function() {
            let history = JSON.parse(localStorage.getItem('search_history_rusak')) || [];
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
                let history = JSON.parse(localStorage.getItem('search_history_rusak')) || [];
                if (!history.includes(query)) {
                    history.unshift(query);
                    localStorage.setItem('search_history_rusak', JSON.stringify(history.slice(0, 5)));
                }
            }
        });

        input.addEventListener('input', function() {
            const query = this.value;
            if (query.length < 2) return; 
            fetch(`/{{ $lokasi }}/rusak/autocomplete?term=${query}`).then(res => res.json()).then(data => {
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