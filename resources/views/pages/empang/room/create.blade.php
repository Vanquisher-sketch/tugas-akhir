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
                <input type="text" name="name" id="name" class="form-control" required placeholder="Contoh: Ruang Rapat">
            </div>
            <div class="form-group">
                <label for="kode_ruangan">Kode Ruangan</label>
                <input type="text" name="kode_ruangan" id="kode_ruangan" class="form-control" placeholder="Akan terisi otomatis...">
                <small class="text-muted">Kode akan tergenerate otomatis, kamu tetap bisa mengubahnya manual.</small>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary mr-1">Simpan</button>
            <a href="{{ route('lokasi.room.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary mr-1">Batal</a>
        </form>
    </div>
</div>

{{-- Tambahkan Script Auto-Generate di sini --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const kodeInput = document.getElementById('kode_ruangan');

        nameInput.addEventListener('keyup', function() {
            let nameValue = nameInput.value.trim();
            
            if (nameValue.length > 0) {
                // 1. Ambil Inisial (Contoh: "Ruang Admin" -> "RA")
                let initials = nameValue.split(' ')
                                        .filter(word => word.length > 0)
                                        .map(word => word.charAt(0))
                                        .join('')
                                        .toUpperCase();

                // 2. Tambahkan nomor random 3 digit (Contoh: 123)
                // Math.random() akan menjamin kode RA-124 berbeda dengan RA-562
                let randomNumber = Math.floor(1 + Math.random() * 100);

                // 3. Gabungkan (Contoh: RA-123)
                kodeInput.value = `${initials}-${randomNumber}`;
            } else {
                kodeInput.value = '';
            }
        });
    });
</script>
@endsection