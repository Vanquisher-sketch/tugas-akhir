@extends('layouts.app')

@section('content')

<style>
    /* STACKING CONTEXT MODAL */
    .modal-backdrop { z-index: 1040 !important; }
    .modal { z-index: 1050 !important; }

    /* --- UI TABEL MENYESUAIKAN GAYA BMD --- */
    .card { border-radius: 0.5rem; }
    
    .table-bmd th {
        background-color: #f8f9fc !important;
        color: #858796;
        font-weight: bold;
        font-size: 13px;
        padding: 15px 10px !important;
        border: 1px solid #e3e6f0 !important;
    }

    .table-bmd td {
        font-size: 13px;
        color: #3a3b45;
        padding: 12px 10px;
        border: 1px solid #e3e6f0 !important;
    }

    /* Kustomisasi Pemecahan Baris (Sub-Row) */
    .sub-row-container {
        display: flex;
        flex-direction: column;
        height: 100%;
        margin: -12px -10px;
    }
    .item-subrow {
        min-height: 48px;
        display: flex;
        align-items: center;
        padding: 8px 12px;
    }
    .item-subrow:not(:last-child) {
        border-bottom: 1px solid #e3e6f0;
    }

    /* Badge & Tombol Aksi */
    .badge-cond {
        font-size: 11px !important;
        font-weight: 700;
        padding: 6px 10px !important;
        border-radius: 4px !important;
    }
    
    /* --- PERBAIKAN SPASI TOMBOL AKSI --- */
    .action-group-container {
        display: flex;
        flex-direction: column;
        gap: 5px;
        width: 100%;
        padding: 2px 0;
    }
    
    .action-row-flex {
        display: flex;
        gap: 5px;
        width: 100%;
    }
    
    .action-group-container .btn {
        border-radius: 4px !important;
        padding: 6px 0 !important;
        font-size: 12px !important;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0; 
    }
    
    .action-row-flex .btn {
        flex: 1; 
    }
</style>

