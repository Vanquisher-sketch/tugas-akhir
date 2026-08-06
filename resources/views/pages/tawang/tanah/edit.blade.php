@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ubah Data KIB A (Tanah) - {{ ucfirst($lokasi) }}</h1>
        <a href="{{ route('lokasi.tanah.index', ['lokasi' => $lokasi]) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-warning text-dark">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-edit mr-2"></i>Form Edit Data Tanah (Kode: {{ $tanah->tanah_kode_barang }})</h6>
        </div>
        <div class="card-body" style="color: #000;">
            <form action="{{ route('lokasi.tanah.update', ['lokasi' => $lokasi, 'kode_barang' => $tanah->tanah_kode_barang]) }}" method="POST">
                @csrf
                @method('PUT')

                <h5 class="font-weight-bold border-bottom pb-2 mb-3 text-primary"><i class="fas fa-barcode mr-2"></i>Informasi Utama Barang</h5>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" name="tanah_kode_barang" class="form-control @error('tanah_kode_barang') is-invalid @enderror" value="{{ old('tanah_kode_barang', $tanah->tanah_kode_barang) }}" maxlength="30" required>
                        @error('tanah_kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="tanah_nama_barang" class="form-control @error('tanah_nama_barang') is-invalid @enderror" value="{{ old('tanah_nama_barang', $tanah->tanah_nama_barang) }}" maxlength="100" required>
                        @error('tanah_nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-2">
                        <label class="font-weight-bold">No. Register</label>
                        <input type="text" name="tanah_nomor_register" class="form-control @error('tanah_nomor_register') is-invalid @enderror" value="{{ old('tanah_nomor_register', $tanah->tanah_nomor_register) }}" maxlength="20">
                        @error('tanah_nomor_register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">Spesifikasi Barang (Luas/Deskripsi)</label>
                        <input type="text" name="tanah_spesifikasi_barang" class="form-control @error('tanah_spesifikasi_barang') is-invalid @enderror" value="{{ old('tanah_spesifikasi_barang', $tanah->tanah_spesifikasi_barang) }}" maxlength="255">
                        @error('tanah_spesifikasi_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">Spesifikasi Lainnya</label>
                        <input type="text" name="tanah_spesifikasi_lainnya" class="form-control @error('tanah_spesifikasi_lainnya') is-invalid @enderror" value="{{ old('tanah_spesifikasi_lainnya', $tanah->tanah_spesifikasi_lainnya) }}" maxlength="255">
                        @error('tanah_spesifikasi_lainnya') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Luas (Volume / Kuantitas) <span class="text-danger">*</span></label>
                        <input type="text" name="tanah_jumlah" class="form-control mask-currency @error('tanah_jumlah') is-invalid @enderror" value="{{ old('tanah_jumlah', (int)$tanah->tanah_jumlah) }}" required>
                        @error('tanah_jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="tanah_satuan" class="form-control @error('tanah_satuan') is-invalid @enderror" value="{{ old('tanah_satuan', $tanah->tanah_satuan) }}" maxlength="20" required>
                        @error('tanah_satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Status Penggunaan <span class="text-danger">*</span></label>
                        <select name="tanah_status_penggunaan" class="form-control @error('tanah_status_penggunaan') is-invalid @enderror" required>
                            <option value="">-- Pilih Status Penggunaan --</option>
                            @php $currentStatus = old('tanah_status_penggunaan', $tanah->tanah_status_penggunaan); @endphp
                            
                            {{-- DI SINI PERBAIKANNYA: 100% Sinkron dengan Controller --}}
                            <option value="Digunakan Sendiri" {{ $currentStatus == 'Digunakan Sendiri' ? 'selected' : '' }}>Digunakan Sendiri</option>
                            <option value="Dipinjamkan" {{ $currentStatus == 'Dipinjamkan' ? 'selected' : '' }}>Dipinjamkan</option>
                            <option value="Disewakan" {{ $currentStatus == 'Disewakan' ? 'selected' : '' }}>Disewakan</option>
                            <option value="Tidak Digunakan" {{ $currentStatus == 'Tidak Digunakan' ? 'selected' : '' }}>Tidak Digunakan</option>
                        </select>
                        @error('tanah_status_penggunaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <h5 class="font-weight-bold border-bottom pb-2 mb-3 mt-4 text-primary"><i class="fas fa-map-marked-alt mr-2"></i>Lokasi & Fisik</h5>
                <div class="row">
                    <div class="form-group col-md-8">
                        <label class="font-weight-bold">Alamat Fisik / Lokasi Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="tanah_lokasi_fisik" class="form-control @error('tanah_lokasi_fisik') is-invalid @enderror" value="{{ old('tanah_lokasi_fisik', $tanah->tanah_lokasi_fisik) }}" maxlength="255" required>
                        @error('tanah_lokasi_fisik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Titik Koordinat</label>
                        <input type="text" name="tanah_titik_koordinat" class="form-control @error('tanah_titik_koordinat') is-invalid @enderror" value="{{ old('tanah_titik_koordinat', $tanah->tanah_titik_koordinat) }}" maxlength="50">
                        @error('tanah_titik_koordinat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <h5 class="font-weight-bold border-bottom pb-2 mb-3 mt-4 text-primary"><i class="fas fa-file-invoice mr-2"></i>Legalitas & Bukti Kepemilikan</h5>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label class="font-weight-bold">Jenis Dokumen Bukti</label>
                        <input type="text" name="tanah_bukti_nama" class="form-control @error('tanah_bukti_nama') is-invalid @enderror" value="{{ old('tanah_bukti_nama', $tanah->tanah_bukti_nama) }}" maxlength="50">
                        @error('tanah_bukti_nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="font-weight-bold">Nomor Dokumen Bukti</label>
                        <input type="text" name="tanah_bukti_nomor" class="form-control @error('tanah_bukti_nomor') is-invalid @enderror" value="{{ old('tanah_bukti_nomor', $tanah->tanah_bukti_nomor) }}" maxlength="50">
                        @error('tanah_bukti_nomor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="font-weight-bold">Tanggal Dokumen</label>
                        <input type="date" name="tanah_bukti_tanggal" class="form-control @error('tanah_bukti_tanggal') is-invalid @enderror" value="{{ old('tanah_bukti_tanggal', $tanah->tanah_bukti_tanggal) }}">
                        @error('tanah_bukti_tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="font-weight-bold">Nama di Dokumen Kepemilikan</label>
                        <input type="text" name="tanah_nama_kepemilikan_dokumen" class="form-control @error('tanah_nama_kepemilikan_dokumen') is-invalid @enderror" value="{{ old('tanah_nama_kepemilikan_dokumen', $tanah->tanah_nama_kepemilikan_dokumen) }}" maxlength="100">
                        @error('tanah_nama_kepemilikan_dokumen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <h5 class="font-weight-bold border-bottom pb-2 mb-3 mt-4 text-primary"><i class="fas fa-coins mr-2"></i>Nilai Aset & Perolehan</h5>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Harga Satuan (Rp)</label>
                        <input type="text" name="tanah_harga_satuan" class="form-control mask-currency @error('tanah_harga_satuan') is-invalid @enderror" value="{{ old('tanah_harga_satuan', $tanah->tanah_harga_satuan ? (int)$tanah->tanah_harga_satuan : '') }}">
                        @error('tanah_harga_satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Nilai Perolehan Total (Rp) <span class="text-danger">*</span></label>
                        <input type="text" name="tanah_nilai_perolehan" class="form-control mask-currency @error('tanah_nilai_perolehan') is-invalid @enderror" value="{{ old('tanah_nilai_perolehan', (int)$tanah->tanah_nilai_perolehan) }}" required>
                        @error('tanah_nilai_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Cara Perolehan <span class="text-danger">*</span></label>
                        <input type="text" name="tanah_cara_perolehan" class="form-control @error('tanah_cara_perolehan') is-invalid @enderror" value="{{ old('tanah_cara_perolehan', $tanah->tanah_cara_perolehan) }}" maxlength="50" required>
                        @error('tanah_cara_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Tanggal Perolehan Aset <span class="text-danger">*</span></label>
                        <input type="date" name="tanah_tanggal_perolehan" class="form-control @error('tanah_tanggal_perolehan') is-invalid @enderror" value="{{ old('tanah_tanggal_perolehan', $tanah->tanah_tanggal_perolehan) }}" required>
                        @error('tanah_tanggal_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-md-8">
                        <label class="font-weight-bold">Keterangan</label>
                        <textarea name="tanah_keterangan" class="form-control @error('tanah_keterangan') is-invalid @enderror" rows="2">{{ old('tanah_keterangan', $tanah->tanah_keterangan) }}</textarea>
                        @error('tanah_keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning px-4 font-weight-bold text-dark">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto formatting titik ribuan saat diketik
        document.querySelectorAll('.mask-currency').forEach(function(input) {
            input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if(value !== "") {
                    e.target.value = new Intl.NumberFormat('id-ID').format(value);
                } else {
                    e.target.value = '';
                }
            });

            // Format pemicu otomatis untuk data lama dari DB saat halaman dimuat pertama kali
            if(input.value) {
                let cleanVal = input.value.replace(/\D/g, '');
                if(cleanVal) input.value = new Intl.NumberFormat('id-ID').format(cleanVal);
            }
        });
    });
</script>
@endsection