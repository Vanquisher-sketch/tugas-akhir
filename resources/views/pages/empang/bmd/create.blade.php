@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    {{-- Card Header --}}
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tambah Penggunaan BMD - {{ ucfirst($lokasi) }}</h6>
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
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">1. Informasi Aset & Lokasi</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Pilih Barang (Peralatan & Mesin) <span class="text-danger">*</span></label>
                        <select name="peralatan_id" class="form-control select2" required>
                            <option value="">-- Cari Barang (Ketik Nama/Kode) --</option>
                            @foreach($peralatans as $alat)
                                <option value="{{ $alat->id }}" {{ old('peralatan_id') == $alat->id ? 'selected' : '' }}>
                                    [{{ $alat->kode_barang }}] {{ $alat->nama_barang }} (NIBR: {{ $alat->nibr ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Lokasi/Alamat Penggunaan <span class="text-danger">*</span></label>
                        <input type="text" name="alamat_penggunaan" class="form-control" placeholder="Contoh: Aula Kecamatan / Rumah Dinas" value="{{ old('alamat_penggunaan') }}" required>
                        <small class="form-text text-muted">Isi dengan nama ruangan atau alamat fisik barang saat ini.</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">2. Data Pemakai</h5>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Nama Pemakai <span class="text-danger">*</span></label>
                        <input type="text" name="pemakai_nama" class="form-control" value="{{ old('pemakai_nama') }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Status Pemakai <span class="text-danger">*</span></label>
                            <select name="pemakai_status" class="form-control" required>
                                <option value="ASN" {{ old('pemakai_status') == 'ASN' ? 'selected' : '' }}>ASN</option>
                                <option value="Non-ASN" {{ old('pemakai_status') == 'Non-ASN' ? 'selected' : '' }}>Non-ASN</option>
                                <option value="Anggota DPRD" {{ old('pemakai_status') == 'Anggota DPRD' ? 'selected' : '' }}>Anggota DPRD</option>
                                <option value="Lainnya" {{ old('pemakai_status') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Jabatan</label>
                            <input type="text" name="pemakai_jabatan" class="form-control" value="{{ old('pemakai_jabatan') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Identitas (NIP/NIK/KTP) <span class="text-danger">*</span></label>
                        <input type="text" name="pemakai_identitas" class="form-control" value="{{ old('pemakai_identitas') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Alamat Pemakai</label>
                        <textarea name="pemakai_alamat" class="form-control" rows="2">{{ old('pemakai_alamat') }}</textarea>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- Row 2: Dokumen --}}
            <div class="row">
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">3. Dokumen Sumber (BAST)</h5>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nomor BAST</label>
                            <input type="text" name="bast_nomor" class="form-control" value="{{ old('bast_nomor') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tanggal BAST</label>
                            <input type="date" name="bast_tanggal" class="form-control" value="{{ old('bast_tanggal') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Upload Scan BAST (PDF/Gambar)</label>
                        <div class="custom-file">
                            <input type="file" name="bast_file" class="custom-file-input" id="bastFile">
                            <label class="custom-file-label" for="bastFile">Pilih file...</label>
                        </div>
                        <small class="text-danger">Maksimal ukuran file 2MB.</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">4. Dokumen Lain (Opsional)</h5>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Nama Dokumen</label>
                            <input type="text" name="dokumen_lain_nama" class="form-control" placeholder="Contoh: SK Bupati" value="{{ old('dokumen_lain_nama') }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Nomor Dokumen</label>
                            <input type="text" name="dokumen_lain_nomor" class="form-control" value="{{ old('dokumen_lain_nomor') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Dokumen Lain</label>
                        <input type="date" name="dokumen_lain_tanggal" class="form-control" value="{{ old('dokumen_lain_tanggal') }}">
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="form-group">
                <label class="font-weight-bold text-dark">Keterangan Tambahan</label>
                <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="reset" class="btn btn-secondary mr-2">
                    <i class="fas fa-sync-alt fa-sm"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary shadow-sm">
                    <i class="fas fa-save fa-sm text-white-50"></i> Simpan Data
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script agar nama file muncul saat upload (Bootstrap Custom File Input)
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>
@endpush