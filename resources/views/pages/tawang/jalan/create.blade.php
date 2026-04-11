@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Data Jalan, Irigasi & Jaringan (KIB D) - {{ ucfirst($lokasi) }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.jalan.store', ['lokasi' => $lokasi]) }}" method="POST">
            @csrf
            <div class="row">
                {{-- Kolom Kiri --}}
                <div class="col-md-6 border-right">
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" required placeholder="Contoh: Aspal Jl. Tawang">
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="kode_barang">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" id="kode_barang" name="kode_barang" value="{{ old('kode_barang') }}" required>
                        @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nibar">NIBAR</label>
                                <input type="text" class="form-control" name="nibar" value="{{ old('nibar') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomor_register">Nomor Register</label>
                                <input type="text" class="form-control" name="nomor_register" value="{{ old('nomor_register') }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nomor_ruas_jalan_jembatan_irigasi">Nomor Ruas</label>
                        <input type="text" class="form-control" name="nomor_ruas_jalan_jembatan_irigasi" value="{{ old('nomor_ruas_jalan_jembatan_irigasi') }}" placeholder="No Ruas/Jaringan">
                    </div>

                    <div class="form-group">
                        <label for="Lok">Lokasi (Alamat) <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('Lok') is-invalid @enderror" name="Lok" rows="2" required>{{ old('Lok') }}</textarea>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jumlah">Panjang/Luas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control money" name="jumlah" value="{{ old('jumlah') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="satuan">Satuan</label>
                                <input type="text" class="form-control" name="satuan" value="{{ old('satuan', 'M') }}" placeholder="M / KM / M2">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga_satuan">Harga Satuan (Rp)</label>
                                <input type="text" class="form-control money" name="harga_satuan" value="{{ old('harga_satuan') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nilai_perolehan">Nilai Perolehan (Rp) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control money" name="nilai_perolehan" value="{{ old('nilai_perolehan') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_perolehan">Tanggal Perolehan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_perolehan" value="{{ old('tanggal_perolehan') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-right">
                <a href="{{ route('lokasi.jalan.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const namaInput = document.getElementById('nama_barang');
        const kodeInput = document.getElementById('kode_barang');

        namaInput.addEventListener('keyup', function() {
            if (kodeInput.dataset.manual !== 'true') {
                let val = this.value.trim();
                if (val.length > 0) {
                    let initials = val.split(' ').map(w => w[0]).join('').toUpperCase().substring(0, 3);
                    let random = Math.floor(100 + Math.random() * 900);
                    kodeInput.value = `JLN-${initials}-${random}`;
                }
            }
        });

        kodeInput.addEventListener('input', function() { this.dataset.manual = 'true'; });

        document.querySelectorAll('.money').forEach(input => {
            input.addEventListener('input', function(e) {
                let value = this.value.replace(/[^0-9]/g, '');
                this.value = value ? parseInt(value).toLocaleString('id-ID') : '';
            });
        });
    });
</script>
@endsection