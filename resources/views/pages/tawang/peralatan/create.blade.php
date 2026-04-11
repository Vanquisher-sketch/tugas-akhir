@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Data Peralatan & Mesin (KIB B) - {{ ucfirst($lokasi) }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.peralatan.store', ['lokasi' => $lokasi]) }}" method="POST">
            @csrf
            <div class="row">
                {{-- KOLOM KIRI --}}
                <div class="col-md-6 border-right">
                    <h6 class="font-weight-bold text-gray-800 mb-3">Identitas & Spesifikasi Barang</h6>
                    
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" required placeholder="Contoh: Laptop Asus">
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="kode_barang">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" id="kode_barang" name="kode_barang" value="{{ old('kode_barang') }}" required>
                        @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="nibr">NIBR</label>
                        <input type="text" class="form-control" id="nibr" name="nibr" value="{{ old('nibr') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="nomor_register">Nomor Register</label>
                        <input type="text" class="form-control" id="nomor_register" name="nomor_register" value="{{ old('nomor_register') }}">
                    </div>

                    <hr>

                    <div class="form-group">
                        <label for="merk_tipe">Merk / Tipe</label>
                        <input type="text" class="form-control" id="merk_tipe" name="merk_tipe" value="{{ old('merk_tipe') }}" placeholder="Contoh: Asus Vivobook">
                    </div>

                    <div class="form-group">
                        <label for="spesifikasi_barang">Spesifikasi Barang</label>
                        <input type="text" class="form-control" id="spesifikasi_barang" name="spesifikasi_barang" value="{{ old('spesifikasi_barang') }}">
                    </div>

                    <div class="form-group">
                        <label for="Lok">Lokasi Fisik / Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('Lok') is-invalid @enderror" id="Lok" name="Lok" rows="2" required>{{ old('Lok') }}</textarea>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="col-md-6">
                    <h6 class="font-weight-bold text-gray-800 mb-3">Detail Kendaraan & Nilai</h6>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nomor_polisi">Nomor Polisi (Plat)</label>
                                <input type="text" class="form-control" id="nomor_polisi" name="nomor_polisi" value="{{ old('nomor_polisi') }}" placeholder="Z 1234 XX">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jumlah">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" value="{{ old('jumlah', 1) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="satuan">Satuan</label>
                                <input type="text" class="form-control" id="satuan" name="satuan" value="{{ old('satuan', 'Unit') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga_satuan">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control money @error('harga_satuan') is-invalid @enderror" id="harga_satuan" name="harga_satuan" value="{{ old('harga_satuan') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nilai_perolehan">Nilai Total (Rp) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control money @error('nilai_perolehan') is-invalid @enderror" id="nilai_perolehan" name="nilai_perolehan" value="{{ old('nilai_perolehan') }}" required>
                            </div>
                        </div>
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
                <a href="{{ route('lokasi.peralatan.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const namaInput = document.getElementById('nama_barang');
        const kodeInput = document.getElementById('kode_barang');

        // 1. Auto Generate Kode
        namaInput.addEventListener('keyup', function() {
            if (kodeInput.dataset.manual !== 'true') {
                let val = this.value.trim();
                if (val.length > 0) {
                    let initials = val.split(' ').map(w => w[0]).join('').toUpperCase().substring(0, 4);
                    let random = Math.floor(100 + Math.random() * 900);
                    kodeInput.value = `ALAT-${initials}-${random}`;
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