@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Barang Rusak Berat - {{ $rusak->no_id_pemda }}</h6>
    </div>
    <div class="card-body">
        {{-- REVISI: Menggunakan no_id_pemda sebagai parameter --}}
        <form action="{{ route('lokasi.rusak.update', ['lokasi' => $lokasi, 'rusak' => $rusak->no_id_pemda]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 border-right">
                    <div class="form-group">
                        <label>No. ID Pemda</label>
                        <input type="text" class="form-control bg-light" value="{{ $rusak->no_id_pemda }}" readonly>
                        <input type="hidden" name="no_id_pemda" value="{{ $rusak->no_id_pemda }}">
                    </div>
                    <div class="form-group">
                        <label for="nama_barang">Nama / Jenis Barang</label>
                        <input type="text" class="form-control" name="nama_barang" value="{{ old('nama_barang', $rusak->nama_barang) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="no_polisi">No. Polisi</label>
                        <input type="text" class="form-control" name="no_polisi" value="{{ old('no_polisi', $rusak->no_polisi) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="harga_perolehan">Harga Perolehan (Rp)</label>
                        <input type="text" class="form-control money" name="harga_perolehan" value="{{ number_format($rusak->harga_perolehan, 0, ',', '.') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="kondisi">Kondisi</label>
                        <input type="text" class="form-control" name="kondisi" value="{{ old('kondisi', $rusak->kondisi) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3">{{ old('keterangan', $rusak->keterangan) }}</textarea>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-right">
                <a href="{{ route('lokasi.rusak.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Update Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.money').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = this.value.replace(/[^0-9]/g, '');
            this.value = value ? parseInt(value).toLocaleString('id-ID') : '';
        });
    });
</script>
@endsection