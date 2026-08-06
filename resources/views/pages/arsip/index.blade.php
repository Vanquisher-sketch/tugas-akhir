@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Pusat Arsip Data - {{ ucfirst($lokasi) }}</h1>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary mb-3">
                <i class="fas fa-trash-alt mr-2"></i>Daftar Data Terhapus Sementara
            </h6>
            <ul class="nav nav-tabs card-header-tabs" id="arsipTab" role="tablist">
                <li class="nav-item"><a class="nav-link active font-weight-bold" data-toggle="tab" href="#tab-tanah">KIB A</a></li>
                <li class="nav-item"><a class="nav-link font-weight-bold" data-toggle="tab" href="#tab-peralatan">KIB B</a></li>
                <li class="nav-item"><a class="nav-link font-weight-bold" data-toggle="tab" href="#tab-gedung">KIB C</a></li>
                <li class="nav-item"><a class="nav-link font-weight-bold" data-toggle="tab" href="#tab-jalan">KIB D</a></li>
                <li class="nav-item"><a class="nav-link font-weight-bold" data-toggle="tab" href="#tab-rusak">Rusak</a></li>
                <li class="nav-item"><a class="nav-link font-weight-bold" data-toggle="tab" href="#tab-bmd">BMD</a></li>
                <li class="nav-item"><a class="nav-link font-weight-bold" data-toggle="tab" href="#tab-inventaris">Inventaris</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                
                @php
                    $kategoriList = [
                        ['id' => 'tab-tanah', 'data' => $arsipTanah, 'key' => 'tanah', 'active' => true],
                        ['id' => 'tab-peralatan', 'data' => $arsipPeralatan, 'key' => 'peralatan', 'active' => false],
                        ['id' => 'tab-gedung', 'data' => $arsipGedung, 'key' => 'gedung', 'active' => false],
                        ['id' => 'tab-jalan', 'data' => $arsipJalan, 'key' => 'jalan', 'active' => false],
                        ['id' => 'tab-rusak', 'data' => $arsipRusak, 'key' => 'rusak', 'active' => false],
                        ['id' => 'tab-bmd', 'data' => $arsipBmd, 'key' => 'bmd', 'active' => false],
                        ['id' => 'tab-inventaris', 'data' => $arsipInventaris, 'key' => 'inventaris', 'active' => false],
                    ];
                @endphp

                @foreach($kategoriList as $kat)
                <div class="tab-pane fade {{ $kat['active'] ? 'show active' : '' }}" id="{{ $kat['id'] }}">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-hover table-sm text-dark" width="100%" cellspacing="0" style="font-size: 13px;">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="18%">Kode / ID</th>
                                    <th>Nama Barang / Deskripsi</th>
                                    <th width="20%">Tanggal Dihapus</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kat['data'] as $item)
                                    @php 
                                        if($kat['key'] == 'tanah') {
                                            $pk = $item->tanah_kode_barang;
                                            $namaBarang = $item->tanah_nama_barang;
                                        } elseif($kat['key'] == 'peralatan') {
                                            $pk = $item->alat_kode_barang;
                                            $namaBarang = $item->alat_nama_barang;
                                        } elseif($kat['key'] == 'gedung') {
                                            $pk = $item->gedung_kode_barang;
                                            $namaBarang = $item->gedung_nama_barang;
                                        } elseif($kat['key'] == 'jalan') {
                                            $pk = $item->jalan_kode_barang;
                                            $namaBarang = $item->jalan_nama_barang;
                                        } elseif($kat['key'] == 'inventaris') {
                                            // PERBAIKAN: Menggunakan getKey() agar membaca identifier unik dari Model Inventaris
                                            $pk = $item->getKey(); 
                                            $namaBarang = ($item->inv_nama_barang ?? 'Inventaris') . ' - [' . $item->inv_jumlah . ' ' . ($item->inv_satuan ?? 'Unit') . ' ' . ($item->inv_kondisi ?? 'Baik') . ']';
                                        } elseif($kat['key'] == 'rusak') {
                                            $pk = $item->rusak_kode_barang ?? $item->no_id_pemda;
                                            $namaBarang = $item->rusak_nama_barang ?? $item->nama_barang;
                                        } elseif($kat['key'] == 'bmd') {
                                            $pk = $item->id;
                                            $namaBarang = 'Pemakaian: ' . $item->pemakai_nama;
                                        } else {
                                            $pk = '-';
                                            $namaBarang = '-';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                        <td class="font-weight-bold text-primary text-center align-middle">
                                            {{ $kat['key'] == 'inventaris' ? ($item->inv_kode_barang ?? $pk) : $pk }}
                                        </td>
                                        <td class="align-middle font-weight-bold">{{ $namaBarang }}</td>
                                        <td class="text-center text-muted align-middle">{{ $item->deleted_at ? $item->deleted_at->format('d/m/Y H:i:s') : '-' }}</td>
                                        <td class="text-center align-middle">
                                            @if($pk && $pk !== '-')
                                                {{-- Tombol Pulihkan --}}
                                                <form action="{{ route('lokasi.arsip.restore', [$lokasi, $kat['key'], $pk]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success py-1 px-2 shadow-sm" title="Pulihkan Data Ini">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                                
                                                {{-- Tombol Hapus Permanen --}}
                                                <form action="{{ route('lokasi.arsip.permanen', [$lokasi, $kat['key'], $pk]) }}" method="POST" class="d-inline delete-form">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger py-1 px-2 btn-delete shadow-sm" title="Hapus Selamanya">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge badge-danger">ID Missing</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data arsip di kategori ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // SweetAlert2 Hapus Permanen
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('.delete-form');

            Swal.fire({
                title: 'Hapus Permanen?',
                text: "Data ini akan hilang selamanya dari database!",
                icon: 'error', 
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Hapus Selamanya!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush