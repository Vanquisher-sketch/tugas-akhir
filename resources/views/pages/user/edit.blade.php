@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit User</h2>
            <a class="btn btn-secondary" href="{{ route('user.index') }}"> Kembali</a>
        </div>
    </div>
</div>

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

<form action="{{ route('user.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label"><strong>Nama:</strong></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" placeholder="Nama">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="email" class="form-label"><strong>Email:</strong></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" placeholder="Email">
            </div>
        </div>
        <div class="col-md-12">
            <div class="mb-3">
                <label for="password" class="form-label"><strong>Password:</strong></label>
                <input type="password" name="password" class="form-control" placeholder="Password">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
            </div>
        </div>
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </div>
</form>
@endsection