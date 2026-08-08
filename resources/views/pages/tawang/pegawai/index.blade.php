@extends('layouts.app') {{-- Sesuaikan dengan nama layout utama projectmu --}}

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Pegawai - Kecamatan {{ ucfirst($lokasi) }}</h1>
        <a href="{{ route('lokasi.pegawai.create', $lokasi) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pegawai
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-secondary">Daftar Pegawai Aktif</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th>NIP</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan</th>
                            <th>No. HP / WA</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pegawai as $index => $item)
                            <tr>
                                <td class="text-center align-middle">{{ $index + 1 }}</td>
                                {{-- 🌟 Pemanggilan data diubah menggunakan prefix pegawai_ --}}
                                <td class="align-middle font-weight-bold text-primary">{{ $item->pegawai_nip ?? '-' }}</td>
                                <td class="align-middle"><strong>{{ $item->pegawai_nama }}</strong></td>
                                <td class="align-middle">{{ $item->pegawai_jabatan }}</td>
                                <td class="align-middle">
    @if($item->pegawai_no_hp)
        @php
            // 1. Bersihkan spasi atau strip dari nomor database
            $nomorWa = preg_replace('/[^0-9]/', '', $item->pegawai_no_hp); 
            
            // 2. Jika angka depannya 0, ubah jadi 62
            if (substr($nomorWa, 0, 1) === '0') {
                $nomorWa = '62' . substr($nomorWa, 1);
            }
        @endphp
        
        {{-- 3. Masukkan variabel $nomorWa ke link WA --}}
        <a href="https://wa.me/{{ $nomorWa }}" target="_blank" class="text-success font-weight-bold" style="text-decoration: none;">
            <i class="fab fa-whatsapp"></i> {{ $item->pegawai_no_hp }}
        </a>
    @else
        -
    @endif
</td>
                                <td class="align-middle">{{ $item->pegawai_email ?? '-' }}</td>
                                <td class="align-middle">{{ $item->pegawai_alamat }}</td>
                                <td class="text-center align-middle">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('lokasi.pegawai.edit', ['lokasi' => $lokasi, 'nip' => $item->pegawai_nip]) }}" class="btn btn-sm btn-warning text-white" title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('lokasi.pegawai.destroy', ['lokasi' => $lokasi, 'nip' => $item->pegawai_nip]) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini?')">
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
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-2x mb-2 text-gray-300"></i><br>
                                    Belum ada data pegawai di wilayah ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection