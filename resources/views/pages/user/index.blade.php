@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manajemen User</h2>
            <a class="btn btn-success" href="{{ route('user.create') }}"> Buat User Baru</a>
        </div>
    </div>
</div>

{{-- Tampilkan pesan sukses --}}
@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th width="5%">No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Status</th>
            <th width="20%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $loop->iteration }}</td>
            {{-- Menyesuaikan dengan kolom di database / fillable --}}
            <td>{{ $user->user_nama }}</td>
            <td>{{ $user->user_email }}</td>
            
            {{-- 
               Saran Praktik Terbaik: Menampilkan hash password di tabel tidak informatif bagi pengguna. 
               Lebih baik ganti dengan kolom Status atau Role. 
               Di bawah ini saya ganti menjadi user_status. 
            --}}
            <td>
                @if($user->user_status == 'Aktif')
                    <span class="badge bg-success">{{ $user->user_status }}</span>
                @else
                    <span class="badge bg-secondary">{{ $user->user_status ?? 'Tidak Diketahui' }}</span>
                @endif
            </td>
            
            <td>
                {{-- Pemanggilan ID sudah benar menggunakan user_id --}}
                <form action="{{ route('user.destroy', $user->user_id) }}" method="POST">
                    <a class="btn btn-info btn-sm" href="{{ route('user.show', $user->user_id) }}">Detail</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('user.edit', $user->user_id) }}">Edit</a>
                    
                    @csrf
                    @method('DELETE')
                    
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Menampilkan link pagination --}}
{!! $users->links() !!}

@endsection