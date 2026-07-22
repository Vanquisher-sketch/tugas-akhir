@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Ubah Data Ruangan - {{ ucfirst($lokasi) }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.ruangan.update', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="kode_ruangan">Kode Ruangan (Permanen tidak bisa diubah)</label>
                {{-- Input dibuat readonly karena ini primary key, tidak boleh diubah user --}}
                <input type="text" class="form-control bg-light" id="kode_ruangan" name="kode_ruangan" value="{{ $room->kode_ruangan }}" readonly>
            </div>

            <div class="form-group">
                <label for="name">Nama Ruangan <span class="text-danger">*</span></label>
                {{-- Mapping variabel baru $room->ruangan_nama --}}
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $room->ruangan_nama) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('lokasi.ruangan.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary mr-2">Kembali</a>
                <button type="submit" class="btn btn-warning">Perbarui Data</button>
            </div>
        </form>
    </div>
</div>
@endsection