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
        <form action="{{ route('lokasi.bmd.update', [$lokasi, $bmd->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Bagian 1: Informasi Aset --}}
                <div class="col-md-6 border-right">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">1. Informasi Aset</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Pilih Barang (Peralatan & Mesin) <span class="text-danger">*</span></label>
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
                        <label class="font-weight-bold text-dark">Keterangan Tambahan Kondisi Barang</label>
                        <textarea name="keterangan" class="form-control" rows="5" placeholder="Catatan kondisi fisik barang saat diserahkan...">{{ old('keterangan', $bmd->keterangan) }}</textarea>
                    </div>
                </div>

                {{-- Bagian 2: Data Pemakai --}}
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">2. Data Pemakai</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nama Pemakai / ASN <span class="text-danger">*</span></label>
                        <select name="pegawai_id" id="pegawai_id_edit" class="form-control select2" required>
                            @foreach($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}" data-nip="{{ $pegawai->nip }}" data-jabatan="{{ $pegawai->jabatan }}"
                                    {{ (old('pegawai_id', $bmd->pegawai_id) == $pegawai->id) ? 'selected' : '' }}>
                                    {{ $pegawai->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold text-dark">Status Pemakai <span class="text-danger">*</span></label>
                            <select name="pemakai_status" class="form-control" required>
                                <option value="ASN" {{ old('pemakai_status', $bmd->pemakai_status) == 'ASN' ? 'selected' : '' }}>ASN</option>
                                <option value="Non-ASN" {{ old('pemakai_status', $bmd->pemakai_status) == 'Non-ASN' ? 'selected' : '' }}>Non-ASN</option>
                                <option value="Lainnya" {{ old('pemakai_status', $bmd->pemakai_status) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold text-dark">Identitas (NIP/NIK) <span class="text-danger">*</span></label>
                            <input type="text" name="pemakai_identitas" id="pemakai_identitas_edit" class="form-control bg-light" value="{{ old('pemakai_identitas', $bmd->pemakai_identitas) }}" readonly required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Jabatan Pemakai</label>
                        <input type="text" id="pemakai_jabatan_edit_display" class="form-control bg-light" value="{{ $bmd->pegawai->jabatan ?? '-' }}" readonly>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Pilih Bendahara Wilayah / Penyerah Aset <span class="text-danger">*</span></label>
                        <select name="bendahara_id" class="form-control select2" required>
                            @foreach($pegawais as $bendahara)
                                <option value="{{ $bendahara->id }}" {{ (old('bendahara_id', $bmd->bendahara_id) == $bendahara->id) ? 'selected' : '' }}>
                                    {{ $bendahara->nama }} @if($bendahara->jabatan) ({{ $bendahara->jabatan }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Row 2: Dokumen Sistem --}}
            <div class="row">
                <div class="col-md-12">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">3. Dokumen Sumber (Referensi Sistem)</h5>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold text-dark">Nomor BAST</label>
                            {{-- Dibikin Readonly karena nomor dokumen sudah mutlak milik sistem PANDAWA --}}
                            <input type="text" name="bast_nomor" class="form-control bg-light" value="{{ old('bast_nomor', $bmd->bast_nomor) }}" readonly required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold text-dark">Tanggal Registrasi BAST</label>
                            <input type="date" name="bast_tanggal" class="form-control bg-light" value="{{ old('bast_tanggal', $bmd->bast_tanggal ? \Carbon\Carbon::parse($bmd->bast_tanggal)->format('Y-m-d') : '') }}" readonly required>
                        </div>
                    </div>

                    @if($bmd->bast_file)
                        <div class="mt-3 alert alert-info py-2 d-flex align-items-center justify-content-between">
                            <div>
                                <small class="font-weight-bold d-block mb-1"><i class="fas fa-file-pdf"></i> Dokumen Fisik Terbitan Sistem:</small>
                                <span class="small text-dark">Arsip Surat BAST saat ini sudah tersimpan aman di server lokal.</span>
                            </div>
                            <a href="{{ asset('storage/' . $bmd->bast_file) }}" target="_blank" class="btn btn-sm btn-info px-3 font-weight-bold shadow-sm"><i class="fas fa-external-link-alt mr-1"></i> Buka PDF BAST</a>
                        </div>
                    @endif
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('lokasi.bmd.index', $lokasi) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-warning shadow-sm px-5 text-dark font-weight-bold">
                    <i class="fas fa-save fa-sm"></i> Simpan Perubahan & Re-generate BAST
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Otomatisasi isi data NIP & Jabatan saat melakukan edit pilihan pegawai
        $('#pegawai_id_edit').on('change', function() {
            var selected = $(this).find('option:selected');
            var nip = selected.data('nip');
            var jabatan = selected.data('jabatan');

            $('#pemakai_identitas_edit').val(nip ? nip : '-');
            $('#pemakai_jabatan_edit_display').val(jabatan ? jabatan : '-');
        });
    });
</script>
@endpush