@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    {{-- Card Header - Aksi dan Pencarian --}}
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data Gedung & Bangunan (KIB C) - {{ ucfirst($lokasi) }}</h6>
        
        <div class="d-flex">
            {{-- Form Pencarian --}}
            <form action="{{ route('lokasi.gedung.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Cari data..." name="search" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>

            {{-- Tombol Aksi --}}
            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.gedung.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle fa-sm text-white-50"></i> Tambah Data
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <h6 class="dropdown-header">Opsi Lain:</h6>
                    <a class="dropdown-item" href="{{ route('lokasi.gedung.print', ['lokasi' => $lokasi]) }}" target="_blank">
                        <i class="fas fa-print fa-fw mr-2 text-gray-400"></i>Cetak Data
                    </a>
                    <a class="dropdown-item" href="{{ route('lokasi.export.excel', ['lokasi' => $lokasi, 'menu' => 'gedung']) }}">
                        <i class="fas fa-file-excel fa-fw mr-2 text-gray-400"></i>Export Excel
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
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light text-center" style="font-size: 14px;">
                    <tr>
                        <th class="align-middle">No</th>
                        <th class="align-middle">Kode Barang</th>
                        <th class="align-middle">Nama Barang</th>
                        <th class="align-middle">NIBAR</th>
                        <th class="align-middle">No. Register</th>
                        {{-- Sesuai Model: spesifikasi_barang --}}
                        <th class="align-middle">Spesifikasi Barang</th> 
                        <th class="align-middle">Spesifikasi Lainnya</th>
                        {{-- Sesuai Model: jumlah_lantai --}}
                        <th class="align-middle">Jml Lantai</th> 
                        {{-- Sesuai Model: jumlah & satuan --}}
                        <th class="align-middle">Jumlah</th>
                        <th class="align-middle">Satuan</th>
                        {{-- Sesuai Model: Lok (Huruf Besar) --}}
                        <th class="align-middle">Lokasi / Alamat</th> 
                        <th class="align-middle">Titik Koordinat</th>
                        <th class="align-middle">Status Tanah</th>
                        <th class="align-middle">Harga Satuan (Rp)</th>
                        <th class="align-middle">Nilai Perolehan (Rp)</th>
                        <th class="align-middle">Cara Perolehan</th>
                        <th class="align-middle">Tgl Perolehan</th>
                        <th class="align-middle">Status Penggunaan</th>
                        <th class="align-middle">Keterangan</th>
                        <th class="align-middle" style="min-width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                    @forelse ($dataGedung as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + $dataGedung->firstItem() - 1 }}</td>
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->nbar }}</td>
                        <td>{{ $item->nomor_register }}</td>
                        
                        {{-- Perbaikan: menggunakan spesifikasi_barang --}}
                        <td>{{ $item->spesifikasi_barang }}</td>
                        <td>{{ $item->spesifikasi_lainnya }}</td>
                        
                        {{-- Perbaikan: Menambahkan jumlah_lantai --}}
                        <td class="text-center">{{ $item->jumlah_lantai }}</td>
                        
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td>{{ $item->satuan }}</td>
                        
                        {{-- Perbaikan: menggunakan Lok (Kapital L) --}}
                        <td>{{ $item->Lok }}</td>
                        
                        <td>{{ $item->titik_koordinat }}</td>
                        <td>{{ $item->status_kepemilikan_tanah }}</td>
                        
                        {{-- Menghapus kolom 'jumlah_satuan_tanah' karena tidak ada di Model --}}
                        
                        <td class="text-right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                        <td>{{ $item->cara_perolehan }}</td>
                        <td class="text-center">{{ $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d-m-Y') : '-' }}</td>
                        <td>{{ $item->status_penggunaan }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td class="text-center">
                            <a href="{{ route('lokasi.gedung.edit', ['lokasi' => $lokasi, 'gedung' => $item->id]) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('lokasi.gedung.destroy', ['lokasi' => $lokasi, 'gedung' => $item->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- Adjusted colspan --}}
                        <td colspan="20" class="text-center">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $dataGedung->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</div>
@endsection