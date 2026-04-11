@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Inventaris Ruangan: {{ $room->name }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.inventaris.store', ['lokasi' => $lokasi, 'room' => $room->id]) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" name="nama_barang" value="{{ old('nama_barang') }}" required>
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" name="kode_barang" value="{{ old('kode_barang') }}">
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
                        <input type="number" class="form-control @error('tahun_perolehan') is-invalid @enderror" name="tahun_perolehan" value="{{ old('tahun_perolehan') }}" placeholder="Contoh: 2024">
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
            <a href="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->id]) }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection