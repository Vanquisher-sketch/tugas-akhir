<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\DetailInventaris; // 🌟 PENTING: Panggil Model Detail Baru
use App\Models\Room;
use App\Models\Rusak;
use App\Models\User;
use App\Notifications\DataModificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InventarisController extends Controller
{
    public function index(Request $request, $lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) { abort(404); }

        $search = $request->query('search');
        $query = Inventaris::where('room_kode', $room->kode_ruangan);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                    ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                    ->orWhere('merk_tipe', 'LIKE', "%{$search}%");
            });
        }

        $dataInventaris = $query->orderBy('kode_barang', 'desc')->paginate(10);
        $allRooms = Room::where('lokasi', $lokasi)->orderBy('name')->get();
        $daftarSatuan = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];

        return view("pages.{$lokasi}.inventaris.index", compact('dataInventaris', 'lokasi', 'room', 'search', 'allRooms', 'daftarSatuan'));
    }

    public function create($lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) { abort(404); }
        $daftarSatuan = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];
        return view("pages.{$lokasi}.inventaris.create", compact('lokasi', 'room', 'daftarSatuan'));
    }

    public function store(Request $request, $lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) { abort(404); }

        $validated = $request->validate([
            'nibar'              => 'nullable|string|max:255',
            'nomor_register'     => 'nullable|string|max:255',
            'kode_barang'        => 'required|string|max:100|unique:inventaris,kode_barang', 
            'nama_barang'        => 'required|string|max:255',
            'spesifikasi_barang' => 'nullable|string',
            'merk_tipe'          => 'nullable|string|max:255',
            'tahun_perolehan'    => 'required|digits:4',
            'jumlah'             => 'required|integer|min:1',
            'satuan'             => 'required|string',
            'kondisi'            => 'required|in:Baik,Rusak Ringan,Rusak Berat', 
            'keterangan'         => 'nullable|string',
        ]);

        $validated['room_kode'] = $room->kode_ruangan;
        $validated['lokasi']    = $lokasi; 

        // 🌟 DATABASE TRANSACTION: Mengamankan proses simpan ganda (Induk & Detail)
        $inventaris = DB::transaction(function () use ($validated, $room, $lokasi) {
            // 1. Simpan ke tabel induk (inventaris)
            $item = Inventaris::create($validated);

            // 2. OTOMATIS PECAH UNIT: Lakukan looping sebanyak jumlah kuantitas unit yang di-input
            $jumlahUnit = (int) $item->jumlah;
            for ($i = 1; $i <= $jumlahUnit; $i++) {
                // Bikin nomor register buntut otomatis (0001, 0002, 0003...) sepanjang 4 digit
                $noUrutBuntut = str_pad($i, 4, '0', STR_PAD_LEFT);
                
                DetailInventaris::create([
                    'id_barang'     => $item->kode_barang, // Foreign Key ke tabel inventaris
                    'kode_barcode'  => $item->kode_barang . '.' . $noUrutBuntut, // Contoh: KODE.0001
                    'kondisi'       => $item->kondisi,
                    'lokasi'        => $room->name, // Mengambil string nama ruangan dari master room
                    'status_pinjam' => 'Tersedia', // Status awal aset satuan
                    'tanggal_cek'   => now()->toDateString() // Otomatis terisi tanggal hari ini
                ]);
            }

            // Pemicu log penampung barang rusak jika kondisi awal disetel Rusak Berat
            if ($item->kondisi === 'Rusak Berat') {
                Rusak::updateOrCreate(
                    ['kode_barang' => $item->kode_barang],
                    [
                        'jenis_asal'  => 'Inventaris',
                        'keterangan'  => $item->keterangan ?? "Aset Inventaris Ruangan {$room->name} mengalami kerusakan berat.",
                        'lokasi'      => $lokasi
                    ]
                );
            }

            return $item;
        });

        $this->sendNotification('ditambahkan', $inventaris->nama_barang);

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])
            ->with('success', 'Data induk inventaris dan baris pecahan detail berhasil disimpan.');
    }

    public function edit($lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) { abort(404); }

        $daftarSatuan = ['Unit', 'Buah', 'Set', 'Meter', 'Lembar', 'Paket', 'Dus', 'Pcs'];
        return view("pages.{$lokasi}.inventaris.edit", compact('lokasi', 'room', 'inventari', 'daftarSatuan'));
    }

    public function update(Request $request, $lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) { abort(404); }
        
        $validated = $request->validate([
            'nibar'           => 'nullable', 
            'nomor_register'  => 'nullable', 
            'kode_barang'     => 'required|string|unique:inventaris,kode_barang,' . $inventari->kode_barang . ',kode_barang',
            'nama_barang'     => 'required', 
            'tahun_perolehan' => 'required|digits:4',
            'jumlah'          => 'required|integer|min:0', 
            'satuan'          => 'required',
            'kondisi'         => 'required|in:Baik,Rusak Ringan,Rusak Berat', 
            'keterangan'      => 'nullable|string'
        ]);

        $inventari->update($validated);

        if ($inventari->kondisi === 'Rusak Berat') {
            Rusak::updateOrCreate(
                ['kode_barang' => $inventari->kode_barang],
                [
                    'jenis_asal'  => 'Inventaris',
                    'keterangan'  => $inventari->keterangan ?? "Aset Inventaris Ruangan {$room->name} mengalami kerusakan berat.",
                    'lokasi'      => $lokasi
                ]
            );
        } else {
            Rusak::where('kode_barang', $inventari->kode_barang)->delete();
        }

        $this->sendNotification('diperbarui', $inventari->nama_barang);

        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])->with('success', 'Data inventaris diperbarui.');
    }

    public function move(Request $request, $lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) { abort(404); }

        $request->validate([
            'new_room_id' => 'required|exists:rooms,kode_ruangan',
            'qty_to_move' => 'required|integer|min:1',
        ]);

        $qtyPindah = (int) $request->qty_to_move;
        $newRoom = Room::where('kode_ruangan', $request->new_room_id)->firstOrFail();

        if ($qtyPindah > $inventari->jumlah) {
            return redirect()->back()->with('error', "Gagal: Stok tidak mencukupi.");
        }

        try {
            DB::transaction(function () use ($inventari, $newRoom, $qtyPindah, $lokasi) {
                $inventari->decrement('jumlah', $qtyPindah);

                $targetItem = Inventaris::where('kode_barang', $inventari->kode_barang)
                    ->where('room_kode', $newRoom->kode_ruangan)
                    ->first();

                if ($targetItem) {
                    $targetItem->update([
                        'jumlah' => $targetItem->jumlah + $qtyPindah
                    ]);
                } else {
                    $newItem = $inventari->replicate();
                    $newItem->kode_barang = $inventari->kode_barang; 
                    $newItem->room_kode = $newRoom->kode_ruangan;
                    $newItem->jumlah = $qtyPindah;
                    $newItem->lokasi = $lokasi;
                    $newItem->save();
                }

                if ($inventari->fresh()->jumlah <= 0) {
                    Rusak::where('kode_barang', $inventari->kode_barang)->delete();
                    $inventari->delete();
                }
            });

            $this->sendNotification("memindahkan $qtyPindah unit ke {$newRoom->name}", $inventari->nama_barang);
            return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])->with('success', 'Berhasil memindahkan barang.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy($lokasi, Room $room, Inventaris $inventari)
    {
        if ($room->lokasi !== $lokasi || $inventari->room_kode !== $room->kode_ruangan) { abort(404); }
        $namaBarang = $inventari->nama_barang;
        
        Rusak::where('kode_barang', $inventari->kode_barang)->delete();
        $inventari->delete(); 

        $this->sendNotification('dihapus permanen', $namaBarang);
        return redirect()->route('lokasi.inventaris.index', ['lokasi' => $lokasi, 'room' => $room->kode_ruangan])->with('success', 'Data berhasil dihapus.');
    }

    /**
     * 🌟 POIN 3: Ambil Data Riil Pecahan dari Database Detail Inventaris
     */
    public function showDetail($lokasi, $room_kode, $kode_barang)
    {
        $room = Room::where('kode_ruangan', $room_kode)->firstOrFail();
        $item = Inventaris::where('kode_barang', $kode_barang)->where('room_kode', $room_kode)->firstOrFail();

        // Ambil baris data pecahan satuan langsung dari tabel detail_inventaris di MySQL
        $unitPecahan = DetailInventaris::where('id_barang', $kode_barang)->get();

        return view("pages.{$lokasi}.inventaris.detail", compact('lokasi', 'room', 'item', 'unitPecahan'));
    }

    public function print($lokasi, Room $room)
    {
        if ($room->lokasi !== $lokasi) { abort(404); }
        $dataInventaris = Inventaris::where('room_kode', $room->kode_ruangan)->orderBy('nama_barang', 'asc')->get();
        return view("pages.{$lokasi}.inventaris.print", compact('dataInventaris', 'lokasi', 'room'));
    }

    private function sendNotification($action, $namaBarang)
    {
        $recipients = User::whereIn('role_id', [1, 2])->get();
        if ($recipients->count() > 0) {
            Notification::send($recipients, new DataModificationNotification(Auth::user(), $action, 'Inventaris', $namaBarang));
        }
    }
}