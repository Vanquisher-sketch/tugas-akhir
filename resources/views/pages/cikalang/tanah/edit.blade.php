@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Inventaris Tanah (KIB A) - {{ $tanah->kode_barang }}</h6>
    </div>
    <div class="card-body">
        {{-- REVISI: Route menggunakan kode_barang --}}
        <form action="{{ route('lokasi.tanah.update', ['lokasi' => $lokasi, 'tanah' => $tanah->kode_barang]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" class="form-control bg-light" id="kode_barang" name="kode_barang" value="{{ $tanah->kode_barang }}" readonly>
                        <small class="text-muted">Kode barang (Primary Key) tidak dapat diubah.</small>
                    </div>
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" name="nama_barang" value="{{ old('nama_barang', $tanah->nama_barang) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Luas (M2)</label>
                        <input type="text" class="form-control money" name="jumlah" value="{{ number_format($tanah->jumlah, 0, ',', '.') }}">
                    </div>
                    <div class="form-group">
                        <label for="Lok">Lokasi / Alamat</label>
                        <textarea class="form-control" name="Lok" rows="3">{{ old('Lok', $tanah->Lok) }}</textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nilai_perolehan">Nilai Perolehan (Rp)</label>
                        <input type="text" class="form-control money" name="nilai_perolehan" value="{{ number_format($tanah->nilai_perolehan, 0, ',', '.') }}">
                    </div>
                    <div class="form-group">
                        <label for="bukti_nomor">Nomor Bukti</label>
                        <input type="text" class="form-control" name="bukti_nomor" value="{{ old('bukti_nomor', $tanah->bukti_nomor) }}">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_perolehan">Tanggal Perolehan</label>
                        <input type="date" class="form-control" name="tanggal_perolehan" value="{{ $tanah->tanggal_perolehan }}">
                    </div>
                    <div class="form-group">
                        <label for="status_penggunaan">Status</label>
                        <input type="text" class="form-control" name="status_penggunaan" value="{{ old('status_penggunaan', $tanah->status_penggunaan) }}">
                    </div>
                </div>
            </div>

            <hr>
            <div class="text-right">
                <a href="{{ route('lokasi.tanah.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
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