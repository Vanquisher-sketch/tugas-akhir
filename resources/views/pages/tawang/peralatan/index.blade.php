@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-boxes mr-2"></i>Data Peralatan & Mesin (KIB B) - Kecamatan {{ ucfirst($lokasi) }}
        </h6>
        
        <div class="d-flex">
            <form action="{{ route('lokasi.peralatan.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" 
                           placeholder="Cari kode/nama/nopol..." 
                           name="search" id="autoSearch" list="searchList"
                           value="{{ request('search') }}" autocomplete="off"
                           data-url="{{ url($lokasi . '/peralatan/autocomplete') }}">
                    <datalist id="searchList"></datalist>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit" title="Cari Data"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>

            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.peralatan.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle fa-sm text-white-50 mr-1"></i> Tambah Data
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
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-dark" width="100%" cellspacing="0" style="font-size: 11px; vertical-align: middle;">
                <thead class="thead-light text-center">
                    <tr class="font-weight-bold">
                        <th rowspan="2" class="align-middle" style="width: 3%;">No</th>
                        <th rowspan="2" class="align-middle">Foto</th>
                        <th rowspan="2" class="align-middle">Kode Barang</th>
                        <th rowspan="2" class="align-middle">Nama Barang</th>
                        <th rowspan="2" class="align-middle">NIBAR</th>
                        <th rowspan="2" class="align-middle">No. Reg</th>
                        <th rowspan="2" class="align-middle">Merk / Tipe</th>
                        <th rowspan="2" class="align-middle">Lokasi Fisik</th>
                        <th colspan="3" class="align-middle">Identitas Fisik & Legalitas Kendaraan</th>
                        <th colspan="2" class="align-middle">Sistem Patroli Pajak</th>
                        <th rowspan="2" class="align-middle">Jml</th>
                        <th rowspan="2" class="align-middle">Satuan</th>
                        <th rowspan="2" class="align-middle">Harga Satuan (Rp)</th>
                        <th rowspan="2" class="align-middle">Nilai Perolehan (Rp)</th>
                        <th rowspan="2" class="align-middle">Cara Perolehan</th>
                        <th rowspan="2" class="align-middle">Tgl Perolehan</th>
                        <th rowspan="2" class="align-middle" style="width: 7%;">Status Pakai</th>
                        <th rowspan="2" class="align-middle" style="width: 7%;">Kondisi</th>
                        <th rowspan="2" class="align-middle">Aksi</th>
                    </tr>
                    <tr class="font-weight-bold">
                        <th>No. Polisi</th>
                        <th>No. Rangka</th>
                        <th>No. BPKB</th>
                        <th>Pajak Tahunan</th>
                        <th>STNK (5 Thn)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataPeralatan as $item)
                    <tr>
                        <td class="text-center align-middle font-weight-bold">{{ $loop->iteration + $dataPeralatan->firstItem() - 1 }}</td>
                        
                        {{-- Tampilkan Foto --}}
                        <td class="text-center align-middle">
                            @if($item->alat_foto)
                                <a href="{{ asset('storage/' . $item->alat_foto) }}" target="_blank">
                                    <!-- UBAH MENJADI SEPERTI INI -->
