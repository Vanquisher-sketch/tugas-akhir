@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Jalan, Irigasi & Jaringan - Kode: {{ $jalan->kode_barang }}</h6>
        <a href="{{ route('lokasi.jalan.index', ['lokasi' => $lokasi]) }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.jalan.update', ['lokasi' => $lokasi, 'jalan' => $jalan->kode_barang]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="lokasi" value="{{ $lokasi }}">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" name="kode_barang" class="form-control @error('kode_barang') is-invalid @enderror" value="{{ old('kode_barang', $jalan->kode_barang) }}" readonly>
                        @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">No. Register <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_register" class="form-control @error('nomor_register') is-invalid @enderror" value="{{ old('nomor_register', $jalan->nomor_register) }}" required>
                        @error('nomor_register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang', $jalan->nama_barang) }}" required>
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nibar (Nomor Ikatan Barang)</label>
                        <input type="text" name="nibar" class="form-control @error('nibar') is-invalid @enderror" value="{{ old('nibar', $jalan->nibar) }}">
                        @error('nibar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Spesifikasi Barang</label>
                        <input type="text" name="spesifikasi_barang" class="form-control @error('spesifikasi_barang') is-invalid @enderror" value="{{ old('spesifikasi_barang', $jalan->spesifikasi_barang) }}">
                        @error('spesifikasi_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Spesifikasi Lainnya</label>
                        <input type="text" name="spesifikasi_lainnya" class="form-control @error('spesifikasi_lainnya') is-invalid @enderror" value="{{ old('spesifikasi_lainnya', $jalan->spesifikasi_lainnya) }}">
                        @error('spesifikasi_lainnya') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nomor Ruas Jalan/Jembatan/Irigasi</label>
                        <input type="text" name="nomor_ruas_jalan_jembatan_irigasi" class="form-control @error('nomor_ruas_jalan_jembatan_irigasi') is-invalid @enderror" value="{{ old('nomor_ruas_jalan_jembatan_irigasi', $jalan->nomor_ruas_jalan_jembatan_irigasi) }}">
                        @error('nomor_ruas_jalan_jembatan_irigasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Alamat / Lokasi Fisik (Lok) <span class="text-danger">*</span></label>
                        <input type="text" name="Lok" class="form-control @error('Lok') is-invalid @enderror" value="{{ old('Lok', $jalan->Lok) }}" required>
                        @error('Lok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Titik Koordinat</label>
                        <input type="text" name="titik_koordinat" class="form-control @error('titik_koordinat') is-invalid @enderror" value="{{ old('titik_koordinat', $jalan->titik_koordinat) }}">
                        @error('titik_koordinat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Status Kepemilikan Tanah</label>
                        <input type="text" name="status_kepemilikan_tanah" class="form-control @error('status_kepemilikan_tanah') is-invalid @enderror" value="{{ old('status_kepemilikan_tanah', $jalan->status_kepemilikan_tanah) }}">
                        @error('status_kepemilikan_tanah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Volume (Jumlah) <span class="text-danger">*</span></label>
                                <input type="number" min="0" name="jumlah" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', $jalan->jumlah) }}" required>
                                @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Satuan <span class="text-danger">*</span></label>
                                <input type="text" name="satuan" class="form-control @error('satuan') is-invalid @enderror" value="{{ old('satuan', $jalan->satuan) }}" required>
                                @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="harga_satuan" id="harga_satuan" class="form-control @error('harga_satuan') is-invalid @enderror" value="{{ old('harga_satuan', $jalan->harga_satuan) }}" required>
                                @error('harga_satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Nilai Perolehan (Rp) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="nilai_perolehan" id="nilai_perolehan" class="form-control @error('nilai_perolehan') is-invalid @enderror" value="{{ old('nilai_perolehan', $jalan->nilai_perolehan) }}" required readonly>
                                @error('nilai_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Cara Perolehan <span class="text-danger">*</span></label>
                        <input type="text" name="cara_perolehan" class="form-control @error('cara_perolehan') is-invalid @enderror" value="{{ old('cara_perolehan', $jalan->cara_perolehan) }}" required>
                        @error('cara_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Tanggal Perolehan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_perolehan" class="form-control @error('tanggal_perolehan') is-invalid @enderror" value="{{ old('tanggal_perolehan', $jalan->tanggal_perolehan) }}" required>
                        @error('tanggal_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Status Penggunaan</label>
                        <input type="text" name="status_penggunaan" class="form-control @error('status_penggunaan') is-invalid @enderror" value="{{ old('status_penggunaan', $jalan->status_penggunaan) }}">
                        @error('status_penggunaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="form-group mt-2">
                <label class="font-weight-bold text-dark">Keterangan</label>
                <textarea name="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $jalan->keterangan) }}</textarea>
                @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <hr>
            <button type="submit" class="btn btn-warning text-dark font-weight-bold px-4"><i class="fas fa-edit"></i> Perbarui Data</button>
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
            totalInput.value = (jumlah * harga).toFixed(2);
        }

        jumlahInput.addEventListener('input', hitungTotal);
        hargaInput.addEventListener('input', hitungTotal);
    });
</script>
@endsection