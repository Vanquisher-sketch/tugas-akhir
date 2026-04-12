@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    {{-- Card Header --}}
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Kartu Inventaris Ruangan: {{ $room->name }}</h6>
        <div class="d-flex">
            <form action="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" method="GET" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Cari barang..." name="search" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>
            
            <div class="btn-group ml-3">
                <a href="{{ route('lokasi.inventaris.create', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" class="btn btn-primary">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Data
                </a>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <h6 class="dropdown-header">Opsi Lain:</h6>
                    <a class="dropdown-item" href="{{ route('lokasi.inventaris.print', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" target="_blank">
                        <i class="fas fa-print fa-fw mr-2 text-gray-400"></i>Cetak Data
                    </a>
                    <a class="dropdown-item" href="{{ route('lokasi.export.excel', ['lokasi' => $lokasi, 'menu' => 'inventaris', 'room_id' => $room->kode_ruangan]) }}">
                        <i class="fas fa-file-excel fa-fw mr-2 text-gray-400"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- Flash Message (Opsional jika di app.blade sudah ada) --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-dark" width="100%" cellspacing="0">
                <thead class="thead-light text-center font-weight-bold">
                    <tr>
                        <th rowspan="2" class="align-middle">No</th>
                        <th rowspan="2" class="align-middle">NIBAR</th>
                        <th rowspan="2" class="align-middle">Register</th>
                        <th rowspan="2" class="align-middle">Kode Barang</th>
                        <th rowspan="2" class="align-middle">Nama Barang</th>
                        <th colspan="2">Spesifikasi Barang</th>
                        <th rowspan="2" class="align-middle">Jumlah</th>
                        <th rowspan="2" class="align-middle">Satuan</th>
                        <th rowspan="2" class="align-middle">Aksi</th>
                    </tr>
                    <tr>
                        <th>Merek/Tipe</th>
                        <th>Tahun</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataInventaris as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + $dataInventaris->firstItem() - 1 }}</td>
                        <td>{{ $item->nibar }}</td>
                        <td>{{ $item->nomor_register }}</td>
                        <td class="font-weight-bold text-primary">{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->merk_tipe }}</td>
                        <td class="text-center">{{ $item->tahun_perolehan }}</td>
                        <td class="text-center font-weight-bold text-info">{{ $item->jumlah }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                {{-- Tombol Mutasi --}}
                                <button type="button" class="btn btn-sm btn-info move-btn mr-1 shadow-sm" 
                                    data-toggle="modal" data-target="#moveModal" 
                                    data-id="{{ $item->kode_barang }}" 
                                    data-nama="{{ $item->nama_barang }}"
                                    data-max="{{ $item->jumlah }}"
                                    title="Mutasi Barang">
                                    <i class="fas fa-truck fa-sm"></i>
                                </button>
                                
                                {{-- Edit --}}
                                <a href="{{ route('lokasi.inventaris.edit', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan, 'inventari' => $item->kode_barang]) }}" 
                                   class="btn btn-warning btn-sm mr-1 shadow-sm" title="Edit"><i class="fas fa-edit fa-sm"></i></a>
                                
                                {{-- Hapus dengan SweetAlert2 --}}
                                <form action="{{ route('lokasi.inventaris.destroy', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan, 'inventari' => $item->kode_barang]) }}" 
                                      method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm btn-delete shadow-sm" title="Hapus">
                                        <i class="fas fa-trash fa-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">Belum ada data inventaris.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $dataInventaris->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- MODAL MUTASI (Tetap Sama) --}}
<div class="modal fade" id="moveModal" tabindex="-1" role="dialog" aria-labelledby="moveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-left-info shadow">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-info" id="moveModalLabel"><i class="fas fa-exchange-alt mr-2"></i>Mutasi Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="moveForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Barang: <strong id="namaBarangPindah"></strong></p>
                    <p>Stok: <strong id="stokMaksimal" class="text-primary">0</strong></p>
                    <hr>
                    <div class="form-group">
                        <label>Jumlah Pindah:</label>
                        <input type="number" name="qty_to_move" id="qty_to_move" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Ruangan Tujuan:</label>
                        <select name="new_room_id" class="form-control" required>
                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                            @foreach ($allRooms as $roomOption)
                                @if($roomOption->kode_ruangan != $room->kode_ruangan)
                                    <option value="{{ $roomOption->kode_ruangan }}">{{ $roomOption->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info px-4">Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // 1. LOGIKA MODAL MUTASI
        $('.move-btn').on('click', function() {
            var itemKode = $(this).data('id');
            var itemName = $(this).data('nama');
            var maxQty = $(this).data('max');
            var url = "{{ url($lokasi . '/room/' . $room->kode_ruangan . '/inventaris') }}/" + itemKode + "/move";
            $('#moveForm').attr('action', url);
            $('#namaBarangPindah').text(itemName);
            $('#stokMaksimal').text(maxQty);
            $('#qty_to_move').attr('max', maxQty).val(1);
        });

        // 2. LOGIKA SWEETALERT HAPUS
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('.delete-form');

            Swal.fire({
                title: 'Hapus Data?',
                text: "Data akan dipindahkan ke Pusat Arsip!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Hapus!',
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