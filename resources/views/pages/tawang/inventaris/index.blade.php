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
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-door-open mr-2"></i>
            Inventaris Ruangan:
            <span class="text-dark">{{ $room->name }}</span>
            ({{ ucfirst($lokasi) }})
        </h6>

        <div class="d-flex">

            {{-- Search --}}
            <form action="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}"
                  method="GET"
                  class="form-inline form-search mr-3">

                <div class="input-group">
                    <input type="text"
                           class="form-control bg-light border-0 small"
                           placeholder="Cari nama / kode / merk..."
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

            {{-- Button --}}
            <div class="btn-group">

                <a href="{{ route('lokasi.inventaris.create', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}"
                   class="btn btn-primary btn-sm font-weight-bold">
                    <i class="fas fa-plus-circle mr-1"></i>
                    Tambah Data Aset
                </a>

                <button type="button"
                        class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split"
                        data-toggle="dropdown">
                </button>

                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item font-weight-bold"
                       href="{{ route('lokasi.inventaris.print', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}"
                       target="_blank">
                        <i class="fas fa-print fa-fw mr-2 text-gray-400"></i>
                        Cetak Daftar Ruangan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- BODY --}}
    <div class="card-body">

        {{-- ALERT SUCCESS --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-1"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- ALERT ERROR --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- TABLE --}}
        <div class="table-responsive">

            <table class="table table-bordered table-hover text-dark mb-0"
                   width="100%"
                   cellspacing="0"
                   style="font-size:11px; vertical-align:middle;">

                <thead class="thead-light text-center">
                    <tr class="font-weight-bold">
                        <th style="width: 3%;">No</th>
                        <th style="width: 12%;">Kode Barang</th>
                        <th style="width: 8%;">NIBAR</th>
                        <th style="width: 8%;">No Reg</th>
                        <th>Nama Barang</th>
                        <th style="width: 12%;">Merk</th>
                        <th style="width: 5%;">Tahun</th>
                        <th style="width: 5%;">Jml</th>
                        <th style="width: 6%;">Satuan</th>
                        <th style="width: 10%;">Kondisi</th>
                        <th>Keterangan</th>
                        <th style="width: 14%;">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                @forelse ($dataInventaris as $item)

                    <tr>
                        <td class="text-center align-middle font-weight-bold">
                            {{ $loop->iteration + $dataInventaris->firstItem() - 1 }}
                        </td>

                        <td class="text-center align-middle font-weight-bold text-primary">
                            {{ $item->kode_barang }}
                        </td>

                        <td class="text-center align-middle">
                            {{ $item->nibar ?? '-' }}
                        </td>

                        <td class="text-center align-middle font-weight-bold text-secondary">
                            {{ $item->nomor_register ?? '-' }}
                        </td>

                        <td class="align-middle">
                            <b class="text-gray-900">{{ $item->nama_barang }}</b>
                            @if($item->spesifikasi_barang)
                                <br>
                                <small class="text-muted">
                                    {{ $item->spesifikasi_barang }}
                                </small>
                            @endif
                        </td>

                        <td class="align-middle">{{ $item->merk_tipe ?? '-' }}</td>

                        <td class="text-center align-middle">
                            {{ $item->tahun_perolehan }}
                        </td>

                        <td class="text-center align-middle font-weight-bold text-info" style="font-size: 12px;">
                            {{ $item->jumlah }}
                        </td>

                        <td class="text-center align-middle text-uppercase">
                            <span class="badge badge-light border px-2 py-1 font-weight-bold">{{ $item->satuan }}</span>
                        </td>

                        <td class="text-center align-middle">
                            @if(($item->kondisi ?? 'Baik') === 'Baik')
                                <span class="badge badge-success px-2 py-1 font-weight-bold text-uppercase" style="font-size: 9px;">
                                    <i class="fas fa-check-circle mr-1"></i> Baik
                                </span>
                            @elseif($item->kondisi === 'Rusak Ringan')
                                <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold text-uppercase" style="font-size: 9px;">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Rusak Rgn
                                </span>
                            @elseif($item->kondisi === 'Rusak Berat')
                                <span class="badge badge-danger px-2 py-1 font-weight-bold text-uppercase shadow-sm" style="font-size: 9px;">
                                    <i class="fas fa-times-circle mr-1"></i> Rusak Brt
                                </span>
                            @endif
                        </td>

                        <td class="font-italic text-secondary align-middle bg-light">
                            {{ $item->keterangan ?? '-' }}
                        </td>

                        <td class="text-center align-middle">
                            {{-- 🌟 GRID BUTTON REVISI: Sejajar, Presisi Kotak, Rapi Terjaga --}}
                            <div class="d-flex align-items-center justify-content-center">

                                {{-- DETAIL --}}
                                <a href="{{ route('lokasi.inventaris.detail', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan, 'kode_barang' => $item->kode_barang]) }}"
                                   class="btn btn-sm btn-primary mx-1 d-inline-flex align-items-center justify-content-center" 
                                   style="width: 32px; height: 32px; border-radius: 4px;" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- EDIT --}}
                                <a href="{{ route('lokasi.inventaris.edit', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan, 'inventari' => $item->kode_barang]) }}"
                                   class="btn btn-sm btn-warning mx-1 d-inline-flex align-items-center justify-content-center text-white" 
                                   style="width: 32px; height: 32px; border-radius: 4px;" title="Edit Item">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- MUTASI --}}
                                <button type="button"
                                        class="btn btn-sm btn-info mx-1 d-inline-flex align-items-center justify-content-center"
                                        data-toggle="modal"
                                        data-target="#moveModal{{ md5($item->kode_barang) }}"
                                        style="width: 32px; height: 32px; border-radius: 4px;" title="Mutasi Barang">
                                    <i class="fas fa-exchange-alt"></i>
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
                                            style="width: 32px; height: 32px; border-radius: 4px;" title="Hapus Permanen">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="12" class="text-center py-5 text-gray-500 font-weight-bold">
                            <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i><br>
                            Belum ada data inventaris di ruangan ini.
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

{{-- ========================================= --}}
{{-- MODAL DI LUAR CARD / CONTENT --}}
{{-- ========================================= --}}

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
                        <i class="fas fa-truck-moving mr-2"></i>
                        Form Mutasi Perpindahan Aset
                    </h5>
                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body text-dark" style="font-size: 13px;">
                    <p class="mb-3">Aset yang akan dipindahkan: <b class="text-primary">{{ $item->nama_barang }} ({{ $item->kode_barang }})</b></p>

                    {{-- ROOM --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Pilih Ruangan Tujuan:</label>
                        <select name="new_room_id" class="form-control font-weight-bold text-dark" style="font-size: 13px;" required>
                            <option value="" disabled selected>-- Pilih Ruangan Target --</option>
                            @foreach($allRooms as $r)
                                @if($r->kode_ruangan !== $room->kode_ruangan)
                                    <option value="{{ $r->kode_ruangan }}">
                                        {{ $r->name }} ({{ $r->kode_ruangan }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- JUMLAH --}}
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Jumlah Dipindah (Stok Tersedia: {{ $item->jumlah }}):</label>
                        <input type="number"
                               name="qty_to_move"
                               class="form-control font-weight-bold text-dark"
                               min="1"
                               max="{{ $item->jumlah }}"
                               value="1"
                               style="font-size: 13px;"
                               required>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold">
                        <i class="fas fa-paper-plane mr-1"></i>
                        Eksekusi Mutasi
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@endforeach