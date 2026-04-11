@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        {{-- Menampilkan Nama Ruangan sebagai konteks --}}
        <h6 class="m-0 font-weight-bold text-primary">Edit Inventaris Ruangan: {{ $room->name }}</h6>
    </div>
    <div class="card-body">
        {{-- REVISI: Parameter room menggunakan kode_ruangan, dan inventari menggunakan kode_barang --}}
        <form action="{{ route('lokasi.inventaris.update', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan, 'inventari' => $inventari->kode_barang]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" name="nama_barang" value="{{ old('nama_barang', $inventari->nama_barang) }}" required>
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="kode_barang">Kode Barang</label>
                        {{-- Field Kode Barang tetap tampil sebagai identitas utama --}}
                        <input type="text" class="form-control @error('kode_barang') is-invalid @enderror" name="kode_barang" value="{{ old('kode_barang', $inventari->kode_barang) }}" required>
                        <small class="text-danger">Catatan: Mengubah kode barang akan memperbarui identitas unik aset ini di database.</small>
                        @error('kode_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nibar">NIBAR (Nomor Inventaris Barang)</label>
                        <input type="text" class="form-control @error('nibar') is-invalid @enderror" name="nibar" value="{{ old('nibar', $inventari->nibar) }}">
                        @error('nibar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nomor_register">Nomor Register</label>
                        <input type="text" class="form-control @error('nomor_register') is-invalid @enderror" name="nomor_register" value="{{ old('nomor_register', $inventari->nomor_register) }}">
                        @error('nomor_register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="spesifikasi_barang">Spesifikasi Nama Barang</label>
                        <input type="text" class="form-control @error('spesifikasi_barang') is-invalid @enderror" name="spesifikasi_barang" value="{{ old('spesifikasi_barang', $inventari->spesifikasi_barang) }}">
                        @error('spesifikasi_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="merk_tipe">Merek / Tipe</label>
                        <input type="text" class="form-control @error('merk_tipe') is-invalid @enderror" name="merk_tipe" value="{{ old('merk_tipe', $inventari->merk_tipe) }}">
                        @error('merk_tipe') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="tahun_perolehan">Tahun Perolehan</label>
                        <input type="number" class="form-control @error('tahun_perolehan') is-invalid @enderror" name="tahun_perolehan" value="{{ old('tahun_perolehan', $inventari->tahun_perolehan) }}">
                        @error('tahun_perolehan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah</label>
                        <input type="number" class="form-control @error('jumlah') is-invalid @enderror" name="jumlah" value="{{ old('jumlah', $inventari->jumlah) }}">
                        @error('jumlah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="satuan">Satuan</label>
                        <input type="text" class="form-control @error('satuan') is-invalid @enderror" name="satuan" value="{{ old('satuan', $inventari->satuan) }}">
                        @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" name="keterangan" rows="3">{{ old('keterangan', $inventari->keterangan) }}</textarea>
                        @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            <hr>
            {{-- REVISI: Link Batal diarahkan kembali menggunakan kode_ruangan --}}
            <a href="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" class="btn btn-secondary shadow-sm">Batal</a>
            <button type="submit" class="btn btn-primary shadow-sm">Ubah Data Inventaris</button>
        </form>
    </div>
</div>
@endsection