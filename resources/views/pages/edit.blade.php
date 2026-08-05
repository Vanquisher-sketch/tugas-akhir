@extends('layouts.app')

@section('content')

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">Profil Akun Saya</h1>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@php
    $hasPasswordError = $errors->has('current_password') || $errors->has('new_password') || $errors->has('new_password_confirmation');
@endphp

<div class="row">

    <!-- Kolom Kiri: Info Pengguna Sederhana -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Pengguna</h6>
            </div>
            <div class="card-body text-center">
                <img class="img-fluid img-profile rounded-circle mx-auto mb-3" 
                     src="https://placehold.co/150x150/4e73df/ffffff?text={{ strtoupper(substr(Auth::user()->name, 0, 1)) }}" 
                     alt="Foto Profil" 
                     style="width: 150px; height: 150px; object-fit: cover;">
                
                <h4 class="font-weight-bold">{{ Auth::user()->name }}</h4>
                <p class="text-muted">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Detail Akun & Keamanan -->
    <div class="col-lg-8">
        
        <!-- Nav Tabs -->
        <ul class="nav nav-tabs" id="profileTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $hasPasswordError ? '' : 'active' }}" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="{{ $hasPasswordError ? 'false' : 'true' }}">Update Detail</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $hasPasswordError ? 'active' : '' }}" id="password-tab" data-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="{{ $hasPasswordError ? 'true' : 'false' }}">Ganti Password</a>
            </li>
        </ul>

        <div class="tab-content" id="profileTabContent">
            
            <!-- Tab Pane: Update Detail -->
            <div class="tab-pane fade {{ $hasPasswordError ? '' : 'show active' }}" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                <div class="card shadow mb-4 border-top-0 rounded-0">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi Akun</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group row">
                                <label for="name" class="col-sm-3 col-form-label">Nama Lengkap</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::user()->name) }}">
                                    @error('name') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>
                            </div>
                           
                            <div class="form-group row">
                                <label for="email" class="col-sm-3 col-form-label">Email (Login)</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::user()->email) }}">
                                    @error('email') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-primary">Update Profil</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tab Pane: Ganti Password -->
            <div class="tab-pane fade {{ $hasPasswordError ? 'show active' : '' }}" id="password" role="tabpanel" aria-labelledby="password-tab">
                <div class="card shadow mb-4 border-top-0 rounded-0">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ubah Password</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="current_password">Password Saat Ini</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                @error('current_password') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password">Password Baru</label>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                                @error('new_password') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">Ubah Password</button>
                        </form>
                    </div>
                </div>
            </div>

        </div> <!-- End Tab Content -->

    </div>
</div>
@endsection