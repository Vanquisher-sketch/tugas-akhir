@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ubah Data Inventaris Ruangan</h1>
        <a href="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-warning text-dark">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Manifes Data: {{ $inventari->nama_barang }}</h6>
        </div>
        <div class="card-body text-dark">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('lokasi.inventaris.update', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan, 'inventari' => $inventari->kode_barang]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    {{-- Kode Barang (Primary Key - Biasanya di-lock/readonly agar PK tidak berubah kacau) --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Kode Barang (PK) <span class="text-danger">*</span></label>
                        <input type="text" name="kode_barang" class="form-control @error('kode_barang') is-invalid @enderror" value="{{ old('kode_barang', $inventari->kode_barang) }}" required>
                    </div>

                    {{-- Nama Barang --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control @error('nama_barang') is-invalid @enderror" value="{{ old('nama_barang', $inventari->nama_barang) }}" required>
                    </div>
                </div>

                <div class="row">
                    {{-- NIBAR --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">NIBAR (Nomor Induk Barang)</label>
                        <input type="text" name="nibar" class="form-control" value="{{ old('nibar', $inventari->nibar) }}">
                    </div>

                    {{-- Nomor Register --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Nomor Register</label>
                        <input type="text" name="nomor_register" class="form-control" value="{{ old('nomor_register', $inventari->nomor_register) }}">
                    </div>
                </div>

                <div class="row">
                    {{-- Merk / Tipe --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Merk / Tipe</label>
                        <input type="text" name="merk_tipe" class="form-control" value="{{ old('merk_tipe', $inventari->merk_tipe) }}">
                    </div>

                    {{-- Tahun Perolehan --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Tahun Perolehan <span class="text-danger">*</span></label>
                        <input type="number" name="tahun_perolehan" class="form-control @error('tahun_perolehan') is-invalid @enderror" value="{{ old('tahun_perolehan', $inventari->tahun_perolehan) }}" required>
                    </div>
                </div>

                <div class="row">
                    {{-- Jumlah --}}
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Volume Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', $inventari->jumlah) }}" min="0" required>
                    </div>

                    {{-- Satuan --}}
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Satuan <span class="text-danger">*</span></label>
                        <select name="satuan" class="form-control text-dark font-weight-bold" required>
                            @foreach($daftarSatuan as $sat)
                                <option value="{{ $sat }}" {{ old('satuan', $inventari->satuan) == $sat ? 'selected' : '' }}>{{ $sat }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Poin 6: Opsi Kondisi 3 Tingkat Berubah Dinamis --}}
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold text-primary">Kondisi Fisik Barang (Poin 6) <span class="text-danger">*</span></label>
                        <select name="kondisi" class="form-control font-weight-bold text-dark" required>
                            <option value="Baik" {{ old('kondisi', $inventari->kondisi ?? 'Baik') == 'Baik' ? 'selected' : '' }}>🟢 Baik</option>
                            <option value="Rusak Ringan" {{ old('kondisi', $inventari->kondisi) == 'Rusak Ringan' ? 'selected' : '' }}>🟡 Rusak Ringan</option>
                            <option value="Rusak Berat" {{ old('kondisi', $inventari->kondisi) == 'Rusak Berat' ? 'selected' : '' }}>🔴 Rusak Berat (Auto Kirim Jurnal Rusak)</option>
                        </select>
                    </div>
                </div>

                {{-- Spesifikasi Barang --}}
                <div class="form-group">
                    <label class="font-weight-bold">Spesifikasi Barang</label>
                    <textarea name="spesifikasi_barang" class="form-control" rows="2">{{ old('spesifikasi_barang', $inventari->spesifikasi_barang) }}</textarea>
                </div>

                {{-- Keterangan --}}
                <div class="form-group">
                    <label class="font-weight-bold">Keterangan / Alasan Perubahan Data</label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Berikan catatan tambahan mengenai riwayat aset atau alasan kerusakan fisik...">{{ old('keterangan', $inventari->keterangan) }}</textarea>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-warning font-weight-bold text-dark"><i class="fas fa-save mr-1"></i> Perbarui Data Aset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection