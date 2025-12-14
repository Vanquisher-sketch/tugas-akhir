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
            <th>Password</th>
            <th width="20%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->password }}</td>
            <td>
                <form action="{{ route('user.destroy', $user->id) }}" method="POST">
                    <a class="btn btn-info btn-sm" href="{{ route('user.show', $user->id) }}">Detail</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('user.edit', $user->id) }}">Edit</a>
                    
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