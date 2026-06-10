@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Data Inventaris Ruangan</h1>
        <a href="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    {{-- ALERT ERROR FLASH --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow">
            <i class="fas fa-exclamation-triangle mr-2"></i><strong>Pemberitahuan Sistem:</strong>
            <p class="mb-0 mt-1">{{ session('error') }}</p>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-plus mr-2"></i>Form Entri Aset Ruangan: {{ $room->name }}</h6>
        </div>
        <div class="card-body text-dark">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('lokasi.inventaris.store', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan]) }}" method="POST">
                @csrf
                
                <div class="row">
                    {{-- DROPDOWN HYBRID --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Pilih Sumber Aset Barang <span class="text-danger">*</span></label>
                        <select name="sumber_aset" id="sumber_aset" class="form-control select2" style="width: 100%;" required>
                            <option value="">-- Pilih Kode Barang Master / Input Manual --</option>
                            <option value="MANUAL" class="font-weight-bold text-primary">✍️ [INPUT MANUAL] Meja, Kursi, Lemari, dll</option>
                            
                            <optgroup label="Peralatan & Mesin (KIB B)">
                                @foreach($masterPeralatan as $peralatan)
                                    @if($peralatan->sisa_stok > 0)
                                        <option value="{{ $peralatan->kode_barang }}" 
                                                data-nama="{{ $peralatan->nama_barang }}"
                                                data-merk="{{ $peralatan->merk_tipe }}"
                                                data-tahun="{{ $peralatan->tahun_perolehan }}"
                                                data-spek="{{ $peralatan->spesifikasi_barang ?? $peralatan->keterangan }}">
                                            {{ $peralatan->kode_barang }} - {{ $peralatan->nama_barang }} (Sisa Stok: {{ $peralatan->sisa_stok }} {{ $peralatan->satuan ?? 'Unit' }})
                                        </option>
                                    @else
                                        <option value="" disabled class="text-danger bg-light">
                                            🚫 {{ $peralatan->kode_barang }} - {{ $peralatan->nama_barang }} [STOK HABIS]
                                        </option>
                                    @endif
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    {{-- Kode Barang --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Kode Barang <span class="text-danger">*</span></label>
                        <input type="text" id="kode_barang" name="kode_barang" class="form-control" value="{{ old('kode_barang') }}" placeholder="Contoh: 1.02.01.01.050" required>
                    </div>
                </div>

                <div class="row">
                    {{-- Nama Barang --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" id="nama_barang" name="nama_barang" class="form-control" value="{{ old('nama_barang') }}" placeholder="Contoh: Meja Kerja Kayu / Kursi Lipat" required>
                    </div>

                    {{-- NIBAR --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">NIBAR (Nomor Induk Barang)</label>
                        <input type="text" name="nibar" class="form-control" value="{{ old('nibar') }}" placeholder="Isi NIBAR jika ada">
                    </div>
                </div>

                <div class="row">
                    {{-- Nomor Register --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Nomor Register</label>
                        <input type="text" name="nomor_register" class="form-control" value="{{ old('nomor_register') }}" placeholder="Contoh: 0001">
                    </div>

                    {{-- Merk / Tipe --}}
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold">Merk / Tipe</label>
                        <input type="text" id="merk_tipe" name="merk_tipe" class="form-control" value="{{ old('merk_tipe') }}" placeholder="Contoh: Meja Setengah Biro / Chitose">
                    </div>
                </div>

                <div class="row">
                    {{-- Tahun Perolehan --}}
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Tahun Perolehan <span class="text-danger">*</span></label>
                        <input type="number" id="tahun_perolehan" name="tahun_perolehan" class="form-control" value="{{ old('tahun_perolehan', date('Y')) }}" placeholder="Contoh: 2026" required>
                    </div>

                    {{-- Jumlah --}}
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Volume Jumlah Penempatan <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah', 1) }}" min="1" required>
                    </div>

                    {{-- Satuan --}}
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold">Satuan <span class="text-danger">*</span></label>
                        <select name="satuan" class="form-control text-dark font-weight-bold" required>
                            @foreach($daftarSatuan as $sat)
                                <option value="{{ $sat }}" {{ old('satuan') == $sat ? 'selected' : '' }}>{{ $sat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    {{-- Kondisi --}}
                    <div class="col-md-4 form-group">
                        <label class="font-weight-bold text-primary">Kondisi Fisik Barang <span class="text-danger">*</span></label>
                        <select name="kondisi" class="form-control font-weight-bold text-dark" required>
                            <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>🟢 Baik</option>
                            <option value="Rusak Ringan" {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>🟡 Rusak Ringan</option>
                            <option value="Rusak Berat" {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>🔴 Rusak Berat (Auto Jurnal Rusak)</option>
                        </select>
                    </div>
                </div>

                {{-- Spesifikasi Barang --}}
                <div class="form-group">
                    <label class="font-weight-bold">Spesifikasi Barang</label>
                    <textarea id="spesifikasi_barang" name="spesifikasi_barang" class="form-control" rows="2" placeholder="Detail deskripsi fisik spesifikasi meja/kursi/laptop...">{{ old('spesifikasi_barang') }}</textarea>
                </div>

                {{-- Keterangan --}}
                <div class="form-group">
                    <label class="font-weight-bold">Keterangan / Catatan Tambahan Ruangan</label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan posisi penempatan di ruangan...">{{ old('keterangan') }}</textarea>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-secondary mr-2">Reset Form</button>
                    <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-save mr-1"></i> Simpan Data Aset</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIKA HYBRID INPUT --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#sumber_aset').on('change select2:select', function () {
            var selectedOption = $(this).find(':selected');
            var val = selectedOption.val();
            
            if (val === "MANUAL") {
                // Bersihkan form agar admin bebas ketik dari awal
                $('#kode_barang').val('').prop('readonly', false).focus();
                $('#nama_barang').val('').prop('readonly', false);
                $('#merk_tipe').val('').prop('readonly', false);
                $('#tahun_perolehan').val('{{ date("Y") }}').prop('readonly', false);
                $('#spesifikasi_barang').val('').prop('readonly', false);
            } else if (val !== "") {
                // Tarik otomatis data master dari KIB B
                var kode = val;
                var nama = selectedOption.attr('data-nama') || '';
                var merk = selectedOption.attr('data-merk') || '';
                var tahun = selectedOption.attr('data-tahun') || '';
                var spek = selectedOption.attr('data-spek') || '';

                // Mengisi data secara otomatis, namun properti readonly di-set FALSE agar bisa diedit kembali
                $('#kode_barang').val(kode).prop('readonly', false);
                $('#nama_barang').val(nama).prop('readonly', false);
                $('#merk_tipe').val(merk).prop('readonly', false);
                $('#tahun_perolehan').val(tahun).prop('readonly', false);
                $('#spesifikasi_barang').val(spek).prop('readonly', false);
            }
        });

        // Cek kondisi awal saat halaman dimuat (untuk menjaga error validation Laravel / 'old value')
        if ($('#sumber_aset').val() !== "") {
            $('#kode_barang').prop('readonly', false);
            $('#nama_barang').prop('readonly', false);
            $('#merk_tipe').prop('readonly', false);
            $('#tahun_perolehan').prop('readonly', false);
            $('#spesifikasi_barang').prop('readonly', false);
        }
    });
</script>
@endsection