@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    {{-- Card Header --}}
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Hubungkan Penggunaan BMD - Wilayah {{ ucfirst($lokasi) }}</h6>
        <a href="{{ route('lokasi.bmd.index', $lokasi) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    {{-- Card Body --}}
    <div class="card-body">
        
        {{-- Notifikasi Informasi Otomatisasi --}}
        <div class="alert alert-success alert-dismissible fade show py-2 mb-4" role="alert" style="font-size: 12px;">
            <i class="fas fa-magic mr-1"></i> <strong>Sistem Otomatisasi Aktif:</strong> Nomor, Tanggal Dokumen BAST, dan Dokumen PDF akan diproses secara otomatis oleh sistem setelah Anda menekan tombol simpan.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="padding: 0.5rem 0.75rem;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        {{-- Menampilkan Detail Eror Validasi jika ada --}}
        @if ($errors->any())
            <div class="alert alert-danger pb-0 py-2 mb-4" style="font-size: 13px;">
                <h6 class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal Menyimpan Data:</h6>
                <ul class="pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('lokasi.bmd.store', $lokasi) }}" method="POST">
            @csrf

            <div class="row">
                {{-- ====== POIN 1: INFORMASI ASET ====== --}}
                <div class="col-md-6 border-right">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">1. Informasi Aset</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Pilih Barang (Peralatan & Mesin) <span class="text-danger">*</span></label>
                        <select name="peralatan_kode" class="form-control select2 @error('peralatan_kode') is-invalid @enderror" required>
                            <option value="">-- Cari Nama atau Kode Barang --</option>
                            @forelse($peralatans as $alat)
                                <option value="{{ $alat->kode_barang }}" {{ old('peralatan_kode') == $alat->kode_barang ? 'selected' : '' }}>
                                    [{{ $alat->kode_barang }}] {{ $alat->nama_barang }} 
                                    @if($alat->nomor_polisi) | Plat: {{ $alat->nomor_polisi }} @endif
                                </option>
                            @empty
                                <option value="" disabled>-- Tidak ada aset tersedia di lokasi ini --</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Keterangan Catatan Kondisi Barang</label>
                        <textarea name="keterangan" class="form-control" rows="4" placeholder="Catatan opsional kondisi fisik barang saat diserahkan (misal: Kondisi Baik, Kunci Lengkap)...">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                {{-- ====== POIN 2: DATA PEGAWAI (PILIH NAMA -> OTOMATIS SELESAI) ====== --}}
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">2. Data Pemakai & Penyerah</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nama Pemakai / Pemegang Aset <span class="text-danger">*</span></label>
                        <select name="pegawai_id" id="pegawai_id" class="form-control select2 @error('pegawai_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Pegawai --</option>
                            @foreach($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}" data-nip="{{ $pegawai->nip }}" data-jabatan="{{ $pegawai->jabatan }}" {{ old('pegawai_id') == $pegawai->id ? 'selected' : '' }}>
                                    {{ $pegawai->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold text-dark">Status Pemakai <span class="text-danger">*</span></label>
                            <select name="pemakai_status" class="form-control" required>
                                <option value="ASN" {{ old('pemakai_status') == 'ASN' ? 'selected' : '' }}>ASN</option>
                                <option value="Non-ASN" {{ old('pemakai_status') == 'Non-ASN' ? 'selected' : '' }}>Non-ASN</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold text-dark">Identitas NIP/NIK</label>
                            <input type="text" name="pemakai_identitas" id="pemakai_identitas" class="form-control bg-light" value="{{ old('pemakai_identitas') }}" placeholder="Otomatis..." readonly required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Jabatan Terdeteksi</label>
                        <input type="text" id="pemakai_jabatan_display" class="form-control bg-light" placeholder="Otomatis..." readonly>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Pilih Bendahara Wilayah (Pihak Penyerah) <span class="text-danger">*</span></label>
                        <select name="bendahara_id" class="form-control select2 @error('bendahara_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Bendahara --</option>
                            @foreach($pegawais as $bendahara)
                                <option value="{{ $bendahara->id }}" {{ old('bendahara_id') == $bendahara->id ? 'selected' : '' }}>
                                    {{ $bendahara->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary px-5 shadow-sm font-weight-bold btn-block d-sm-inline-block">
                    <i class="fas fa-check-circle mr-1"></i> Proses Pencocokan & Terbitkan BAST
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Fungsi pemicu otomatisasi data ketika nama pegawai dipilih dari dropdown select2
        function isiDataPegawaiOtomatis() {
            var selected = $('#pegawai_id').find('option:selected');
            var nip = selected.data('nip');
            var jabatan = selected.data('jabatan');

            // Set nilai ke input text pendukung
            $('#pemakai_identitas').val(nip ? nip : '-');
            $('#pemakai_jabatan_display').val(jabatan ? jabatan : '-');
        }

        // Trigger 1: Jalankan fungsi setiap kali ada perubahan pilihan nama pegawai
        $('#pegawai_id').on('change', function() {
            isiDataPegawaiOtomatis();
        });

        // Trigger 2: Jalankan fungsi saat halaman pertama kali dimuat (menangani old value jika validasi sempat gagal)
        if ($('#pegawai_id').val() !== '') {
            isiDataPegawaiOtomatis();
        }
    });
</script>
@endpush