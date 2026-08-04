@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data Jalan, Irigasi & Jaringan (KIB D) - Kelurahan {{ ucfirst($lokasi) }}</h6>
        
        <div class="d-flex">
            <form action="{{ route('lokasi.jalan.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" 
                           placeholder="Cari jalan/jaringan..." 
                           name="search" id="autoSearch" list="searchList"
                           value="{{ request('search') }}" autocomplete="off">
                    <datalist id="searchList"></datalist>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>

            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.jalan.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle fa-sm text-white-50"></i> Tambah Data
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"></button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="{{ route('lokasi.jalan.print', ['lokasi' => $lokasi]) }}" target="_blank">
                        <i class="fas fa-print fa-fw mr-2 text-gray-400"></i>Cetak Data
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0" style="font-size: 0.72rem; color: #000; white-space: nowrap;">
                <thead class="thead-light text-center align-middle">
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>No. Register</th>
                        <th>Nama Barang</th>
                        <th>Nibar</th>
                        <th>Spesifikasi Barang</th>
                        <th>Spesifikasi Lainnya</th>
                        <th>No. Ruas</th>
                        <th>Alamat/Lokasi Fisik</th>
                        <th>Titik Koordinat</th>
                        <th>Status Tanah</th>
                        <th>Volume/Jumlah</th>
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
                    @forelse ($dataJalan as $item)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration + $dataJalan->firstItem() - 1 }}</td>
                        <td class="font-weight-bold text-primary align-middle">{{ $item->jalan_kode_barang }}</td>
                        <td class="text-center align-middle">{{ $item->jalan_nomor_register }}</td>
                        <td class="align-middle">{{ $item->jalan_nama_barang }}</td>
                        <td class="text-center align-middle">{{ $item->jalan_nibar ?? '-' }}</td>
                        <td class="align-middle">{{ $item->jalan_spesifikasi_barang ?? '-' }}</td>
                        <td class="align-middle">{{ $item->jalan_spesifikasi_lainnya ?? '-' }}</td>
                        <td class="text-center align-middle">{{ $item->jalan_nomor_ruas_jalan_jembatan_irigasi ?? '-' }}</td>
                        <td class="align-middle">{{ $item->jalan_lokasi_fisik ?? '-' }}</td>
                        <td class="align-middle">{{ $item->jalan_titik_koordinat ?? '-' }}</td>
                        <td class="align-middle">{{ $item->jalan_status_kepemilikan_tanah ?? '-' }}</td>
                        <td class="text-center align-middle">{{ number_format($item->jalan_jumlah, 0, ',', '.') }}</td>
                        <td class="text-center align-middle">{{ $item->jalan_satuan }}</td>
                        <td class="text-right align-middle">{{ number_format($item->jalan_harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right font-weight-bold align-middle">{{ number_format($item->jalan_nilai_perolehan, 0, ',', '.') }}</td>
                        <td class="align-middle">{{ $item->jalan_cara_perolehan }}</td>
                        <td class="text-center align-middle">{{ $item->jalan_tanggal_perolehan ? \Carbon\Carbon::parse($item->jalan_tanggal_perolehan)->format('d/m/Y') : '-' }}</td>
                        <td class="text-center align-middle">
                            @if($item->jalan_status_penggunaan)
                                <span class="badge badge-info">{{ $item->jalan_status_penggunaan }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="align-middle">{{ Str::limit($item->jalan_keterangan, 30) ?? '-' }}</td>
                        <td class="text-center align-middle bg-white">
                            {{-- 🌟 PERUBAHAN DI SINI: Ubah 'jalan' menjadi 'kode_barang' --}}
                            <a href="{{ route('lokasi.jalan.edit', ['lokasi' => $lokasi, 'kode_barang' => $item->jalan_kode_barang]) }}" class="btn btn-sm btn-warning mb-1" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('lokasi.jalan.destroy', ['lokasi' => $lokasi, 'kode_barang' => $item->jalan_kode_barang]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data dengan Kode {{ $item->jalan_kode_barang }} ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-1" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="20" class="text-center py-4">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $dataJalan->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('autoSearch');
        const list = document.getElementById('searchList');

        input.addEventListener('focus', function() {
            let history = JSON.parse(localStorage.getItem('search_history_jalan')) || [];
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
                let history = JSON.parse(localStorage.getItem('search_history_jalan')) || [];
                if (!history.includes(query)) {
                    history.unshift(query);
                    localStorage.setItem('search_history_jalan', JSON.stringify(history.slice(0, 5)));
                }
            }
        });

        input.addEventListener('input', function() {
            const query = this.value;
            if (query.length < 2) return; 
            fetch(`/{{ $lokasi }}/jalan/autocomplete?term=${query}`).then(res => res.json()).then(data => {
                list.innerHTML = ''; 
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