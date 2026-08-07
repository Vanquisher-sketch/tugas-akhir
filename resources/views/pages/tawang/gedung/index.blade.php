@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data Gedung & Bangunan (KIB C) - Kecamatan {{ ucfirst($lokasi) }}</h6>
        
        <div class="d-flex">
            <form action="{{ route('lokasi.gedung.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" 
                           placeholder="Cari gedung/kode..." 
                           name="search" id="autoSearch" list="searchList"
                           value="{{ request('search') }}" autocomplete="off">
                    <datalist id="searchList"></datalist>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>

            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.gedung.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle fa-sm text-white-50"></i> Tambah Data
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="{{ route('lokasi.gedung.print', ['lokasi' => $lokasi]) }}" target="_blank">
                        <i class="fas fa-print fa-fw mr-2 text-gray-400"></i> Cetak Data
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive" style="position: relative;">
            <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0" style="font-size: 0.75rem; color: #000; white-space: nowrap;">
                <thead class="thead-light text-center align-middle">
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Lokasi</th>
                        <th>Nama Barang</th>
                        <th>No. Register</th>
                        <th>Spesifikasi Barang</th>
                        <th>Spesifikasi Lainnya</th>
                        <th>Jml Lantai</th>
                        <th>Lok (Fisik)</th>
                        <th>Titik Koordinat</th>
                        <th>Status Tanah</th>
                        <th>Jumlah</th>
                        <th>Satuan</th>
                        <th>Harga Satuan (Rp)</th>
                        <th>Nilai Perolehan (Rp)</th>
                        <th>Cara Perolehan</th>
                        <th>Tgl Perolehan</th>
                        <th>Status Penggunaan</th>
                        <th>Keterangan</th>
                        <th style="position: sticky; right: 0; background-color: #eaecf4; z-index: 2; border-left: 2px solid #e3e6f0;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataGedung as $item)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration + $dataGedung->firstItem() - 1 }}</td>
                        <td class="font-weight-bold text-primary align-middle">{{ $item->gedung_kode_barang }}</td>
                        <td class="align-middle">{{ $item->lokasi }}</td>
                        <td class="align-middle">{{ $item->gedung_nama_barang }}</td>
                        <td class="text-center align-middle">{{ $item->gedung_nomor_register }}</td>
                        <td class="align-middle">{{ $item->gedung_spesifikasi_barang ?? '-' }}</td>
                        <td class="align-middle">{{ $item->gedung_spesifikasi_lainnya ?? '-' }}</td>
                        <td class="text-center align-middle">{{ $item->gedung_jumlah_lantai ?? '-' }}</td>
                        <td class="align-middle">{{ $item->gedung_lokasi_fisik ?? '-' }}</td>
                        <td class="align-middle">{{ $item->gedung_titik_koordinat ?? '-' }}</td>
                        <td class="align-middle">{{ $item->gedung_status_kepemilikan_tanah ?? '-' }}</td>
                        
                        <td class="text-center align-middle">{{ $item->gedung_jumlah }}</td>
                        <td class="text-center align-middle">{{ $item->gedung_satuan }}</td>
                        
                        <td class="text-right align-middle">{{ number_format($item->gedung_harga_satuan, 2, ',', '.') }}</td>
                        <td class="text-right font-weight-bold align-middle">{{ number_format($item->gedung_nilai_perolehan, 2, ',', '.') }}</td>
                        
                        <td class="align-middle">{{ $item->gedung_cara_perolehan }}</td>
                        <td class="text-center align-middle">{{ $item->gedung_tanggal_perolehan ? \Carbon\Carbon::parse($item->gedung_tanggal_perolehan)->format('d/m/Y') : '-' }}</td>
                        
                        <td class="text-center align-middle">
                            @if($item->gedung_status_penggunaan)
                                <span class="badge badge-info">{{ $item->gedung_status_penggunaan }}</span>
                            @else
                                -
                            @endif
                        </td>
                        
                        <td class="align-middle">{{ Str::limit($item->gedung_keterangan, 30) ?? '-' }}</td>
                        
                        <td class="text-center align-middle" style="position: sticky; right: 0; background-color: #fff; z-index: 1; border-left: 2px solid #e3e6f0;">
                            <a href="{{ route('lokasi.gedung.edit', ['lokasi' => $lokasi, 'kode_barang' => $item->gedung_kode_barang]) }}" class="btn btn-sm btn-warning mb-1" title="Edit">
    <i class="fas fa-edit"></i>
</a>

<form action="{{ route('lokasi.gedung.destroy', ['lokasi' => $lokasi, 'kode_barang' => $item->gedung_kode_barang]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?')">
    @csrf 
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger mb-1"><i class="fas fa-trash"></i></button>
</form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="21" class="text-center py-4 text-muted">Data gedung tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $dataGedung->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('autoSearch');
        const list = document.getElementById('searchList');

        input.addEventListener('focus', function() {
            let history = JSON.parse(localStorage.getItem('search_history_gedung')) || [];
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
                let history = JSON.parse(localStorage.getItem('search_history_gedung')) || [];
                if (!history.includes(query)) {
                    history.unshift(query);
                    localStorage.setItem('search_history_gedung', JSON.stringify(history.slice(0, 5)));
                }
            }
        });

        input.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length < 2) return; 
            
            fetch(`/{{ $lokasi }}/gedung/autocomplete?term=${query}`)
                .then(res => res.json())
                .then(data => {
                    list.innerHTML = ''; 
                    data.forEach(item => {
                        let option = document.createElement('option');
                        option.value = item.label;
                        list.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching autocomplete:', error));
        });
    });
</script>
@endsection