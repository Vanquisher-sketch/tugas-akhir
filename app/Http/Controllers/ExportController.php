<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// KIB Models
use App\Models\Tanah;
use App\Models\Peralatan;
use App\Models\Gedung;
use App\Models\Jalan;
use App\Models\Rusak;

// Ruangan & Inventaris Models
use App\Models\Ruangan; // 🌟 REVISI: Model diganti dari Room menjadi Ruangan
use App\Models\Inventaris;

// Import Model BMD
use App\Models\Bmd;

class ExportController extends Controller
{
    public function export(Request $request, $lokasi, $menu)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $data = [];
        $title = 'Laporan Data';
        $headerRowStart = 3; 
        $headerEndRow = 4;   
        $dataStartRow = 5;   
        $highestColumn = 'A'; 
        
        switch ($menu) {
            case 'tanah':
                $title = 'Laporan Data Tanah (KIB A)';
                $collection = Tanah::where('lokasi', $lokasi)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [
                        $key + 1, 
                        $item->tanah_kode_barang, 
                        $item->tanah_nama_barang, 
                        $item->tanah_nibar, 
                        $item->tanah_nomor_register,
                        $item->tanah_spesifikasi_lainnya, 
                        $item->tanah_jumlah, 
                        $item->tanah_satuan, 
                        $item->tanah_lokasi_fisik,
                        $item->tanah_titik_koordinat, 
                        $item->tanah_bukti_nomor,
                        $item->tanah_bukti_tanggal ? \Carbon\Carbon::parse($item->tanah_bukti_tanggal)->format('d-m-Y') : '',
                        $item->tanah_nama_kepemilikan_dokumen, 
                        $item->tanah_harga_satuan, 
                        $item->tanah_nilai_perolehan,
                        $item->tanah_cara_perolehan,
                        $item->tanah_tanggal_perolehan ? \Carbon\Carbon::parse($item->tanah_tanggal_perolehan)->format('d-m-Y') : '',
                        '-', // Tanggal Penggunaan spesifik tidak dicatat di KIB A terbaru, dikosongkan
                        $item->tanah_status_penggunaan, 
                        $item->tanah_keterangan
                    ];
                }
                $highestColumn = 'T';
                $headerEndRow = 5;
                $dataStartRow = 6;
                
