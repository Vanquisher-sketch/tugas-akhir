@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    {{-- Card Header --}}
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Penggunaan BMD - {{ ucfirst($lokasi) }}</h6>
        <a href="{{ route('lokasi.bmd.index', $lokasi) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    {{-- Card Body --}}
    <div class="card-body">
        <form action="{{ route('lokasi.bmd.update', [$lokasi, $bmd->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Bagian 1: Informasi Aset --}}
                <div class="col-md-6 border-right">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">1. Informasi Aset & Lokasi</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Pilih Barang (Peralatan & Mesin)</label>
                        {{-- REVISI: name disesuaikan ke peralatan_kode --}}
                        <select name="peralatan_kode" class="form-control select2" required>
                            @foreach($peralatans as $alat)
                                <option value="{{ $alat->kode_barang }}" 
                                    {{ (old('peralatan_kode', $bmd->peralatan_kode) == $alat->kode_barang) ? 'selected' : '' }}>
                                    [{{ $alat->kode_barang }}] {{ $alat->nama_barang }} @if($alat->nomor_polisi) ({{ $alat->nomor_polisi }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Lokasi/Alamat Penggunaan Saat Ini</label>
                        <input type="text" name="alamat_penggunaan" class="form-control" value="{{ old('alamat_penggunaan', $bmd->alamat_penggunaan) }}" required>
                    </div>
                </div>

                {{-- Bagian 2: Data Pemakai --}}
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">2. Data Pemakai</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nama Pemakai</label>
                        <input type="text" name="pemakai_nama" class="form-control" value="{{ old('pemakai_nama', $bmd->pemakai_nama) }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Status Pemakai</label>
                            <select name="pemakai_status" class="form-control" required>
                                <option value="ASN" {{ old('pemakai_status', $bmd->pemakai_status) == 'ASN' ? 'selected' : '' }}>ASN</option>
                                <option value="Non-ASN" {{ old('pemakai_status', $bmd->pemakai_status) == 'Non-ASN' ? 'selected' : '' }}>Non-ASN</option>
                                <option value="Lainnya" {{ old('pemakai_status', $bmd->pemakai_status) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Identitas (NIP/NIK)</label>
                            <input type="text" name="pemakai_identitas" class="form-control" value="{{ old('pemakai_identitas', $bmd->pemakai_identitas) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Jabatan Pemakai</label>
                        <input type="text" name="pemakai_jabatan" class="form-control" value="{{ old('pemakai_jabatan', $bmd->pemakai_jabatan) }}">
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Row 2: Dokumen & Pajak --}}
            <div class="row">
                <div class="col-md-6 border-right">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">3. Dokumen Sumber (BAST)</h5>
                    
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <label>Nomor BAST</label>
                            <input type="text" name="bast_nomor" class="form-control" value="{{ old('bast_nomor', $bmd->bast_nomor) }}">
                        </div>
                        <div class="form-group col-md-5">
                            <label>Tanggal BAST</label>
                            <input type="date" name="bast_tanggal" class="form-control" value="{{ $bmd->bast_tanggal ? $bmd->bast_tanggal->format('Y-m-d') : '' }}">
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label class="font-weight-bold text-dark">Upload Scan BAST (Isi jika ingin ganti file)</label>
                        <div class="custom-file">
                            <input type="file" name="bast_file" class="custom-file-input" id="bastFileEdit">
                            <label class="custom-file-label" for="bastFileEdit">Pilih file baru...</label>
                        </div>
                        @if($bmd->bast_file)
                            <div class="mt-2">
                                <small class="text-success"><i class="fas fa-check-circle"></i> File tersimpan:</small>
                                <a href="{{ asset('storage/' . $bmd->bast_file) }}" target="_blank" class="badge badge-info">Lihat Dokumen</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">4. Monitoring Pajak Kendaraan</h5>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Jatuh Tempo Pajak</label>
                            <input type="date" name="tanggal_pajak" class="form-control" value="{{ $bmd->tanggal_pajak ? $bmd->tanggal_pajak->format('Y-m-d') : '' }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Ganti STNK (5 Tahunan)</label>
                            <input type="date" name="tanggal_stnk" class="form-control" value="{{ $bmd->tanggal_stnk ? $bmd->tanggal_stnk->format('Y-m-d') : '' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $bmd->keterangan) }}</textarea>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('lokasi.bmd.index', $lokasi) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-warning shadow-sm px-4 text-dark font-weight-bold">
                    <i class="fas fa-save fa-sm"></i> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Update nama file di input custom bootstrap
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
@endpush