<div class="card shadow mb-4 border-0">

    {{-- Bagian Header Modul --}}
    <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary" style="font-size: 16px;">
            KARTU INVENTARIS RUANGAN (KIR) - 
            <span class="text-dark">{{ $room->ruangan_nama }}</span>
        </h6>

        <div class="d-flex align-items-center">
            {{-- Form Pencarian --}}
            <form action="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan]) }}"
                  method="GET" class="form-inline mr-3">
                <div class="input-group">
                    <input type="text"
                           class="form-control bg-light border-0 small"
                           placeholder="Cari data aset..."
                           name="search"
                           value="{{ request('search') }}"
                           autocomplete="off"
                           style="border-radius: 4px 0 0 4px;">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit" style="border-radius: 0 4px 4px 0;">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>

            {{-- Tombol Aksi Tambah --}}
            <a href="{{ route('lokasi.inventaris.create', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan]) }}"
               class="btn btn-primary font-weight-bold" style="border-radius: 4px; padding: 6px 12px; font-size: 14px;">
                <i class="fas fa-plus mr-1"></i> Tambah Data
            </a>
            
            {{-- Tombol Cetak PDF --}}
            <a href="{{ route('lokasi.inventaris.print', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan]) }}"
               target="_blank"
               class="btn btn-info font-weight-bold ml-2" style="border-radius: 4px; padding: 6px 12px; font-size: 14px;"
               title="Cetak Dokumen KIR">
                <i class="fas fa-print"></i>
            </a>
        </div>
    </div>

    {{-- Bagian Konten Tabel --}}
    <div class="card-body p-0">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        <div class="table-responsive p-3">
            <table class="table table-bordered table-bmd mb-0 text-center" width="100%" cellspacing="0">
                <thead class="align-middle">
                    <tr>
                        <th rowspan="2" style="width: 4%; vertical-align: middle;">No</th>
                        <th rowspan="2" style="width: 8%; vertical-align: middle;">No. Reg</th>
                        <th rowspan="2" style="width: 10%; vertical-align: middle;">Kode Barang</th>
                        <th rowspan="2" style="vertical-align: middle;">Nama Barang</th>
                        <th rowspan="2" style="width: 15%; vertical-align: middle;">Spesifikasi</th>
                        <th colspan="2" style="width: 15%;">Detail Barang</th>
                        <th rowspan="2" style="width: 5%; vertical-align: middle;">Total</th>
                        <th rowspan="2" style="width: 5%; vertical-align: middle;">Satuan</th>
                        <th rowspan="2" style="width: 12%; vertical-align: middle;">Kondisi & Unit</th>
                        <th rowspan="2" style="width: 10%; vertical-align: middle;">Keterangan</th>
                        <th rowspan="2" style="width: 10%; vertical-align: middle;">Aksi</th>
                    </tr>
                    <tr>
                        <th style="font-size: 12px;">Merk/Tipe</th>
                        <th style="font-size: 12px;">Tahun Perolehan</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($dataInventaris->groupBy('inv_kode_barang') as $kodeBarang => $items)
                    @php
                        $firstItem = $items->first();
                        $totalJumlah = $items->sum('inv_jumlah');
                    @endphp
                    <tr>
                        <td class="align-middle text-dark">{{ $loop->iteration }}</td>
                        <td class="align-middle text-dark">{{ $firstItem->inv_nomor_register ?? '-' }}</td>
                        <td class="align-middle font-weight-bold text-dark">{{ $firstItem->inv_kode_barang }}</td>
                        
                        <td class="align-middle font-weight-bold text-dark">
                            {{ optional($firstItem->peralatan)->alat_nama_barang ?? $firstItem->inv_nama_barang }}
                        </td>

                        <td class="align-middle text-dark">
                            {{ optional($firstItem->peralatan)->alat_spesifikasi_barang ?? ($firstItem->inv_spesifikasi_barang ?? '-') }}
                        </td>

                        <td class="align-middle text-dark">{{ optional($firstItem->peralatan)->alat_merk_tipe ?? ($firstItem->inv_merk_tipe ?? '-') }}</td>
                        
                        {{-- 🌟 ISI DATA KOLOM TAHUN (MENGGUNAKAN CARBON) 🌟 --}}
                        <td class="align-middle text-dark text-center">
                            @php
                                $tglPenuh = optional($firstItem->peralatan)->alat_tanggal_perolehan;
                                $tahun = $tglPenuh ? \Carbon\Carbon::parse($tglPenuh)->format('Y') : ($firstItem->inv_tahun_perolehan ?? '-');
                            @endphp
                            {{ $tahun }}
                        </td>
                        
                        <td class="align-middle font-weight-bold text-primary" style="font-size: 15px;">{{ $totalJumlah }}</td>
                        <td class="align-middle"><span class="badge badge-light border text-dark px-2 py-1">{{ $firstItem->inv_satuan }}</span></td>

                        {{-- Kolom Kondisi & Unit (Sub-Row) --}}
                        <td class="p-0 align-middle">
                            <div class="sub-row-container">
                                @foreach ($items as $subItem)
                                    <div class="item-subrow justify-content-center">
                                        @if(($subItem->inv_kondisi ?? 'Baik') === 'Baik')
                                            <span class="badge badge-success badge-cond text-white w-100">
                                                {{ $subItem->inv_jumlah }} Baik
                                            </span>
                                        @elseif($subItem->inv_kondisi === 'Rusak Ringan')
                                            <span class="badge badge-warning badge-cond text-dark w-100">
                                                {{ $subItem->inv_jumlah }} Rsk Ringan
                                            </span>
                                        @elseif($subItem->inv_kondisi === 'Rusak Berat')
                                            <span class="badge badge-danger badge-cond text-white w-100">
                                                {{ $subItem->inv_jumlah }} Rsk Berat
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </td>

                        {{-- Kolom Keterangan (Sub-Row) --}}
                        <td class="p-0 align-middle">
                            <div class="sub-row-container">
                                @foreach ($items as $subItem)
                                    <div class="item-subrow justify-content-center text-center">
                                        <span class="text-muted small text-truncate" style="max-width: 100px;" title="{{ $subItem->inv_keterangan }}">
                                            {{ $subItem->inv_keterangan ?? '-' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </td>

                        {{-- Kolom Aksi (Sub-Row) --}}
                        <td class="p-0 align-middle">
                            <div class="sub-row-container">
                                @foreach ($items as $subItem)
                                    <div class="item-subrow justify-content-center">
                                        <div class="action-group-container">
                                            
                                            <div class="action-row-flex">
                                                <a href="{{ route('lokasi.inventaris.edit', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan, 'inv_kode_barang' => $subItem->inv_kode_barang, 'kond' => $subItem->inv_kondisi]) }}"
                                                   class="btn btn-warning text-white shadow-sm" title="Ubah Kondisi">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @if($subItem->inv_kondisi === 'Baik')
                                                    <button type="button" class="btn btn-info shadow-sm" data-toggle="modal" data-target="#moveModal{{ md5($subItem->inv_kode_barang . $subItem->inv_kondisi) }}" title="Mutasi Ruangan">
                                                        <i class="fas fa-exchange-alt"></i>
                                                    </button>
                                                @endif
                                            </div>
                                            
                                            <form action="{{ route('lokasi.inventaris.destroy', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan, 'inv_kode_barang' => $subItem->inv_kode_barang, 'kond' => $subItem->inv_kondisi]) }}"
                                                  method="POST" class="m-0"
                                                  onsubmit="return confirm('Yakin ingin menghapus data kondisi {{ $subItem->inv_kondisi }} secara permanen?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger w-100 shadow-sm" title="Hapus Data">
                                                    <i class="fas fa-trash-alt"></i>
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
                        <td colspan="12" class="text-center py-5">
                            <span class="text-muted font-weight-bold">Belum ada data inventaris di ruangan ini.</span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="d-flex justify-content-end px-4 pb-3">
            {{ $dataInventaris->appends(['search' => request('search')])->links() }}
        </div>

    </div>
</div>

{{-- Modal Mutasi --}}
@foreach ($dataInventaris as $item)
    @if($item->inv_kondisi === 'Baik')
        <div class="modal fade" id="moveModal{{ md5($item->inv_kode_barang . $item->inv_kondisi) }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('lokasi.inventaris.move', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan, 'inv_kode_barang' => $item->inv_kode_barang]) }}" method="POST">
                        @csrf
                        <div class="modal-header bg-primary text-white py-3">
                            <h6 class="modal-title font-weight-bold"><i class="fas fa-truck-moving mr-2"></i> Mutasi Pemindahan Aset</h6>
                            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body p-4 text-dark">
                            <div class="alert alert-info py-2 px-3 mb-4 shadow-sm" style="font-size: 13px;">
                                Memindahkan: <strong>{{ optional($item->peralatan)->alat_nama_barang ?? $item->inv_nama_barang }} ({{ $item->inv_kode_barang }})</strong>
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark">Ruangan Tujuan <span class="text-danger">*</span></label>
                                <select name="new_room_id" class="form-control" required>
                                    <option value="" disabled selected>-- Pilih Ruangan --</option>
                                    @foreach($allRooms as $r)
                                        @if($r->kode_ruangan !== $room->kode_ruangan)
                                            <option value="{{ $r->kode_ruangan }}">{{ $r->ruangan_nama }} ({{ $r->kode_ruangan }})</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <label class="font-weight-bold text-dark">Jumlah Mutasi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="qty_to_move" class="form-control" min="1" max="{{ $item->inv_jumlah }}" value="1" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-light">Maksimal: {{ $item->inv_jumlah }} Unit</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2">
                            <button type="button" class="btn btn-secondary font-weight-bold px-4" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary font-weight-bold px-4"><i class="fas fa-check mr-1"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.modal').appendTo("body");
    });
</script>

@endsection