@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    {{-- Card Header - Aksi dan Pencarian --}}
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data Peralatan & Mesin (KIB B) - {{ ucfirst($lokasi) }}</h6>
        
        <div class="d-flex">
            {{-- Form Pencarian --}}
            <form action="{{ route('lokasi.peralatan.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Cari barang/merk..." name="search" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>

            {{-- Tombol Aksi --}}
            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.peralatan.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle fa-sm text-white-50"></i> Tambah Data
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <h6 class="dropdown-header">Opsi Lain:</h6>
                    <a class="dropdown-item" href="{{ route('lokasi.peralatan.print', ['lokasi' => $lokasi]) }}" target="_blank">
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
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0" style="font-size: 0.7rem; color: #000;">
                <thead class="thead-light text-center" style="background-color: #eaecf4; font-weight: bold;">
                    <tr>
                        <th rowspan="2" class="align-middle">No</th>
                        <th rowspan="2" class="align-middle">Kode Barang</th>
                        <th rowspan="2" class="align-middle">Nama Barang</th>
                        <th rowspan="2" class="align-middle">NIBR</th>
                        <th rowspan="2" class="align-middle">No. Reg</th>
                        <th rowspan="2" class="align-middle">Lokasi</th>
                        
                        {{-- GROUP: SPESIFIKASI (Sesuai Model) --}}
                        <th colspan="3" class="align-middle">Spesifikasi Barang</th>
                        
                        {{-- GROUP: NOMOR (Sesuai Model: Hanya Rangka, Polisi, BPKB) --}}
                        <th colspan="3" class="align-middle">Nomor Kendaraan</th>
                        
                        <th rowspan="2" class="align-middle">Jml</th>
                        <th rowspan="2" class="align-middle">Satuan</th>
                        <th rowspan="2" class="align-middle">Harga Satuan (Rp)</th>
                        <th rowspan="2" class="align-middle">Nilai Perolehan (Rp)</th>
                        <th rowspan="2" class="align-middle">Cara Perolehan</th>
                        <th rowspan="2" class="align-middle">Tgl Perolehan</th>
                        <th rowspan="2" class="align-middle">Status</th>
                        <th rowspan="2" class="align-middle">Ket</th>
                        <th rowspan="2" class="align-middle" style="min-width: 80px;">Aksi</th>
                    </tr>
                    <tr>
                        {{-- Sub-Header Spesifikasi --}}
                        <th>Merk/Tipe</th>
                        <th>Spesifikasi</th>
                        <th>Lainnya</th>
                        
                        {{-- Sub-Header Nomor --}}
                        <th>Rangka</th>
                        <th>Polisi</th>
                        <th>BPKB</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataPeralatan as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + $dataPeralatan->firstItem() - 1 }}</td>
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->nibr }}</td> {{-- REVISI: nibar -> nibr --}}
                        <td>{{ $item->nomor_register }}</td>
                        <td>{{ $item->Lok }}</td> {{-- REVISI: L Besar --}}

                        {{-- Spesifikasi --}}
                        <td>{{ $item->merk_tipe }}</td> {{-- REVISI: merk_type -> merk_tipe --}}
                        <td>{{ $item->spesifikasi_barang }}</td>
                        <td>{{ $item->spesifikasi_lainnya }}</td>

                        {{-- Nomor (Yang di Model cuma 3 ini) --}}
                        <td>{{ $item->nomor_rangka }}</td>
                        <td>{{ $item->nomor_polisi }}</td>
                        <td>{{ $item->nomor_bpkb }}</td>

                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td class="text-right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($item->nilai_perolehan, 0, ',', '.') }}</td>
                        
                        <td>{{ $item->cara_perolehan }}</td>
                        
                        <td class="text-center">{{ $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d-m-Y') : '-' }}</td>
                        <td>{{ $item->status_penggunaan }}</td>
                        <td>{{ $item->keterangan }}</td>

                        <td class="text-center">
                            <a href="{{ route('lokasi.peralatan.edit', ['lokasi' => $lokasi, 'peralatan' => $item->id]) }}" class="btn btn-sm btn-warning mb-1" title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('lokasi.peralatan.destroy', ['lokasi' => $lokasi, 'peralatan' => $item->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-1" title="Hapus Data">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form> 
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="19" class="text-center py-5">
                            <div class="text-gray-500">
                                <i class="fas fa-folder-open fa-3x mb-3"></i><br>
                                @if (request('search'))
                                    Data tidak ditemukan untuk pencarian '{{ request('search') }}'.
                                @else
                                    Belum ada data peralatan & mesin.
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $dataPeralatan->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</div>
@endsection