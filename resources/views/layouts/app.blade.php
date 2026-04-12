<!DOCTYPE html>
<html lang="id"> {{-- Diganti ke ID agar sesuai standar TA --}}

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PANDAWA - Dashboard</title>
    <link rel="icon" href="{{ asset('img/tsk.png') }}" type="image/png">

    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="{{ asset('template/css/sb-admin-2.min.css')}}" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        /* MENGATUR LEBAR SIDEBAR AGAR LEBIH LEGA */
        @media (min-width: 768px) {
            .sidebar {
                width: 18rem !important;
            }
            .sidebar.toggled {
                width: 6.5rem !important;
            }
            
            /* Sidebar Sticky */
            #accordionSidebar {
                position: sticky !important;
                top: 0;
                height: 100vh;
                z-index: 1050;
                overflow-y: auto;
            }

            #accordionSidebar::-webkit-scrollbar {
                width: 4px;
            }
            #accordionSidebar::-webkit-scrollbar-thumb {
                background: rgba(255,255,255,0.2);
                border-radius: 10px;
            }
        }

        /* NAVBAR (TOPBAR) STICKY */
        .navbar.navbar-expand {
            position: sticky !important;
            top: 0;
            z-index: 1030;
            background-color: #fff;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15) !important;
        }

        #content-wrapper {
            display: flex;
            flex-direction: column;
            width: 100%;
            overflow-x: hidden;
        }

        .sidebar .nav-item .collapse .collapse-inner .collapse-item {
            white-space: normal !important;
            word-wrap: break-word;
            line-height: 1.2;
            padding: 0.5rem 1rem;
        }

        /* 3. Penyesuaian font agar lebih ramah dibaca untuk dokumen TA */
        body {
            color: #2d3436;
        }
    </style>
</head>

<body id="page-top">

    <div id="wrapper">

        @include('layouts.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                @include('layouts.navbar')
                
                <div class="container-fluid pt-4 animate__animated animate__fadeIn"> {{-- Ditambah animasi fade-in saat ganti halaman --}}
                    @yield('content')
                </div>
                
            </div>
            
            @include('layouts.footer')
            
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title font-weight-bold text-primary" id="exampleModalLabel">Konfirmasi Keluar</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Apakah Anda yakin akan keluar dari aplikasi PANDAWA?</div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary shadow-sm px-4">Keluar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('template/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <script src="{{ asset('template/js/sb-admin-2.min.js')}}"></script>

    {{-- 4. Global Script untuk SweetAlert2 Flash Messages --}}
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: "{{ session('error') }}",
            confirmButtonColor: '#4e73df'
        });
    </script>
    @endif

    @stack('scripts')

</body>
</html>