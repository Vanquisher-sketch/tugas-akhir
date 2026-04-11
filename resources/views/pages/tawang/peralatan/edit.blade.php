@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Data Peralatan & Mesin (KIB B) - {{ $peralatan->kode_barang }}</h6>
    </div>
    <div class="card-body">
        {{-- REVISI: Menggunakan kode_barang sebagai parameter --}}
        <form action="{{ route('lokasi.peralatan.update', ['lokasi' => $lokasi, 'peralatan' => $peralatan->kode_barang]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 border-right">
                    <div class="form-group">
                        <label for="kode_barang">Kode Barang</label>
                        <input type="text" class="form-control bg-light" value="{{ $peralatan->kode_barang }}" readonly>
                        <input type="hidden" name="kode_barang" value="{{ $peralatan->kode_barang }}">
                        <small class="text-muted">Kode barang tidak dapat diubah.</small>
                    </div>
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" class="form-control @error('nama_barang') is-invalid @enderror" name="nama_barang" value="{{ old('nama_barang', $peralatan->nama_barang) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="merk_tipe">Merk / Tipe</label>
                        <input type="text" class="form-control" name="merk_tipe" value="{{ old('merk_tipe', $peralatan->merk_tipe) }}">
                    </div>
                    <div class="form-group">
                        <label for="Lok">Lokasi Fisik / Alamat</label>
                        <textarea class="form-control" name="Lok" rows="2">{{ old('Lok', $peralatan->Lok) }}</textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nomor_polisi">Nomor Polisi (Plat)</label>
                        <input type="text" class="form-control" name="nomor_polisi" value="{{ old('nomor_polisi', $peralatan->nomor_polisi) }}">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga_satuan">Harga Satuan (Rp)</label>
                                <input type="text" class="form-control money" name="harga_satuan" value="{{ number_format($peralatan->harga_satuan, 0, ',', '.') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nilai_perolehan">Nilai Total (Rp)</label>
                                <input type="text" class="form-control money" name="nilai_perolehan" value="{{ number_format($peralatan->nilai_perolehan, 0, ',', '.') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status_penggunaan">Status Penggunaan</label>
                        <input type="text" class="form-control" name="status_penggunaan" value="{{ old('status_penggunaan', $peralatan->status_penggunaan) }}">
                    </div>
                </div>
            </div>

            <hr>
            <div class="text-right">
                <a href="{{ route('lokasi.peralatan.index', ['lokasi' => $lokasi]) }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.money').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = this.value.replace(/[^0-9]/g, '');
            this.value = value ? parseInt(value).toLocaleString('id-ID') : '';
        });
    });
</script>
@endsection