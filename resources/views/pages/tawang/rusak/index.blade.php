@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    {{-- Header Card --}}
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between border-bottom-danger">
        <h6 class="m-0 font-weight-bold text-danger">
            <i class="fas fa-exclamation-triangle mr-2"></i>Jurnal Pemantauan Barang Rusak Berat - {{ ucfirst($lokasi) }}
        </h6>
        
        <div>
            <a class="btn btn-danger btn-sm shadow-sm" href="{{ route('lokasi.rusak.print', ['lokasi' => $lokasi]) }}" target="_blank">
                <i class="fas fa-print fa-fw mr-1"></i> Cetak PDF Laporan
            </a>
        </div>
    </div>

    <div class="card-body">
        {{-- Flash Notification --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        {{-- Tabel Utama Penampung Data --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-dark" width="100%" cellspacing="0" style="font-size: 12px; vertical-align: middle;">
                <thead class="thead-light text-center">
                    <tr class="font-weight-bold">
                        <th style="width: 5%;">No</th>
                        <th style="width: 20%;">Kode / ID Barang</th>
                        <th style="width: 25%;">Nama Barang Terdeteksi</th>
                        <th style="width: 15%;">Asal Modul</th>
                        <th>Alasan & Keterangan Kronologi Rusak</th>
                        <th style="width: 10%;">Kondisi</th>
                        <th style="width: 12%;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataRusak as $item)
                    <tr>
                        <td class="text-center font-weight-bold">{{ $loop->iteration + $dataRusak->firstItem() - 1 }}</td>
                        
                        {{-- Kolom kode_barang (SUDAH DIREVISI MENJADI rusak_kode_barang) --}}
                        <td class="font-weight-bold text-danger text-center">{{ $item->rusak_kode_barang }}</td>
                        
                        {{-- Kolom nama_barang (Hasil mapping live dari Controller) --}}
                        <td class="font-weight-bold text-gray-900">{{ $item->nama_barang }}</td>
                        
                        {{-- Kolom jenis_asal --}}
                        <td class="text-center">
                            <span class="badge {{ $item->jenis_asal === 'Peralatan' ? 'badge-primary' : 'badge-info' }} px-2 py-1 font-weight-bold">
                                {{ $item->jenis_asal === 'Peralatan' ? 'KIB B Peralatan' : 'Inventaris Ruangan' }}
                            </span>
                        </td>
                        
                        {{-- Kolom keterangan alasan kerusakan --}}
                        <td class="font-italic text-muted bg-light font-weight-bold">
                            {{ $item->keterangan ?? 'Tidak ada catatan kronologi kerusakan.' }}
                        </td>
                        
                        {{-- Kolom status kondisi tetap --}}
                        <td class="text-center">
                            <span class="badge badge-danger text-uppercase px-2 py-1 font-weight-bold">
                                Rusak Berat
                            </span>
                        </td>
                        
                        {{-- Kolom Aksi Tunggal Tindakan Pemulihan --}}
                        <td class="text-center">
                            <form action="{{ route('lokasi.rusak.destroy', ['lokasi' => $lokasi, 'rusak' => $item->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah barang ini sudah selesai diperbaiki? Jika ya, status kondisi barang di modul asal akan otomatis pulih menjadi Baik.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-outline-success font-weight-bold shadow-sm py-1">
                                    <i class="fas fa-wrench mr-1"></i> Selesai Perbaikan
                                </button>
                            </form> 
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <div class="mb-3">
                                <i class="fas fa-check-circle fa-3x text-success"></i>
                            </div>
                            <h5 class="font-weight-bold text-success mb-1">Luar Biasa, Semua Aset Aman!</h5>
                            <p class="small mb-0 text-secondary">Tidak mendeteksi adanya aset dinas atau inventaris ruangan berstatus <b>Rusak Berat</b> di wilayah operasional ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Navigasi Pagination --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $dataRusak->links() }}
        </div>
    </div>
</div>
@endsection