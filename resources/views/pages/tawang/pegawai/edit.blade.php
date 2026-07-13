@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ubah Data Pegawai ({{ ucfirst($lokasi) }})</h1>
        <a href="{{ route('lokasi.pegawai.index', $lokasi) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-secondary">Formulir Pembaruan Data Pegawai</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('lokasi.pegawai.update', [$lokasi, $pegawai->id]) }}" method="POST">
                @csrf
                @method('PUT') {{-- Wajib digunakan untuk proses update data di Laravel --}}

                <div class="form-group row">
                    <label for="nip" class="col-sm-2 col-form-label">NIP (PNS/PPPK)</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('nip') is-invalid @enderror" 
                               id="nip" name="nip" value="{{ old('nip', $pegawai->nip) }}" placeholder="Masukkan 18 digit NIP (Kosongkan jika Honorer)">
                        @error('nip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="nama" class="col-sm-2 col-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                               id="nama" name="nama" value="{{ old('nama', $pegawai->nama) }}" placeholder="Masukkan nama beserta gelar jika ada" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="jabatan" class="col-sm-2 col-form-label">Jabatan <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('jabatan') is-invalid @enderror" 
                               id="jabatan" name="jabatan" value="{{ old('jabatan', $pegawai->jabatan) }}" placeholder="Contoh: Kasi Pemerintahan, Staff Administrasi" required>
                        @error('jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="no_hp" class="col-sm-2 col-form-label">No. HP / WhatsApp <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror" 
                               id="no_hp" name="no_hp" value="{{ old('no_hp', $pegawai->no_hp) }}" placeholder="Contoh: 081234567890" required>
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $pegawai->email) }}" placeholder="Contoh: pegawai@tawang.go.id">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="alamat" class="col-sm-2 col-form-label">Alamat <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('alamat') is-invalid @enderror" 
                               id="alamat" name="alamat" value="{{ old('alamat', $pegawai->alamat) }}" placeholder="Contoh: Jln. Tanuwijaya No 8" required>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-warning text-dark font-weight-bold">
                            <i class="fas fa-edit mr-1"></i> Perbarui Data
                        </button>
                        <a href="{{ route('lokasi.pegawai.index', $lokasi) }}" class="btn btn-light border">Batal</a>
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection