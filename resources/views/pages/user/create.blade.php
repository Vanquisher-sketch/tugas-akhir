@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Tambah User Baru</h2>
            <a class="btn btn-secondary" href="{{ route('user.index') }}"> Kembali</a>
        </div>
    </div>
</div>

{{-- Tampilkan error validasi --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> Ada masalah dengan input Anda.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('user.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label"><strong>Nama:</strong></label>
                <input type="text" name="name" class="form-control" placeholder="Nama Lengkap" value="{{ old('name') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="email" class="form-label"><strong>Email:</strong></label>
                <input type="email" name="email" class="form-control" placeholder="email@contoh.com" value="{{ old('email') }}">
            </div>
        </div>
        <div class="col-md-12">
            <div class="mb-3">
                <label for="password" class="form-label"><strong>Password:</strong></label>
                <input type="password" name="password" class="form-control" placeholder="Password">
            </div>
        </div>
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</form>
@endsection