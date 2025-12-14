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
use App\Models\Room;
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
        $headerRowStart = 3; // Baris untuk memulai header
        $headerEndRow = 4;   // Default, nanti diubah per case
        $dataStartRow = 5;   // Default
        $highestColumn = 'A'; 
        
        switch ($menu) {
            case 'tanah':
                // ... (Kode Tanah Tetap Sama) ...
                $title = 'Laporan Data Tanah (KIB A)';
                $collection = Tanah::where('lokasi', $lokasi)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [
                        $key + 1, $item->kode_barang, $item->nama_barang, $item->nibar, $item->nomor_register,
                        $item->spesifikasi_lainnya, $item->jumlah, $item->satuan, $item->lokasi,
                        $item->titik_koordinat, $item->bukti_nomor,
                        $item->bukti_tanggal ? \Carbon\Carbon::parse($item->bukti_tanggal)->format('d-m-Y') : '',
                        $item->bukti_nama_kepemilikan, $item->harga_satuan, $item->nilai_perolehan,
                        $item->cara_perolehan,
                        $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d-m-Y') : '',
                        $item->tanggal_penggunaan ? \Carbon\Carbon::parse($item->tanggal_penggunaan)->format('d-m-Y') : '',
                        $item->status, $item->keterangan
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
                // ... (Kode Peralatan Tetap Sama) ...
                $title = 'Laporan Data Peralatan & Mesin (KIB B)';
                $collection = Peralatan::where('lokasi', $lokasi)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [
                        $key + 1, $item->kode_barang, $item->nama_barang, $item->nibar, $item->nomor_register,
                        $item->merek_tipe, $item->ukuran, $item->spesifikasi_lainnya, $item->nomor_rangka,
                        $item->nomor_mesin, $item->nomor_polisi, $item->nomor_bpkb, $item->jumlah,
                        $item->satuan, $item->harga_satuan, $item->nilai_perolehan, $item->cara_perolehan,
                        $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d-m-Y') : '',
                        $item->status_penggunaan, $item->keterangan
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
                // REVISI: Disesuaikan dengan Model Gedung Terbaru
                // Kolom: No, Kode, Nama, Nibar, Reg, Spesifikasi, Spesifikasi Lain, Lantai, Luas(Jml, Satuan), Lokasi(Lok), Koordinat, Status Tanah, Tgl, Harga, Nilai, Cara, Status Guna, Ket
                $title = 'Laporan Data Gedung & Bangunan (KIB C)';
                $collection = Gedung::where('lokasi', $lokasi)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [
                        $key + 1, 
                        $item->kode_barang, 
                        $item->nama_barang, 
                        $item->nibar, 
                        $item->nomor_register,
                        $item->spesifikasi_barang, // REVISI: nama kolom
                        $item->spesifikasi_lainnya, // REVISI: kolom baru
                        $item->jumlah_lantai,       // REVISI: kolom baru
                        $item->jumlah, 
                        $item->satuan,
                        $item->Lok,              // REVISI: 'Lok' huruf besar
                        $item->titik_koordinat, 
                        $item->status_kepemilikan_tanah,
                        $item->harga_satuan, 
                        $item->nilai_perolehan, 
                        $item->cara_perolehan,
                        $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d-m-Y') : '',
                        $item->status_penggunaan, 
                        $item->keterangan
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
                // REVISI: Disesuaikan dengan Model Jalan Terbaru
                // Kolom: No, Kode, Nama, Nibar, Reg, Spesifikasi, Spesifikasi Lain, No Ruas, Lokasi(Lok), Koordinat, Status Tanah, Jml, Satuan, Harga, Nilai, Cara, Tgl, Status Guna, Ket
                $title = 'Laporan Data Jalan, Irigasi & Jaringan (KIB D)';
                $collection = Jalan::where('lokasi', $lokasi)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [
                        $key + 1, 
                        $item->kode_barang, 
                        $item->nama_barang, 
                        $item->nibar, 
                        $item->nomor_register,
                        $item->spesifikasi_barang,  // REVISI
                        $item->spesifikasi_lainnya, // REVISI: kolom baru
                        $item->nomor_ruas_jalan_jembatan_irigasi, // REVISI: nama kolom panjang
                        $item->Lok,              // REVISI: 'Lok' huruf besar
                        $item->titik_koordinat,
                        $item->status_kepemilikan_tanah, // REVISI: sesuaikan nama kolom
                        $item->jumlah, 
                        $item->satuan, 
                        $item->harga_satuan, 
                        $item->nilai_perolehan,
                        $item->cara_perolehan,
                        $item->tanggal_perolehan ? \Carbon\Carbon::parse($item->tanggal_perolehan)->format('d-m-Y') : '',
                        $item->status_penggunaan, 
                        $item->keterangan
                    ];
                }
                $highestColumn = 'S'; // Tambah 1 kolom (Spesifikasi Lainnya)
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
                // ... (Kode Rusak Tetap Sama) ...
                $title = 'Laporan Data Barang Rusak Berat';
                $headers = ['No.', 'No. ID Pemda', 'Nama/Jenis Barang', 'Spesifikasi', 'No. Polisi', 'Tahun Perolehan', 'Harga Perolehan (Rp)', 'Kondisi', 'Tercatat di KIB', 'Keterangan'];
                $collection = Rusak::where('lokasi', $lokasi)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [$key + 1, $item->id_pemda, $item->nama_barang, $item->spesifikasi, $item->no_polisi, $item->tahun_perolehan, $item->harga_perolehan, $item->kondisi, $item->tercatat_di_kib, $item->keterangan];
                }
                
                $highestColumn = chr(64 + count($headers));
                $headerEndRow = 3;
                $dataStartRow = 4;
                $sheet->mergeCells('A1:' . $highestColumn . '1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->fromArray($data, NULL, 'A4');
                break;
            
            case 'ruangan':
                // ... (Kode Ruangan Tetap Sama) ...
                $title = 'Laporan Data Ruangan';
                $headers = ['No.', 'Nama Ruangan', 'Kode Ruangan'];
                $collection = Room::where('lokasi', $lokasi)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [$key + 1, $item->name, $item->kode_ruangan];
                }

                $highestColumn = chr(64 + count($headers));
                $headerEndRow = 3;
                $dataStartRow = 4;
                $sheet->mergeCells('A1:' . $highestColumn . '1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));
                $sheet->fromArray($headers, NULL, 'A3');
                $sheet->fromArray($data, NULL, 'A4');
                break;
            
            case 'inventaris':
                // ... (Kode Inventaris Tetap Sama) ...
                $roomId = $request->query('room_id');
                if (!$roomId) {
                    return redirect()->back()->with('error', 'Ruangan tidak ditemukan untuk ekspor.');
                }
                $room = Room::findOrFail($roomId);
                $title = 'Kartu Inventaris Ruangan: ' . $room->name;

                $collection = Inventaris::where('room_id', $roomId)->get();
                foreach ($collection as $key => $item) {
                    $data[] = [
                        $key + 1, $item->nibar, $item->nomor_register, $item->kode_barang, $item->nama_barang,
                        $item->spesifikasi_nama_barang, $item->merek_tipe, $item->tahun_perolehan,
                        $item->jumlah, $item->satuan, $item->keterangan
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
                $collection = Bmd::with('peralatan')->where('lokasi', $lokasi)->get();

                foreach ($collection as $key => $item) {
                    $data[] = [
                        $key + 1,
                        $item->peralatan->nibr ?? '-',      
                        $item->peralatan->kode_barang ?? '-',
                        $item->peralatan->nama_barang ?? '-',
                        $item->peralatan->merk_tipe ?? '-', 
                        $item->alamat_penggunaan,           
                        $item->pemakai_nama,
                        $item->pemakai_status,
                        $item->pemakai_jabatan,
                        "'" . $item->pemakai_identitas, 
                        $item->pemakai_alamat,
                        $item->bast_nomor,
                        $item->bast_tanggal ? \Carbon\Carbon::parse($item->bast_tanggal)->format('d-m-Y') : '-',
                        $item->dokumen_lain_nama,
                        $item->dokumen_lain_nomor,
                        $item->dokumen_lain_tanggal ? \Carbon\Carbon::parse($item->dokumen_lain_tanggal)->format('d-m-Y') : '-',
                        $item->keterangan
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
             // Jika ada sub-header/nomor kolom terpisah (seperti case tanah, peralatan, gedung, jalan yang headerEndRow-nya dipakai untuk sub-judul kolom)
             // kita perlu memastikan baris nomor kolom (1), (2), (3) itu yang diwarnai abu.
             // Di logika case di atas:
             // Tanah/Peralatan/Inventaris/BMD: headerEndRow=5, dataStart=6. Baris 5 adalah (1),(2).
             // Gedung/Jalan: headerEndRow=4, dataStart=5. Baris 4 adalah (1),(2).
             // Jadi logikanya bisa pakai $headerEndRow jika di set tepat di case masing-masing.
             
             // KOREKSI LOGIKA WARNA ABU:
             // Case Tanah, Peralatan, Inventaris, BMD: Baris nomor kolom ada di row 5.
             // Case Gedung, Jalan: Baris nomor kolom ada di row 4.
             // Variabel $headerEndRow di switch case sudah diset menunjuk ke baris terakhir sebelum data (yaitu baris nomor kolom).
             
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