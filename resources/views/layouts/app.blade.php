<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>PANDAWA - Dashboard</title>
    <link rel="icon" href="{{ asset('img/tsk.png') }}" type="image/png">

    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="{{ asset('template/css/sb-admin-2.min.css')}}" rel="stylesheet">

    <style>
        /* 1. MENGATUR LEBAR SIDEBAR AGAR LEBIH LEGA */
        @media (min-width: 768px) {
            .sidebar {
                width: 18rem !important; /* Standar 14rem, kita naikkan ke 18rem */
            }
            .sidebar.toggled {
                width: 6.5rem !important;
            }
            
            /* Sidebar Tetap Diam (Sticky) */
            #accordionSidebar {
                position: sticky !important;
                top: 0;
                height: 100vh;
                z-index: 1050;
                overflow-y: auto;
            }

            /* Sembunyikan scrollbar sidebar agar tetap estetik */
            #accordionSidebar::-webkit-scrollbar {
                width: 4px;
            }
            #accordionSidebar::-webkit-scrollbar-thumb {
                background: rgba(255,255,255,0.2);
                border-radius: 10px;
            }
        }

        /* 2. NAVBAR (TOPBAR) TETAP DIAM */
        .navbar.navbar-expand {
            position: sticky !important;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1030;
            background-color: #fff;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15) !important;
        }

        /* 3. PENYESUAIAN KONTEN UTAMA */
        #content-wrapper {
            display: flex;
            flex-direction: column;
            width: 100%;
            overflow-x: hidden;
        }

        /* Agar teks di dalam sidebar tidak terpotong (Wrap text) */
        .sidebar .nav-item .collapse .collapse-inner .collapse-item {
            white-space: normal !important;
            word-wrap: break-word;
            line-height: 1.2;
            padding: 0.5rem 1rem;
        }
    </style>
</head>

<body id="page-top">

    <div id="wrapper">

        @include('layouts.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                @include('layouts.navbar')
                <div class="container-fluid pt-4">
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
            <form action="/logout" method="post">
                @csrf
                @method('POST')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Logout?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Apakah anda yakin akan keluar dari aplikasi?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('template/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <script src="{{ asset('template/js/sb-admin-2.min.js')}}"></script>

    @stack('scripts')

</body>
</html>