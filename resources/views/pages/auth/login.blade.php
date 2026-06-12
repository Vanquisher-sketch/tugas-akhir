<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="PANDAWA - Sistem Pengelolaan Aset Daerah">
    <title>PANDAWA - Login Portal</title>

    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800&display=swap" rel="stylesheet">

    <link href="{{ asset('template/css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <style>
        /* 🌟 LUXURY CINEMATIC DARK BACKGROUND */
        body.bg-luxury-universe {
            background-color: #030712 !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        /* Efek Ombak Cahaya Neon di Latar Belakang (Ambient Aura Animation) */
        body.bg-luxury-universe::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.25) 0%, transparent 65%);
            top: -10%;
            left: -10%;
            animation: ambientWave 12s ease-in-out infinite alternate;
            pointer-events: none;
        }

        body.bg-luxury-universe::after {
            content: '';
            position: absolute;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.18) 0%, transparent 70%);
            bottom: -20%;
            right: -10%;
            animation: ambientWave 15s ease-in-out infinite alternate-reverse;
            pointer-events: none;
        }

        @keyframes ambientWave {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(80px, 50px) scale(1.15); }
        }

        .luxury-login-wrapper {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
        }

        /* 🌟 BOX KOTAK LOGIN DENGAN ANIMATED GRADIENT BORDER (PREMIUM EFFECT) */
        .luxury-gradient-card {
            position: relative;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 24px !important;
            padding: 3.5rem 2.5rem;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            overflow: hidden;
        }

        /* Garis Kilaun Berjalan Halus di Bingkai Card */
        .luxury-gradient-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, transparent, #4f46e5, #6366f1, #06b6d4, transparent);
            background-size: 200% 100%;
            animation: borderGlow 6s linear infinite;
        }

        @keyframes borderGlow {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }

        /* Identity Top Layout */
        .avatar-glow-zone {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .avatar-glow-zone img {
            max-height: 75px;
            width: auto;
            filter: drop-shadow(0 0 20px rgba(99, 102, 241, 0.4));
        }

        .text-luxury-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 3px;
            margin-bottom: 0.4rem;
            text-align: center;
            text-shadow: 0 2px 10px rgba(255,255,255,0.1);
        }

        .text-luxury-subtitle {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 3rem;
            text-align: center;
        }

        /* Interactive Inputs (Clean Border Animation) */
        .form-group-luxury {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group-luxury label {
            font-size: 0.78rem;
            font-weight: 700;
            color: #cbd5e1;
            margin-bottom: 0.6rem;
            display: block;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding-left: 2px;
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }

        .form-control-luxury {
            border-radius: 14px !important;
            height: auto !important;
            padding: 0.85rem 1rem 0.85rem 2.8rem !important;
            font-size: 0.9rem !important;
            border: 1.5px solid rgba(255, 255, 255, 0.12) !important;
            background: rgba(255, 255, 255, 0.03) !important;
            color: #ffffff !important;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .form-control-luxury:focus {
            border-color: #6366f1 !important; /* Indigo Cyber Glow */
            background: rgba(15, 23, 42, 0.8) !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25) !important;
            outline: none;
        }

        /* Efek icon menyala saat input aktif */
        .form-control-luxury:focus + i {
            color: #6366f1;
            filter: drop-shadow(0 0 8px rgba(99, 102, 241, 0.6));
        }

        /* Button Cyber Pulse Action */
        .btn-luxury-action {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
            border: none !important;
            border-radius: 14px !important;
            padding: 0.95rem !important;
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            box-shadow: 0 8px 24px -6px rgba(79, 70, 229, 0.5) !important;
            transition: all 0.3s ease !important;
            margin-top: 1rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .btn-luxury-action:hover {
            background: linear-gradient(135deg, #5a52e6 0%, #7578f5 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 12px 28px -5px rgba(99, 102, 241, 0.6) !important;
        }

        /* Checkbox Style Alignment */
        .custom-control-label::before {
            background-color: rgba(255,255,255,0.05) !important;
            border: 1px solid rgba(255,255,255,0.2) !important;
        }

        /* Handle Error Validation Laravel */
        .form-group-luxury.has-error .form-control-luxury {
            border-color: #ef4444 !important;
            background-color: rgba(239, 68, 68, 0.08) !important;
        }

        .form-group-luxury.has-error .help-block {
            color: #ef4444;
            font-size: 0.78rem;
            margin-top: 6px;
            display: block;
            font-weight: 600;
        }

        .footer-luxury-text {
            color: #4b5563;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            margin-top: 2.5rem;
            letter-spacing: 0.8px;
        }
    </style>
</head>

<body class="bg-luxury-universe">

    <div class="luxury-login-wrapper">
        
        <div class="card luxury-gradient-card">
            <div class="card-body p-0">
                
                <div class="avatar-glow-zone">
                    <img src="{{ asset('img/tsk.png') }}" alt="Logo Pemerintahan">
                </div>

                <h1 class="text-luxury-title">PANDAWA</h1>
                <p class="text-luxury-subtitle">Sistem Tata Kelola Aset • Tawang</p>

                {{-- Flash Message Success Handler --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show small py-2.5 mb-4" role="alert" style="border-radius: 12px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399;">
                        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: #34d399;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- FORM SYSTEM PROCESS --}}
                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="form-group-luxury {{ $errors->has('email') ? 'has-error' : '' }}">
                        <label for="inputEmail">Alamat Email Resmi</label>
                        <div class="input-icon-wrapper">
                            <input type="email" class="form-control form-control-luxury" 
                                   id="inputEmail" name="email" placeholder="nama@email.com" 
                                   value="{{ old('email') }}" required autocomplete="email" autofocus>
                            <i class="fas fa-envelope"></i>
                        </div>
                        @if ($errors->has('email'))
                            <span class="help-block"><i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first('email') }}</span>
                        @endif
                    </div>

                    <div class="form-group-luxury {{ $errors->has('password') ? 'has-error' : '' }}">
                        <label for="inputPassword">Kata Sandi Akun</label>
                        <div class="input-icon-wrapper">
                            <input type="password" name="password" class="form-control form-control-luxury" 
                                   id="inputPassword" placeholder="••••••••" required autocomplete="current-password">
                            <i class="fas fa-lock"></i>
                        </div>
                        @if ($errors->has('password'))
                            <span class="help-block"><i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first('password') }}</span>
                        @endif
                    </div>

                    <div class="form-group mb-4" style="padding-left: 2px; margin-top: 1.2rem;">
                        <div class="custom-control custom-checkbox small">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="remember">
                            <label class="custom-control-label text-muted font-weight-bold" for="customCheck" style="cursor: pointer; user-select: none;">Ingat Sesi Login</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-luxury-action btn-block font-weight-bold">
                        Masuk Ke Sistem <i class="fas fa-chevron-right fa-sm ml-1"></i>
                    </button>
                </form>

                <div class="footer-luxury-text">
                    Pemerintah Kota Tasikmalaya &copy; 2026
                </div>

            </div>
        </div>

    </div>

    <script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>
</body>

</html>