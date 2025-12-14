@php
    $user = Auth::user();
    $roleId = $user->role_id;

    // Mengambil semua data ruangan sekali saja untuk efisiensi
    $allRoomsByLocation = \App\Models\Room::orderBy('name')->get()->groupBy('lokasi');
    $tawangRooms = $allRoomsByLocation['tawang'] ?? collect();

    $fullKelurahanList = [
        ['name' => 'Lengkongsari', 'slug' => 'lengkongsari'],
        ['name' => 'Cikalang', 'slug' => 'cikalang'],
        ['name' => 'Empang', 'slug' => 'empang'],
        ['name' => 'Kahuripan', 'slug' => 'kahuripan'],
        ['name' => 'Tawangsari', 'slug' => 'tawangsari'],
    ];

    $visibleKelurahan = [];
    if ($roleId == 1 || $roleId == 2) { 
        $visibleKelurahan = $fullKelurahanList;
    } elseif ($roleId == 3) { 
        foreach ($fullKelurahanList as $kelurahan) {
            if ('Kelurahan ' . $kelurahan['name'] === $user->name) {
                $visibleKelurahan[] = $kelurahan;
                break;
            }
        }
    }
@endphp

<ul class="navbar-nav bg-gradient-secondary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('img/tsk.png') }}" alt="Logo Pemkot Tasikmalaya" style="height: 40px;">
        </div>
        <div class="sidebar-brand-text mx-2">PANDAWA</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    {{-- ============================================================ --}}
    {{-- BAGIAN KECAMATAN TAWANG --}}
    {{-- ============================================================ --}}
    @if ($roleId == 1 || $roleId == 2 || $user->name == 'Kecamatan Tawang')
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        Kecamatan Tawang
    </div>

    {{-- Menu Ruangan --}}
    <li class="nav-item {{ request()->is('tawang/room*') && !request()->is('tawang/room/*/inventaris*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('lokasi.room.index', ['lokasi' => 'tawang']) }}">
            <i class="fas fa-fw fa-door-open"></i>
            <span>Data Ruangan</span>
        </a>
    </li>

    {{-- Menu Inventori Ruangan --}}
    <li class="nav-item {{ request()->is('tawang/room/*/inventaris*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInventarisTawang" aria-expanded="true" aria-controls="collapseInventarisTawang">
            <i class="fas fa-fw fa-boxes"></i>
            <span>Data Inventori Ruangan</span>
        </a>
        <div id="collapseInventarisTawang" class="collapse {{ request()->is('tawang/room/*/inventaris*') ? 'show' : '' }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Pilih Ruangan:</h6>
                @forelse ($tawangRooms as $room)
                    <a class="collapse-item {{ request()->route('room') && request()->route('room')->id == $room->id ? 'active' : '' }}"
                       href="{{ route('lokasi.inventaris.index', ['lokasi' => 'tawang', 'room' => $room->id]) }}"
                       style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                       title="{{ $room->name }}">
                        {{ $room->name }}
                    </a>
                @empty
                    <a class="collapse-item" href="{{ route('lokasi.room.create', ['lokasi' => 'tawang']) }}">Tambah Ruangan Dulu</a>
                @endforelse
            </div>
        </div>
    </li>

    {{-- Menu Kartu Inventaris Barang --}}
    <li class="nav-item {{ 
        request()->is('tawang/tanah*') || 
        request()->is('tawang/peralatan*') || 
        request()->is('tawang/gedung*') || 
        request()->is('tawang/jalan*') || 
        request()->is('tawang/rusak*') ? 'active' : '' 
    }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsebarang" aria-expanded="true" aria-controls="collapsebarang">
            <i class="fas fa-fw fa-archive"></i>
            <span>Kartu Inventaris Barang</span>
        </a>
        <div id="collapsebarang" class="collapse {{ 
            request()->is('tawang/tanah*') || 
            request()->is('tawang/peralatan*') || 
            request()->is('tawang/gedung*') || 
            request()->is('tawang/jalan*') || 
            request()->is('tawang/rusak*') ? 'show' : '' 
        }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Pilih Data:</h6>
                <a class="collapse-item" href="{{ route('lokasi.tanah.index', ['lokasi' => 'tawang']) }}">Tanah</a>
                <a class="collapse-item" href="{{ route('lokasi.peralatan.index', ['lokasi' => 'tawang']) }}">Peralatan & Mesin</a>
                <a class="collapse-item" href="{{ route('lokasi.gedung.index', ['lokasi' => 'tawang']) }}">Gedung & Bangunan</a>
                <a class="collapse-item" href="{{ route('lokasi.jalan.index', ['lokasi' => 'tawang']) }}">Jalan, Irigasi & Jaringan</a>
                <a class="collapse-item" href="{{ route('lokasi.rusak.index', ['lokasi' => 'tawang']) }}">Barang Rusak</a>
            </div>
        </div>
    </li>

    {{-- Menu Penggunaan BMD (Sejajar) --}}
    <li class="nav-item {{ request()->is('tawang/bmd*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('lokasi.bmd.index', ['lokasi' => 'tawang']) }}">
            <i class="fas fa-fw fa-file-contract"></i>
            <span>Daftar Penggunaan BMD</span>
        </a>
    </li>

    {{-- [BARU] Menu Monitoring Pajak --}}
    <li class="nav-item {{ request()->is('tawang/pajak*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('lokasi.pajak.index', ['lokasi' => 'tawang']) }}">
            <i class="fas fa-fw fa-hand-holding-usd"></i>
            <span>Monitoring Pajak</span>
        </a>
    </li>

    @endif


    {{-- ============================================================ --}}
    {{-- BAGIAN LOOPING KELURAHAN --}}
    {{-- ============================================================ --}}
    @foreach ($visibleKelurahan as $kelurahan)
        <hr class="sidebar-divider">
        <div class="sidebar-heading">
            Kelurahan {{ $kelurahan['name'] }}
        </div>
        
        {{-- Menu Ruangan Kelurahan --}}
        <li class="nav-item {{ request()->is($kelurahan['slug'] . '/room*') && !request()->is($kelurahan['slug'] . '/room/*/inventaris*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('lokasi.room.index', ['lokasi' => $kelurahan['slug']]) }}">
                <i class="fas fa-fw fa-door-closed"></i>
                <span>Data Ruangan</span>
            </a>
        </li>
    
        {{-- Menu Inventori Kelurahan --}}
        <li class="nav-item {{ request()->is($kelurahan['slug'] . '/room/*/inventaris*') ? 'active' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInventori{{ ucfirst($kelurahan['slug']) }}" aria-expanded="true" aria-controls="collapseInventori{{ ucfirst($kelurahan['slug']) }}">
                <i class="fas fa-fw fa-boxes"></i>
                <span>Data Inventori Ruangan</span>
            </a>
            <div id="collapseInventori{{ ucfirst($kelurahan['slug']) }}" class="collapse {{ request()->is($kelurahan['slug'] . '/room/*/inventaris*') ? 'show' : '' }}" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Pilih Ruangan:</h6>
                    @forelse ($allRoomsByLocation[$kelurahan['slug']] ?? [] as $room)
                        <a class="collapse-item {{ request()->route('room') && request()->route('room')->id == $room->id ? 'active' : '' }}"
                           href="{{ route('lokasi.inventaris.index', ['lokasi' => $kelurahan['slug'], 'room' => $room->id]) }}"
                           style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                           title="{{ $room->name }}">
                            {{ $room->name }}
                        </a>
                    @empty
                        <a class="collapse-item" href="{{ route('lokasi.room.create', ['lokasi' => $kelurahan['slug']]) }}">Tambah Ruangan Dulu</a>
                    @endforelse
                </div>
            </div>
        </li>
    
        {{-- Menu KIB Kelurahan --}}
        <li class="nav-item {{ 
            request()->is($kelurahan['slug'] . '/tanah*') || 
            request()->is($kelurahan['slug'] . '/peralatan*') || 
            request()->is($kelurahan['slug'] . '/gedung*') || 
            request()->is($kelurahan['slug'] . '/jalan*') || 
            request()->is($kelurahan['slug'] . '/rusak*') ? 'active' : '' 
        }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBarang{{ ucfirst($kelurahan['slug']) }}" aria-expanded="true" aria-controls="collapseBarang{{ ucfirst($kelurahan['slug']) }}">
                <i class="fas fa-fw fa-archive"></i>
                <span>Kartu Inventaris Barang</span>
            </a>
            <div id="collapseBarang{{ ucfirst($kelurahan['slug']) }}" class="collapse {{ 
                request()->is($kelurahan['slug'] . '/tanah*') || 
                request()->is($kelurahan['slug'] . '/peralatan*') || 
                request()->is($kelurahan['slug'] . '/gedung*') || 
                request()->is($kelurahan['slug'] . '/jalan*') || 
                request()->is($kelurahan['slug'] . '/rusak*') ? 'show' : '' 
            }}" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Pilih Data:</h6>
                    <a class="collapse-item" href="{{ route('lokasi.tanah.index', ['lokasi' => $kelurahan['slug']]) }}">Tanah</a>
                    <a class="collapse-item" href="{{ route('lokasi.peralatan.index', ['lokasi' => $kelurahan['slug']]) }}">Peralatan & Mesin</a>
                    <a class="collapse-item" href="{{ route('lokasi.gedung.index', ['lokasi' => $kelurahan['slug']]) }}">Gedung & Bangunan</a>
                    <a class="collapse-item" href="{{ route('lokasi.jalan.index', ['lokasi' => $kelurahan['slug']]) }}">Jalan, Irigasi & Jaringan</a>
                    <a class="collapse-item" href="{{ route('lokasi.rusak.index', ['lokasi' => $kelurahan['slug']]) }}">Barang Rusak</a>
                </div>
            </div>
        </li>

        {{-- Menu Penggunaan BMD Kelurahan --}}
        <li class="nav-item {{ request()->is($kelurahan['slug'] . '/bmd*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('lokasi.bmd.index', ['lokasi' => $kelurahan['slug']]) }}">
                <i class="fas fa-fw fa-file-contract"></i>
                <span>Daftar Penggunaan BMD</span>
            </a>
        </li>

        {{-- [BARU] Menu Monitoring Pajak Kelurahan --}}
        <li class="nav-item {{ request()->is($kelurahan['slug'] . '/pajak*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('lokasi.pajak.index', ['lokasi' => $kelurahan['slug']]) }}">
                <i class="fas fa-fw fa-hand-holding-usd"></i>
                <span>Monitoring Pajak</span>
            </a>
        </li>
    @endforeach


    @if ($roleId == 1 )
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        account
    </div>
    <li class="nav-item">
        <a class="nav-link" href="{{ route ('user.index')}}">
            <i class="fas fa-fw fa-user-circle"></i>
            <span>Account</span>
        </a>
    </li>
    @endif

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>