@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    {{-- Card Header --}}
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Penggunaan BMD (BAST) - {{ ucfirst($lokasi) }}</h6>
        <a href="{{ route('lokasi.bmd.index', $lokasi) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    {{-- Card Body --}}
    <div class="card-body">
        <form action="{{ route('lokasi.bmd.store', $lokasi) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Row 1: Data Barang & Lokasi --}}
            <div class="row">
                <div class="col-md-6 border-right">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">1. Informasi Aset & Lokasi</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Pilih Barang (Peralatan & Mesin) <span class="text-danger">*</span></label>
                        
                        {{-- NOTIFIKASI FILTER --}}
                        <div class="alert alert-info py-1 px-2 mb-2" style="font-size: 11px;">
                            <i class="fas fa-info-circle mr-1"></i> Sistem hanya menampilkan aset yang <strong>tersedia</strong> (belum memiliki BAST aktif).
                        </div>

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
                        @error('peralatan_kode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Lokasi/Alamat Penggunaan <span class="text-danger">*</span></label>
                        <input type="text" name="alamat_penggunaan" class="form-control @error('alamat_penggunaan') is-invalid @enderror" 
                               placeholder="Contoh: Ruang Kerja Camat / Alamat Rumah Dinas" value="{{ old('alamat_penggunaan') }}" required>
                        <small class="form-text text-muted">Titik koordinat atau alamat fisik barang saat ini berada.</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">2. Data Pemakai</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nama Pemakai <span class="text-danger">*</span></label>
                        <input type="text" name="pemakai_nama" class="form-control" placeholder="Nama Lengkap & Gelar" value="{{ old('pemakai_nama') }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Status Pemakai <span class="text-danger">*</span></label>
                            <select name="pemakai_status" class="form-control" required>
                                <option value="ASN" {{ old('pemakai_status') == 'ASN' ? 'selected' : '' }}>ASN</option>
                                <option value="Non-ASN" {{ old('pemakai_status') == 'Non-ASN' ? 'selected' : '' }}>Non-ASN</option>
                                <option value="Pihak Ketiga" {{ old('pemakai_status') == 'Pihak Ketiga' ? 'selected' : '' }}>Pihak Ketiga</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Identitas (NIP/NIK) <span class="text-danger">*</span></label>
                            <input type="text" name="pemakai_identitas" class="form-control" placeholder="Masukkan Nomor Identitas" value="{{ old('pemakai_identitas') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Jabatan Pemakai</label>
                        <input type="text" name="pemakai_jabatan" class="form-control" placeholder="Contoh: Kasi Pemerintahan" value="{{ old('pemakai_jabatan') }}">
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Row 2: Dokumen & Pajak --}}
            <div class="row">
                <div class="col-md-6 border-right">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">3. Dokumen Sumber (BAST)</h5>
                    <div class="form-row">
                        <div class="col-md-7">
                            <label>Nomor BAST</label>
                            <input type="text" name="bast_nomor" class="form-control" placeholder="No. BAST / SK" value="{{ old('bast_nomor') }}">
                        </div>
                        <div class="col-md-5">
                            <label>Tanggal BAST</label>
                            <input type="date" name="bast_tanggal" class="form-control" value="{{ old('bast_tanggal') }}">
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label class="font-weight-bold text-dark">Upload Scan BAST (PDF/JPG)</label>
                        <div class="custom-file">
                            <input type="file" name="bast_file" class="custom-file-input" id="bastFile">
                            <label class="custom-file-label" for="bastFile">Pilih file...</label>
                        </div>
                        <small class="text-muted">Maksimal file 5MB.</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">4. Pajak & STNK (Khusus Kendaraan)</h5>
                    <div class="form-row">
                        <div class="col-md-6">
                            <label>Tgl Pajak Tahunan</label>
                            <input type="date" name="tanggal_pajak" class="form-control" value="{{ old('tanggal_pajak') }}">
                        </div>
                        <div class="col-md-6">
                            <label>Tgl STNK (5 Tahunan)</label>
                            <input type="date" name="tanggal_stnk" class="form-control" value="{{ old('tanggal_stnk') }}">
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label>Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan khusus kondisi barang saat diserahkan...">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end mt-4">
                <button type="reset" class="btn btn-secondary mr-2">Reset</button>
                <button type="submit" class="btn btn-primary px-5 shadow-sm font-weight-bold">
                    <i class="fas fa-save mr-1"></i> Simpan Data Penggunaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle nama file upload muncul di label
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    });
</script>
@endpush