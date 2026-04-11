@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Inventaris Ruangan: {{ $room->name }}</h6>
    </div>
    <div class="card-body">
        {{-- REVISI: Route parameter menggunakan $room->kode_ruangan --}}
        <form action="{{ route('lokasi.inventaris.store', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" id="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" name="nama_barang" value="{{ old('nama_barang') }}" required placeholder="Contoh: Meja Kerja">
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="kode_barang">Kode Barang</label>
                        {{-- REVISI: Ditambahkan ID untuk JavaScript auto-gen --}}
                        <input type="text" id="kode_barang" class="form-control @error('kode_barang') is-invalid @enderror" name="kode_barang" value="{{ old('kode_barang') }}" required>
                        <small class="text-muted">Kode barang otomatis (Inisial-Nomor). Bisa diubah manual.</small>
                        @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nibar">NIBAR (Nomor Inventaris Barang)</label>
                        <input type="text" class="form-control @error('nibar') is-invalid @enderror" name="nibar" value="{{ old('nibar') }}">
                        @error('nibar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nomor_register">Nomor Register</label>
                        <input type="text" class="form-control @error('nomor_register') is-invalid @enderror" name="nomor_register" value="{{ old('nomor_register') }}">
                        @error('nomor_register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="spesifikasi_barang">Spesifikasi Nama Barang</label>
                        <input type="text" class="form-control @error('spesifikasi_barang') is-invalid @enderror" name="spesifikasi_barang" value="{{ old('spesifikasi_barang') }}">
                        @error('spesifikasi_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="merk_tipe">Merek / Tipe</label>
                        <input type="text" class="form-control @error('merk_tipe') is-invalid @enderror" name="merk_tipe" value="{{ old('merk_tipe') }}">
                        @error('merk_tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="tahun_perolehan">Tahun Perolehan</label>
                        <input type="number" class="form-control @error('tahun_perolehan') is-invalid @enderror" name="tahun_perolehan" value="{{ old('tahun_perolehan', date('Y')) }}" placeholder="Contoh: 2026">
                        @error('tahun_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah</label>
                        <input type="number" class="form-control @error('jumlah') is-invalid @enderror" name="jumlah" value="{{ old('jumlah', 1) }}">
                        @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="satuan">Satuan</label>
                        <input type="text" class="form-control @error('satuan') is-invalid @enderror" name="satuan" value="{{ old('satuan') }}" placeholder="Contoh: Buah, Unit, Set">
                        @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            <hr>
            {{-- REVISI: Link Batal menggunakan kode_ruangan --}}
            <a href="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary shadow-sm">Simpan Data</button>
        </form>
    </div>
</div>

{{-- SCRIPT AUTO-GENERATE KODE BARANG SINGKAT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const namaBarang = document.getElementById('nama_barang');
        const kodeBarang = document.getElementById('kode_barang');

        namaBarang.addEventListener('keyup', function() {
            let val = namaBarang.value.trim();
            if (val.length > 0) {
                // Ambil inisial (Contoh: "Kursi Lipat" -> "KL")
                let initials = val.split(' ')
                                  .filter(word => word.length > 0)
                                  .map(word => word.charAt(0))
                                  .join('')
                                  .toUpperCase();

                // Tambah nomor acak agar tidak bentrok sebagai Primary Key
                let rand = Math.floor(100 + Math.random() * 900);
                
                kodeBarang.value = `${initials}-${rand}`;
            } else {
                kodeBarang.value = '';
            }
        });
    });
</script>
@endsection