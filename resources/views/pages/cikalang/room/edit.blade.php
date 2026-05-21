@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Ruangan - {{ ucfirst($lokasi) }}</h6>
    </div>
    <div class="card-body">
        {{-- REVISI: Ganti $room->id menjadi $room->kode_ruangan --}}
        <form action="{{ route('lokasi.room.update', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name">Nama Ruangan</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $room->name) }}" required>
            </div>

            <div class="form-group">
                <label for="kode_ruangan">Kode Ruangan</label>
                {{-- Di sini tampilkan kode_ruangan yang sudah ada --}}
                <input type="text" name="kode_ruangan" id="kode_ruangan" class="form-control" value="{{ old('kode_ruangan', $room->kode_ruangan) }}">
                <small class="text-info">Hati-hati: Mengubah kode ruangan akan berpengaruh pada data aset yang terhubung ke ruangan ini.</small>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('lokasi.room.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection