<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PANDAWA - Login</title>

    <!-- Custom fonts for this template--><link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template--><link href="{{ asset('template/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .bg-login-image {
            background: linear-gradient(to right, #4e73df, #224abe); /* Contoh gradien */
            /* Jika Anda punya gambar khusus untuk sisi kiri, bisa pakai ini: */
            /* background: url('{{ asset('img/your-custom-login-image.jpg') }}'); */
            background-position: center;
            background-size: cover;
        }
        .form-group.has-error .form-control {
            border-color: #e74a3b;
        }
        .form-group.has-error .help-block {
            color: #e74a3b;
            font-size: 0.8em;
            margin-top: 5px;
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="container">

        <!-- Outer Row --><div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body --><div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        {{-- Tambah Logo --}}
                                        <img src="{{ asset('img/tsk.png') }}" alt="Logo Pemkot Tasikmalaya" class="mb-4" style="max-width: 100px;">
                                        <h1 class="h4 text-gray-900 mb-4">Selamat Datang di PANDAWA</h1>
                                    </div>

                                    {{-- Tampilkan pesan success --}}
                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif

                                    <form class="user" action="{{ route('login') }}" method="POST">
                                        @csrf
                                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                            <input type="email" class="form-control form-control-user"
                                                id="inputEmail" name="email" aria-describedby="emailHelp"
                                                placeholder="Masukkan Alamat Email..." value="{{ old('email') }}">
                                            @if ($errors->has('email'))
                                                <span class="help-block">{{ $errors->first('email') }}</span>
                                            @endif
                                        </div>
                                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                                            <input type="password" name="password" class="form-control form-control-user"
                                                id="inputPassword" placeholder="Password">
                                            @if ($errors->has('password'))
                                                <span class="help-block">{{ $errors->first('password') }}</span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck" name="remember">
                                                <label class="custom-control-label" for="customCheck">Ingat Saya</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                        <hr>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript--><script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript--><script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages--><script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>

</body>

</html>
