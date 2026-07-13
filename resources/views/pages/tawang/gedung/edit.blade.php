@extends('layouts.app')

@section('content')

<div class="card shadow mb-4">

    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            Edit Data Gedung & Bangunan (KIB C)
        </h6>
    </div>

    <form action="{{ route('lokasi.gedung.update', ['lokasi' => $lokasi, 'gedung' => $gedung->kode_barang]) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="row">

                {{-- Kode Barang --}}
                <div class="col-md-4 mb-3">
                    <label>Kode Barang</label>
                    <input type="text"
                           name="kode_barang"
                           class="form-control @error('kode_barang') is-invalid @enderror"
                           value="{{ old('kode_barang', $gedung->kode_barang) }}"
                           required>

                    @error('kode_barang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Lokasi --}}
                <div class="col-md-4 mb-3">
                    <label>Lokasi</label>
                    <input type="text"
                           name="lokasi"
                           class="form-control"
                           value="{{ old('lokasi', $gedung->lokasi) }}">
                </div>

                {{-- Nama Barang --}}
                <div class="col-md-4 mb-3">
                    <label>Nama Barang</label>
                    <input type="text"
                           name="nama_barang"
                           class="form-control"
                           value="{{ old('nama_barang', $gedung->nama_barang) }}"
                           required>
                </div>

                {{-- NBAR --}}
                <div class="col-md-3 mb-3">
                    <label>NBAR</label>
                    <input type="text"
                           name="nbar"
                           class="form-control"
                           value="{{ old('nbar', $gedung->nbar) }}">
                </div>

                {{-- Register --}}
                <div class="col-md-3 mb-3">
                    <label>No Register</label>
                    <input type="text"
                           name="nomor_register"
                           class="form-control"
                           value="{{ old('nomor_register', $gedung->nomor_register) }}">
                </div>

                {{-- Spesifikasi --}}
                <div class="col-md-6 mb-3">
                    <label>Spesifikasi Barang</label>
                    <input type="text"
                           name="spesifikasi_barang"
                           class="form-control"
                           value="{{ old('spesifikasi_barang', $gedung->spesifikasi_barang) }}">
                </div>

                {{-- Spesifikasi Lain --}}
                <div class="col-md-6 mb-3">
                    <label>Spesifikasi Lainnya</label>
                    <input type="text"
                           name="spesifikasi_lainnya"
                           class="form-control"
                           value="{{ old('spesifikasi_lainnya', $gedung->spesifikasi_lainnya) }}">
                </div>

                {{-- Jumlah Lantai --}}
                <div class="col-md-3 mb-3">
                    <label>Jumlah Lantai</label>
                    <input type="number"
                           name="jumlah_lantai"
                           class="form-control"
                           value="{{ old('jumlah_lantai', $gedung->jumlah_lantai) }}">
                </div>

                {{-- Lokasi Fisik --}}
                <div class="col-md-3 mb-3">
                    <label>Lokasi (Fisik)</label>
                    <input type="text"
                           name="Lok"
                           class="form-control"
                           value="{{ old('Lok', $gedung->Lok) }}">
                </div>

                {{-- Koordinat --}}
                <div class="col-md-6 mb-3">
                    <label>Titik Koordinat</label>
                    <input type="text"
                           name="titik_koordinat"
                           class="form-control"
                           value="{{ old('titik_koordinat', $gedung->titik_koordinat) }}">
                </div>

                {{-- Status Tanah --}}
                <div class="col-md-6 mb-3">
                    <label>Status Kepemilikan Tanah</label>
                    <input type="text"
                           name="status_kepemilikan_tanah"
                           class="form-control"
                           value="{{ old('status_kepemilikan_tanah', $gedung->status_kepemilikan_tanah) }}">
                </div>

                {{-- Jumlah --}}
                <div class="col-md-3 mb-3">
                    <label>Jumlah</label>
                    <input type="number"
                           name="jumlah"
                           class="form-control"
                           value="{{ old('jumlah', $gedung->jumlah) }}">
                </div>

                {{-- Satuan --}}
                <div class="col-md-3 mb-3">
                    <label>Satuan</label>
                    <input type="text"
                           name="satuan"
                           class="form-control"
                           value="{{ old('satuan', $gedung->satuan) }}">
                </div>

                {{-- Harga Satuan --}}
                <div class="col-md-3 mb-3">
                    <label>Harga Satuan (Rp)</label>
                    <input type="number"
                           step="0.01"
                           name="harga_satuan"
                           class="form-control"
                           value="{{ old('harga_satuan', $gedung->harga_satuan) }}">
                </div>

                {{-- Nilai Perolehan --}}
                <div class="col-md-3 mb-3">
                    <label>Nilai Perolehan (Rp)</label>
                    <input type="number"
                           step="0.01"
                           name="nilai_perolehan"
                           class="form-control"
                           value="{{ old('nilai_perolehan', $gedung->nilai_perolehan) }}">
                </div>

                {{-- Cara Perolehan --}}
                <div class="col-md-4 mb-3">
                    <label>Cara Perolehan</label>
                    <input type="text"
                           name="cara_perolehan"
                           class="form-control"
                           value="{{ old('cara_perolehan', $gedung->cara_perolehan) }}">
                </div>

                {{-- Tanggal --}}
                <div class="col-md-4 mb-3">
                    <label>Tanggal Perolehan</label>
                    <input type="date"
                           name="tanggal_perolehan"
                           class="form-control"
                           value="{{ old('tanggal_perolehan', $gedung->tanggal_perolehan ? \Carbon\Carbon::parse($gedung->tanggal_perolehan)->format('Y-m-d') : '') }}">
                </div>

                {{-- Status Penggunaan --}}
                <div class="col-md-4 mb-3">
                    <label>Status Penggunaan</label>

                    <select name="status_penggunaan" class="form-control">

                        <option value="">-- Pilih --</option>

                        <option value="Digunakan"
                            {{ old('status_penggunaan',$gedung->status_penggunaan)=='Digunakan' ? 'selected' : '' }}>
                            Digunakan
                        </option>

                        <option value="Tidak Digunakan"
                            {{ old('status_penggunaan',$gedung->status_penggunaan)=='Tidak Digunakan' ? 'selected' : '' }}>
                            Tidak Digunakan
                        </option>

                    </select>

                </div>

                {{-- Keterangan --}}
                <div class="col-12 mb-3">

                    <label>Keterangan</label>

                    <textarea name="keterangan"
                              rows="4"
                              class="form-control">{{ old('keterangan',$gedung->keterangan) }}</textarea>

                </div>

            </div>

        </div>

        <div class="card-footer text-right">

            <a href="{{ route('lokasi.gedung.index',['lokasi'=>$lokasi]) }}"
               class="btn btn-secondary">

                <i class="fas fa-arrow-left"></i>
                Kembali

            </a>

            <button type="submit" class="btn btn-primary">

                <i class="fas fa-save"></i>
                Update Data

            </button>

        </div>

    </form>

</div>

@endsection