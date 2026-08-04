@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data Tanah (KIB A) - Kelurahan {{ ucfirst($lokasi) }}</h6>
        
        <div class="d-flex">
            <form action="{{ route('lokasi.tanah.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" 
                           placeholder="Cari data tanah..." 
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
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0" style="font-size: 0.72rem; color: #000; white-space: nowrap;">
                <thead class="thead-light text-center align-middle">
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Sistem Lokasi</th>
                        <th>No. Register</th>
                        <th>Nama Barang</th>
                        <th>Nibar</th>
                        <th>Spesifikasi Barang (Luas)</th>
                        <th>Spesifikasi Lainnya</th>
                        <th>Luas (Volume)</th>
                        <th>Satuan</th>
                        <th>Alamat Fisik (Lok)</th>
                        <th>Koordinat</th>
                        <th>Bukti Dokumen</th>
                        <th>No. Dokumen</th>
                        <th>Tgl Dokumen</th>
                        <th>Nama di Dokumen</th>
                        <th>Harga Satuan (Rp)</th>
                        <th>Nilai Perolehan (Rp)</th>
                        <th>Cara Perolehan</th>
                        <th>Tgl Perolehan</th>
                        <th>Status Penggunaan</th>
                        <th>Keterangan</th>
                        <th style="position: sticky; right: 0; z-index: 3; background-color: #eaecf4; box-shadow: -2px 0 5px rgba(0,0,0,0.05);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataTanah as $item)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration + $dataTanah->firstItem() - 1 }}</td>
                        <td class="font-weight-bold text-primary align-middle">{{ $item->tanah_kode_barang }}</td>
                        <td class="text-center align-middle">{{ $item->lokasi ?? '-' }}</td>
                        <td class="text-center align-middle">{{ $item->tanah_nomor_register ?? '-' }}</td>
                        <td class="align-middle">{{ $item->tanah_nama_barang }}</td>
                        <td class="align-middle">{{ $item->tanah_nibar ?? '-' }}</td>
                        <td class="align-middle">{{ $item->tanah_spesifikasi_barang ?? '-' }}</td>
                        <td class="align-middle">{{ $item->tanah_spesifikasi_lainnya ?? '-' }}</td>
                        <td class="text-center align-middle">{{ number_format($item->tanah_jumlah, 2, ',', '.') }}</td>
                        <td class="text-center align-middle">{{ $item->tanah_satuan }}</td>
                        <td class="align-middle">{{ $item->tanah_lokasi_fisik }}</td>
                        <td class="align-middle">{{ $item->tanah_titik_koordinat ?? '-' }}</td>
                        <td class="align-middle">{{ $item->tanah_bukti_nama ?? '-' }}</td>
                        <td class="align-middle">{{ $item->tanah_bukti_nomor ?? '-' }}</td>
                        <td class="text-center align-middle">{{ $item->tanah_bukti_tanggal ? \Carbon\Carbon::parse($item->tanah_bukti_tanggal)->format('d/m/Y') : '-' }}</td>
                        <td class="align-middle">{{ $item->tanah_nama_kepemilikan_dokumen ?? '-' }}</td>
                        <td class="text-right align-middle">{{ $item->tanah_harga_satuan ? number_format($item->tanah_harga_satuan, 0, ',', '.') : '-' }}</td>
                        <td class="text-right font-weight-bold align-middle">{{ number_format($item->tanah_nilai_perolehan, 0, ',', '.') }}</td>
                        <td class="align-middle">{{ $item->tanah_cara_perolehan }}</td>
                        <td class="text-center align-middle">{{ $item->tanah_tanggal_perolehan ? \Carbon\Carbon::parse($item->tanah_tanggal_perolehan)->format('d/m/Y') : '-' }}</td>
                        <td class="text-center align-middle">
                            @if($item->tanah_status_penggunaan)
                                <span class="badge badge-info">{{ $item->tanah_status_penggunaan }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="align-middle">
                            {{ $item->tanah_keterangan ? Str::limit($item->tanah_keterangan, 30) : '-' }}
                        </td>
                        {{-- INI YANG DIPERBAIKI (tanah diganti jadi kode_barang) --}}
                        <td class="text-center align-middle bg-white" style="position: sticky; right: 0; z-index: 2; box-shadow: -2px 0 5px rgba(0,0,0,0.05);">
                            <a href="{{ route('lokasi.tanah.edit', ['lokasi' => $lokasi, 'kode_barang' => $item->tanah_kode_barang]) }}" class="btn btn-sm btn-warning mb-1" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('lokasi.tanah.destroy', ['lokasi' => $lokasi, 'kode_barang' => $item->tanah_kode_barang]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data dengan kode {{ $item->tanah_kode_barang }} ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-1" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="23" class="text-center py-4">Data tanah tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $dataTanah->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</div>
@endsection