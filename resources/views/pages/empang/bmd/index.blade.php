@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    {{-- Card Header - Aksi dan Pencarian --}}
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Penggunaan BMD - {{ ucfirst($lokasi) }}</h6>
        
        <div class="d-flex">
            {{-- Form Pencarian --}}
            <form action="{{ route('lokasi.bmd.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Cari pemakai/barang..." name="search" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>

            {{-- Tombol Aksi dengan Dropdown --}}
            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.bmd.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle fa-sm text-white-50"></i> Tambah Data
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <h6 class="dropdown-header">Opsi Lain:</h6>
                    
                    {{-- Cetak PDF --}}
                    <a class="dropdown-item" href="{{ route('lokasi.bmd.print', ['lokasi' => $lokasi]) }}" target="_blank">
                        <i class="fas fa-print fa-fw mr-2 text-gray-400"></i>Cetak Laporan (PDF)
                    </a>
                    
                    {{-- Export Excel (Fitur Baru) --}}
                    <a class="dropdown-item" href="{{ route('lokasi.export.excel', ['lokasi' => $lokasi, 'menu' => 'bmd']) }}">
                        <i class="fas fa-file-excel fa-fw mr-2 text-success"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Card Body - Tabel Data --}}
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0" style="font-size: 12px;">
                <thead class="thead-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle">No</th>
                        <th rowspan="2" class="align-middle">NIBAR</th>
                        <th rowspan="2" class="align-middle">Kode Barang</th>
                        <th rowspan="2" class="align-middle" style="min-width: 150px;">Nama Barang</th>
                        <th rowspan="2" class="align-middle">Spesifikasi</th>
                        <th rowspan="2" class="align-middle">Lokasi/Alamat Penggunaan</th>
                        <th colspan="5">Data Pemakai</th>
                        <th colspan="3">Dokumen Sumber (BAST)</th>
                        <th colspan="3">Dokumen Pendukung Lain</th>
                        <th rowspan="2" class="align-middle">Keterangan</th>
                        <th rowspan="2" class="align-middle" style="min-width: 80px;">Aksi</th>
                    </tr>
                    <tr>
                        {{-- Sub Kolom Pemakai --}}
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Jabatan</th>
                        <th>Identitas</th>
                        <th>Alamat</th>
                        
                        {{-- Sub Kolom BAST --}}
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>File</th>
                        
                        {{-- Sub Kolom Lain --}}
                        <th>Nama</th>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bmds as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + $bmds->firstItem() - 1 }}</td>
                        
                        {{-- Data Barang (Relasi) --}}
                        <td>{{ $item->peralatan->nibr ?? '-' }}</td>
                        <td>{{ $item->peralatan->kode_barang ?? '-' }}</td>
                        <td class="font-weight-bold">{{ $item->peralatan->nama_barang ?? '-' }}</td>
                        <td>{{ $item->peralatan->merk_tipe ?? '-' }}</td>
                        
                        {{-- Data Lokasi Fisik --}}
                        <td class="text-primary font-weight-bold">{{ $item->alamat_penggunaan }}</td>
                        
                        {{-- Data Pemakai --}}
                        <td>{{ $item->pemakai_nama }}</td>
                        <td>{{ $item->pemakai_status }}</td>
                        <td>{{ $item->pemakai_jabatan }}</td>
                        <td>{{ $item->pemakai_identitas }}</td>
                        <td>{{ $item->pemakai_alamat }}</td>
                        
                        {{-- BAST --}}
                        <td>{{ $item->bast_nomor }}</td>
                        <td>{{ $item->bast_tanggal ? $item->bast_tanggal->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">
                            @if($item->bast_file)
                                <a href="{{ asset('storage/' . $item->bast_file) }}" target="_blank" class="btn btn-info btn-circle btn-sm" title="Lihat File">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        
                        {{-- Dokumen Lain --}}
                        <td>{{ $item->dokumen_lain_nama }}</td>
                        <td>{{ $item->dokumen_lain_nomor }}</td>
                        <td>{{ $item->dokumen_lain_tanggal ? $item->dokumen_lain_tanggal->format('d/m/Y') : '-' }}</td>
                        
                        <td>{{ $item->keterangan }}</td>
                        
                        {{-- Aksi --}}
                        <td class="text-center">
                            <a href="{{ route('lokasi.bmd.edit', [$lokasi, $item->id]) }}" class="btn btn-sm btn-warning" title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('lokasi.bmd.destroy', [$lokasi, $item->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Data">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="18" class="text-center py-5 text-gray-500">
                            <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i><br>
                            Belum ada data penggunaan BMD di lokasi {{ ucfirst($lokasi) }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end">
            {{ $bmds->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</div>
@endsection