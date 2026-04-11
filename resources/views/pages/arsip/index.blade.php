@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pusat Arsip Data - {{ ucfirst($lokasi) }}</h1>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary mb-3"><i class="fas fa-trash-alt mr-2"></i>Daftar Data Terhapus Sementara</h6>
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
                    // Array bantu untuk looping 7 kategori sekaligus
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
                        <table class="table table-bordered table-hover table-sm" width="100%" cellspacing="0" style="font-size: 13px; color: #000;">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Kode / ID</th>
                                    <th>Nama Barang / Deskripsi</th>
                                    <th width="20%">Tanggal Dihapus</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kat['data'] as $item)
                                    @php 
                                        // Penentuan PK (Primary Key) otomatis
                                        if($kat['key'] == 'rusak') $pk = $item->no_id_pemda;
                                        elseif($kat['key'] == 'bmd' || $kat['key'] == 'inventaris') $pk = $item->id;
                                        else $pk = $item->kode_barang;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="font-weight-bold text-primary">{{ $pk }}</td>
                                        <td>
                                            @if($kat['key'] == 'bmd')
                                                Pemakaian: {{ $item->pemakai_nama }}
                                            @else
                                                {{ $item->nama_barang }}
                                            @endif
                                        </td>
                                        <td class="text-center text-muted">{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            {{-- Form Pulihkan --}}
                                            <form action="{{ route('lokasi.arsip.restore', [$lokasi, $kat['key'], $pk]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success py-0 px-2" title="Pulihkan">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                            
                                            {{-- Form Hapus Permanen --}}
                                            <form action="{{ route('lokasi.arsip.permanen', [$lokasi, $kat['key'], $pk]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen data ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger py-0 px-2" title="Hapus Selamanya">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">Data arsip kosong.</td></tr>
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