<?php

namespace App\Http\Controllers;

use App\Models\DetailPeralatan;
use App\Models\Inventaris;
use App\Models\Peralatan;
use App\Models\Ruangan;
use App\Models\Rusak;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class InventarisController extends Controller
{
    private const DAFTAR_SATUAN = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];

    /**
     * Helper privat untuk mengambil data ruangan serta memverifikasi lokasinya.
     */
    private function getRoom(string $lokasi, string $kode_ruangan): Ruangan
    {
        return Ruangan::where('kode_ruangan', $kode_ruangan)
            ->where('lokasi', $lokasi)
            ->firstOrFail();
    }

    /**
     * Helper privat untuk sinkronisasi status barang rusak ke tabel Rusak.
     */
    private function syncRusakStatus(string $kodeBarang, string $lokasi, ?string $keterangan = null): void
    {
        $hasDamaged = Inventaris::where('inv_kode_barang', $kodeBarang)
            ->where('lokasi', $lokasi)
            ->whereIn('inv_kondisi', ['Rusak Ringan', 'Rusak Berat'])
            ->exists();

        if ($hasDamaged) {
            Rusak::updateOrCreate(
                ['rusak_kode_barang' => $kodeBarang, 'lokasi' => $lokasi],
                [
                    'rusak_jenis_asal' => 'Inventaris',
                    'rusak_keterangan' => $keterangan,
                ]
            );
        } else {
            Rusak::where('rusak_kode_barang', $kodeBarang)
                ->where('lokasi', $lokasi)
                ->delete();
        }
    }

    /**
     * Helper privat untuk melakukan soft delete record inventaris dengan reset jumlah ke 0.
     */
    private function softDeleteInventaris(string $kodeBarang, string $kodeRuangan, string $kondisi): void
    {
        Inventaris::where('inv_kode_barang', $kodeBarang)
            ->where('inv_ruangan_kode', $kodeRuangan)
            ->where('inv_kondisi', $kondisi)
            ->update(['inv_jumlah' => 0]);

        Inventaris::where('inv_kode_barang', $kodeBarang)
            ->where('inv_ruangan_kode', $kodeRuangan)
            ->where('inv_kondisi', $kondisi)
            ->delete();
    }

    public function index(Request $request, string $lokasi, string $kode_ruangan): View
    {
        $room = $this->getRoom($lokasi, $kode_ruangan);
        $search = $request->query('search');

        $query = Inventaris::where('inv_ruangan_kode', $room->kode_ruangan);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('inv_nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('inv_kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('inv_merk_tipe', 'LIKE', "%{$search}%")
                  ->orWhere('inv_kondisi', 'LIKE', "%{$search}%");
            });
        }

        $dataInventaris = $query->orderBy('inv_kode_barang', 'desc')->paginate(5);
        $allRooms = Ruangan::where('lokasi', $lokasi)->orderBy('ruangan_nama')->get();
        $daftarSatuan = self::DAFTAR_SATUAN;

        return view("pages.{$lokasi}.inventaris.index", compact('dataInventaris', 'lokasi', 'room', 'search', 'allRooms', 'daftarSatuan'));
    }

    public function create(string $lokasi, string $kode_ruangan): View
    {
        $room = $this->getRoom($lokasi, $kode_ruangan);
        $daftarSatuan = self::DAFTAR_SATUAN;

        // Optimasi: Agregasi stok terpakai dalam 1 query
        $stokTerpakai = Inventaris::select('inv_kode_barang', DB::raw('SUM(inv_jumlah) as total'))
            ->groupBy('inv_kode_barang')
            ->pluck('total', 'inv_kode_barang');

        $allPeralatan = Peralatan::where('lokasi', $lokasi)
            ->orderBy('alat_nama_barang', 'asc')
            ->get()
            ->map(function ($barang) use ($stokTerpakai) {
                $terpakai = $stokTerpakai->get($barang->alat_kode_barang, 0);
                $barang->sisa_stok = (int) $barang->alat_jumlah - (int) $terpakai;

                $barang->kode_barang = $barang->alat_kode_barang;
                $barang->nama_barang = $barang->alat_nama_barang;
                $barang->merk_tipe = $barang->alat_merk_tipe ?? '-';
                $barang->tahun_perolehan = $barang->alat_tahun_perolehan ?? '-';
                $barang->satuan = $barang->alat_satuan ?? 'Buah';

                return $barang;
            });

        return view("pages.{$lokasi}.inventaris.create", compact('lokasi', 'room', 'daftarSatuan', 'allPeralatan'));
    }

    public function store(Request $request, string $lokasi, string $kode_ruangan): RedirectResponse
    {
        $room = $this->getRoom($lokasi, $kode_ruangan);

        $request->validate([
            'kode_barang' => 'required|string|max:100',
            'jumlah'      => 'required|integer|min:1',
            'satuan'      => 'required|string',
            'kondisi'     => 'required|in:Baik,Rusak Ringan,Rusak Berat',
        ]);

        $masterPeralatan = Peralatan::where('alat_kode_barang', $request->kode_barang)->first();
        if (!$masterPeralatan) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Kode barang tidak valid di Master KIB B.');
        }

        $totalStokMaster = (int) $masterPeralatan->alat_jumlah;
        $stokTerpakaiDiruangan = (int) Inventaris::where('inv_kode_barang', $request->kode_barang)->sum('inv_jumlah');
        $sisaStokTersedia = $totalStokMaster - $stokTerpakaiDiruangan;

        if ((int) $request->jumlah > $sisaStokTersedia) {
            return redirect()->back()->withInput()->with('error', 'Gagal! Stok tidak mencukupi.');
        }

        $tahunPerolehan = $request->tahun_perolehan;
        if (empty($tahunPerolehan) || $tahunPerolehan === '-') {
            $tahunPerolehan = $masterPeralatan->alat_tahun_perolehan 
                ?? $masterPeralatan->tahun_perolehan 
                ?? date('Y');
        }

        $namaBarang = $request->nama_barang ?? $masterPeralatan->alat_nama_barang ?? '-';
        $merkTipe = $request->merk_tipe ?? $masterPeralatan->alat_merk_tipe ?? '-';
        $nomorRegister = $request->nomor_register ?? $masterPeralatan->alat_nomor_register ?? null;
        $spesifikasi = $request->spesifikasi_barang ?? $masterPeralatan->alat_spesifikasi_barang ?? null;

        DB::transaction(function () use ($request, $room, $lokasi, $tahunPerolehan, $namaBarang, $merkTipe, $nomorRegister, $spesifikasi) {
            
            $item = Inventaris::withTrashed()
                ->where('inv_kode_barang', $request->kode_barang)
                ->where('inv_ruangan_kode', $room->kode_ruangan)
                ->where('inv_kondisi', $request->kondisi)
                ->first();

            if ($item) {
                if ($item->trashed()) {
                    $item->restore();
                    $item->inv_jumlah = 0;
                }

                $item->update([
                    'inv_jumlah'     => $item->inv_jumlah + (int) $request->jumlah,
                    'inv_keterangan' => $request->keterangan ?? $item->inv_keterangan,
                ]);
            } else {
                Inventaris::create([
                    'inv_kode_barang'        => $request->kode_barang,
                    'inv_ruangan_kode'       => $room->kode_ruangan,
                    'inv_nomor_register'     => $nomorRegister,
                    'inv_nama_barang'        => $namaBarang,
                    'inv_spesifikasi_barang' => $spesifikasi,
                    'inv_merk_tipe'          => $merkTipe,
                    'inv_tahun_perolehan'    => $tahunPerolehan,
                    'inv_jumlah'             => $request->jumlah,
                    'inv_satuan'             => $request->satuan,
                    'inv_kondisi'            => $request->kondisi,
                    'inv_keterangan'         => $request->keterangan,
                    'lokasi'                 => $lokasi,
                ]);
            }

            // PERBAIKAN: Gunakan withTrashed() dan urutkan berdasarkan dt_alat_id (Primary Key)
            // agar data soft deleted tetap terbaca sehingga nomor urut barcode tidak pernah berulang.
            $lastDetail = DetailPeralatan::withTrashed()
                ->where('dt_alat_kode_barang', $request->kode_barang)
                ->orderBy('dt_alat_id', 'desc')
                ->first();

            $lastNum = 0;
            if ($lastDetail && str_contains($lastDetail->dt_alat_kode_barcode, '.')) {
                $parts = explode('.', $lastDetail->dt_alat_kode_barcode);
                $lastNum = (int) end($parts);
            }

            // Bulk insert ke DetailPeralatan untuk meningkatkan performa
            $detailsToInsert = [];
            $now = now();
            $nowDate = $now->toDateString();
            $jumlahBarang = (int) $request->jumlah;

            for ($i = 1; $i <= $jumlahBarang; $i++) {
                $nomorUrut = $lastNum + $i;
                $noUrutBuntut = str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);

                $detailsToInsert[] = [
                    'dt_alat_kode_barang'   => $request->kode_barang,
                    'dt_alat_kode_barcode'  => $request->kode_barang . '.' . $noUrutBuntut,
                    'dt_alat_kondisi'       => $request->kondisi,
                    'lokasi'                => $room->ruangan_nama,
                    'dt_alat_status_pinjam' => 'Tersedia',
                    'dt_alat_tanggal_cek'   => $nowDate,
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ];
            }

            DetailPeralatan::insert($detailsToInsert);

            $this->syncRusakStatus($request->kode_barang, $lokasi, $request->keterangan);
        });

        $this->sendNotification('ditambahkan', $namaBarang);

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan])
            ->with('success', 'Data inventaris berhasil disimpan.');
    }

    public function edit(Request $request, string $lokasi, string $kode_ruangan, string $inv_kode_barang): View
    {
        $room = $this->getRoom($lokasi, $kode_ruangan);
        $kondisi = $request->query('kond');

        $query = Inventaris::where('inv_kode_barang', $inv_kode_barang)
            ->where('inv_ruangan_kode', $kode_ruangan);

        if ($kondisi) {
            $query->where('inv_kondisi', $kondisi);
        }

        $inventari = $query->firstOrFail();
        $daftarSatuan = self::DAFTAR_SATUAN;

        return view("pages.{$lokasi}.inventaris.edit", compact('lokasi', 'room', 'inventari', 'daftarSatuan'))->with('item', $inventari);
    }

    public function update(Request $request, string $lokasi, string $kode_ruangan, string $inv_kode_barang): RedirectResponse
    {
        $room = $this->getRoom($lokasi, $kode_ruangan);

        $request->validate([
            'inv_kondisi_lama' => 'nullable|string',
            'inv_jumlah'       => 'nullable|integer|min:1',
            'qty_ubah'         => 'nullable|integer|min:1',
            'kondisi_baru'     => 'nullable|in:Baik,Rusak Ringan,Rusak Berat',
            'inv_kondisi'      => 'nullable|in:Baik,Rusak Ringan,Rusak Berat',
            'keterangan'       => 'nullable|string',
        ]);

        $kondisiLamaInput = $request->input('inv_kondisi_lama') ?? $request->query('kond');

        $query = Inventaris::where('inv_kode_barang', $inv_kode_barang)
            ->where('inv_ruangan_kode', $kode_ruangan);

        if ($kondisiLamaInput) {
            $query->where('inv_kondisi', $kondisiLamaInput);
        }

        $inventari = $query->firstOrFail();
        $kondisiLama = $inventari->inv_kondisi;

        $qtyUbah = (int) ($request->qty_ubah ?? $request->inv_jumlah ?? $inventari->inv_jumlah);
        $kondisiBaru = $request->kondisi_baru ?? $request->inv_kondisi ?? $inventari->inv_kondisi;

        if ($qtyUbah > $inventari->inv_jumlah) {
            return redirect()->back()->withInput()->with('error', 'Jumlah unit yang diubah melebihi stok yang ada pada kondisi ini.');
        }

        DB::transaction(function () use ($inv_kode_barang, $kode_ruangan, $kondisiLama, $kondisiBaru, $qtyUbah, $request, $room, $lokasi, $inventari) {

            if ($kondisiLama !== $kondisiBaru) {
                // 1. Kurangi atau hapus kuantitas dari record kondisi lama
                if ($qtyUbah >= $inventari->inv_jumlah) {
                    $this->softDeleteInventaris($inv_kode_barang, $kode_ruangan, $kondisiLama);
                } else {
                    Inventaris::where('inv_kode_barang', $inv_kode_barang)
                        ->where('inv_ruangan_kode', $kode_ruangan)
                        ->where('inv_kondisi', $kondisiLama)
                        ->decrement('inv_jumlah', $qtyUbah);
                }

                // 2. Tambahkan/gabungkan kuantitas ke record kondisi baru
                $targetItem = Inventaris::withTrashed()
                    ->where('inv_kode_barang', $inv_kode_barang)
                    ->where('inv_ruangan_kode', $kode_ruangan)
                    ->where('inv_kondisi', $kondisiBaru)
                    ->first();

                if ($targetItem) {
                    if ($targetItem->trashed()) {
                        $targetItem->restore();
                        $targetItem->inv_jumlah = 0;
                    }
                    $targetItem->update([
                        'inv_jumlah'     => $targetItem->inv_jumlah + $qtyUbah,
                        'inv_keterangan' => $request->keterangan ?? $targetItem->inv_keterangan,
                    ]);
                } else {
                    Inventaris::create([
                        'inv_kode_barang'        => $inventari->inv_kode_barang,
                        'inv_ruangan_kode'       => $kode_ruangan,
                        'inv_nomor_register'     => $inventari->inv_nomor_register,
                        'inv_nama_barang'        => $inventari->inv_nama_barang,
                        'inv_spesifikasi_barang' => $inventari->inv_spesifikasi_barang,
                        'inv_merk_tipe'          => $inventari->inv_merk_tipe,
                        'inv_tahun_perolehan'    => $inventari->inv_tahun_perolehan ?? date('Y'),
                        'inv_jumlah'             => $qtyUbah,
                        'inv_satuan'             => $inventari->inv_satuan,
                        'inv_kondisi'            => $kondisiBaru,
                        'inv_keterangan'         => $request->keterangan ?? $inventari->inv_keterangan,
                        'lokasi'                 => $lokasi,
                    ]);
                }

                // 3. Update DetailPeralatan sejumlah unit yang diubah
                $detailIds = DetailPeralatan::where('dt_alat_kode_barang', $inv_kode_barang)
                    ->where('lokasi', $room->ruangan_nama)
                    ->where('dt_alat_kondisi', $kondisiLama)
                    ->take($qtyUbah)
                    ->pluck('dt_alat_id');

                DetailPeralatan::whereIn('dt_alat_id', $detailIds)
                    ->update(['dt_alat_kondisi' => $kondisiBaru]);

            } else {
                Inventaris::where('inv_kode_barang', $inv_kode_barang)
                    ->where('inv_ruangan_kode', $kode_ruangan)
                    ->where('inv_kondisi', $kondisiLama)
                    ->update([
                        'inv_keterangan' => $request->keterangan ?? $inventari->inv_keterangan,
                    ]);
            }

            // Sinkronisasi Tabel Rusak
            $this->syncRusakStatus($inv_kode_barang, $lokasi, $request->keterangan ?? $inventari->inv_keterangan);
        });

        $this->sendNotification('diperbarui kondisinya', $inventari->inv_nama_barang);

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan])
            ->with('success', 'Data inventaris berhasil diperbarui.');
    }

    public function move(Request $request, string $lokasi, string $kode_ruangan, string $inv_kode_barang): RedirectResponse
    {
        $room = $this->getRoom($lokasi, $kode_ruangan);
        $kondisi = $request->input('inv_kondisi') ?? $request->query('kond');

        $query = Inventaris::where('inv_kode_barang', $inv_kode_barang)
            ->where('inv_ruangan_kode', $kode_ruangan);

        if ($kondisi) {
            $query->where('inv_kondisi', $kondisi);
        }

        $inventari = $query->firstOrFail();

        $request->validate([
            'new_room_id' => 'required|exists:ruangans,kode_ruangan',
            'qty_to_move' => 'required|integer|min:1|max:' . $inventari->inv_jumlah,
        ]);

        $qtyPindah = (int) $request->qty_to_move;
        $newRoom = $this->getRoom($lokasi, $request->new_room_id);

        try {
            DB::transaction(function () use ($inventari, $newRoom, $qtyPindah, $lokasi, $room, $inv_kode_barang, $kode_ruangan) {
                
                $detailUnitIds = DetailPeralatan::where('dt_alat_kode_barang', $inv_kode_barang)
                    ->where('lokasi', $room->ruangan_nama)
                    ->where('dt_alat_kondisi', $inventari->inv_kondisi)
                    ->take($qtyPindah)
                    ->pluck('dt_alat_id');

                Inventaris::where('inv_kode_barang', $inv_kode_barang)
                    ->where('inv_ruangan_kode', $kode_ruangan)
                    ->where('inv_kondisi', $inventari->inv_kondisi)
                    ->decrement('inv_jumlah', $qtyPindah);

                $targetItem = Inventaris::withTrashed()
                    ->where('inv_kode_barang', $inv_kode_barang)
                    ->where('inv_ruangan_kode', $newRoom->kode_ruangan)
                    ->where('inv_kondisi', $inventari->inv_kondisi)
                    ->first();

                if ($targetItem) {
                    if ($targetItem->trashed()) {
                        $targetItem->restore();
                        $targetItem->inv_jumlah = 0;
                    }
                    $targetItem->increment('inv_jumlah', $qtyPindah);
                } else {
                    Inventaris::create([
                        'inv_kode_barang'        => $inventari->inv_kode_barang,
                        'inv_ruangan_kode'       => $newRoom->kode_ruangan,
                        'inv_nomor_register'     => $inventari->inv_nomor_register,
                        'inv_nama_barang'        => $inventari->inv_nama_barang,
                        'inv_spesifikasi_barang' => $inventari->inv_spesifikasi_barang,
                        'inv_merk_tipe'          => $inventari->inv_merk_tipe,
                        'inv_tahun_perolehan'    => $inventari->inv_tahun_perolehan ?? date('Y'),
                        'inv_jumlah'             => $qtyPindah,
                        'inv_satuan'             => $inventari->inv_satuan,
                        'inv_kondisi'            => $inventari->inv_kondisi,
                        'lokasi'                 => $lokasi,
                    ]);
                }

                DetailPeralatan::whereIn('dt_alat_id', $detailUnitIds)
                    ->update(['lokasi' => $newRoom->ruangan_nama]);

                $sisaStok = Inventaris::where('inv_kode_barang', $inv_kode_barang)
                    ->where('inv_ruangan_kode', $kode_ruangan)
                    ->where('inv_kondisi', $inventari->inv_kondisi)
                    ->value('inv_jumlah');

                if ($sisaStok <= 0) {
                    $this->softDeleteInventaris($inv_kode_barang, $kode_ruangan, $inventari->inv_kondisi);
                }
            });

            $this->sendNotification("memindahkan {$qtyPindah} unit ke {$newRoom->ruangan_nama}", $inventari->inv_nama_barang);
            return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan])
                ->with('success', "Berhasil memindahkan {$qtyPindah} unit aset.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memindahkan barang: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, string $lokasi, string $kode_ruangan, string $inv_kode_barang): RedirectResponse
    {
        $room = $this->getRoom($lokasi, $kode_ruangan);
        $kondisi = $request->query('kond');

        $query = Inventaris::where('inv_kode_barang', $inv_kode_barang)
            ->where('inv_ruangan_kode', $kode_ruangan);

        if ($kondisi) {
            $query->where('inv_kondisi', $kondisi);
        }

        $item = $query->firstOrFail();
        $namaBarang = $item->inv_nama_barang;

        DB::transaction(function () use ($inv_kode_barang, $kode_ruangan, $item, $room, $lokasi) {
            // 1. Zero out & Soft Delete record Inventaris
            $this->softDeleteInventaris($inv_kode_barang, $kode_ruangan, $item->inv_kondisi);

            // 2. Hapus unit perincian barcode terkait
            DetailPeralatan::where('dt_alat_kode_barang', $inv_kode_barang)
                ->where('lokasi', $room->ruangan_nama)
                ->where('dt_alat_kondisi', $item->inv_kondisi)
                ->delete();

            // 3. Sinkronisasi tabel Rusak
            $this->syncRusakStatus($inv_kode_barang, $lokasi);
        });

        $this->sendNotification('dihapus', $namaBarang);
        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'kode_ruangan' => $room->kode_ruangan])
            ->with('success', 'Baris data berhasil dihapus.');
    }

    public function showDetail(Request $request, string $lokasi, string $room_kode, string $kode_barang): View
    {
        $room = $this->getRoom($lokasi, $room_kode);
        $kondisi = $request->query('kond');

        $query = Inventaris::where('inv_kode_barang', $kode_barang)
            ->where('inv_ruangan_kode', $room_kode);

        if ($kondisi) {
            $query->where('inv_kondisi', $kondisi);
        }

        $item = $query->firstOrFail();

        $unitPecahan = DetailPeralatan::where('dt_alat_kode_barang', $kode_barang)
            ->where('lokasi', $room->ruangan_nama)
            ->when($kondisi, function ($q) use ($kondisi) {
                $q->where('dt_alat_kondisi', $kondisi);
            })
            ->get();

        return view("pages.{$lokasi}.inventaris.detail", compact('lokasi', 'room', 'item', 'unitPecahan'));
    }

    public function print(string $lokasi, string $kode_ruangan): View
    {
        $room = $this->getRoom($lokasi, $kode_ruangan);
        $dataInventaris = Inventaris::where('inv_ruangan_kode', $room->kode_ruangan)
            ->orderBy('inv_nama_barang', 'asc')
            ->get();

        return view("pages.{$lokasi}.inventaris.print", compact('dataInventaris', 'lokasi', 'room'));
    }

    private function sendNotification(string $action, string $namaBarang): void
    {
        $recipients = User::whereIn('user_role_id', [1, 2])->get();
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), $action, 'Inventaris', $namaBarang));
        }
    }
}