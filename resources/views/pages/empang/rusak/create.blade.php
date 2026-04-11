@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Data Barang Rusak Berat - {{ ucfirst($lokasi) }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.rusak.store', ['lokasi' => $lokasi]) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="no_id_pemda">No. ID Pemda</label>
                        <input type="text" class="form-control" name="no_id_pemda" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_barang">Nama / Jenis Barang</label>
                        <input type="text" class="form-control" name="nama_barang" required>
                    </div>
                    <div class="form-group">
                        <label for="spesifikasi">Spesifikasi</label>
                        <input type="text" class="form-control" name="spesifikasi">
                    </div>
                    <div class="form-group">
                        <label for="no_polisi">No. Polisi</label>
                        <input type="text" class="form-control" name="no_polisi">
                    </div>
                     <div class="form-group">
                        <label for="tahun_perolehan">Tahun Perolehan</label>
                        <input type="number" class="form-control" name="tahun_perolehan" placeholder="YYYY" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="harga_perolehan">Harga Perolehan (Rp)</label>
                        <input type="number" class="form-control" name="harga_perolehan" required>
                    </div>
                    <div class="form-group">
                        <label for="kondisi">Kondisi</label>
                        <input type="text" class="form-control" name="kondisi" value="RB" required>
                    </div>
                    <div class="form-group">
                        <label for="tercatat_di_kib">Tercatat di KIB</label>
                        <input type="text" class="form-control" name="tercatat_di_kib">
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('lokasi.rusak.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

