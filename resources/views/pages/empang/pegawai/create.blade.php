@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Pegawai Baru ({{ ucfirst($lokasi) }})</h1>
        <a href="{{ route('lokasi.pegawai.index', $lokasi) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-secondary">Formulir Data Pegawai</h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger shadow-sm mb-4">
                <h6 class="font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Gagal Menyimpan Data!</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <form action="{{ route('lokasi.pegawai.store', $lokasi) }}" method="POST">
                @csrf

                <div class="form-group row">
                    <label for="pegawai_nip" class="col-sm-2 col-form-label">NIP (PNS/PPPK)</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('pegawai_nip') is-invalid @enderror" 
                               id="pegawai_nip" name="pegawai_nip" value="{{ old('pegawai_nip') }}" placeholder="Masukkan 18 digit NIP (Kosongkan jika Honorer)">
                        @error('pegawai_nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pegawai_nama" class="col-sm-2 col-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('pegawai_nama') is-invalid @enderror" 
                               id="pegawai_nama" name="pegawai_nama" value="{{ old('pegawai_nama') }}" placeholder="Masukkan nama beserta gelar jika ada" required>
                        @error('pegawai_nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pegawai_jabatan" class="col-sm-2 col-form-label">Jabatan <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('pegawai_jabatan') is-invalid @enderror" 
                               id="pegawai_jabatan" name="pegawai_jabatan" value="{{ old('pegawai_jabatan') }}" placeholder="Contoh: Kasi Pemerintahan, Staff Administrasi" required>
                        @error('pegawai_jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pegawai_no_hp" class="col-sm-2 col-form-label">No. HP / WhatsApp <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('pegawai_no_hp') is-invalid @enderror" 
                               id="pegawai_no_hp" name="pegawai_no_hp" value="{{ old('pegawai_no_hp') }}" placeholder="Contoh: 081234567890" required>
                        @error('pegawai_no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pegawai_email" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control @error('pegawai_email') is-invalid @enderror" 
                               id="pegawai_email" name="pegawai_email" value="{{ old('pegawai_email') }}" placeholder="Contoh: pegawai@tawang.go.id">
                        @error('pegawai_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pegawai_alamat" class="col-sm-2 col-form-label">Alamat <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('pegawai_alamat') is-invalid @enderror" 
                               id="pegawai_alamat" name="pegawai_alamat" value="{{ old('pegawai_alamat') }}" placeholder="Contoh: Jln. Tanuwijaya No 8" required>
                        @error('pegawai_alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Data
                        </button>
                        <button type="reset" class="btn btn-light border">Reset</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection