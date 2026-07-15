@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Ubah Penggunaan BMD & BAST - Wilayah {{ ucfirst($lokasi) }}</h6>
        <a href="{{ route('lokasi.bmd.index', $lokasi) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <div class="card-body">
        <div class="alert alert-warning alert-dismissible fade show py-2 mb-4" role="alert" style="font-size: 12px;">
            <i class="fas fa-exclamation-triangle mr-1"></i> <strong>Perhatian:</strong> Mengubah data pada form ini akan otomatis merevisi (mencetak ulang) dokumen PDF BAST Anda dengan data terbaru.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="padding: 0.5rem 0.75rem;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger pb-0 py-2 mb-4" style="font-size: 13px;">
                <h6 class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal Memperbarui Data:</h6>
                <ul class="pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🌟 Perbaikan Rute Parameter bmd_id --}}
        <form action="{{ route('lokasi.bmd.update', ['lokasi' => $lokasi, 'id' => $bmd->bmd_id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- ====== POIN 1: INFORMASI ASET ====== --}}
                <div class="col-md-6 border-right">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">1. Informasi Aset</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Ubah Barang (Peralatan & Mesin) <span class="text-danger">*</span></label>
                        <select name="peralatan_kode" class="form-control select2 @error('peralatan_kode') is-invalid @enderror" required>
                            <option value="">-- Cari Nama atau Kode Barang --</option>
                            @forelse($peralatans as $alat)
                                {{-- 🌟 Penyesuaian ke atribut peralatans --}}
                                <option value="{{ $alat->alat_kode_barang }}" {{ (old('peralatan_kode', $bmd->bmd_alat_kode) == $alat->alat_kode_barang) ? 'selected' : '' }}>
                                    [{{ $alat->alat_kode_barang }}] {{ $alat->alat_nama_barang }} 
                                    @if($alat->alat_nomor_polisi) | Plat: {{ $alat->alat_nomor_polisi }} @endif
                                </option>
                            @empty
                                <option value="" disabled>-- Tidak ada aset tersedia di lokasi ini --</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Keterangan Catatan Kondisi Barang</label>
                        <textarea name="keterangan" class="form-control" rows="4" placeholder="Catatan opsional...">{{ old('keterangan', $bmd->bmd_keterangan) }}</textarea>
                    </div>
                </div>

                {{-- ====== POIN 2: DATA PEGAWAI ====== --}}
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">2. Data Pemakai & Penyerah</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nama Pemakai / Pemegang Aset <span class="text-danger">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="form-control select2 @error('pegawai_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($pegawais as $pegawai)
                                {{-- 🌟 REVISI: Value mengirim nip, cek seleksi menggunakan bmd_pegawai_nip --}}
                                <option value="{{ $pegawai->pegawai_nip }}" data-nip="{{ $pegawai->pegawai_nip }}" data-jabatan="{{ $pegawai->pegawai_jabatan }}" {{ (old('pegawai_id', $bmd->bmd_pegawai_nip) == $pegawai->pegawai_nip) ? 'selected' : '' }}>
                                    {{ $pegawai->pegawai_nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold text-dark">Status Pemakai <span class="text-danger">*</span></label>
                            <select name="pemakai_status" class="form-control" required>
                                <option value="ASN" {{ old('pemakai_status', $bmd->bmd_pemakai_status) == 'ASN' ? 'selected' : '' }}>ASN</option>
                                <option value="Non-ASN" {{ old('pemakai_status', $bmd->bmd_pemakai_status) == 'Non-ASN' ? 'selected' : '' }}>Non-ASN</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold text-dark">Identitas NIP/NIK</label>
                            <input type="text" name="pemakai_identitas" id="pemakai_identitas" class="form-control bg-light" value="{{ old('pemakai_identitas', $bmd->bmd_pemakai_identitas) }}" readonly required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Jabatan Terdeteksi</label>
                        <input type="text" id="pemakai_jabatan_display" class="form-control bg-light" readonly>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Pilih Bendahara Wilayah (Pihak Penyerah) <span class="text-danger">*</span></label>
                        <select name="bendahara_id" class="form-control select2 @error('bendahara_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Bendahara --</option>
                            @foreach($pegawais as $bendahara)
                                {{-- 🌟 REVISI: Value mengirim nip, cek seleksi menggunakan bmd_bendahara_nip --}}
                                <option value="{{ $bendahara->pegawai_nip }}" {{ (old('bendahara_id', $bmd->bmd_bendahara_nip) == $bendahara->pegawai_nip) ? 'selected' : '' }}>
                                    {{ $bendahara->pegawai_nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-warning px-5 shadow-sm font-weight-bold btn-block d-sm-inline-block text-white">
                    <i class="fas fa-sync-alt mr-1"></i> Perbarui Data & Revisi BAST
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        function isiDataPegawaiOtomatis() {
            var selected = $('#pegawai_id').find('option:selected');
            var nip = selected.data('nip');
            var jabatan = selected.data('jabatan');

            $('#pemakai_identitas').val(nip ? nip : '-');
            $('#pemakai_jabatan_display').val(jabatan ? jabatan : '-');
        }

        $('#pegawai_id').on('change', function() {
            isiDataPegawaiOtomatis();
        });

        if ($('#pegawai_id').val() !== '') {
            isiDataPegawaiOtomatis();
        }
    });
</script>
@endpush