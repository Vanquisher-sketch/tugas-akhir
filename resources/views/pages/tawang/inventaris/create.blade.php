@extends('layouts.app')

@section('content')
<div class="card shadow mb-4 border-left-primary">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Inventaris Ruangan: {{ $room->name }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.inventaris.store', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" method="POST">
            @csrf
            <div class="row text-dark">
                {{-- KOLOM KIRI --}}
                <div class="col-md-6 border-right">
                    <div class="form-group">
                        <label class="font-weight-bold text-primary">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" id="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" name="nama_barang" value="{{ old('nama_barang') }}" required placeholder="Contoh: Meja Kerja">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" id="kode_barang" class="form-control @error('kode_barang') is-invalid @enderror" name="kode_barang" value="{{ old('kode_barang') }}" required>
                    </div>
                    <div class="form-group">
                        <label>NIBAR (Nomor Inventaris Barang)</label>
                        <input type="text" class="form-control" name="nibar" value="{{ old('nibar') }}" placeholder="Contoh: 12.01.xx">
                    </div>
                    <div class="form-group">
                        <label>Nomor Register</label>
                        <input type="text" class="form-control" name="nomor_register" value="{{ old('nomor_register') }}" placeholder="Contoh: 0001">
                    </div>
                    <div class="form-group">
                        <label>Spesifikasi Barang</label>
                        <textarea class="form-control" name="spesifikasi_barang" rows="2" placeholder="Contoh: Bahan Kayu Jati">{{ old('spesifikasi_barang') }}</textarea>
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Merek / Tipe</label>
                        <input type="text" class="form-control" name="merk_tipe" value="{{ old('merk_tipe') }}" placeholder="Contoh: Olympic / L-40">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-primary">Tahun Perolehan <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="tahun_perolehan" value="{{ old('tahun_perolehan', date('Y')) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-primary">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="jumlah" value="{{ old('jumlah', 1) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold text-primary">Satuan <span class="text-danger">*</span></label>
                        <select name="satuan" class="form-control" required>
                            <option value="">-- Pilih Satuan --</option>
                            @foreach($daftarSatuan as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="2" placeholder="Contoh: Kondisi Baik">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>
            <hr>
            <a href="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" class="btn btn-secondary shadow-sm">Batal</a>
            <button type="submit" class="btn btn-primary shadow-sm">Simpan Data</button>
        </form>
    </div>
</div>

{{-- Script Generate Kode Otomatis --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nama = document.getElementById('nama_barang');
        const kode = document.getElementById('kode_barang');
        nama.addEventListener('keyup', function() {
            let val = nama.value.trim();
            if (val.length > 0) {
                let initials = val.split(' ').filter(word => word.length > 0).map(word => word.charAt(0)).join('').toUpperCase();
                let rand = Math.floor(100 + Math.random() * 900);
                kode.value = `${initials}-${rand}`;
            }
        });
    });
</script>
@endsection