@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Update Data Pajak & Kontak</h1>
        <a href="{{ route('lokasi.pajak.index', $lokasi) }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Update Informasi</h6>
                </div>
                <div class="card-body">
                    
                    {{-- Tampilkan Alert Error jika ada --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('lokasi.pajak.update', [$lokasi, $pajak->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info">
                            <strong>Aset:</strong> {{ $pajak->peralatan->nama_barang ?? '-' }} <br>
                            <strong>Nopol/Merk:</strong> {{ $pajak->peralatan->nomor_polisi ?? $pajak->peralatan->merk_tipe ?? '-' }} <br>
                            <strong>Pemakai Saat Ini:</strong> {{ $pajak->pemakai_nama }}
                        </div>

                        <hr>

                        <h6 class="font-weight-bold text-gray-800 mb-3"><i class="fab fa-whatsapp text-success"></i> Kontak WhatsApp (Untuk Notifikasi)</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nomor HP Pemakai</label>
                                    <input type="number" name="nomor_pemakai" class="form-control" 
                                           placeholder="Contoh: 08123456789"
                                           value="{{ old('nomor_pemakai', $pajak->nomor_pemakai) }}">
                                    <small class="text-muted">Nomor yang memegang kendaraan.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nomor HP Bendahara/Pengurus</label>
                                    <input type="number" name="nomor_bendahara" class="form-control" 
                                           placeholder="Contoh: 08123456789"
                                           value="{{ old('nomor_bendahara', $pajak->nomor_bendahara) }}">
                                    <small class="text-muted">Cadangan penerima notifikasi.</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="font-weight-bold text-gray-800 mb-3"><i class="far fa-calendar-alt text-warning"></i> Tanggal Jatuh Tempo</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group p-3 border rounded bg-light">
                                    <label class="font-weight-bold text-primary">Pajak Tahunan</label>
                                    <input type="date" name="tanggal_pajak" class="form-control" 
                                           value="{{ old('tanggal_pajak', $pajak->tanggal_pajak) }}">
                                    <small class="text-muted">Tanggal ulang tahun pajak di STNK.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group p-3 border rounded bg-light">
                                    <label class="font-weight-bold text-danger">5 Tahunan (Ganti Kaleng)</label>
                                    <input type="date" name="tanggal_stnk" class="form-control" 
                                           value="{{ old('tanggal_stnk', $pajak->tanggal_stnk) }}">
                                    <small class="text-muted">Tanggal habis masa berlaku plat nomor.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary shadow-sm">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection