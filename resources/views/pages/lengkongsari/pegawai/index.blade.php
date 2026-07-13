@extends('layouts.app') {{-- Sesuaikan dengan nama layout utama projectmu --}}

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Pegawai - Wilayah {{ ucfirst($lokasi) }}</h1>
        <a href="{{ route('lokasi.pegawai.create', $lokasi) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pegawai
        </a>
    </div>

    <!-- Flash Message Sukses -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-secondary">Daftar Pegawai Aktif</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>NIP</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan</th>
                            <th>No. HP / WA</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pegawai as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nip ?? '-' }}</td>
                                <td><strong>{{ $item->nama }}</strong></td>
                                <td>{{ $item->jabatan }}</td>
                                <td>
                                    @if($item->no_hp)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->no_hp) }}" target="_blank" class="text-success">
                                            <i class="fab fa-whatsapp"></i> {{ $item->no_hp }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->email ?? '-' }}</td>
                                <td>{{ $item->alamat }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <!-- Tombol Edit -->
                                        <a href="{{ route('lokasi.pegawai.edit', [$lokasi, $item->id]) }}" class="btn btn-sm btn-warning" title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Tombol Hapus (Menggunakan Form karena Method DELETE) -->
                                        <form action="{{ route('lokasi.pegawai.destroy', [$lokasi, $item->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" title="Hapus Data">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada data pegawai di wilayah ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection