<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Hanya KIB Models (A - D)
use App\Models\Tanah;
use App\Models\Peralatan;
use App\Models\Gedung;
use App\Models\Jalan;

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
                        $item->tanah_nomor_register, // NIBAR Dihapus
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
                        '-', 
                        $item->tanah_status_penggunaan, 
                        $item->tanah_keterangan
                    ];
                }
                $highestColumn = 'S'; // Maju dari T ke S
                $headerEndRow = 5;
                $dataStartRow = 6;
                
                $sheet->mergeCells('A1:'.$highestColumn.'1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));
                $sheet->mergeCells('A3:A4')->setCellValue('A3', 'No.');
                $sheet->mergeCells('B3:B4')->setCellValue('B3', 'Kode Barang');
                $sheet->mergeCells('C3:C4')->setCellValue('C3', 'Nama Barang');
                $sheet->mergeCells('D3:D4')->setCellValue('D3', 'Nomor Register');
                $sheet->mergeCells('E3:E4')->setCellValue('E3', 'Spesifikasi Lainnya');
                $sheet->mergeCells('F3:F4')->setCellValue('F3', 'Jumlah');
                $sheet->mergeCells('G3:G4')->setCellValue('G3', 'Satuan');
                $sheet->mergeCells('H3:H4')->setCellValue('H3', 'Lokasi');
                $sheet->mergeCells('I3:I4')->setCellValue('I3', 'Titik Koordinat');
                $sheet->mergeCells('J3:L3')->setCellValue('J3', 'Bukti Kepemilikan');
                $sheet->mergeCells('M3:M4')->setCellValue('M3', 'Harga Satuan (Rp)');
                $sheet->mergeCells('N3:N4')->setCellValue('N3', 'Nilai Perolehan (Rp)');
                $sheet->mergeCells('O3:O4')->setCellValue('O3', 'Cara Perolehan');
                $sheet->mergeCells('P3:P4')->setCellValue('P3', 'Tanggal Perolehan');
                $sheet->mergeCells('Q3:Q4')->setCellValue('Q3', 'Tanggal Penggunaan');
                $sheet->mergeCells('R3:R4')->setCellValue('R3', 'Status');
                $sheet->mergeCells('S3:S4')->setCellValue('S3', 'Keterangan');
                
                $sheet->setCellValue('J4', 'Nomor');
                $sheet->setCellValue('K4', 'Tanggal');
                $sheet->setCellValue('L4', 'Nama Kepemilikan');
                
                for ($i = 0; $i < 19; $i++) {
                    $sheet->setCellValue(chr(65 + $i) . '5', '(' . ($i + 1) . ')');
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
                        $item->alat_nomor_register, // NIBAR Dihapus
                        $item->alat_merk_tipe, 
                        $item->alat_spesifikasi_barang, 
                        $item->alat_spesifikasi_lainnya, 
                        $item->alat_nomor_rangka,
                        '-', 
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
                $highestColumn = 'S'; // Maju dari T ke S
                $headerEndRow = 5;
                $dataStartRow = 6;
                $sheet->mergeCells('A1:'.$highestColumn.'1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));

                $sheet->mergeCells('A3:A4')->setCellValue('A3', 'No');
                $sheet->mergeCells('B3:B4')->setCellValue('B3', 'Kode Barang');
                $sheet->mergeCells('C3:C4')->setCellValue('C3', 'Nama Barang');
                $sheet->mergeCells('D3:D4')->setCellValue('D3', 'Nomor Register');
                $sheet->mergeCells('E3:G3')->setCellValue('E3', 'Spesifikasi Barang');
                $sheet->mergeCells('H3:K3')->setCellValue('H3', 'Kendaraan (Diisi*)');
                $sheet->mergeCells('L3:L4')->setCellValue('L3', 'Jumlah');
                $sheet->mergeCells('M3:M4')->setCellValue('M3', 'Satuan');
                $sheet->mergeCells('N3:N4')->setCellValue('N3', 'Harga Satuan (Rp)');
                $sheet->mergeCells('O3:O4')->setCellValue('O3', 'Nilai Perolehan (Rp)');
                $sheet->mergeCells('P3:P4')->setCellValue('P3', 'Cara Perolehan');
                $sheet->mergeCells('Q3:Q4')->setCellValue('Q3', 'Tanggal Perolehan');
                $sheet->mergeCells('R3:R4')->setCellValue('R3', 'Status Penggunaan');
                $sheet->mergeCells('S3:S4')->setCellValue('S3', 'Keterangan');

                $sheet->setCellValue('E4', 'Merek/Tipe');
                $sheet->setCellValue('F4', 'Ukuran');
                $sheet->setCellValue('G4', 'Spesifikasi Lainnya');
                $sheet->setCellValue('H4', 'No. Rangka');
                $sheet->setCellValue('I4', 'No. Mesin');
                $sheet->setCellValue('J4', 'No. Polisi');
                $sheet->setCellValue('K4', 'BPKB');

                for ($i = 0; $i < 19; $i++) {
                    $sheet->setCellValue(chr(65 + $i) . '5', '(' . ($i + 1) . ')');
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
                        $item->gedung_nomor_register, // NIBAR Dihapus
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
                $highestColumn = 'R'; // Maju dari S ke R
                $headerEndRow = 4;
                $dataStartRow = 5;
                $sheet->mergeCells('A1:'.$highestColumn.'1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));

                $sheet->setCellValue('A3', 'No.');
                $sheet->setCellValue('B3', 'Kode Barang');
                $sheet->setCellValue('C3', 'Nama Barang');
                $sheet->setCellValue('D3', 'Nomor Register');
                $sheet->setCellValue('E3', 'Spesifikasi Barang');
                $sheet->setCellValue('F3', 'Spesifikasi Lainnya');
                $sheet->setCellValue('G3', 'Lantai');
                $sheet->setCellValue('H3', 'Luas/Jumlah');
                $sheet->setCellValue('I3', 'Satuan');
                $sheet->setCellValue('J3', 'Lokasi / Alamat');
                $sheet->setCellValue('K3', 'Titik Koordinat');
                $sheet->setCellValue('L3', 'Status Tanah');
                $sheet->setCellValue('M3', 'Harga Satuan (Rp)');
                $sheet->setCellValue('N3', 'Nilai Perolehan (Rp)');
                $sheet->setCellValue('O3', 'Cara Perolehan');
                $sheet->setCellValue('P3', 'Tanggal Perolehan');
                $sheet->setCellValue('Q3', 'Status Penggunaan');
                $sheet->setCellValue('R3', 'Keterangan');

                for ($i = 0; $i < 18; $i++) {
                    $sheet->setCellValue(chr(65 + $i) . '4', '(' . ($i + 1) . ')');
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
                        $item->jalan_nomor_register, // NIBAR Dihapus
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
                $highestColumn = 'R'; // Maju dari S ke R
                $headerEndRow = 4;
                $dataStartRow = 5;
                $sheet->mergeCells('A1:'.$highestColumn.'1')->setCellValue('A1', strtoupper($title . ' - LOKASI: ' . $lokasi));
                
                $sheet->setCellValue('A3', 'No.');
                $sheet->setCellValue('B3', 'Kode Barang');
                $sheet->setCellValue('C3', 'Nama Barang');
                $sheet->setCellValue('D3', 'Nomor Register');
                $sheet->setCellValue('E3', 'Spesifikasi Barang');
                $sheet->setCellValue('F3', 'Spesifikasi Lainnya');
                $sheet->setCellValue('G3', 'No. Ruas Jalan/Irigasi');
                $sheet->setCellValue('H3', 'Lokasi / Alamat');
                $sheet->setCellValue('I3', 'Titik Koordinat');
                $sheet->setCellValue('J3', 'Status Tanah');
                $sheet->setCellValue('K3', 'Jumlah');
                $sheet->setCellValue('L3', 'Satuan');
                $sheet->setCellValue('M3', 'Harga Satuan (Rp)');
                $sheet->setCellValue('N3', 'Nilai Perolehan (Rp)');
                $sheet->setCellValue('O3', 'Cara Perolehan');
                $sheet->setCellValue('P3', 'Tanggal Perolehan');
                $sheet->setCellValue('Q3', 'Status Penggunaan');
                $sheet->setCellValue('R3', 'Keterangan');
                
                for ($i = 0; $i < 18; $i++) {
                    $sheet->setCellValue(chr(65 + $i) . '4', '(' . ($i + 1) . ')');
                }
                $sheet->fromArray($data, NULL, 'A'.$dataStartRow);
                break;

            default:
                return redirect()->back()->with('error', 'Jenis KIB untuk ekspor tidak valid.');
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
        
        // Style Baris Nomor Kolom Khusus KIB A - D
        if (in_array($menu, ['tanah', 'peralatan', 'gedung', 'jalan'])) {
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
        
        // Perbaikan Lebar Kolom Tertentu jika AutoSize terlalu lebar
        if ($highestColumn >= 'B') $sheet->getColumnDimension('B')->setWidth(20);
        if ($highestColumn >= 'C') $sheet->getColumnDimension('C')->setWidth(30);

        // Output File
        $filename = "Export_{$menu}_{$lokasi}_" . date('Y-m-d') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}