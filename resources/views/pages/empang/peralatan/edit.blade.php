@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Peralatan & Mesin (KIB B) - {{ ucfirst($lokasi) }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.peralatan.update', ['lokasi' => $lokasi, 'peralatan' => $peralatan->id]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                {{-- KOLOM KIRI --}}
                <div class="col-md-6">
                    <h6 class="font-weight-bold text-gray-800 mb-3">Identitas & Spesifikasi Barang</h6>
                    
                    <div class="form-group">
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" id="kode_barang" name="kode_barang" value="{{ old('kode_barang', $peralatan->kode_barang) }}">
                        @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $peralatan->nama_barang) }}" required>
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nibr">NIBR</label>
                        <input type="text" class="form-control @error('nibr') is-invalid @enderror" id="nibr" name="nibr" value="{{ old('nibr', $peralatan->nibr) }}">
                        @error('nibr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nomor_register">Nomor Register</label>
                        <input type="text" class="form-control @error('nomor_register') is-invalid @enderror" id="nomor_register" name="nomor_register" value="{{ old('nomor_register', $peralatan->nomor_register) }}">
                        @error('nomor_register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr class="my-4">

                    <div class="form-group">
                        <label for="merk_tipe">Merk / Tipe</label>
                        <input type="text" class="form-control @error('merk_tipe') is-invalid @enderror" id="merk_tipe" name="merk_tipe" value="{{ old('merk_tipe', $peralatan->merk_tipe) }}">
                        @error('merk_tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="spesifikasi_barang">Spesifikasi Barang</label>
                        <input type="text" class="form-control @error('spesifikasi_barang') is-invalid @enderror" id="spesifikasi_barang" name="spesifikasi_barang" value="{{ old('spesifikasi_barang', $peralatan->spesifikasi_barang) }}">
                        @error('spesifikasi_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="spesifikasi_lainnya">Spesifikasi Lainnya</label>
                        <input type="text" class="form-control @error('spesifikasi_lainnya') is-invalid @enderror" id="spesifikasi_lainnya" name="spesifikasi_lainnya" value="{{ old('spesifikasi_lainnya', $peralatan->spesifikasi_lainnya) }}">
                        @error('spesifikasi_lainnya') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="Lok">Lokasi Fisik / Alamat Barang</label>
                        <textarea class="form-control @error('Lok') is-invalid @enderror" id="Lok" name="Lok" rows="2">{{ old('Lok', $peralatan->Lok) }}</textarea>
                        @error('Lok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="col-md-6">
                    <h6 class="font-weight-bold text-gray-800 mb-3">Detail Nomor Kendaraan (Jika Ada)</h6>
                    
                    <div class="form-group">
                        <label for="nomor_rangka">Nomor Rangka</label>
                        <input type="text" class="form-control @error('nomor_rangka') is-invalid @enderror" id="nomor_rangka" name="nomor_rangka" value="{{ old('nomor_rangka', $peralatan->nomor_rangka) }}">
                        @error('nomor_rangka') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomor_polisi">Nomor Polisi</label>
                                <input type="text" class="form-control @error('nomor_polisi') is-invalid @enderror" id="nomor_polisi" name="nomor_polisi" value="{{ old('nomor_polisi', $peralatan->nomor_polisi) }}">
                                @error('nomor_polisi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomor_bpkb">Nomor BPKB</label>
                                <input type="text" class="form-control @error('nomor_bpkb') is-invalid @enderror" id="nomor_bpkb" name="nomor_bpkb" value="{{ old('nomor_bpkb', $peralatan->nomor_bpkb) }}">
                                @error('nomor_bpkb') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="font-weight-bold text-gray-800 mb-3">Perolehan & Status</h6>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jumlah">Jumlah</label>
                                <input type="number" class="form-control @error('jumlah') is-invalid @enderror" id="jumlah" name="jumlah" value="{{ old('jumlah', $peralatan->jumlah) }}">
                                @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="satuan">Satuan</label>
                                <input type="text" class="form-control @error('satuan') is-invalid @enderror" id="satuan" name="satuan" value="{{ old('satuan', $peralatan->satuan) }}">
                                @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga_satuan">Harga Satuan (Rp)</label>
                                <input type="number" class="form-control @error('harga_satuan') is-invalid @enderror" id="harga_satuan" name="harga_satuan" value="{{ old('harga_satuan', $peralatan->harga_satuan) }}">
                                @error('harga_satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nilai_perolehan">Nilai Perolehan (Rp)</label>
                                <input type="number" class="form-control @error('nilai_perolehan') is-invalid @enderror" id="nilai_perolehan" name="nilai_perolehan" value="{{ old('nilai_perolehan', $peralatan->nilai_perolehan) }}">
                                @error('nilai_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cara_perolehan">Cara Perolehan</label>
                        <input type="text" class="form-control @error('cara_perolehan') is-invalid @enderror" id="cara_perolehan" name="cara_perolehan" value="{{ old('cara_perolehan', $peralatan->cara_perolehan) }}">
                        @error('cara_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="tanggal_perolehan">Tanggal Perolehan</label>
                        <input type="date" class="form-control @error('tanggal_perolehan') is-invalid @enderror" id="tanggal_perolehan" name="tanggal_perolehan" value="{{ old('tanggal_perolehan', $peralatan->tanggal_perolehan ? \Carbon\Carbon::parse($peralatan->tanggal_perolehan)->format('Y-m-d') : '') }}">
                        @error('tanggal_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="status_penggunaan">Status Penggunaan</label>
                        <input type="text" class="form-control @error('status_penggunaan') is-invalid @enderror" id="status_penggunaan" name="status_penggunaan" value="{{ old('status_penggunaan', $peralatan->status_penggunaan) }}">
                        @error('status_penggunaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $peralatan->keterangan) }}</textarea>
                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('lokasi.peralatan.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection