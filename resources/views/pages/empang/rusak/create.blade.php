@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Data Barang Rusak Berat - {{ ucfirst($lokasi) }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.rusak.store', ['lokasi' => $lokasi]) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 border-right">
                    <div class="form-group">
                        <label for="nama_barang">Nama / Jenis Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" required>
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="no_id_pemda">No. ID Pemda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('no_id_pemda') is-invalid @enderror" id="no_id_pemda" name="no_id_pemda" value="{{ old('no_id_pemda') }}" required>
                        @error('no_id_pemda') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="spesifikasi">Spesifikasi</label>
                        <input type="text" class="form-control" name="spesifikasi" value="{{ old('spesifikasi') }}">
                    </div>
                    <div class="form-group">
                        <label for="no_polisi">No. Polisi</label>
                        <input type="text" class="form-control" name="no_polisi" value="{{ old('no_polisi') }}" placeholder="Contoh: Z 1234 XX">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tahun_perolehan">Tahun Perolehan <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="tahun_perolehan" value="{{ old('tahun_perolehan') }}" placeholder="YYYY" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga_perolehan">Harga Perolehan (Rp) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control money" name="harga_perolehan" value="{{ old('harga_perolehan') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="kondisi">Kondisi</label>
                        <input type="text" class="form-control" name="kondisi" value="{{ old('kondisi', 'RB') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="tercatat_di_kib">Tercatat di KIB</label>
                        <input type="text" class="form-control" name="tercatat_di_kib" value="{{ old('tercatat_di_kib') }}" placeholder="Contoh: KIB B">
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="2">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-right">
                <a href="{{ route('lokasi.rusak.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const namaInput = document.getElementById('nama_barang');
        const idInput = document.getElementById('no_id_pemda');

        namaInput.addEventListener('keyup', function() {
            if (idInput.dataset.manual !== 'true') {
                let val = this.value.trim();
                if (val.length > 0) {
                    let initials = val.split(' ').map(w => w[0]).join('').toUpperCase().substring(0, 3);
                    let random = Math.floor(1000 + Math.random() * 9000);
                    idInput.value = `RSK-${initials}-${random}`;
                }
            }
        });
        idInput.addEventListener('input', function() { this.dataset.manual = 'true'; });

        document.querySelectorAll('.money').forEach(input => {
            input.addEventListener('input', function(e) {
                let value = this.value.replace(/[^0-9]/g, '');
                this.value = value ? parseInt(value).toLocaleString('id-ID') : '';
            });
        });
    });
</script>
@endsection