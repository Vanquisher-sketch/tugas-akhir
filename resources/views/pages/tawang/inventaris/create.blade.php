@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-plus-circle mr-2"></i> Tambah Data Inventaris Ruangan - {{ $room->ruangan_nama }}
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('lokasi.inventaris.store', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan]) }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="font-weight-bold text-danger">Pilih Barang dari Master Peralatan (KIB B) <span class="text-danger">*</span></label>
                
                {{-- Dropdown Cerdas dengan Fallback Nama Kolom --}}
                <select id="peralatan_select" class="form-control font-weight-bold" style="border: 2px solid #4e73df; font-size: 14px;" required>
                    <option value="" disabled selected>-- Klik di sini untuk memilih Aset Master KIB B --</option>
                    @foreach($allPeralatan as $p)
                        <option value="{{ $p->alat_kode_barang ?? $p->kode_barang }}" 
                                data-kode="{{ $p->alat_kode_barang ?? $p->kode_barang }}"
                                data-nama="{{ $p->alat_nama_barang ?? $p->nama_barang }}"
                                data-merk="{{ $p->alat_merk_tipe ?? $p->merk_tipe ?? $p->merk ?? '-' }}"
                                data-tahun="{{ $p->alat_tahun_perolehan ?? $p->tahun_perolehan ?? $p->tahun ?? date('Y') }}"
                                data-satuan="{{ $p->alat_satuan ?? $p->satuan ?? 'Buah' }}"
                                data-register="{{ $p->alat_nomor_register ?? $p->nomor_register ?? '-' }}"
                                data-kondisi="{{ $p->alat_kondisi ?? $p->kondisi ?? 'Baik' }}"
                                data-spesifikasi="{{ $p->alat_spesifikasi_barang ?? $p->spesifikasi_barang ?? '-' }}"
                                data-keterangan="{{ $p->alat_keterangan ?? $p->keterangan ?? '-' }}"
                                data-sisa="{{ $p->sisa_stok }}">
                            {{ $p->alat_kode_barang ?? $p->kode_barang }} - {{ $p->alat_nama_barang ?? $p->nama_barang }} (Sisa Stok: {{ $p->sisa_stok }} {{ $p->alat_satuan ?? $p->satuan ?? 'Buah' }})
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-primary font-weight-bold mt-2">
                    <i class="fas fa-info-circle"></i> Seluruh kolom di bawah ini akan terisi otomatis mengikuti master KIB B. Anda hanya perlu mengisi kolom JUMLAH.
                </small>
            </div>

            <hr class="my-4">

            {{-- HANYA KOLOM JUMLAH YANG BISA DIEDIT USER --}}
            <div class="row mb-4 bg-light p-3 rounded" style="border: 1px dashed #f6c23e;">
                <div class="col-md-12 text-center mb-2">
                    <label class="font-weight-bold text-warning text-uppercase" style="font-size: 14px;">Masukkan Jumlah Barang Untuk Ruangan Ini</label>
                </div>
                <div class="col-md-6 offset-md-3 form-group text-center">
                    <div class="input-group input-group-lg">
                        <input type="number" id="jumlah" name="jumlah" class="form-control text-center font-weight-bold text-dark" value="1" min="1" required style="font-size: 20px;">
                        <div class="input-group-append">
                            <span class="input-group-text font-weight-bold bg-white text-dark" id="satuan_addon">Satuan</span>
                        </div>
                    </div>
                    <small id="sisa_info" class="form-text text-danger font-weight-bold mt-2">Pilih barang master terlebih dahulu.</small>
                </div>
            </div>

            {{-- ======================================================= --}}
            {{-- KOLOM READONLY (TERKUNCI) - OTOMATIS DARI KIB B         --}}
            {{-- ======================================================= --}}
            <h6 class="font-weight-bold text-secondary mb-3 border-bottom pb-2">Detail Master Barang (Otomatis & Terkunci)</h6>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-muted" style="font-size: 12px;">Kode Barang</label>
                    <input type="text" id="kode_barang" name="kode_barang" class="form-control bg-light text-muted" readonly required>
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-muted" style="font-size: 12px;">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" class="form-control bg-light text-muted" readonly required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-muted" style="font-size: 12px;">Merk / Tipe</label>
                    <input type="text" id="merk_tipe" name="merk_tipe" class="form-control bg-light text-muted" readonly>
                </div>
                {{-- 🌟 KOLOM TAHUN DIKEMBALIKAN KE SINI 🌟 --}}
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-muted" style="font-size: 12px;">Tahun Perolehan</label>
                    <input type="text" id="tahun_perolehan" name="tahun_perolehan" class="form-control bg-light text-muted" readonly>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-muted" style="font-size: 12px;">Nomor Register</label>
                    <input type="text" id="nomor_register" name="nomor_register" class="form-control bg-light text-muted" readonly>
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-muted" style="font-size: 12px;">Kondisi Barang</label>
                    <input type="text" id="kondisi" name="kondisi" class="form-control bg-light text-muted font-weight-bold" readonly required>
                </div>
            </div>

            {{-- Hidden input untuk satuan agar tetap tersubmit --}}
            <input type="hidden" id="satuan_hidden" name="satuan">

            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-muted" style="font-size: 12px;">Spesifikasi Barang</label>
                    <textarea id="spesifikasi_barang" name="spesifikasi_barang" class="form-control bg-light text-muted" rows="2" readonly></textarea>
                </div>
                <div class="col-md-6 form-group">
                    <label class="font-weight-bold text-muted" style="font-size: 12px;">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" class="form-control bg-light text-muted" rows="2" readonly></textarea>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan]) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan ke Inventaris</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('peralatan_select').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        
        // Isi Form Readonly
        document.getElementById('kode_barang').value = opt.getAttribute('data-kode') || '';
        document.getElementById('nama_barang').value = opt.getAttribute('data-nama') || '';
        document.getElementById('merk_tipe').value = opt.getAttribute('data-merk') || '-';
        document.getElementById('nomor_register').value = opt.getAttribute('data-register') || '-';
        document.getElementById('kondisi').value = opt.getAttribute('data-kondisi') || 'Baik';
        document.getElementById('spesifikasi_barang').value = opt.getAttribute('data-spesifikasi') || '-';
        document.getElementById('keterangan').value = opt.getAttribute('data-keterangan') || '-';

        // 🌟 JavaScript Pengekstrak Tahun 🌟
        const tglPenuh = opt.getAttribute('data-tahun');
        let tahun = '-';
        if (tglPenuh && tglPenuh !== '' && tglPenuh !== 'null') {
            // Jika datanya YYYY-MM-DD, potong ambil 4 angka di depan
            tahun = tglPenuh.substring(0, 4); 
        }
        document.getElementById('tahun_perolehan').value = tahun;

        // Update Satuan
        const satuanText = opt.getAttribute('data-satuan') || 'Buah';
        document.getElementById('satuan_addon').innerText = satuanText;
        document.getElementById('satuan_hidden').value = satuanText;

        // Kontrol Limit Jumlah Input
        const sisa = parseInt(opt.getAttribute('data-sisa')) || 0;
        const jumlahInput = document.getElementById('jumlah');
        const sisaInfo = document.getElementById('sisa_info');

        jumlahInput.max = sisa; 
        
        if(sisa <= 0) {
            sisaInfo.innerText = "Stok HABIS! Barang ini sudah didistribusikan semua ke ruangan.";
            sisaInfo.className = "form-text text-danger font-weight-bold mt-2";
            jumlahInput.value = 0;
            jumlahInput.readOnly = true;
        } else {
            sisaInfo.innerText = "Stok maksimal yang bisa diinput: " + sisa + " " + satuanText;
            sisaInfo.className = "form-text text-success font-weight-bold mt-2";
            jumlahInput.value = 1;
            jumlahInput.readOnly = false;
        }
    });
</script>
@endpush