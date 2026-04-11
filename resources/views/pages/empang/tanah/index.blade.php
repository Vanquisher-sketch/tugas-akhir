@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    {{-- Card Header - Aksi dan Pencarian --}}
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data Inventaris Tanah (KIB A) - {{ ucfirst($lokasi) }}</h6>
        
        <div class="d-flex">
            {{-- Form Pencarian --}}
            <form action="{{ route('lokasi.tanah.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Cari nama/alamat..." name="search" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>

            {{-- Tombol Aksi --}}
            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.tanah.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle fa-sm text-white-50"></i> Tambah Data
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <h6 class="dropdown-header">Opsi Lain:</h6>
                    <a class="dropdown-item" href="{{ route('lokasi.tanah.print', ['lokasi' => $lokasi]) }}" target="_blank">
                        <i class="fas fa-print fa-fw mr-2 text-gray-400"></i>Cetak Data
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
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0" style="font-size: 0.8rem; color: #000;">
                <thead class="thead-light text-center" style="background-color: #eaecf4; font-weight: bold;">
                    <tr>
                        <th rowspan="2" class="align-middle">No.</th>
                        <th rowspan="2" class="align-middle">Kode Barang</th>
                        <th rowspan="2" class="align-middle">Nama Barang</th>
                        <th rowspan="2" class="align-middle">NIBAR</th>
                        <th rowspan="2" class="align-middle">Nomor Register</th>
                        
                        {{-- REVISI: Menambahkan kolom Spesifikasi Barang --}}
                        <th rowspan="2" class="align-middle">Spesifikasi Barang</th>
                        <th rowspan="2" class="align-middle">Spesifikasi Lainnya</th>
                        
                        <th rowspan="2" class="align-middle">Jumlah</th>
                        <th rowspan="2" class="align-middle">Satuan</th>
                        <th rowspan="2" class="align-middle">Lokasi</th>
                        <th rowspan="2" class="align-middle">Titik Koordinat</th>
                        
                        {{-- Bukti Kepemilikan (4 Kolom) --}}
                        <th colspan="4" class="align-middle">Bukti Kepemilikan</th>
                        
                        <th rowspan="2" class="align-middle">Harga Satuan (Rp)</th>
                        <th rowspan="2" class="align-middle">Nilai Perolehan (Rp)</th>
                        <th rowspan="2" class="align-middle">Cara Perolehan</th>
                        <th rowspan="2" class="align-middle">Tgl Perolehan</th>
                        <th rowspan="2" class="align-middle">Status</th>
                        <th rowspan="2" class="align-middle">Keterangan</th>
                        <th rowspan="2" class="align-middle" style="min-width: 80px;">Aksi</th>
                    </tr>
                    <tr>
                        {{-- Sub-Header Bukti Kepemilikan --}}
                        <th class="align-middle">Nama</th>
                        <th class="align-middle">Nomor</th>
                        <th class="align-middle">Tanggal</th>
                        <th class="align-middle">Nama Kepemilikan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataTanah as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration + $dataTanah->firstItem() - 1 }}</td>
                            
                            <td>{{ $item->kode_barang }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td>{{ $item->nibar }}</td>
                            <td>{{ $item->nomor_register }}</td>
                            
                            {{-- ISI DATA: Spesifikasi Barang & Lainnya --}}
                            <td>{{ $item->spesifikasi_barang }}</td>
                            <td>{{ $item->spesifikasi_lainnya }}</td>
                            
                            <td class="text-center">{{ $item->jumlah }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>{{ $item->Lok }}</td>
                            <td>{{ $item->titik_koordinat }}</td>
                            
                            {{-- ISI DATA BUKTI KEPEMILIKAN (4 Kolom) --}}
                            <td>{{ $item->bukti_nama }}</td>
                            <td>{{ $item->bukti_nomor }}</td>
                            <td class="text-center">{{ $item->bukti_tanggal ? \Carbon\Carbon::parse($item->bukti_tanggal)->format('d-m-Y') : '-' }}</td>
                            <td>{{ $item->nama_kepemilikan_dokumen }}</td>
                            
                            <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                            
                            <td>{{ $item->cara_perolehan }}</td>
                            <td class="text-center">{{ $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d-m-Y') : '-' }}</td>
                            
                            <td>{{ $item->status_penggunaan }}</td>
                            <td>{{ $item->keterangan }}</td>

                            <td class="text-center">
                                <a href="{{ route('lokasi.tanah.edit', ['lokasi' => $lokasi, 'tanah' => $item->id]) }}" class="btn btn-sm btn-warning mb-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('lokasi.tanah.destroy', ['lokasi' => $lokasi, 'tanah' => $item->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mb-1" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form> 
                            </td>
                        </tr>
                    @empty
                        <tr>
                            {{-- Colspan jadi 22 (karena tambah 1 kolom spesifikasi) --}}
                            <td colspan="22" class="text-center py-5">
                                <div class="text-gray-500">
                                    <i class="fas fa-folder-open fa-3x mb-3"></i><br>
                                    @if (request('search'))
                                        Data tidak ditemukan untuk pencarian '{{ request('search') }}'.
                                    @else
                                        Belum ada data inventaris tanah.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $dataTanah->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</div>
@endsection