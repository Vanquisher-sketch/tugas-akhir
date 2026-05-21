@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data Ruangan - {{ ucfirst($lokasi) }}</h6>
        <div class="d-flex">
            {{-- Form Pencarian dengan Datalist --}}
            <form action="{{ route('lokasi.room.index', ['lokasi' => $lokasi]) }}" method="GET" id="searchForm" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" 
                           id="roomSearchInput"
                           class="form-control bg-light border-0 small" 
                           placeholder="Cari ruangan..." 
                           name="search" 
                           value="{{ $search ?? '' }}"
                           list="roomSuggestions"
                           autocomplete="off">
                    
                    {{-- Wadah Saran & History --}}
                    <datalist id="roomSuggestions">
                        @if(isset($allRooms))
                            @foreach($allRooms as $suggestion)
                                <option value="{{ $suggestion->name }}">
                            @endforeach
                        @endif
                    </datalist>

                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
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
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Nama Ruangan</th>
                        <th>Kode Ruangan</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
    @forelse ($dataRuangan as $room)
    <tr>
        <td class="text-center">{{ $loop->iteration + $dataRuangan->firstItem() - 1 }}</td>
        <td>{{ $room->name }}</td>
        <td>{{ $room->kode_ruangan }}</td>
        <td class="text-center">
            {{-- REVISI: Ganti $room->id menjadi $room->kode_ruangan --}}
            <a href="{{ route('lokasi.room.edit', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" class="btn btn-warning btn-sm" title="Edit">
                <i class="fas fa-edit"></i>
            </a>

            {{-- REVISI: Ganti $room->id menjadi $room->kode_ruangan --}}
            <form action="{{ route('lokasi.room.destroy', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus ruangan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="4" class="text-center text-muted">Belum ada data ruangan atau hasil pencarian tidak ditemukan.</td>
    </tr>
    @endforelse
</tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Menampilkan {{ $dataRuangan->firstItem() }} sampai {{ $dataRuangan->lastItem() }} dari {{ $dataRuangan->total() }} data</small>
            {{ $dataRuangan->appends(['search' => $search ?? ''])->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const searchInput = document.getElementById('roomSearchInput');
        const dataList = document.getElementById('roomSuggestions');
        const historyKey = 'search_history_' + '{{ $lokasi }}'; // History per lokasi

        // 1. Ambil History dari LocalStorage
        let history = JSON.parse(localStorage.getItem(historyKey)) || [];

        // 2. Masukkan history ke Datalist agar muncul sebagai saran
        function loadHistory() {
            history.forEach(item => {
                // Cek agar tidak duplikat dengan saran database
                let exists = false;
                $('#roomSuggestions option').each(function(){
                    if (this.value == item) exists = true;
                });
                if(!exists) {
                    $(dataList).prepend(`<option value="${item}"> (Riwayat)</option>`);
                }
            });
        }

        loadHistory();

        // 3. Simpan Kata Kunci saat Form di-submit
        $('#searchForm').submit(function() {
            let query = searchInput.value.trim();
            if (query.length > 1) {
                // Hapus jika sudah ada di list (biar jadi paling baru/atas)
                history = history.filter(item => item !== query);
                // Tambah ke awal array
                history.unshift(query);
                // Simpan maksimal 10 riwayat terakhir
                if (history.length > 10) history.pop();
                localStorage.setItem(historyKey, JSON.stringify(history));
            }
        });
    });
</script>
@endpush