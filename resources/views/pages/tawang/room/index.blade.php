@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data Ruangan - {{ ucfirst($lokasi) }}</h6>
        <div class="d-flex">
            <form action="{{ route('lokasi.room.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Cari ruangan..." name="search" value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>
            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.room.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Data
                </a>
            </div>
        </div>
    </div>
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
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Nama Ruangan</th>
                        <th>Kode Ruangan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataRuangan as $room)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + $dataRuangan->firstItem() - 1 }}</td>
                        <td>{{ $room->name }}</td>
                        <td>{{ $room->kode_ruangan }}</td>
                        <td class="text-center">
                            <a href="{{ route('lokasi.room.edit', ['lokasi' => $lokasi, 'room' => $room->id]) }}" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('lokasi.room.destroy', ['lokasi' => $lokasi, 'room' => $room->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus ruangan ini? Semua data inventaris di dalamnya juga akan terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Belum ada data ruangan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $dataRuangan->appends(['search' => $search ?? ''])->links() }}
        </div>
    </div>
</div>
@endsection