<img src="{{ asset('storage/' . $item->alat_foto) }}" alt="foto" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                                </a>
                            @else
                                <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 9px;">NO IMG</span>
                            @endif
                        </td>

                        <td class="font-weight-bold text-primary align-middle text-center">{{ $item->alat_kode_barang }}</td>
                        <td class="align-middle font-weight-bold text-gray-900">{{ $item->alat_nama_barang }}</td>
                        <td class="align-middle text-center">{{ $item->alat_nibar ?? '-' }}</td>
                        <td class="align-middle text-center font-weight-bold text-secondary">{{ $item->alat_nomor_register ?? '-' }}</td>
                        <td class="align-middle">{{ $item->alat_merk_tipe ?? '-' }}</td>
                        <td class="align-middle text-center font-weight-bold text-dark"><span class="badge badge-light border px-2 py-1">{{ $item->alat_lokasi_fisik ?? '-' }}</span></td>
                        
                        {{-- Identitas Kendaraan --}}
                        <td class="text-center align-middle">
                            @if($item->alat_nomor_polisi)
                                <span class="badge badge-dark px-2 py-1 font-weight-bold">{{ $item->alat_nomor_polisi }}</span>
                            @else - @endif
                        </td>
                        <td class="align-middle text-center"><small class="font-weight-bold text-secondary">{{ $item->alat_nomor_rangka ?? '-' }}</small></td>
                        <td class="align-middle text-center"><small class="font-weight-bold text-secondary">{{ $item->alat_nomor_bpkb ?? '-' }}</small></td>
                        
                        {{-- Patroli Pajak & STNK --}}
                        <td class="text-center align-middle">
                            @if($item->alat_tanggal_pajak)
                                <span class="{{ \Carbon\Carbon::parse($item->alat_tanggal_pajak)->isPast() ? 'text-danger font-weight-bold animate__animated animate__flash animate__infinite animate__slower' : 'font-weight-bold' }}">
                                    {{ \Carbon\Carbon::parse($item->alat_tanggal_pajak)->format('d/m/Y') }}
                                </span>
                            @else - @endif
                        </td>
                        <td class="text-center align-middle">
                            @if($item->alat_tanggal_stnk)
                                <span class="{{ \Carbon\Carbon::parse($item->alat_tanggal_stnk)->isPast() ? 'text-danger font-weight-bold' : 'font-weight-bold' }}">
                                    {{ \Carbon\Carbon::parse($item->alat_tanggal_stnk)->format('d/m/Y') }}
                                </span>
                            @else - @endif
                        </td>

                        {{-- Jumlah & Satuan --}}
                        <td class="text-center align-middle font-weight-bold text-primary">{{ $item->alat_jumlah }}</td>
                        <td class="text-center align-middle text-uppercase"><span class="badge badge-secondary px-1">{{ $item->alat_satuan }}</span></td>
                        
                        {{-- Finansial Harga --}}
                        <td class="text-right align-middle">{{ number_format($item->alat_harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right font-weight-bold align-middle text-gray-900 bg-light">{{ number_format($item->alat_nilai_perolehan, 0, ',', '.') }}</td>
                        
                        {{-- Asal Usul Perolehan --}}
                        <td class="text-center align-middle font-weight-bold text-capitalize"><small>{{ $item->alat_cara_perolehan }}</small></td>
                        <td class="text-center align-middle">
                            <small>{{ $item->alat_tanggal_perolehan ? \Carbon\Carbon::parse($item->alat_tanggal_perolehan)->format('d/m/Y') : '-' }}</small>
                        </td>

                        {{-- Status Penggunaan --}}
                        <td class="text-center align-middle">
                            @if($item->alat_status_penggunaan === 'Aktif')
                                <span class="badge badge-primary px-2 py-1 font-weight-bold text-uppercase" style="font-size: 9px;">
                                    <i class="fas fa-user-check mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="badge badge-light border text-muted px-2 py-1 font-weight-bold text-uppercase" style="font-size: 9px;">
                                    Tersedia
                                </span>
                            @endif
                        </td>

                        {{-- Status Kondisi 3 Tingkat --}}
                        <td class="text-center align-middle">
                            @if($item->alat_kondisi === 'Baik')
                                <span class="badge badge-success px-2 py-1 font-weight-bold text-uppercase" style="font-size: 9px;">
                                    <i class="fas fa-check-circle mr-1"></i> Baik
                                </span>
                            @elseif($item->alat_kondisi === 'Rusak Ringan')
                                <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold text-uppercase" style="font-size: 9px;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Rusak Rgn
                                </span>
                            @elseif($item->alat_kondisi === 'Rusak Berat')
                                <span class="badge badge-danger px-2 py-1 font-weight-bold text-uppercase shadow-sm" style="font-size: 9px;">
                                    <i class="fas fa-times-circle mr-1"></i> Rusak Brt
                                </span>
                            @else
                                <span class="badge badge-secondary px-2 py-1 font-weight-bold">-</span>
                            @endif
                        </td>
                        
                        {{-- Kolom Aksi Transaksional --}}
                        <td class="text-center align-middle">
                            <div class="btn-group shadow-sm">
                                <a href="{{ route('lokasi.peralatan.show', ['lokasi' => $lokasi, 'kode_barang' => $item->alat_kode_barang]) }}" class="btn btn-sm btn-info py-1 text-white" title="Detail Aset">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('lokasi.peralatan.edit', ['lokasi' => $lokasi, 'kode_barang' => $item->alat_kode_barang]) }}" class="btn btn-sm btn-warning py-1" title="Ubah Aset">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('lokasi.peralatan.destroy', ['lokasi' => $lokasi, 'kode_barang' => $item->alat_kode_barang]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data aset KIB B ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger py-1" title="Hapus Aset"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="22" class="text-center py-5 text-gray-500 font-weight-bold">
                            <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i><br>
                            Data Peralatan & Mesin (KIB B) belum terisi untuk lokasi wilayah operasional ini.
                        </td>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('autoSearch');
        const list = document.getElementById('searchList');
        let timeout = null; 

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

        input.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value;
            const baseUrl = this.getAttribute('data-url');
            
            if (query.length < 2) return; 
            
            timeout = setTimeout(() => {
                fetch(`${baseUrl}?term=${query}`)
                    .then(res => res.json())
                    .then(data => {
                        list.innerHTML = '';
                        data.forEach(item => {
                            let option = document.createElement('option');
                            option.value = item.label;
                            list.appendChild(option);
                        });
                    })
                    .catch(err => console.error('Error fetching autocomplete:', err));
            }, 300);
        });
    });
</script>
@endsection