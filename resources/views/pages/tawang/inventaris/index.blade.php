@extends('layouts.app')

@section('content')

<style>
    /* PERBAIKAN STACKING CONTEXT & Z-INDEX MODAL */
    .modal-backdrop {
        z-index: 1040 !important;
    }

    .modal {
        z-index: 1050 !important;
    }

    /* KUSTOMISASI DESAIN SUB-ROW TABEL KIR */
    .sub-row-container {
        display: flex;
        flex-direction: column;
        height: 100%;
        justify-content: center;
    }

    .item-subrow {
        min-height: 42px;
        display: flex;
        align-items: center;
        padding: 4px 8px;
    }

    .item-subrow:not(:last-child) {
        border-bottom: 1px dashed #e3e6f0;
    }

    .badge-cond {
        font-size: 10px !important;
        font-weight: 700;
        letter-spacing: 0.3px;
        border-radius: 50rem !important;
        padding: 5px 10px !important;
    }

    .btn-action-group .btn {
        padding: 3px 7px !important;
        font-size: 11px !important;
        line-height: 1.2;
    }
</style>

<div class="card shadow mb-4 border-0">

    {{-- Bagian Header Modul --}}
    <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between border-bottom">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>
            KARTU INVENTARIS RUANGAN (KIR) - 
            <span class="text-dark">{{ $room->ruangan_nama }}</span>
        </h6>

        <div class="d-flex">
            {{-- Form Pencarian --}}
            <form action="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan]) }}"
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

            {{-- Tombol Aksi --}}
            <div class="btn-group">
                <a href="{{ route('lokasi.inventaris.create', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan]) }}"
                   class="btn btn-primary btn-sm font-weight-bold">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Data Aset
                </a>
                <button type="button"
                        class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split"
                        data-toggle="dropdown">
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item font-weight-bold"
                       href="{{ route('lokasi.inventaris.print', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan]) }}"
                       target="_blank">
                        <i class="fas fa-print fa-fw mr-2 text-gray-400"></i> Cetak Dokumen KIR
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Bagian Konten --}}
    <div class="card-body p-0">

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3">
                <i class="fas fa-check-circle mr-1"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- Format Tabel Permendagri --}}
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
                        <th rowspan="2" style="width: 9%; vertical-align: middle;">Kode Barang</th>
                        <th rowspan="2" style="vertical-align: middle;">Nama Barang</th>
                        <th rowspan="2" style="vertical-align: middle;">Spesifikasi Nama Barang</th>
                        <th colspan="2" style="width: 15%;">Spesifikasi Barang</th>
                        <th rowspan="2" style="width: 5%; vertical-align: middle;">Total</th>
                        <th rowspan="2" style="width: 5%; vertical-align: middle;">Satuan</th>
                        <th rowspan="2" style="width: 12%; vertical-align: middle;">Kondisi & Unit</th>
                        <th rowspan="2" style="width: 12%; vertical-align: middle;">Keterangan</th>
                        <th rowspan="2" style="width: 10%; vertical-align: middle;">Aksi</th>
                    </tr>
                    <tr class="font-weight-bold">
                        <th style="font-size: 10px;">Merk/Tipe</th>
                        <th style="font-size: 10px;">Tahun Perolehan</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($dataInventaris->groupBy('inv_kode_barang') as $kodeBarang => $items)
                    @php
                        $firstItem = $items->first();
                        $totalJumlah = $items->sum('inv_jumlah');
                    @endphp
                    <tr>
                        <td class="text-center align-middle font-weight-bold">
                            {{ $loop->iteration }}
                        </td>

                        <td class="text-center align-middle">
                            {{ $firstItem->inv_nibar ?? '-' }}
                        </td>

                        <td class="text-center align-middle font-weight-bold text-secondary">
                            {{ $firstItem->inv_nomor_register ?? '-' }}
                        </td>

                        <td class="text-center align-middle font-weight-bold text-primary">
                            {{ $firstItem->inv_kode_barang }}
                        </td>

                        <td class="align-middle font-weight-bold">
                            {{ optional($firstItem->peralatan)->alat_nama_barang ?? $firstItem->inv_nama_barang }}
                        </td>

                        <td class="align-middle text-muted">
                            {{ optional($firstItem->peralatan)->alat_spesifikasi_barang ?? ($firstItem->inv_spesifikasi_barang ?? '-') }}
                        </td>

                        <td class="align-middle">
                            {{ optional($firstItem->peralatan)->alat_merk_tipe ?? ($firstItem->inv_merk_tipe ?? '-') }}
                        </td>

                        <td class="text-center align-middle">
                            {{ optional($firstItem->peralatan)->alat_tahun_perolehan ?? ($firstItem->inv_tahun_perolehan ?? '-') }}
                        </td>

                        <td class="text-center align-middle font-weight-bold text-primary" style="font-size: 12px;">
                            {{ $totalJumlah }}
                        </td>

                        <td class="text-center align-middle">
                            <span class="badge badge-light border px-2 py-1">{{ $firstItem->inv_satuan }}</span>
                        </td>

                        {{-- Kolom Kondisi & Unit --}}
                        <td class="p-0 align-middle">
                            <div class="sub-row-container">
                                @foreach ($items as $subItem)
                                    <div class="item-subrow justify-content-center">
                                        @if(($subItem->inv_kondisi ?? 'Baik') === 'Baik')
                                            <span class="badge badge-success badge-cond shadow-sm">
                                                <i class="fas fa-check-circle mr-1"></i> {{ $subItem->inv_jumlah }} Baik
                                            </span>
                                        @elseif($subItem->inv_kondisi === 'Rusak Ringan')
                                            <span class="badge badge-warning text-dark badge-cond shadow-sm">
                                                <i class="fas fa-exclamation-triangle mr-1"></i> {{ $subItem->inv_jumlah }} Rusak Ringan
                                            </span>
                                        @elseif($subItem->inv_kondisi === 'Rusak Berat')
                                            <span class="badge badge-danger badge-cond shadow-sm">
                                                <i class="fas fa-times-circle mr-1"></i> {{ $subItem->inv_jumlah }} Rusak Berat
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </td>

                        {{-- Kolom Keterangan --}}
                        <td class="p-0 align-middle">
                            <div class="sub-row-container">
                                @foreach ($items as $subItem)
                                    <div class="item-subrow justify-content-start text-left px-2">
                                        <span class="font-italic text-secondary small text-truncate" title="{{ $subItem->inv_keterangan }}">
                                            {{ $subItem->inv_keterangan ?? '-' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </td>

                        {{-- Kolom Aksi --}}
                        <td class="p-0 align-middle">
                            <div class="sub-row-container">
                                @foreach ($items as $subItem)
                                    <div class="item-subrow justify-content-center">
                                        <div class="btn-group btn-group-sm btn-action-group shadow-sm" role="group">
                                            
                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('lokasi.inventaris.edit', [
                                                    'lokasi' => $lokasi, 
                                                    'kode_ruangan' => $room->kode_ruangan, 
                                                    'inv_kode_barang' => $subItem->inv_kode_barang,
                                                    'kond' => $subItem->inv_kondisi
                                                ]) }}"
                                               class="btn btn-warning text-white" 
                                               title="Ubah Data Kondisi {{ $subItem->inv_kondisi }}">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- Tombol Mutasi --}}
                                            @if($subItem->inv_kondisi === 'Baik')
                                                <button type="button"
                                                        class="btn btn-info"
                                                        data-toggle="modal"
                                                        data-target="#moveModal{{ md5($subItem->inv_kode_barang . $subItem->inv_kondisi) }}"
                                                        title="Mutasi Aset">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                            @endif

                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route('lokasi.inventaris.destroy', [
                                                    'lokasi' => $lokasi, 
                                                    'kode_ruangan' => $room->kode_ruangan, 
                                                    'inv_kode_barang' => $subItem->inv_kode_barang,
                                                    'kond' => $subItem->inv_kondisi
                                                ]) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus data kondisi {{ $subItem->inv_kondisi }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Hapus Permanen">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                @endforeach
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

        <div class="d-flex justify-content-end p-3">
            {{ $dataInventaris->appends(['search' => request('search')])->links() }}
        </div>

    </div>
</div>

{{-- Modal Mutasi --}}
@foreach ($dataInventaris as $item)
    @if($item->inv_kondisi === 'Baik')
        <div class="modal fade"
             id="moveModal{{ md5($item->inv_kode_barang . $item->inv_kondisi) }}" 
             tabindex="-1"
             role="dialog"
             aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg text-left">
                    
                    <form action="{{ route('lokasi.inventaris.move', [
                            'lokasi' => $lokasi, 
                            'kode_ruangan' => $room->kode_ruangan, 
                            'inv_kode_barang' => $item->inv_kode_barang
                        ]) }}" method="POST">
                        @csrf

                        <div class="modal-header bg-primary text-white py-3">
                            <h5 class="modal-title font-weight-bold" style="font-size: 15px;">
                                <i class="fas fa-truck-moving mr-2"></i> Form Mutasi Perpindahan Aset
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                        </div>

                        <div class="modal-body text-dark" style="font-size: 13px;">
                            <p class="mb-3">Aset yang akan dipindahkan: <b class="text-primary">{{ optional($item->peralatan)->alat_nama_barang ?? $item->inv_nama_barang }} ({{ $item->inv_kode_barang }})</b></p>

                            <div class="form-group">
                                <label class="font-weight-bold">Pilih Ruangan Tujuan:</label>
                                <select name="new_room_id" class="form-control font-weight-bold text-dark" style="font-size: 13px;" required>
                                    <option value="" disabled selected>-- Pilih Ruangan Target --</option>
                                    @foreach($allRooms as $r)
                                        @if($r->kode_ruangan !== $room->kode_ruangan)
                                            <option value="{{ $r->kode_ruangan }}">{{ $r->ruangan_nama }} ({{ $r->kode_ruangan }})</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Jumlah Dipindah (Stok Kondisi Baik: {{ $item->inv_jumlah }}):</label>
                                <input type="number" name="qty_to_move" class="form-control font-weight-bold text-dark" min="1" max="{{ $item->inv_jumlah }}" value="1" style="font-size: 13px;" required>
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
    @endif
@endforeach

{{-- SCRIPT PERBAIKAN BACKDROP MODAL --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Pindahkan elemen modal langsung ke tag <body> agar tidak terperangkap z-index layout
        $('.modal').appendTo("body");
    });
</script>

@endsection