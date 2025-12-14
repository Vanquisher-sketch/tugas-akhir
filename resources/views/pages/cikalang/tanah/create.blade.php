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
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" id="kode_barang" name="kode_barang" value="{{ old('kode_barang') }}">
                        @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" required>
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nibar">NIBAR</label>
                        <input type="text" class="form-control @error('nibar') is-invalid @enderror" id="nibar" name="nibar" value="{{ old('nibar') }}">
                        @error('nibar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nomor_register">Nomor Register</label>
                        <input type="text" class="form-control @error('nomor_register') is-invalid @enderror" id="nomor_register" name="nomor_register" value="{{ old('nomor_register') }}">
                        @error('nomor_register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- BARU: Spesifikasi Barang --}}
                    <div class="form-group">
                        <label for="spesifikasi_barang">Spesifikasi Barang</label>
                        <input type="text" class="form-control @error('spesifikasi_barang') is-invalid @enderror" id="spesifikasi_barang" name="spesifikasi_barang" value="{{ old('spesifikasi_barang') }}">
                        @error('spesifikasi_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="spesifikasi_lainnya">Spesifikasi Lainnya</label>
                        <input type="text" class="form-control @error('spesifikasi_lainnya') is-invalid @enderror" id="spesifikasi_lainnya" name="spesifikasi_lainnya" value="{{ old('spesifikasi_lainnya') }}">
                        @error('spesifikasi_lainnya') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="form-group">
                        <label for="jumlah">Jumlah</label>
                        <input type="number" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah" name="jumlah" value="{{ old('jumlah', 1) }}">
                        @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="satuan">Satuan</label>
                        <input type="text" class="form-control @error('satuan') is-invalid @enderror" id="satuan" name="satuan" value="{{ old('satuan') }}">
                        @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    {{-- REVISI: name="Lok" --}}
                    <div class="form-group">
                        <label for="Lok">Lokasi / Alamat</label>
                        <textarea class="form-control @error('Lok') is-invalid @enderror" id="Lok" name="Lok" rows="3">{{ old('Lok') }}</textarea>
                        @error('Lok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="titik_koordinat">Titik Koordinat</label>
                        <input type="text" class="form-control @error('titik_koordinat') is-invalid @enderror" id="titik_koordinat" name="titik_koordinat" value="{{ old('titik_koordinat') }}">
                        @error('titik_koordinat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <hr>
                    <h6>Bukti Kepemilikan</h6>
                    
                    {{-- BARU: Nama Bukti --}}
                    <div class="form-group">
                        <label for="bukti_nama">Nama Bukti (Sertifikat/dll)</label>
                        <input type="text" class="form-control @error('bukti_nama') is-invalid @enderror" id="bukti_nama" name="bukti_nama" value="{{ old('bukti_nama') }}">
                        @error('bukti_nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="bukti_nomor">Nomor</label>
                        <input type="text" class="form-control @error('bukti_nomor') is-invalid @enderror" id="bukti_nomor" name="bukti_nomor" value="{{ old('bukti_nomor') }}">
                        @error('bukti_nomor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                     <div class="form-group">
                        <label for="bukti_tanggal">Tanggal</label>
                        <input type="date" class="form-control @error('bukti_tanggal') is-invalid @enderror" id="bukti_tanggal" name="bukti_tanggal" value="{{ old('bukti_tanggal') }}">
                        @error('bukti_tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    {{-- REVISI: name="nama_kepemilikan_dokumen" --}}
                    <div class="form-group">
                        <label for="nama_kepemilikan_dokumen">Nama Kepemilikan</label>
                        <input type="text" class="form-control @error('nama_kepemilikan_dokumen') is-invalid @enderror" id="nama_kepemilikan_dokumen" name="nama_kepemilikan_dokumen" value="{{ old('nama_kepemilikan_dokumen') }}">
                        @error('nama_kepemilikan_dokumen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="harga_satuan">Harga Satuan (Rp)</label>
                        <input type="number" class="form-control @error('harga_satuan') is-invalid @enderror" id="harga_satuan" name="harga_satuan" value="{{ old('harga_satuan') }}">
                        @error('harga_satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nilai_perolehan">Nilai Perolehan (Rp)</label>
                        <input type="number" class="form-control @error('nilai_perolehan') is-invalid @enderror" id="nilai_perolehan" name="nilai_perolehan" value="{{ old('nilai_perolehan') }}">
                        @error('nilai_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="cara_perolehan">Cara Perolehan</label>
                        <input type="text" class="form-control @error('cara_perolehan') is-invalid @enderror" id="cara_perolehan" name="cara_perolehan" value="{{ old('cara_perolehan') }}">
                        @error('cara_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="tanggal_perolehan">Tanggal Perolehan</label>
                        <input type="date" class="form-control @error('tanggal_perolehan') is-invalid @enderror" id="tanggal_perolehan" name="tanggal_perolehan" value="{{ old('tanggal_perolehan') }}">
                        @error('tanggal_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="tanggal_penggunaan">Tanggal Penggunaan</label>
                        <input type="date" class="form-control @error('tanggal_penggunaan') is-invalid @enderror" id="tanggal_penggunaan" name="tanggal_penggunaan" value="{{ old('tanggal_penggunaan') }}">
                        @error('tanggal_penggunaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    {{-- REVISI: name="status_penggunaan" --}}
                     <div class="form-group">
                        <label for="status_penggunaan">Status</label>
                        <input type="text" class="form-control @error('status_penggunaan') is-invalid @enderror" id="status_penggunaan" name="status_penggunaan" value="{{ old('status_penggunaan') }}">
                        @error('status_penggunaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="5">{{ old('keterangan') }}</textarea>
                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <hr>
            <a href="{{ route('lokasi.tanah.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection