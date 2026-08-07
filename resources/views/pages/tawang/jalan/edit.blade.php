@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-warning">
        <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-edit mr-2"></i>Ubah Data KIB D (Jalan, Irigasi & Jaringan) - {{ ucfirst($lokasi) }}</h6>
        <a href="{{ route('lokasi.jalan.index', ['lokasi' => $lokasi]) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card-body" style="color: #000;">
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
                {{-- KOLOM KIRI: Informasi Utama Barang --}}
                <div class="col-md-6 border-right pr-4">
                    <h5 class="font-weight-bold text-primary border-bottom pb-2 mb-3"><i class="fas fa-road mr-2"></i>Informasi Utama Barang</h5>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" name="kode_barang" class="form-control bg-light font-weight-bold text-primary" value="{{ old('kode_barang', $jalan->jalan_kode_barang) }}" required readonly title="Kode Barang tidak dapat diubah">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang', $jalan->jalan_nama_barang) }}" required placeholder="Contoh: Jalan Aspal / Irigasi Permukaan">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">No. Register <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_register" class="form-control bg-light" value="{{ old('nomor_register', $jalan->jalan_nomor_register) }}" required readonly placeholder="0001">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Spesifikasi Barang (Luas/Deskripsi)</label>
                        <textarea name="spesifikasi_barang" class="form-control" rows="2" placeholder="Contoh: Panjang 1.5Km, Lebar 4M">{{ old('spesifikasi_barang', $jalan->jalan_spesifikasi_barang) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Spesifikasi Lainnya</label>
                        <textarea name="spesifikasi_lainnya" class="form-control" rows="2" placeholder="Spesifikasi tambahan jika ada">{{ old('spesifikasi_lainnya', $jalan->jalan_spesifikasi_lainnya) }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="font-weight-bold">Nomor Ruas</label>
                            <input type="text" name="nomor_ruas_jalan_jembatan_irigasi" class="form-control" value="{{ old('nomor_ruas_jalan_jembatan_irigasi', $jalan->jalan_nomor_ruas_jalan_jembatan_irigasi) }}" placeholder="Contoh: Ruas 012">
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: Lokasi & Nilai Ekonomis --}}
                <div class="col-md-6 pl-4">
                    <h5 class="font-weight-bold text-primary border-bottom pb-2 mb-3"><i class="fas fa-map-marked-alt mr-2"></i>Lokasi & Nilai Ekonomis</h5>

                    <div class="form-group">
                        <label class="font-weight-bold">Lokasi Fisik (Alamat Lengkap) <span class="text-danger">*</span></label>
                        <textarea name="Lok" class="form-control" rows="2" required placeholder="Nama Jalan / Lokasi Jaringan">{{ old('Lok', $jalan->jalan_lokasi_fisik) }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Titik Koordinat</label>
                            <input type="text" name="titik_koordinat" class="form-control" value="{{ old('titik_koordinat', $jalan->jalan_titik_koordinat) }}" placeholder="Contoh: -7.329, 108.214">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Status Kepemilikan Tanah</label>
                            <input type="text" name="status_kepemilikan_tanah" class="form-control" value="{{ old('status_kepemilikan_tanah', $jalan->jalan_status_kepemilikan_tanah) }}" placeholder="Contoh: Pemkab">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Jumlah Barang <span class="text-danger">*</span></label>
                            <input type="number" id="jumlah" name="jumlah" class="form-control" value="{{ old('jumlah', (int)$jalan->jalan_jumlah) }}" required min="1">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Satuan <span class="text-danger">*</span></label>
                            <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $jalan->jalan_satuan) }}" required placeholder="Buah / M / Paket">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" id="harga_satuan" name="harga_satuan" class="form-control" value="{{ old('harga_satuan', (float)$jalan->jalan_harga_satuan) }}" required min="0" step="0.01">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold">Nilai Perolehan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" id="nilai_perolehan" name="nilai_perolehan" class="form-control bg-light" value="{{ old('nilai_perolehan', (float)$jalan->jalan_nilai_perolehan) }}" required min="0" step="0.01" readonly placeholder="Otomatis">
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

                    <div class="form-group">
                        <label class="font-weight-bold">Status Penggunaan</label>
                        <select name="status_penggunaan" class="form-control">
                            <option value="">-- Pilih Status Penggunaan --</option>
                            @php $currentStatus = old('status_penggunaan', $jalan->jalan_status_penggunaan); @endphp
                            <option value="Digunakan untuk Kepentingan Umum" {{ $currentStatus == 'Digunakan untuk Kepentingan Umum' ? 'selected' : '' }}>Digunakan untuk Kepentingan Umum</option>
                            <option value="Digunakan untuk Operasional" {{ $currentStatus == 'Digunakan untuk Operasional' ? 'selected' : '' }}>Digunakan untuk Operasional</option>
                            <option value="Dalam Perawatan" {{ $currentStatus == 'Dalam Perawatan' ? 'selected' : '' }}>Dalam Perawatan</option>
                            <option value="Tidak Digunakan" {{ $currentStatus == 'Tidak Digunakan' ? 'selected' : '' }}>Tidak Digunakan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $jalan->jalan_keterangan) }}</textarea>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" class="btn btn-warning px-4 font-weight-bold text-dark"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputJumlah = document.getElementById('jumlah');
        const inputHargaSatuan = document.getElementById('harga_satuan');
        const inputNilaiPerolehan = document.getElementById('nilai_perolehan');

        function hitungNilaiPerolehan() {
            const jumlah = parseFloat(inputJumlah.value) || 0;
            const hargaSatuan = parseFloat(inputHargaSatuan.value) || 0;
            const total = jumlah * hargaSatuan;
            inputNilaiPerolehan.value = total > 0 ? total.toFixed(2) : '';
        }

        // Panggil fungsi saat ada perubahan ketikan pada Jumlah / Harga
        inputJumlah.addEventListener('input', hitungNilaiPerolehan);
        inputHargaSatuan.addEventListener('input', hitungNilaiPerolehan);
    });
</script>
@endsection