@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Penggunaan BMD & BAST - Kecamatan {{ ucfirst($lokasi) }}</h6>
        
        <div class="d-flex">
            <form action="{{ route('lokasi.bmd.index', ['lokasi' => $lokasi]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" 
                           placeholder="Cari pemakai/barang..." 
                           name="search" id="autoSearch" list="searchList"
                           value="{{ request('search') }}" autocomplete="off">
                    <datalist id="searchList"></datalist>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>

            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.bmd.create', ['lokasi' => $lokasi]) }}" class="btn btn-primary"><i class="fas fa-plus fa-sm"></i> Tambah Data</a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0" style="font-size: 0.75rem; color: #000;">
                <thead class="thead-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle">No</th>
                        <th rowspan="2" class="align-middle">Nama Barang</th>
                        <th rowspan="2" class="align-middle">Kode Barang</th>
                        <th colspan="2">Data Pemakai (Pihak Kedua)</th>
                        <th rowspan="2" class="align-middle">Bendahara Barang (Pihak Pertama)</th>
                        <th colspan="2">Dokumen BAST</th>
                        <th rowspan="2" class="align-middle">Status Pajak (Aset)</th>
                        <th rowspan="2" class="align-middle">Aksi</th>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <th>Identitas / Jabatan</th>
                        <th>Nomor</th>
                        <th>Berkas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bmds as $item)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        {{-- 🌟 Penyesuaian ke alat_nama_barang --}}
                        <td class="font-weight-bold align-middle">{{ $item->peralatan->alat_nama_barang ?? '-' }}</td>
                        {{-- 🌟 Penyesuaian ke bmd_alat_kode --}}
                        <td class="align-middle">{{ $item->bmd_alat_kode }}</td>
                        
                        <td class="align-middle">
                            {{-- 🌟 Penyesuaian ke pegawai_nama --}}
                            <strong>{{ $item->pegawai->pegawai_nama ?? 'Bukan Pegawai Aktif' }}</strong>
                        </td>
                        <td class="align-middle">
                            {{-- 🌟 Penyesuaian ke bmd_pemakai_identitas, bmd_pemakai_status, dan pegawai_jabatan --}}
                            <small class="text-muted">ID/NIP: {{ $item->bmd_pemakai_identitas }}</small><br>
                            <small class="text-dark">Status: <span class="badge badge-light border">{{ $item->bmd_pemakai_status }}</span></small><br>
                            <small class="text-dark">Jabatan: {{ $item->pegawai->pegawai_jabatan ?? '-' }}</small>
                        </td>

                        <td class="align-middle">
                            {{-- 🌟 Penyesuaian ke pegawai_nama milik relasi bendahara --}}
                            <strong>{{ $item->bendahara->pegawai_nama ?? '-' }}</strong><br>
                            <small class="text-muted">Bendahara Barang</small>
                        </td>

                        {{-- 🌟 Penyesuaian ke bmd_bast_nomor dan bmd_bast_file --}}
                        <td class="align-middle text-center font-weight-bold">{{ $item->bmd_bast_nomor ?? '-' }}</td>
                        <td class="text-center align-middle">
                            @if($item->bmd_bast_file)
                                <a href="{{ asset('storage/' . $item->bmd_bast_file) }}" target="_blank" class="btn btn-sm btn-info btn-circle" title="Lihat Berkas Surat BAST PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            @else 
                                <span class="text-muted">-</span> 
                            @endif
                        </td>

                        <td class="text-center align-middle">
                            @if(isset($item->peralatan->tanggal_pajak) && $item->peralatan->tanggal_pajak)
                                @php
                                    $tglPajak = \Carbon\Carbon::parse($item->peralatan->tanggal_pajak);
                                    $isExpired = $tglPajak->isPast();
                                @endphp
                                <span class="{{ $isExpired ? 'text-danger font-weight-bold' : 'text-success' }}">
                                    <i class="fas fa-calendar-alt"></i> {{ $tglPajak->format('d/m/Y') }}
                                </span>
                                @if($isExpired)
                                    <br><span class="badge badge-danger">Wajib Pajak!</span>
                                @else
                                    <br><span class="badge badge-success">Aktif</span>
                                @endif
                            @else
                                <span class="badge badge-light text-muted">Bukan Kendaraan</span>
                            @endif
                        </td>

                        <td class="text-center align-middle">
                            {{-- 🌟 Penyesuaian rute menggunakan parameter id => bmd_id --}}
                            <a href="{{ route('lokasi.bmd.edit', ['lokasi' => $lokasi, 'id' => $item->bmd_id]) }}" class="btn btn-sm btn-warning" title="Ubah Transaksi"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('lokasi.bmd.destroy', ['lokasi' => $lokasi, 'id' => $item->bmd_id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data pemakaian ini? Berkas PDF Surat BAST juga akan dihapus di server.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Transaksi"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center py-4">Belum ada data penggunaan aset.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $bmds->links() }}</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('autoSearch');
        const list = document.getElementById('searchList');

        input.addEventListener('focus', function() {
            let history = JSON.parse(localStorage.getItem('search_history_bmd')) || [];
            list.innerHTML = '';
            history.forEach(item => {
                let option = document.createElement('option');
                option.value = item;
                option.label = '(Riwayat)';
                list.appendChild(option);
            });
        });

        input.closest('form').addEventListener('submit', function() {
            let query = input.value.trim();
            if (query.length > 0) {
                let history = JSON.parse(localStorage.getItem('search_history_bmd')) || [];
                if (!history.includes(query)) {
                    history.unshift(query);
                    localStorage.setItem('search_history_bmd', JSON.stringify(history.slice(0, 5)));
                }
            }
        });

        input.addEventListener('input', function() {
            const query = this.value;
            if (query.length < 2) return; 
            fetch(`/{{ $lokasi }}/bmd/autocomplete?term=${query}`).then(res => res.json()).then(data => {
                list.innerHTML = ''; 
                data.forEach(item => {
                    let option = document.createElement('option');
                    option.value = item.value; 
                    option.label = item.label; 
                    list.appendChild(option);
                });
            });
        });
    });
</script>
@endsection