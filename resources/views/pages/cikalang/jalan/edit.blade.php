@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Jalan (KIB D) - {{ $jalan->kode_barang }}</h6>
    </div>
    <div class="card-body">
        {{-- REVISI: Binding menggunakan kode_barang --}}
        <form action="{{ route('lokasi.jalan.update', ['lokasi' => $lokasi, 'jalan' => $jalan->kode_barang]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 border-right">
                    <div class="form-group">
                        <label>Kode Barang</label>
                        <input type="text" class="form-control bg-light" value="{{ $jalan->kode_barang }}" readonly>
                        <input type="hidden" name="kode_barang" value="{{ $jalan->kode_barang }}">
                    </div>
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" name="nama_barang" value="{{ old('nama_barang', $jalan->nama_barang) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="Lok">Lokasi (Alamat)</label>
                        <textarea class="form-control" name="Lok" rows="2">{{ old('Lok', $jalan->Lok) }}</textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nilai_perolehan">Nilai Perolehan (Rp)</label>
                        <input type="text" class="form-control money" name="nilai_perolehan" value="{{ number_format($jalan->nilai_perolehan, 0, ',', '.') }}">
                    </div>
                    <div class="form-group">
                        <label for="status_penggunaan">Status Penggunaan</label>
                        <input type="text" class="form-control" name="status_penggunaan" value="{{ old('status_penggunaan', $jalan->status_penggunaan) }}">
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-right">
                <a href="{{ route('lokasi.jalan.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Update Perubahan</button>
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