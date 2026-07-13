@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Data Tanah (KIB A) - {{ ucfirst($lokasi) }}</h6>
        <a href="{{ route('lokasi.tanah.index', ['lokasi' => $lokasi]) }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.tanah.store', ['lokasi' => $lokasi]) }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 border-right">
                    <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-map-marked-alt"></i> Data Utama & Spesifikasi</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" name="kode_barang" class="form-control @error('kode_barang') is-invalid @enderror" value="{{ old('kode_barang') }}" required>
                        @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Filter Sistem Lokasi (Kecamatan/Wilayah)</label>
                        <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ old('lokasi', $lokasi) }}">
                        @error('lokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang') }}" required>
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nibar (Nomor Ikatan Barang)</label>
                        <input type="text" name="nibar" class="form-control @error('nibar') is-invalid @enderror" value="{{ old('nibar') }}">
                        @error('nibar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">No. Register</label>
                        <input type="text" name="nomor_register" class="form-control @error('nomor_register') is-invalid @enderror" value="{{ old('nomor_register') }}">
                        @error('nomor_register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Luas / Volume (Jumlah) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="jumlah" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah') }}" required>
                                @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Satuan <span class="text-danger">*</span></label>
                                <input type="text" name="satuan" class="form-control @error('satuan') is-invalid @enderror" placeholder="M2 / Ha" value="{{ old('satuan', 'M2') }}" required>
                                @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Spesifikasi Barang (Luas Detail)</label>
                        <textarea name="spesifikasi_barang" rows="2" class="form-control @error('spesifikasi_barang') is-invalid @enderror" placeholder="Catatan luas detail...">{{ old('spesifikasi_barang') }}</textarea>
                        @error('spesifikasi_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Spesifikasi Lainnya</label>
                        <textarea name="spesifikasi_lainnya" rows="2" class="form-control @error('spesifikasi_lainnya') is-invalid @enderror">{{ old('spesifikasi_lainnya') }}</textarea>
                        @error('spesifikasi_lainnya') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Alamat / Lokasi Fisik (Lok) <span class="text-danger">*</span></label>
                        <textarea name="Lok" rows="2" class="form-control @error('Lok') is-invalid @enderror" required>{{ old('Lok') }}</textarea>
                        @error('Lok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Titik Koordinat</label>
                        <input type="text" name="titik_koordinat" class="form-control @error('titik_koordinat') is-invalid @enderror" placeholder="Contoh: -7.3291, 108.2142" value="{{ old('titik_koordinat') }}">
                        @error('titik_koordinat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="text-success font-weight-bold mb-3"><i class="fas fa-file-invoice"></i> Legalitas & Nilai Perolehan</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Bukti Kepemilikan (Nama Dokumen)</label>
                        <input type="text" name="bukti_nama" class="form-control @error('bukti_nama') is-invalid @enderror" placeholder="Contoh: Sertifikat, Girik, Akta" value="{{ old('bukti_nama') }}">
                        @error('bukti_nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nomor Dokumen Bukti</label>
                        <input type="text" name="bukti_nomor" class="form-control @error('bukti_nomor') is-invalid @enderror" value="{{ old('bukti_nomor') }}">
                        @error('bukti_nomor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Tanggal Dokumen Bukti</label>
                        <input type="date" name="bukti_tanggal" class="form-control @error('bukti_tanggal') is-invalid @enderror" value="{{ old('bukti_tanggal') }}">
                        @error('bukti_tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nama di Dokumen Kepemilikan</label>
                        <input type="text" name="nama_kepemilikan_dokumen" class="form-control @error('nama_kepemilikan_dokumen') is-invalid @enderror" value="{{ old('nama_kepemilikan_dokumen') }}">
                        @error('nama_kepemilikan_dokumen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Harga Satuan (Rp)</label>
                                <input type="number" step="0.01" min="0" name="harga_satuan" id="harga_satuan" class="form-control @error('harga_satuan') is-invalid @enderror" value="{{ old('harga_satuan') }}">
                                @error('harga_satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Nilai Perolehan (Rp) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="nilai_perolehan" id="nilai_perolehan" class="form-control @error('nilai_perolehan') is-invalid @enderror" value="{{ old('nilai_perolehan') }}" required>
                                @error('nilai_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Cara Perolehan <span class="text-danger">*</span></label>
                        <input type="text" name="cara_perolehan" class="form-control @error('cara_perolehan') is-invalid @enderror" placeholder="Contoh: Pembelian, Hibah" value="{{ old('cara_perolehan') }}" required>
                        @error('cara_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Tanggal Perolehan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_perolehan" class="form-control @error('tanggal_perolehan') is-invalid @enderror" value="{{ old('tanggal_perolehan') }}" required>
                        @error('tanggal_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Status Penggunaan <span class="text-danger">*</span></label>
                        <input type="text" name="status_penggunaan" class="form-control @error('status_penggunaan') is-invalid @enderror" value="{{ old('status_penggunaan') }}" required>
                        @error('status_penggunaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="form-group mt-2">
                <label class="font-weight-bold text-dark">Keterangan</label>
                <textarea name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan') }}</textarea>
                @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <hr>
            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save"></i> Simpan Data Tanah</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jumlahInput = document.getElementById('jumlah');
        const hargaInput = document.getElementById('harga_satuan');
        const totalInput = document.getElementById('nilai_perolehan');

        function hitungTotal() {
            let jumlah = parseFloat(jumlahInput.value) || 0;
            let harga = parseFloat(hargaInput.value) || 0;
            if(jumlah > 0 && harga > 0) {
                totalInput.value = (jumlah * harga).toFixed(2);
            }
        }
        jumlahInput.addEventListener('input', hitungTotal);
        hargaInput.addEventListener('input', hitungTotal);
    });
</script>
@endsection