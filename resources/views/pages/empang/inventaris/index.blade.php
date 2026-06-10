@extends('layouts.app')

@section('content')

<style>
    /* FIX MODAL TERTIMPA LAYOUT */
    .modal {
        z-index: 99999 !important;
    }

    .modal-backdrop {
        z-index: 99998 !important;
    }

    .modal-dialog {
        position: relative;
        z-index: 100000 !important;
    }

    #wrapper,
    #content-wrapper,
    .content-wrapper,
    .card,
    .card-body {
        overflow: visible !important;
    }
</style>

<div class="card shadow mb-4">

    {{-- Header Modul --}}
    <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>
            KARTU INVENTARIS RUANGAN (KIR) BMD - 
            <span class="text-dark">{{ $room->name }}</span>
        </h6>

        <div class="d-flex">
            {{-- Search Form --}}
            <form action="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}"
                  method="GET"
                  class="form-inline form-search mr-3">

                <div class="input-group">
                    <input type="text"
                           class="form-control bg-light border-0 small"
                           placeholder="Cari data aset..."
                           name="search"
                           value="{{ request('search') }}"
                           autocomplete="off">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>

            {{-- Action Buttons --}}
            <div class="btn-group">
                <a href="{{ route('lokasi.inventaris.create', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}"
                   class="btn btn-primary btn-sm font-weight-bold">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Data Aset
                </a>
                <button type="button"
                        class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split"
                        data-toggle="dropdown">
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item font-weight-bold"
                       href="{{ route('lokasi.inventaris.print', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}"
                       target="_blank">
                        <i class="fas fa-print fa-fw mr-2 text-gray-400"></i> Cetak Dokumen KIR
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- BODY --}}
    <div class="card-body">

        {{-- ALERTS --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-1"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- TABLE FORMAT PERMENDAGRI + KOLOM KONDISI MANDIRI --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-dark mb-0"
                   width="100%"
                   cellspacing="0"
                   style="font-size:11px; vertical-align:middle;">

                <thead class="thead-light text-center align-middle">
                    <tr class="font-weight-bold">
                        <th rowspan="2" style="width: 3%; vertical-align: middle;">No.</th>
                        <th rowspan="2" style="width: 7%; vertical-align: middle;">NIBAR</th>
                        <th rowspan="2" style="width: 7%; vertical-align: middle;">Nomor Register</th>
                        <th rowspan="2" style="width: 10%; vertical-align: middle;">Kode Barang</th>
                        <th rowspan="2" style="vertical-align: middle;">Nama Barang</th>
                        <th rowspan="2" style="vertical-align: middle;">Spesifikasi Nama Barang</th>
                        <th colspan="2" style="width: 16%;">Spesifikasi Barang</th>
                        <th rowspan="2" style="width: 4%; vertical-align: middle;">Jumlah</th>
                        <th rowspan="2" style="width: 5%; vertical-align: middle;">Satuan</th>
                        <th rowspan="2" style="width: 9%; vertical-align: middle;">Kondisi</th> {{-- 🌟 Header Baru --}}
                        <th rowspan="2" style="width: 10%; vertical-align: middle;">Keterangan</th>
                        <th rowspan="2" style="width: 12%; vertical-align: middle;">Aksi</th>
                    </tr>
                    <tr class="font-weight-bold">
                        <th style="font-size: 10px;">Merk/Tipe</th>
                        <th style="font-size: 10px;">Tahun Perolehan</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($dataInventaris as $item)
                    <tr>
                        <td class="text-center align-middle font-weight-bold">
                            {{ $loop->iteration + $dataInventaris->firstItem() - 1 }}
                        </td>

                        <td class="text-center align-middle">
                            {{ $item->nibar ?? '-' }}
                        </td>

                        <td class="text-center align-middle font-weight-bold text-secondary">
                            {{ $item->nomor_register ?? '-' }}
                        </td>

                        <td class="text-center align-middle font-weight-bold text-primary">
                            {{ $item->kode_barang }}
                        </td>

                        <td class="align-middle font-weight-bold">
                            {{ optional($item->peralatan)->nama_barang ?? $item->nama_barang }}
                        </td>

                        <td class="align-middle text-muted">
                            {{ optional($item->peralatan)->spesifikasi_barang ?? ($item->spesifikasi_barang ?? '-') }}
                        </td>

                        <td class="align-middle">
                            {{ optional($item->peralatan)->merk_tipe ?? ($item->merk_tipe ?? '-') }}
                        </td>

                        <td class="text-center align-middle">
                            {{ optional($item->peralatan)->tahun_perolehan ?? $item->tahun_perolehan }}
                        </td>

                        <td class="text-center align-middle font-weight-bold text-info" style="font-size: 11.5px;">
                            {{ $item->jumlah }}
                        </td>

                        <td class="text-center align-middle">
                            <span class="badge badge-light border px-2 py-1">{{ $item->satuan }}</span>
                        </td>

                        <td class="text-center align-middle">
                            @if(($item->kondisi ?? 'Baik') === 'Baik')
                                <span class="badge badge-success px-2 py-1 font-weight-bold text-uppercase" style="font-size: 8.5px; letter-spacing: 0.3px;">
                                    <i class="fas fa-check-circle mr-1"></i> Baik
                                </span>
                            @elseif($item->kondisi === 'Rusak Ringan')
                                <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold text-uppercase" style="font-size: 8.5px; letter-spacing: 0.3px;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Rusak Rgn
                                </span>
                            @elseif($item->kondisi === 'Rusak Berat')
                                <span class="badge badge-danger px-2 py-1 font-weight-bold text-uppercase shadow-sm" style="font-size: 8.5px; letter-spacing: 0.3px;">
                                    <i class="fas fa-times-circle mr-1"></i> Rusak Brt
                                </span>
                            @endif
                        </td>

                        <td class="font-italic text-secondary align-middle">
                            {{ $item->keterangan ?? '-' }}
                        </td>

                        <td class="text-center align-middle">
                            <div class="d-flex align-items-center justify-content-center">
                                {{-- DETAIL --}}
                                <a href="{{ route('lokasi.inventaris.detail', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan, 'kode_barang' => $item->kode_barang]) }}"
                                   class="btn btn-sm btn-primary mx-1 d-inline-flex align-items-center justify-content-center" 
                                   style="width: 28px; height: 28px; border-radius: 4px;" title="Lihat Detail">
                                    <i class="fas fa-eye" style="font-size: 11px;"></i>
                                </a>

                                {{-- EDIT --}}
                                <a href="{{ route('lokasi.inventaris.edit', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan, 'inventari' => $item->kode_barang]) }}"
                                   class="btn btn-sm btn-warning mx-1 d-inline-flex align-items-center justify-content-center text-white" 
                                   style="width: 28px; height: 28px; border-radius: 4px;" title="Edit Item">
                                    <i class="fas fa-edit" style="font-size: 11px;"></i>
                                </a>

                                {{-- MUTASI --}}
                                <button type="button"
                                        class="btn btn-sm btn-info mx-1 d-inline-flex align-items-center justify-content-center"
                                        data-toggle="modal"
                                        data-target="#moveModal{{ md5($item->kode_barang) }}"
                                        style="width: 28px; height: 28px; border-radius: 4px;" title="Mutasi Barang">
                                    <i class="fas fa-exchange-alt" style="font-size: 11px;"></i>
                                </button>

                                {{-- DELETE --}}
                                <form action="{{ route('lokasi.inventaris.destroy', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan, 'inventari' => $item->kode_barang]) }}"
                                      method="POST"
                                      class="d-inline m-0 p-0"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data aset ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-danger mx-1 d-inline-flex align-items-center justify-content-center"
                                            style="width: 28px; height: 28px; border-radius: 4px;" title="Hapus Permanen">
                                        <i class="fas fa-trash" style="font-size: 11px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center py-5 text-gray-500 font-weight-bold">
                            <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i><br>
                            Belum ada data Kartu Inventaris Ruangan (KIR).
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{ $dataInventaris->appends(['search' => request('search')])->links() }}
        </div>

    </div>
</div>

@endsection

{{-- MODAL MUTASI --}}
@foreach ($dataInventaris as $item)
<div class="modal fade"
     id="moveModal{{ md5($item->kode_barang) }}"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg text-left">
            <form action="{{ route('lokasi.inventaris.move', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan, 'inventari' => $item->kode_barang]) }}"
                  method="POST">
                @csrf

                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold" style="font-size: 15px;">
                        <i class="fas fa-truck-moving mr-2"></i> Form Mutasi Perpindahan Aset
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body text-dark" style="font-size: 13px;">
                    <p class="mb-3">Aset yang akan dipindahkan: <b class="text-primary">{{ optional($item->peralatan)->nama_barang ?? $item->nama_barang }} ({{ $item->kode_barang }})</b></p>

                    <div class="form-group">
                        <label class="font-weight-bold">Pilih Ruangan Tujuan:</label>
                        <select name="new_room_id" class="form-control font-weight-bold text-dark" style="font-size: 13px;" required>
                            <option value="" disabled selected>-- Pilih Ruangan Target --</option>
                            @foreach($allRooms as $r)
                                @if($r->kode_ruangan !== $room->kode_ruangan)
                                    <option value="{{ $r->kode_ruangan }}">{{ $r->name }} ({{ $r->kode_ruangan }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Jumlah Dipindah (Stok: {{ $item->jumlah }}):</label>
                        <input type="number" name="qty_to_move" class="form-control font-weight-bold text-dark" min="1" max="{{ $item->jumlah }}" value="1" style="font-size: 13px;" required>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold"><i class="fas fa-paper-plane mr-1"></i> Eksekusi Mutasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach