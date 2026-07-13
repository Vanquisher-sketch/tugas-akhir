@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">

    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Tambah Data Gedung & Bangunan (KIB C)
        </h6>
    </div>

    <form action="{{ route('lokasi.gedung.store',['lokasi'=>$lokasi]) }}" method="POST">
        @csrf

        <div class="card-body">

            <div class="row">

                <div class="col-md-4 mb-3">
                    <label>Kode Barang</label>
                    <input type="text"
                           name="kode_barang"
                           class="form-control @error('kode_barang') is-invalid @enderror"
                           value="{{ old('kode_barang') }}"
                           required>

                    @error('kode_barang')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label>Lokasi</label>
                    <input type="text"
                           name="lokasi"
                           class="form-control"
                           value="{{ old('lokasi',$lokasi) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Nama Barang</label>
                    <input type="text"
                           name="nama_barang"
                           class="form-control"
                           value="{{ old('nama_barang') }}"
                           required>
                </div>

                <div class="col-md-3 mb-3">
                    <label>NBAR</label>
                    <input type="text"
                           name="nbar"
                           class="form-control"
                           value="{{ old('nbar') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label>No Register</label>
                    <input type="text"
                           name="nomor_register"
                           class="form-control"
                           value="{{ old('nomor_register') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Spesifikasi Barang</label>
                    <input type="text"
                           name="spesifikasi_barang"
                           class="form-control"
                           value="{{ old('spesifikasi_barang') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Spesifikasi Lainnya</label>
                    <input type="text"
                           name="spesifikasi_lainnya"
                           class="form-control"
                           value="{{ old('spesifikasi_lainnya') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Jumlah Lantai</label>
                    <input type="number"
                           name="jumlah_lantai"
                           class="form-control"
                           value="{{ old('jumlah_lantai') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Lokasi Fisik</label>
                    <input type="text"
                           name="Lok"
                           class="form-control"
                           value="{{ old('Lok') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Titik Koordinat</label>
                    <input type="text"
                           name="titik_koordinat"
                           class="form-control"
                           value="{{ old('titik_koordinat') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Status Kepemilikan Tanah</label>
                    <input type="text"
                           name="status_kepemilikan_tanah"
                           class="form-control"
                           value="{{ old('status_kepemilikan_tanah') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Jumlah</label>
                    <input type="number"
                           name="jumlah"
                           class="form-control"
                           value="{{ old('jumlah',1) }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Satuan</label>
                    <input type="text"
                           name="satuan"
                           class="form-control"
                           value="{{ old('satuan') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Harga Satuan</label>
                    <input type="number"
                           step="0.01"
                           name="harga_satuan"
                           class="form-control"
                           value="{{ old('harga_satuan') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Nilai Perolehan</label>
                    <input type="number"
                           step="0.01"
                           name="nilai_perolehan"
                           class="form-control"
                           value="{{ old('nilai_perolehan') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Cara Perolehan</label>
                    <input type="text"
                           name="cara_perolehan"
                           class="form-control"
                           value="{{ old('cara_perolehan') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Tanggal Perolehan</label>
                    <input type="date"
                           name="tanggal_perolehan"
                           class="form-control"
                           value="{{ old('tanggal_perolehan') }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Status Penggunaan</label>
                    <select name="status_penggunaan" class="form-control">
                        <option value="">--Pilih--</option>
                        <option value="Digunakan">Digunakan</option>
                        <option value="Tidak Digunakan">Tidak Digunakan</option>
                    </select>
                </div>

                <div class="col-12 mb-3">
                    <label>Keterangan</label>
                    <textarea name="keterangan"
                              rows="3"
                              class="form-control">{{ old('keterangan') }}</textarea>
                </div>

            </div>

        </div>

        <div class="card-footer text-right">

            <a href="{{ route('lokasi.gedung.index',['lokasi'=>$lokasi]) }}"
               class="btn btn-secondary">
                Kembali
            </a>

            <button class="btn btn-primary">
                Simpan
            </button>

        </div>

    </form>

</div>
@endsection