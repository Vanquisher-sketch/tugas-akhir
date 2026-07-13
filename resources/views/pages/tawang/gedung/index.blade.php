@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data Gedung & Bangunan (KIB C) - {{ ucfirst($lokasi) }}</h6>
        
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
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0" style="font-size: 0.75rem; color: #000; white-space: nowrap;">
                <thead class="thead-light text-center align-middle">
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Lokasi</th>
                        <th>Nama Barang</th>
                        <th>NBAR</th>
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
                        <th class="sticky-top right-0 bg-light">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataGedung as $item)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration + $dataGedung->firstItem() - 1 }}</td>
                        <td class="font-weight-bold text-primary align-middle">{{ $item->kode_barang }}</td>
                        <td class="align-middle">{{ $item->lokasi }}</td>
                        <td class="align-middle">{{ $item->nama_barang }}</td>
                        <td class="text-center align-middle">{{ $item->nbar ?? '-' }}</td>
                        <td class="text-center align-middle">{{ $item->nomor_register }}</td>
                        <td class="align-middle">{{ $item->spesifikasi_barang ?? '-' }}</td>
                        <td class="align-middle">{{ $item->spesifikasi_lainnya ?? '-' }}</td>
                        <td class="text-center align-middle">{{ $item->jumlah_lantai ?? '-' }}</td>
                        <td class="align-middle">{{ $item->Lok }}</td>
                        <td class="align-middle">{{ $item->titik_koordinat ?? '-' }}</td>
                        <td class="align-middle">{{ $item->status_kepemilikan_tanah ?? '-' }}</td>
                        
                        <td class="text-center align-middle">{{ $item->jumlah }}</td>
                        <td class="text-center align-middle">{{ $item->satuan }}</td>
                        
                        <td class="text-right align-middle">{{ number_format($item->harga_satuan, 2, ',', '.') }}</td>
                        <td class="text-right font-weight-bold align-middle">{{ number_format($item->nilai_perolehan, 2, ',', '.') }}</td>
                        
                        <td class="align-middle">{{ $item->cara_perolehan }}</td>
                        <td class="text-center align-middle">{{ $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d/m/Y') : '-' }}</td>
                        
                        <td class="text-center align-middle">
                            @if($item->status_penggunaan)
                                <span class="badge badge-info">{{ $item->status_penggunaan }}</span>
                            @else
                                -
                            @endif
                        </td>
                        
                        <td class="align-middle">{{ Str::limit($item->keterangan, 30) ?? '-' }}</td>
                        
                        <td class="text-center align-middle bg-white">
                            <a href="{{ route('lokasi.gedung.edit', ['lokasi' => $lokasi, 'gedung' => $item->kode_barang]) }}" class="btn btn-sm btn-warning mb-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('lokasi.gedung.destroy', ['lokasi' => $lokasi, 'gedung' => $item->kode_barang]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data dengan Kode {{ $item->kode_barang }} ini?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-1" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
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