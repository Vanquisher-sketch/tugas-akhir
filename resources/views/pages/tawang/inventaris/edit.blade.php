@extends('layouts.app')

@section('content')
<div class="card shadow mb-4 border-left-warning">
    <div class="card-header py-3 text-dark font-weight-bold">
        Edit Inventaris: {{ $inventari->nama_barang }}
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.inventaris.update', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan, 'inventari' => $inventari->kode_barang]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row text-dark">
                {{-- KOLOM KIRI --}}
                <div class="col-md-6 border-right">
                    <div class="form-group text-danger font-weight-bold">
                        <label>Kode Barang (ID Utama)</label>
                        <input type="text" class="form-control border-danger" name="kode_barang" value="{{ old('kode_barang', $inventari->kode_barang) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" class="form-control" name="nama_barang" value="{{ old('nama_barang', $inventari->nama_barang) }}" required>
                    </div>
                    <div class="form-group">
                        <label>NIBAR</label>
                        <input type="text" class="form-control" name="nibar" value="{{ old('nibar', $inventari->nibar) }}">
                    </div>
                    <div class="form-group">
                        <label>Register</label>
                        <input type="text" class="form-control" name="nomor_register" value="{{ old('nomor_register', $inventari->nomor_register) }}">
                    </div>
                </div>

                {{-- KOLOM KANAN --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Merek / Tipe</label>
                        <input type="text" class="form-control" name="merk_tipe" value="{{ old('merk_tipe', $inventari->merk_tipe) }}">
                    </div>
                    <div class="form-group">
                        <label>Tahun Perolehan</label>
                        <input type="number" class="form-control" name="tahun_perolehan" value="{{ old('tahun_perolehan', $inventari->tahun_perolehan) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" class="form-control text-primary font-weight-bold" name="jumlah" value="{{ old('jumlah', $inventari->jumlah) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <select name="satuan" class="form-control" required>
                            @foreach($daftarSatuan as $s)
                                <option value="{{ $s }}" {{ $inventari->satuan == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-right">
                <a href="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-warning shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection