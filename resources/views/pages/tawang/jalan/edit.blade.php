@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Jalan & Irigasi - {{ ucfirst($lokasi) }}</h6>
        <a href="{{ route('lokasi.jalan.index', ['lokasi' => $lokasi]) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger pb-0 py-2 mb-4">
                <h6 class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal Memperbarui Data:</h6>
                <ul class="pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('lokasi.jalan.update', ['lokasi' => $lokasi, 'kode_barang' => $jalan->jalan_kode_barang]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- KOLOM KIRI: Identitas & Fisik --}}
                <div class="col-md-6 border-right pr-4">
                    <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Identitas & Fisik Jaringan</h5>

                    <div class="form-group">
                        <label class="font-weight-bold">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" name="kode_barang" class="form-control" value="{{ old('kode_barang', $jalan->jalan_kode_barang) }}" required readonly title="Kode Barang tidak dapat diubah">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang', $jalan->jalan_nama_barang) }}" required>
                    </div>

                    <div class="form-row">
                        
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Nomor Register <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_register" class="form-control" value="{{ old('nomor_register', $jalan->jalan_nomor_register) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Spesifikasi Barang</label>
                        <textarea name="spesifikasi_barang" class="form-control" rows="2">{{ old('spesifikasi_barang', $jalan->jalan_spesifikasi_barang) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Spesifikasi Lainnya</label>
                        <textarea name="spesifikasi_lainnya" class="form-control" rows="2">{{ old('spesifikasi_lainnya', $jalan->jalan_spesifikasi_lainnya) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Lokasi Fisik / Alamat Ruas <span class="text-danger">*</span></label>
                        <textarea name="Lok" class="form-control" rows="2" required>{{ old('Lok', $jalan->jalan_lokasi_fisik) }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label class="font-weight-bold">Nomor Ruas</label>
                            <input type="text" name="nomor_ruas_jalan_jembatan_irigasi" class="form-control" value="{{ old('nomor_ruas_jalan_jembatan_irigasi', $jalan->jalan_nomor_ruas_jalan_jembatan_irigasi) }}">
                        </div>
                        <div class="form-group col-md-7">
                            <label class="font-weight-bold">Titik Koordinat</label>
                            <input type="text" name="titik_koordinat" class="form-control" value="{{ old('titik_koordinat', $jalan->jalan_titik_koordinat) }}">
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: Nilai, Perolehan & Status --}}
                <div class="col-md-6 pl-4">
                    <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Perolehan & Nilai Aset</h5>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Volume / Jumlah <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" class="form-control" value="{{ old('jumlah', $jalan->jalan_jumlah) }}" required min="1">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $jalan->jalan_satuan) }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="harga_satuan" class="form-control" value="{{ old('harga_satuan', $jalan->jalan_harga_satuan) }}" required min="0" step="0.01">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Nilai Perolehan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="nilai_perolehan" class="form-control" value="{{ old('nilai_perolehan', $jalan->jalan_nilai_perolehan) }}" required min="0" step="0.01">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Cara Perolehan <span class="text-danger">*</span></label>
                            <select name="cara_perolehan" class="form-control" required>
                                <option value="">-- Pilih --</option>
                                <option value="Pembangunan" {{ old('cara_perolehan', $jalan->jalan_cara_perolehan) == 'Pembangunan' ? 'selected' : '' }}>Pembangunan</option>
                                <option value="Pembelian" {{ old('cara_perolehan', $jalan->jalan_cara_perolehan) == 'Pembelian' ? 'selected' : '' }}>Pembelian</option>
                                <option value="Hibah" {{ old('cara_perolehan', $jalan->jalan_cara_perolehan) == 'Hibah' ? 'selected' : '' }}>Hibah</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Tanggal Perolehan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_perolehan" class="form-control" value="{{ old('tanggal_perolehan', $jalan->jalan_tanggal_perolehan ? \Carbon\Carbon::parse($jalan->jalan_tanggal_perolehan)->format('Y-m-d') : '') }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Status Kepemilikan Tanah</label>
                            <input type="text" name="status_kepemilikan_tanah" class="form-control" value="{{ old('status_kepemilikan_tanah', $jalan->jalan_status_kepemilikan_tanah) }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Status Penggunaan</label>
                            <select name="status_penggunaan" class="form-control">
                                <option value="Digunakan" {{ old('status_penggunaan', $jalan->jalan_status_penggunaan) == 'Digunakan' ? 'selected' : '' }}>Digunakan</option>
                                <option value="Mangkrak" {{ old('status_penggunaan', $jalan->jalan_status_penggunaan) == 'Mangkrak' ? 'selected' : '' }}>Mangkrak</option>
                                <option value="Rusak" {{ old('status_penggunaan', $jalan->jalan_status_penggunaan) == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $jalan->jalan_keterangan) }}</textarea>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-warning px-4 font-weight-bold"><i class="fas fa-edit"></i> Perbarui Data Jalan</button>
            </div>
        </form>
    </div>
</div>
@endsection