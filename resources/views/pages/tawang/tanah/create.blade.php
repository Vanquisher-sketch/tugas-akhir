@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Data Inventaris Tanah (KIB A) - {{ ucfirst($lokasi) }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.tanah.store', ['lokasi' => $lokasi]) }}" method="POST">
            @csrf
            <div class="row">
                {{-- Kolom Kiri --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" required placeholder="Contoh: Tanah Kantor Kecamatan">
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="kode_barang">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" id="kode_barang" name="kode_barang" value="{{ old('kode_barang') }}" required placeholder="Ketik manual atau otomatis dari nama">
                        @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nibar">NIBAR</label>
                        <input type="text" class="form-control" id="nibar" name="nibar" value="{{ old('nibar') }}">
                    </div>
                    <div class="form-group">
                        <label for="nomor_register">Nomor Register</label>
                        <input type="text" class="form-control" id="nomor_register" name="nomor_register" value="{{ old('nomor_register') }}">
                    </div>
                    <div class="form-group">
                        <label for="spesifikasi_barang">Luas (M2) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control money @error('jumlah') is-invalid @enderror" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" required>
                        @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="satuan">Satuan</label>
                        <input type="text" class="form-control" id="satuan" name="satuan" value="{{ old('satuan', 'M2') }}">
                    </div>
                    <div class="form-group">
                        <label for="Lok">Lokasi / Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('Lok') is-invalid @enderror" id="Lok" name="Lok" rows="3" required>{{ old('Lok') }}</textarea>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nilai_perolehan">Nilai Perolehan (Rp) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control money @error('nilai_perolehan') is-invalid @enderror" id="nilai_perolehan" name="nilai_perolehan" value="{{ old('nilai_perolehan') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="harga_satuan">Harga Satuan (Rp)</label>
                        <input type="text" class="form-control money" id="harga_satuan" name="harga_satuan" value="{{ old('harga_satuan') }}">
                    </div>
                    <div class="form-group">
                        <label for="bukti_nama">Jenis Bukti (Sertifikat/Girik)</label>
                        <input type="text" class="form-control" name="bukti_nama" value="{{ old('bukti_nama') }}">
                    </div>
                    <div class="form-group">
                        <label for="bukti_nomor">Nomor Bukti</label>
                        <input type="text" class="form-control" name="bukti_nomor" value="{{ old('bukti_nomor') }}">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_perolehan">Tanggal Perolehan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_perolehan" value="{{ old('tanggal_perolehan') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="status_penggunaan">Status Penggunaan</label>
                        <input type="text" class="form-control" name="status_penggunaan" value="{{ old('status_penggunaan', 'Aktif') }}">
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>

            <hr>
            <div class="text-right">
                <a href="{{ route('lokasi.tanah.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT AUTO GENERATE & MASKING --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const namaInput = document.getElementById('nama_barang');
        const kodeInput = document.getElementById('kode_barang');

        // 1. Auto Generate Kode
        namaInput.addEventListener('keyup', function() {
            if (kodeInput.dataset.manual !== 'true') {
                let val = this.value.trim();
                if (val.length > 0) {
                    let initials = val.split(' ').map(w => w[0]).join('').toUpperCase().substring(0, 3);
                    let random = Math.floor(100 + Math.random() * 900);
                    kodeInput.value = `TNH-${initials}-${random}`;
                }
            }
        });

        kodeInput.addEventListener('input', function() {
            this.dataset.manual = 'true';
        });

        // 2. Simple Money Formatter
        document.querySelectorAll('.money').forEach(input => {
            input.addEventListener('input', function(e) {
                let value = this.value.replace(/[^0-9]/g, '');
                this.value = value ? parseInt(value).toLocaleString('id-ID') : '';
            });
        });
    });
</script>
@endsection