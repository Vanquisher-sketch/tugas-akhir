@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Data Gedung & Bangunan</h1>
        <a href="{{ route('lokasi.gedung.index', ['lokasi' => $lokasi]) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">Perbarui Data KIB C: {{ $gedung->gedung_kode_barang }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('lokasi.gedung.update', ['lokasi' => $lokasi, 'kode_barang' => $gedung->gedung_kode_barang]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 border-right">
                        <h5 class="font-weight-bold text-dark mb-3">Informasi Utama</h5>
                        
                        <div class="form-group">
                            <label for="kode_barang" class="font-weight-bold">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" name="kode_barang" id="kode_barang" class="form-control @error('kode_barang') is-invalid @enderror" value="{{ old('kode_barang', $gedung->gedung_kode_barang) }}" maxlength="30" required>
                            @error('kode_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nama_barang" class="font-weight-bold">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" id="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang', $gedung->gedung_nama_barang) }}" maxlength="100" required>
                            @error('nama_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nbar" class="font-weight-bold">NBAR</label>
                                    <input type="text" name="nbar" id="nbar" class="form-control @error('nbar') is-invalid @enderror" value="{{ old('nbar', $gedung->gedung_nibar) }}" maxlength="30">
                                    @error('nbar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nomor_register" class="font-weight-bold">Nomor Register <span class="text-danger">*</span></label>
                                    <input type="text" name="nomor_register" id="nomor_register" class="form-control @error('nomor_register') is-invalid @enderror" value="{{ old('nomor_register', $gedung->gedung_nomor_register) }}" maxlength="20" required>
                                    @error('nomor_register')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="spesifikasi_barang" class="font-weight-bold">Spesifikasi Barang</label>
                            <input type="text" name="spesifikasi_barang" id="spesifikasi_barang" class="form-control @error('spesifikasi_barang') is-invalid @enderror" value="{{ old('spesifikasi_barang', $gedung->gedung_spesifikasi_barang) }}" maxlength="255">
                            @error('spesifikasi_barang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label placeholder="Lain-lain" for="spesifikasi_lainnya" class="font-weight-bold">Spesifikasi Lainnya</label>
                            <input type="text" name="spesifikasi_lainnya" id="spesifikasi_lainnya" class="form-control @error('spesifikasi_lainnya') is-invalid @enderror" value="{{ old('spesifikasi_lainnya', $gedung->gedung_spesifikasi_lainnya) }}" maxlength="255">
                            @error('spesifikasi_lainnya')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jumlah_lantai" class="font-weight-bold">Jumlah Lantai</label>
                                    <input type="number" name="jumlah_lantai" id="jumlah_lantai" class="form-control @error('jumlah_lantai') is-invalid @enderror" value="{{ old('jumlah_lantai', $gedung->gedung_jumlah_lantai) }}" min="0">
                                    @error('jumlah_lantai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status_penggunaan" class="font-weight-bold">Status Penggunaan</label>
                                    <select name="status_penggunaan" id="status_penggunaan" class="form-control @error('status_penggunaan') is-invalid @enderror">
                                        @php $currentStatus = old('status_penggunaan', $gedung->gedung_status_penggunaan); @endphp
                                        <option value="Digunakan Sendiri" {{ $currentStatus == 'Digunakan Sendiri' ? 'selected' : '' }}>Digunakan Sendiri</option>
                                        <option value="Dipinjamkan" {{ $currentStatus == 'Dipinjamkan' ? 'selected' : '' }}>Dipinjamkan</option>
                                        <option value="Disewakan" {{ $currentStatus == 'Disewakan' ? 'selected' : '' }}>Disewakan</option>
                                        <option value="Tidak Digunakan" {{ $currentStatus == 'Tidak Digunakan' ? 'selected' : '' }}>Tidak Digunakan</option>
                                    </select>
                                    @error('status_penggunaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="font-weight-bold text-dark mb-3">Lokasi & Nilai Ekonomis</h5>

                        <div class="form-group">
                            <label for="Lok" class="font-weight-bold">Lokasi Fisik (Alamat Lengkap) <span class="text-danger">*</span></label>
                            <input type="text" name="Lok" id="Lok" class="form-control @error('Lok') is-invalid @enderror" value="{{ old('Lok', $gedung->gedung_lokasi_fisik) }}" maxlength="255" required>
                            @error('Lok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titik_koordinat" class="font-weight-bold">Titik Koordinat</label>
                                    <input type="text" name="titik_koordinat" id="titik_koordinat" class="form-control @error('titik_koordinat') is-invalid @enderror" value="{{ old('titik_koordinat', $gedung->gedung_titik_koordinat) }}" placeholder="Contoh: -7.329, 108.214" maxlength="50">
                                    @error('titik_koordinat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status_kepemilikan_tanah" class="font-weight-bold">Status Kepemilikan Tanah</label>
                                    <input type="text" name="status_kepemilikan_tanah" id="status_kepemilikan_tanah" class="form-control @error('status_kepemilikan_tanah') is-invalid @enderror" value="{{ old('status_kepemilikan_tanah', $gedung->gedung_status_kepemilikan_tanah) }}" maxlength="50">
                                    @error('status_kepemilikan_tanah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jumlah" class="font-weight-bold">Jumlah Barang <span class="text-danger">*</span></label>
                                    <input type="text" name="jumlah" id="jumlah" class="form-control currency-format @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', $gedung->gedung_jumlah) }}" required>
                                    @error('jumlah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="satuan" class="font-weight-bold">Satuan <span class="text-danger">*</span></label>
                                    <input type="text" name="satuan" id="satuan" class="form-control @error('satuan') is-invalid @enderror" value="{{ old('satuan', $gedung->gedung_satuan) }}" maxlength="20" required>
                                    @error('satuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="harga_satuan" class="font-weight-bold">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                                    <input type="text" name="harga_satuan" id="harga_satuan" class="form-control currency-format @error('harga_satuan') is-invalid @enderror" value="{{ old('harga_satuan', (int)$gedung->gedung_harga_satuan) }}" required>
                                    @error('harga_satuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nilai_perolehan" class="font-weight-bold">Nilai Perolehan (Rp) <span class="text-danger">*</span></label>
                                    <input type="text" name="nilai_perolehan" id="nilai_perolehan" class="form-control currency-format @error('nilai_perolehan') is-invalid @enderror" value="{{ old('nilai_perolehan', (int)$gedung->gedung_nilai_perolehan) }}" required>
                                    @error('nilai_perolehan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cara_perolehan" class="font-weight-bold">Cara Perolehan <span class="text-danger">*</span></label>
                                    <input type="text" name="cara_perolehan" id="cara_perolehan" class="form-control @error('cara_perolehan') is-invalid @enderror" value="{{ old('cara_perolehan', $gedung->gedung_cara_perolehan) }}" maxlength="50" required>
                                    @error('cara_perolehan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_perolehan" class="font-weight-bold">Tanggal Perolehan <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_perolehan" id="tanggal_perolehan" class="form-control @error('tanggal_perolehan') is-invalid @enderror" value="{{ old('tanggal_perolehan', $gedung->gedung_tanggal_perolehan) }}" required>
                                    @error('tanggal_perolehan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="keterangan" class="font-weight-bold">Keterangan Tambahan</label>
                            <textarea name="keterangan" id="keterangan" rows="2" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $gedung->gedung_keterangan) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('lokasi.gedung.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-warning text-dark font-weight-bold px-4">Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currencyInputs = document.querySelectorAll('.currency-format');
        
        currencyInputs.forEach(input => {
            // Langsung memformat nilai asli dari database saat halaman selesai dimuat
            if(input.value) {
                input.value = formatRupiah(input.value.toString());
            }

            input.addEventListener('keyup', function(e) {
                this.value = formatRupiah(this.value);
            });
        });

        function formatRupiah(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa  = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }
    });
</script>
@endsection