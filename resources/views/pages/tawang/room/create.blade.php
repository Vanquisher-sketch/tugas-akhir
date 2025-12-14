@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Data Ruangan - {{ ucfirst($lokasi) }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.room.store', ['lokasi' => $lokasi]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Nama Ruangan</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="kode_ruangan">Kode Ruangan</label>
                <input type="text" name="kode_ruangan" id="kode_ruangan" class="form-control">
            </div>
            <hr>
            <button type="submit" class="btn btn-primary mr-1">Simpan</button>
            <a href="{{ route('lokasi.room.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary mr-1">Batal</a>
        </form>
    </div>
</div>
@endsection

