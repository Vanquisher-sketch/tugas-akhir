@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Detail User</h2>
            <a class="btn btn-secondary" href="{{ route('user.index') }}"> Kembali</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="mb-3">
            <strong>Nama:</strong>
            <p>{{ $user->name }}</p>
        </div>
        <div class="mb-3">
            <strong>Email:</strong>
            <p>{{ $user->email }}</p>
        </div>
        <div class="mb-3">
            <strong>Dibuat Pada:</strong>
            <p>{{ $user->created_at->format('d F Y H:i') }}</p>
        </div>
    </div>
</div>
@endsection