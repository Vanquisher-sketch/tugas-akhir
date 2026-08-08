<style>
    /* KUSTOMISASI UI AGAR LEBIH MENARIK */
    .navbar-custom-shadow { box-shadow: 0 4px 20px rgba(0,0,0,0.05) !important; border-bottom: 1px solid #f1f3f6; }
    
    /* Animasi Lonceng Bergoyang */
    .bell-ring { animation: ring 2s infinite; transform-origin: top center; }
    @keyframes ring {
        0% { transform: rotate(0); }
        10% { transform: rotate(15deg); }
        20% { transform: rotate(-10deg); }
        30% { transform: rotate(5deg); }
        40% { transform: rotate(-5deg); }
        50%, 100% { transform: rotate(0); }
    }
    
    /* Animasi Denyut (Pulse) pada Badge Notifikasi */
    .badge-pulse { animation: pulse-red 2s infinite; }
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }

    /* Kustomisasi Dropdown Modern */
    .dropdown-menu-modern { border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; overflow: hidden; padding: 0; }
    .dropdown-header-gradient { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; padding: 15px; font-size: 0.9rem; font-weight: bold; border: none; }
    .dropdown-item-custom:hover { background-color: #f8f9fa; transform: translateX(5px); transition: all 0.2s ease-in-out; }
    
    /* Badge Kapsul Modern */
    .badge-capsule { border-radius: 20px; padding: 6px 15px; font-weight: 600; font-size: 0.85rem; }
</style>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top navbar-custom-shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars text-primary"></i>
    </button>

    <!-- KIRI & TENGAH: MENGISI RUANG KOSONG (Hanya tampil di layar monitor/tablet) -->
    

    <!-- Topbar Navbar (Kanan) -->
    <ul class="navbar-nav ml-auto align-items-center">

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                
                @php $hasNotif = isset($notifications) && $notifications->count() > 0; @endphp
                <i class="fas fa-bell fa-fw {{ $hasNotif ? 'bell-ring text-primary' : 'text-gray-400' }}" style="font-size: 1.2rem;"></i>
                
                @if ($hasNotif)
                    <span class="badge badge-danger badge-counter badge-pulse">{{ $notifications->count() }}</span>
                @endif
            </a>
            
            <div class="dropdown-list dropdown-menu dropdown-menu-right dropdown-menu-modern animated--grow-in"
                aria-labelledby="alertsDropdown">
                
                <div class="dropdown-header-gradient d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-bell mr-2"></i> Pusat Notifikasi</span>
                    @if ($hasNotif)
                        <span class="badge badge-light text-primary">{{ $notifications->count() }} Baru</span>
                    @endif
                </div>

                <div style="max-height: 300px; overflow-y: auto;">
                    @forelse ($notifications as $notification)
                        <a class="dropdown-item d-flex align-items-center dropdown-item-custom py-3 border-bottom" href="#">
                            @if($notification->type === 'App\Notifications\DataModificationNotification')
                                <div class="mr-3">
                                    <div class="icon-circle bg-primary-light" style="background-color: #e3e6f0; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <i class="fas fa-edit text-primary"></i>
                                    </div>
                                </div>
                            @else
                                <div class="mr-3">
                                    <div class="icon-circle" style="background-color: #fff3cd; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <i class="fas fa-file-invoice-dollar text-warning"></i>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <div class="small text-gray-500 mb-1">
                                    <i class="fas fa-history mr-1"></i> {{ $notification->created_at->locale('id')->diffForHumans() }}
                                </div>
                                <span class="font-weight-bold text-gray-800">{{ $notification->data['message'] ?? $notification->data['pesan'] ?? 'Ada pemberitahuan baru' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="text-center p-4">
                            <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                            <p class="text-gray-500 small mb-0">Hore! Tidak ada notifikasi baru.</p>
                        </div>
                    @endforelse
                </div>
                
                @if ($hasNotif)
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="bg-light m-0 p-0">
                        @csrf
                        <button type="submit" class="dropdown-item text-center small text-primary font-weight-bold py-3" style="border: none; background: none; width: 100%;">
                            <i class="fas fa-check-double mr-1"></i> Tandai semua sudah dibaca
                        </button>
                    </form>
                @endif
            </div>
        </li>
        
        <div class="topbar-divider d-none d-sm-block mx-3"></div>

        @auth
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="d-none d-lg-flex flex-column text-right mr-2">
                    <span class="text-gray-800 font-weight-bold small mb-0" style="line-height: 1;">{{ Auth::user()->name }}</span>
                    
                </div>
                <img class="img-profile rounded-circle shadow-sm border"
                     src="https://placehold.co/100x100/4e73df/ffffff?text={{ strtoupper(substr(Auth::user()->name, 0, 1)) }}" style="border-width: 2px !important; border-color: #4e73df !important;">
            </a>
            
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-modern animated--grow-in mt-2"
                aria-labelledby="userDropdown">
                
                <div class="dropdown-header-gradient text-center pb-3">
                    <img class="rounded-circle border mb-2 shadow" src="https://placehold.co/100x100/ffffff/4e73df?text={{ strtoupper(substr(Auth::user()->name, 0, 1)) }}" width="60" height="60" style="border-width: 3px !important; border-color: rgba(255,255,255,0.8) !important;">
                    <h6 class="mb-0 font-weight-bold text-white">{{ Auth::user()->name }}</h6>
                    <small class="text-white-50">{{ Auth::user()->email ?? 'admin@pandawa.com' }}</small>
                </div>

                <div class="p-2">
                    <a class="dropdown-item dropdown-item-custom rounded py-2" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user-cog fa-sm fa-fw mr-2 text-primary"></i>
                        Pengaturan Profil
                    </a>
                    <div class="dropdown-divider my-1"></div>
                    <a class="dropdown-item dropdown-item-custom rounded py-2 text-danger" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger"></i>
                        Keluar Aplikasi
                    </a>
                </div>
            </div>
        </li>
        @endauth
    </ul>

</nav>

<!-- SCRIPT PENGATUR JAM & SAPAAN -->
