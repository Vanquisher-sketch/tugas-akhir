@php
    $user = Auth::user();
    $roleId = $user->user_role_id;

    $allRoomsByLocation = \App\Models\Ruangan::orderBy('ruangan_nama')->get()->groupBy('lokasi');
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
            if ('Kelurahan ' . $kelurahan['name'] === $user->user_nama) {
                $visibleKelurahan[] = $kelurahan;
                break;
            }
        }
    }

    $currentLokasi = request()->segment(1); 
    if(in_array($currentLokasi, ['dashboard', 'user', 'profile', 'notifications', ''])) {
        $currentLokasi = 'tawang'; 
    }
@endphp

<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" 
    style="background: linear-gradient(180deg, #183059 0%, #0b1b33 100%); box-shadow: 3px 0 15px rgba(0,0,0,0.2);">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('img/tsk.png') }}" alt="Logo" style="height: 40px;">
        </div>
        <div class="sidebar-brand-text mx-2">PANDAWA</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    @if ($roleId == 1 || $roleId == 2 || $user->user_nama == 'Kecamatan Tawang')
    <hr class="sidebar-divider">
    @php $isTawangActive = request()->is('tawang*'); @endphp

    <li class="nav-item {{ $isTawangActive ? 'active' : '' }}">
        <a class="nav-link {{ $isTawangActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseTawang" 
           aria-expanded="{{ $isTawangActive ? 'true' : 'false' }}" aria-controls="collapseTawang">
            <i class="fas fa-fw fa-landmark"></i>
            <span>Kecamatan Tawang</span>
        </a>
        <div id="collapseTawang" class="collapse {{ $isTawangActive ? 'show' : '' }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('tawang/ruangan*') && !request()->is('tawang/ruangan/*/inventaris*') ? 'active' : '' }}" 
                   href="{{ route('lokasi.ruangan.index', ['lokasi' => 'tawang']) }}">Data Ruangan</a>
                
                <div class="dropdown-divider"></div>
                <h6 class="collapse-header">Inventori Ruangan:</h6>
                @forelse ($tawangRooms as $room)
                    <a class="collapse-item {{ request()->is('tawang/ruangan/'.$room->kode_ruangan.'/inventaris*') ? 'active' : '' }}" 
                       href="{{ route('lokasi.inventaris.index', ['lokasi' => 'tawang', 'kode_ruangan' => $room->kode_ruangan]) }}"
                       style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.85rem;" 
                       title="{{ $room->ruangan_nama }}">
                       - {{ $room->ruangan_nama }}
                    </a>
                @empty
                    <small class="ml-3 text-muted">Belum ada ruangan</small>
                @endforelse

                <div class="dropdown-divider"></div>
                <h6 class="collapse-header">KIB (A,B,C,D):</h6>
                <a class="collapse-item" href="{{ route('lokasi.tanah.index', ['lokasi' => 'tawang']) }}">Tanah (A)</a>
                <a class="collapse-item" href="{{ route('lokasi.peralatan.index', ['lokasi' => 'tawang']) }}">Peralatan (B)</a>
                <a class="collapse-item" href="{{ route('lokasi.gedung.index', ['lokasi' => 'tawang']) }}">Gedung (C)</a>
                <a class="collapse-item" href="{{ route('lokasi.jalan.index', ['lokasi' => 'tawang']) }}">Jalan (D)</a>
                <a class="collapse-item" href="{{ route('lokasi.rusak.index', ['lokasi' => 'tawang']) }}">Barang Rusak</a>

                <div class="dropdown-divider"></div>
                <a class="collapse-item {{ request()->is('tawang/bmd*') ? 'active' : '' }}" href="{{ route('lokasi.bmd.index', ['lokasi' => 'tawang']) }}">Penggunaan BMD</a>
                <a class="collapse-item {{ request()->is('tawang/pajak*') ? 'active' : '' }}" href="{{ route('lokasi.pajak.index', ['lokasi' => 'tawang']) }}">Monitoring Pajak</a>
            </div>
        </div>
    </li>
    @endif

    @foreach ($visibleKelurahan as $kelurahan)
        @php 
            $slug = $kelurahan['slug'];
            $isKelActive = request()->is($slug . '*');
        @endphp
        <hr class="sidebar-divider my-0">
        <li class="nav-item {{ $isKelActive ? 'active' : '' }}">
            <a class="nav-link {{ $isKelActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" 
               data-target="#collapse{{ $slug }}" aria-expanded="{{ $isKelActive ? 'true' : 'false' }}" 
               aria-controls="collapse{{ $slug }}">
                <i class="fas fa-fw fa-map-marker-alt"></i>
                <span>Kelurahan {{ $kelurahan['name'] }}</span>
            </a>
            <div id="collapse{{ $slug }}" class="collapse {{ $isKelActive ? 'show' : '' }}" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->is($slug . '/ruangan*') && !request()->is($slug . '/ruangan/*/inventaris*') ? 'active' : '' }}" 
                       href="{{ route('lokasi.ruangan.index', ['lokasi' => $slug]) }}">Data Ruangan</a>
                    
                    <div class="dropdown-divider"></div>
                    <h6 class="collapse-header">Inventori Ruangan:</h6>
                    @forelse ($allRoomsByLocation[$slug] ?? [] as $room)
                        <a class="collapse-item {{ request()->is($slug . '/ruangan/'.$room->kode_ruangan.'/inventaris*') ? 'active' : '' }}" 
                           href="{{ route('lokasi.inventaris.index', ['lokasi' => $slug, 'kode_ruangan' => $room->kode_ruangan]) }}"
                           style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.85rem;"
                           title="{{ $room->ruangan_nama }}">
                           - {{ $room->ruangan_nama }}
                        </a>
                    @empty
                        <small class="ml-3 text-muted">Belum ada ruangan</small>
                    @endforelse

                    <div class="dropdown-divider"></div>
                    <h6 class="collapse-header">KIB (A,B,C,D)</h6>
                    <a class="collapse-item" href="{{ route('lokasi.tanah.index', ['lokasi' => $slug]) }}">Tanah (A)</a>
                    <a class="collapse-item" href="{{ route('lokasi.peralatan.index', ['lokasi' => $slug]) }}">Peralatan (B)</a>
                    <a class="collapse-item" href="{{ route('lokasi.gedung.index', ['lokasi' => $slug]) }}">Gedung (C)</a>
                    <a class="collapse-item" href="{{ route('lokasi.jalan.index', ['lokasi' => $slug]) }}">Jalan (D)</a>
                    <a class="collapse-item" href="{{ route('lokasi.rusak.index', ['lokasi' => $slug]) }}">Barang Rusak</a>

                    <div class="dropdown-divider"></div>
                    <a class="collapse-item {{ request()->is($slug . '/bmd*') ? 'active' : '' }}" href="{{ route('lokasi.bmd.index', ['lokasi' => $slug]) }}">Penggunaan BMD</a>
                    <a class="collapse-item {{ request()->is($slug . '/pajak*') ? 'active' : '' }}" href="{{ route('lokasi.pajak.index', ['lokasi' => $slug]) }}">Monitoring Pajak</a>
                </div>
            </div>
        </li>
    @endforeach

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Administrasi </div>

    <li class="nav-item {{ request()->is('*/arsip*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('lokasi.arsip.index', ['lokasi' => $currentLokasi]) }}">
            <i class="fas fa-fw fa-archive"></i>
            <span>Pusat Arsip</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('*/pegawai*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('lokasi.pegawai.index', ['lokasi' => $currentLokasi]) }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Data Pegawai</span>
        </a>
    </li>

    @if ($roleId == 1 )
    <li class="nav-item {{ request()->is('user*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('user.index') }}">
            <i class="fas fa-fw fa-user-circle"></i>
            <span>Manajemen Akun</span>
        </a>
    </li>
    @endif

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>