                $sheet->mergeCells('A1:'.$highestColumn.'1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));
                $sheet->mergeCells('A3:A4')->setCellValue('A3', 'No.');
                $sheet->mergeCells('B3:B4')->setCellValue('B3', 'Kode Barang');
                $sheet->mergeCells('C3:C4')->setCellValue('C3', 'Nama Barang');
                $sheet->mergeCells('D3:D4')->setCellValue('D3', 'NIBAR');
                $sheet->mergeCells('E3:E4')->setCellValue('E3', 'Nomor Register');
                $sheet->mergeCells('F3:F4')->setCellValue('F3', 'Spesifikasi Lainnya');
                $sheet->mergeCells('G3:G4')->setCellValue('G3', 'Jumlah');
                $sheet->mergeCells('H3:H4')->setCellValue('H3', 'Satuan');
                $sheet->mergeCells('I3:I4')->setCellValue('I3', 'Lokasi');
                $sheet->mergeCells('J3:J4')->setCellValue('J3', 'Titik Koordinat');
                $sheet->mergeCells('K3:M3')->setCellValue('K3', 'Bukti Kepemilikan');
                $sheet->mergeCells('N3:N4')->setCellValue('N3', 'Harga Satuan (Rp)');
                $sheet->mergeCells('O3:O4')->setCellValue('O3', 'Nilai Perolehan (Rp)');
                $sheet->mergeCells('P3:P4')->setCellValue('P3', 'Cara Perolehan');
                $sheet->mergeCells('Q3:Q4')->setCellValue('Q3', 'Tanggal Perolehan');
                $sheet->mergeCells('R3:R4')->setCellValue('R3', 'Tanggal Penggunaan');
                $sheet->mergeCells('S3:S4')->setCellValue('S3', 'Status');
                $sheet->mergeCells('T3:T4')->setCellValue('T3', 'Keterangan');
                
                $sheet->setCellValue('K4', 'Nomor');
                $sheet->setCellValue('L4', 'Tanggal');
                $sheet->setCellValue('M4', 'Nama Kepemilikan');
                
                for ($i = 0; $i < 20; $i++) {
                    $sheet->setCellValue(chr(65 + $i) . '5', '(' . ($i + 6) . ')');
                }
                $sheet->fromArray($data, NULL, 'A'.$dataStartRow);
                break;

            case 'peralatan':
                $title = 'Laporan Data Peralatan & Mesin (KIB B)';
                $collection = Peralatan::where('lokasi', $lokasi)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [
                        $key + 1, 
                        $item->alat_kode_barang, 
                        $item->alat_nama_barang, 
                        $item->alat_nibar, 
                        $item->alat_nomor_register,
                        $item->alat_merk_tipe, 
                        $item->alat_spesifikasi_barang, 
                        $item->alat_spesifikasi_lainnya, 
                        $item->alat_nomor_rangka,
                        '-', // Nomor Mesin dihilangkan dari skema
                        $item->alat_nomor_polisi, 
                        $item->alat_nomor_bpkb, 
                        $item->alat_jumlah,
                        $item->alat_satuan, 
                        $item->alat_harga_satuan, 
                        $item->alat_nilai_perolehan, 
                        $item->alat_cara_perolehan,
                        $item->alat_tanggal_perolehan ? \Carbon\Carbon::parse($item->alat_tanggal_perolehan)->format('d-m-Y') : '',
                        $item->alat_status_penggunaan, 
                        $item->alat_keterangan
                    ];
                }
                $highestColumn = 'T';
                $headerEndRow = 5;
                $dataStartRow = 6;
                $sheet->mergeCells('A1:'.$highestColumn.'1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));

                $sheet->mergeCells('A3:A4')->setCellValue('A3', 'No');
                $sheet->mergeCells('B3:B4')->setCellValue('B3', 'Kode Barang');
                $sheet->mergeCells('C3:C4')->setCellValue('C3', 'Nama Barang');
                $sheet->mergeCells('D3:D4')->setCellValue('D3', 'NIBAR');
                $sheet->mergeCells('E3:E4')->setCellValue('E3', 'Nomor Register');
                $sheet->mergeCells('F3:H3')->setCellValue('F3', 'Spesifikasi Barang');
                $sheet->mergeCells('I3:L3')->setCellValue('I3', 'Kendaraan (Diisi*)');
                $sheet->mergeCells('M3:M4')->setCellValue('M3', 'Jumlah');
                $sheet->mergeCells('N3:N4')->setCellValue('N3', 'Satuan');
                $sheet->mergeCells('O3:O4')->setCellValue('O3', 'Harga Satuan (Rp)');
                $sheet->mergeCells('P3:P4')->setCellValue('P3', 'Nilai Perolehan (Rp)');
                $sheet->mergeCells('Q3:Q4')->setCellValue('Q3', 'Cara Perolehan');
                $sheet->mergeCells('R3:R4')->setCellValue('R3', 'Tanggal Perolehan');
                $sheet->mergeCells('S3:S4')->setCellValue('S3', 'Status Penggunaan');
                $sheet->mergeCells('T3:T4')->setCellValue('T3', 'Keterangan');

                $sheet->setCellValue('F4', 'Merek/Tipe');
                $sheet->setCellValue('G4', 'Ukuran');
                $sheet->setCellValue('H4', 'Spesifikasi Lainnya');
                $sheet->setCellValue('I4', 'No. Rangka');
                $sheet->setCellValue('J4', 'No. Mesin');
                $sheet->setCellValue('K4', 'No. Polisi');
                $sheet->setCellValue('L4', 'BPKB');

                for ($i = 0; $i < 20; $i++) {
                    $sheet->setCellValue(chr(65 + $i) . '5', '(' . ($i + 6) . ')');
                }
                $sheet->fromArray($data, NULL, 'A'.$dataStartRow);
                break;

            case 'gedung':
                $title = 'Laporan Data Gedung & Bangunan (KIB C)';
                $collection = Gedung::where('lokasi', $lokasi)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [
                        $key + 1, 
                        $item->gedung_kode_barang, 
                        $item->gedung_nama_barang, 
                        $item->gedung_nibar, 
                        $item->gedung_nomor_register,
                        $item->gedung_spesifikasi_barang, 
                        $item->gedung_spesifikasi_lainnya, 
                        $item->gedung_jumlah_lantai,        
                        $item->gedung_jumlah, 
                        $item->gedung_satuan,
                        $item->gedung_lokasi_fisik,              
                        $item->gedung_titik_koordinat, 
                        $item->gedung_status_kepemilikan_tanah,
                        $item->gedung_harga_satuan, 
                        $item->gedung_nilai_perolehan, 
                        $item->gedung_cara_perolehan,
                        $item->gedung_tanggal_perolehan ? \Carbon\Carbon::parse($item->gedung_tanggal_perolehan)->format('d-m-Y') : '',
                        $item->gedung_status_penggunaan, 
                        $item->gedung_keterangan
                    ];
                }
                $highestColumn = 'S';
                $headerEndRow = 4;
                $dataStartRow = 5;
                $sheet->mergeCells('A1:'.$highestColumn.'1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));

                $sheet->setCellValue('A3', 'No.');
                $sheet->setCellValue('B3', 'Kode Barang');
                $sheet->setCellValue('C3', 'Nama Barang');
                $sheet->setCellValue('D3', 'NIBAR');
                $sheet->setCellValue('E3', 'Nomor Register');
                $sheet->setCellValue('F3', 'Spesifikasi Barang');
                $sheet->setCellValue('G3', 'Spesifikasi Lainnya');
                $sheet->setCellValue('H3', 'Lantai');
                $sheet->setCellValue('I3', 'Luas/Jumlah');
                $sheet->setCellValue('J3', 'Satuan');
                $sheet->setCellValue('K3', 'Lokasi / Alamat');
                $sheet->setCellValue('L3', 'Titik Koordinat');
                $sheet->setCellValue('M3', 'Status Tanah');
                $sheet->setCellValue('N3', 'Harga Satuan (Rp)');
                $sheet->setCellValue('O3', 'Nilai Perolehan (Rp)');
                $sheet->setCellValue('P3', 'Cara Perolehan');
                $sheet->setCellValue('Q3', 'Tanggal Perolehan');
                $sheet->setCellValue('R3', 'Status Penggunaan');
                $sheet->setCellValue('S3', 'Keterangan');

                for ($i = 0; $i < 19; $i++) {
                    $sheet->setCellValue(chr(65 + $i) . '4', '(' . ($i + 6) . ')');
                }
                $sheet->fromArray($data, NULL, 'A'.$dataStartRow);
                break;

            case 'jalan':
                $title = 'Laporan Data Jalan, Irigasi & Jaringan (KIB D)';
                $collection = Jalan::where('lokasi', $lokasi)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [
                        $key + 1, 
                        $item->jalan_kode_barang, 
                        $item->jalan_nama_barang, 
                        $item->jalan_nibar, 
                        $item->jalan_nomor_register,
                        $item->jalan_spesifikasi_barang,  
                        $item->jalan_spesifikasi_lainnya, 
                        $item->jalan_nomor_ruas_jalan_jembatan_irigasi, 
                        $item->jalan_lokasi_fisik,              
                        $item->jalan_titik_koordinat,
                        $item->jalan_status_kepemilikan_tanah, 
                        $item->jalan_jumlah, 
                        $item->jalan_satuan, 
                        $item->jalan_harga_satuan, 
                        $item->jalan_nilai_perolehan,
                        $item->jalan_cara_perolehan,
                        $item->jalan_tanggal_perolehan ? \Carbon\Carbon::parse($item->jalan_tanggal_perolehan)->format('d-m-Y') : '',
                        $item->jalan_status_penggunaan, 
                        $item->jalan_keterangan
                    ];
                }
                $highestColumn = 'S'; 
                $headerEndRow = 4;
                $dataStartRow = 5;
                $sheet->mergeCells('A1:'.$highestColumn.'1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));
                
                $sheet->setCellValue('A3', 'No.');
                $sheet->setCellValue('B3', 'Kode Barang');
                $sheet->setCellValue('C3', 'Nama Barang');
                $sheet->setCellValue('D3', 'NIBAR');
                $sheet->setCellValue('E3', 'Nomor Register');
                $sheet->setCellValue('F3', 'Spesifikasi Barang');
                $sheet->setCellValue('G3', 'Spesifikasi Lainnya');
                $sheet->setCellValue('H3', 'No. Ruas Jalan/Irigasi');
                $sheet->setCellValue('I3', 'Lokasi / Alamat');
                $sheet->setCellValue('J3', 'Titik Koordinat');
                $sheet->setCellValue('K3', 'Status Tanah');
                $sheet->setCellValue('L3', 'Jumlah');
                $sheet->setCellValue('M3', 'Satuan');
                $sheet->setCellValue('N3', 'Harga Satuan (Rp)');
                $sheet->setCellValue('O3', 'Nilai Perolehan (Rp)');
                $sheet->setCellValue('P3', 'Cara Perolehan');
                $sheet->setCellValue('Q3', 'Tanggal Perolehan');
                $sheet->setCellValue('R3', 'Status Penggunaan');
                $sheet->setCellValue('S3', 'Keterangan');
                
                for ($i = 0; $i < 19; $i++) {
                    $sheet->setCellValue(chr(65 + $i) . '4', '(' . ($i + 6) . ')');
                }
                $sheet->fromArray($data, NULL, 'A'.$dataStartRow);
                break;

            case 'rusak':
                $title = 'Laporan Data Barang Rusak Berat';
                $headers = ['No.', 'No. ID Pemda', 'Nama/Jenis Barang', 'Spesifikasi', 'No. Polisi', 'Tahun Perolehan', 'Harga Perolehan (Rp)', 'Kondisi', 'Tercatat di KIB', 'Keterangan'];
                $collection = Rusak::where('lokasi', $lokasi)->get();
                foreach ($collection as $key => $item) {
                    // Penyelarasan relasi Rusak ke Peralatan/Inventaris seperti di Jurnal Controller
                    $namaBarang = 'Aset Telah Diarsip';
                    $spesifikasi = '-';
                    $noPolisi = '-';
                    $tahunPerolehan = '-';
                    $harga = 0;
                    $kondisi = 'Rusak Berat';
                    
                    if ($item->rusak_jenis_asal === 'Peralatan') {
                        $detail = Peralatan::where('lokasi', $lokasi)->where('alat_kode_barang', $item->rusak_kode_barang)->first();
                        $namaBarang = $detail->alat_nama_barang ?? $namaBarang;
                        $spesifikasi = $detail->alat_merk_tipe ?? '-';
                        $noPolisi = $detail->alat_nomor_polisi ?? '-';
                        $tahunPerolehan = $detail->alat_tanggal_perolehan ?? '-';
                        $harga = $detail->alat_nilai_perolehan ?? 0;
                    } elseif ($item->rusak_jenis_asal === 'Inventaris') {
                        $detail = Inventaris::where('inv_kode_barang', $item->rusak_kode_barang)->first();
                        $namaBarang = $detail->inv_nama_barang ?? $namaBarang;
                        $spesifikasi = 'Inventaris Ruangan';
                        $tahunPerolehan = $detail->inv_tahun_perolehan ?? '-';
                    }

                    $data[] = [$key + 1, $item->rusak_kode_barang, $namaBarang, $spesifikasi, $noPolisi, $tahunPerolehan, $harga, $kondisi, $item->rusak_jenis_asal, $item->rusak_keterangan];
                }
                
                $highestColumn = chr(64 + count($headers));
                $headerEndRow = 3;
                $dataStartRow = 4;
                $sheet->mergeCells('A1:' . $highestColumn . '1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->fromArray($data, NULL, 'A4');
                break;
            
            case 'ruangan':
                $title = 'Laporan Data Ruangan';
                $headers = ['No.', 'Nama Ruangan', 'Kode Ruangan'];
                $collection = Ruangan::where('lokasi', $lokasi)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [$key + 1, $item->ruangan_nama, $item->kode_ruangan];
                }

                $highestColumn = chr(64 + count($headers));
                $headerEndRow = 3;
                $dataStartRow = 4;
                $sheet->mergeCells('A1:' . $highestColumn . '1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->fromArray($data, NULL, 'A4');
                break;
            
            case 'inventaris':
                $roomId = $request->query('room_id');
                if (!$roomId) {
                    return redirect()->back()->with('error', 'Ruangan tidak ditemukan untuk ekspor.');
                }
                $room = Ruangan::where('kode_ruangan', $roomId)->firstOrFail();
                $title = 'Kartu Inventaris Ruangan: ' . $room->ruangan_nama;

                $collection = Inventaris::where('inv_ruangan_kode', $room->kode_ruangan)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [
                        $key + 1, 
                        $item->inv_nibar, 
                        $item->inv_nomor_register, 
                        $item->inv_kode_barang, 
                        $item->inv_nama_barang,
                        $item->inv_spesifikasi_barang, 
                        $item->inv_merk_tipe, 
                        $item->inv_tahun_perolehan,
                        $item->inv_jumlah, 
                        $item->inv_satuan, 
                        $item->inv_keterangan
                    ];
                }
                
                $highestColumn = 'K';
                $headerEndRow = 5;
                $dataStartRow = 6;
                $sheet->mergeCells('A1:'.$highestColumn.'1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));

                $sheet->mergeCells('A3:A4')->setCellValue('A3', 'No');
                $sheet->mergeCells('B3:B4')->setCellValue('B3', 'NIBAR');
                $sheet->mergeCells('C3:C4')->setCellValue('C3', 'Nomor Register');
                $sheet->mergeCells('D3:D4')->setCellValue('D3', 'Kode Barang');
                $sheet->mergeCells('E3:E4')->setCellValue('E3', 'Nama Barang');
                $sheet->mergeCells('F3:F4')->setCellValue('F3', 'Spesifikasi Nama Barang');
                $sheet->mergeCells('G3:H3')->setCellValue('G3', 'Spesifikasi Barang');
                $sheet->mergeCells('I3:I4')->setCellValue('I3', 'Jumlah');
                $sheet->mergeCells('J3:J4')->setCellValue('J3', 'Satuan');
                $sheet->mergeCells('K3:K4')->setCellValue('K3', 'Ket.');

                $sheet->setCellValue('G4', 'Merek/Tipe');
                $sheet->setCellValue('H4', 'Tahun Perolehan');

                for ($i = 0; $i < 11; $i++) {
                    $sheet->setCellValue(chr(65 + $i) . '5', '(' . ($i + 5) . ')');
                }
                $sheet->fromArray($data, NULL, 'A'.$dataStartRow);
                break;

            case 'bmd':
                $title = 'DAFTAR PENGGUNAAN BMD (PERALATAN DAN MESIN)';
                $collection = Bmd::with(['peralatan', 'pegawai'])->where('lokasi', $lokasi)->get();

                foreach ($collection as $key => $item) {
                    $data[] = [
                        $key + 1,
                        $item->peralatan->alat_nibar ?? '-',      
                        $item->bmd_alat_kode ?? '-',
                        $item->peralatan->alat_nama_barang ?? '-',
                        $item->peralatan->alat_merk_tipe ?? '-', 
                        '-', // Alamat penggunaan dihilangkan dari skema, di set strip            
                        $item->pegawai->pegawai_nama ?? '-',
                        $item->bmd_pemakai_status,
                        $item->pegawai->pegawai_jabatan ?? '-',
                        "'" . $item->bmd_pemakai_identitas, 
                        $item->pegawai->pegawai_alamat ?? '-',
                        $item->bmd_bast_nomor,
                        $item->bmd_bast_tanggal ? \Carbon\Carbon::parse($item->bmd_bast_tanggal)->format('d-m-Y') : '-',
                        '-', // Dokumen lain dihilangkan dari form store, di set strip
                        '-',
                        '-',
                        $item->bmd_keterangan
                    ];
                }

                $highestColumn = 'Q'; 
                $headerEndRow = 5;    
                $dataStartRow = 6;    

                $sheet->mergeCells('A1:'.$highestColumn.'1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));

                $sheet->mergeCells('A3:A4')->setCellValue('A3', 'No');
                $sheet->mergeCells('B3:B4')->setCellValue('B3', 'NIBAR');
                $sheet->mergeCells('C3:C4')->setCellValue('C3', 'Kode Barang');
                $sheet->mergeCells('D3:D4')->setCellValue('D3', 'Nama Barang');
                $sheet->mergeCells('E3:E4')->setCellValue('E3', 'Spesifikasi');
                $sheet->mergeCells('F3:F4')->setCellValue('F3', 'Lokasi/Alamat Penggunaan');
                
                $sheet->mergeCells('G3:K3')->setCellValue('G3', 'Data Pemakai'); 
                $sheet->mergeCells('L3:M3')->setCellValue('L3', 'Dokumen BAST'); 
                $sheet->mergeCells('N3:P3')->setCellValue('N3', 'Dokumen Pendukung Lain'); 
                
                $sheet->mergeCells('Q3:Q4')->setCellValue('Q3', 'Keterangan');

                $sheet->setCellValue('G4', 'Nama');
                $sheet->setCellValue('H4', 'Status');
                $sheet->setCellValue('I4', 'Jabatan');
                $sheet->setCellValue('J4', 'Identitas');
                $sheet->setCellValue('K4', 'Alamat');
                
                $sheet->setCellValue('L4', 'Nomor');
                $sheet->setCellValue('M4', 'Tanggal');
                
                $sheet->setCellValue('N4', 'Nama Dokumen');
                $sheet->setCellValue('O4', 'Nomor');
                $sheet->setCellValue('P4', 'Tanggal');

                for ($i = 0; $i < 17; $i++) {
                    $sheet->setCellValue(chr(65 + $i) . '5', '(' . ($i + 1) . ')');
                }

                $sheet->fromArray($data, NULL, 'A'.$dataStartRow);
                break;

            default:
                return redirect()->back()->with('error', 'Jenis data untuk ekspor tidak valid.');
        }

        // --- SECTION STYLING (Global) ---

        // Style Judul Utama
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension('1')->setRowHeight(20);

        // Style Header Tabel
        $headerRange = 'A' . $headerRowStart . ':' . $highestColumn . $headerEndRow;
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];
        $sheet->getStyle($headerRange)->applyFromArray($headerStyle);
        
        // Style Baris Nomor Kolom
        if (in_array($menu, ['tanah', 'peralatan', 'gedung', 'jalan', 'inventaris', 'bmd'])) {
             $colNumRow = $headerEndRow; 
             
             $sheet->getStyle('A'.$headerEndRow.':'.$highestColumn.$headerEndRow)->getFont()->setBold(false);
             $sheet->getStyle('A'.$headerEndRow.':'.$highestColumn.$headerEndRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
             $sheet->getStyle('A'.$headerEndRow.':'.$highestColumn.$headerEndRow)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));
        }

        // Style Seluruh Tabel (Border)
        $highestRow = $sheet->getHighestRow();
        $fullTableRange = 'A' . $headerRowStart . ':' . $highestColumn . $highestRow;
        $tableStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ];
        $sheet->getStyle($fullTableRange)->applyFromArray($tableStyle);

        // AutoSize
        foreach (range('A', $highestColumn) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Output File
        $filename = "data_{$menu}_{$lokasi}_" . date('Y-m-d') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}