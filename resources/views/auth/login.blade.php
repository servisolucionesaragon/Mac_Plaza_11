<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — CRM {{ (isset($config) ? $config->nombre_tienda : null) ?? 'Tienda Celulares' }}</title>
    @if(isset($config) && $config->logo)
        <link rel="icon" type="{{ \Illuminate\Support\Facades\Storage::disk('public')->mimeType($config->logo) ?? 'image/png' }}" href="{{ asset('storage/' . $config->logo) }}?v={{ $config->updated_at->timestamp }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a0a3e 0%, #2d1b69 50%, #4c1d95 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 560px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,.4);
        }

        /* Panel izquierdo decorativo */
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #a855f7, #ec4899);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 220px; height: 220px;
            background: rgba(255,255,255,.12);
            border-radius: 50%;
        }

        .login-left::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -40px;
            width: 280px; height: 280px;
            background: rgba(255,255,255,.08);
            border-radius: 50%;
        }

        .login-left .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative; z-index: 1;
        }

        .login-left .brand-icon {
            width: 52px; height: 52px;
            background: rgba(255,255,255,.25);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: #fff;
        }

        .login-left .brand-text h1 {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .login-left .brand-text p {
            color: rgba(255,255,255,.8);
            font-size: 12px;
            margin: 0;
        }

        .login-left .features {
            position: relative; z-index: 1;
        }

        .login-left .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .login-left .feature-icon {
            width: 40px; height: 40px;
            background: rgba(255,255,255,.2);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 16px;
            flex-shrink: 0;
        }

        .login-left .feature-text h6 {
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            margin: 0;
        }

        .login-left .feature-text p {
            color: rgba(255,255,255,.7);
            font-size: 11px;
            margin: 0;
        }

        /* Panel derecho con formulario */
        .login-right {
            flex: 1;
            background: #fff;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1e1b4b;
            margin-bottom: 6px;
        }

        .login-right .subtitle {
            color: #6b7280;
            font-size: 13.5px;
            margin-bottom: 32px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
        }

        .form-control {
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            padding: 10px 16px;
            font-size: 13.5px;
            font-family: 'Poppins', sans-serif;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus {
            border-color: #a855f7;
            box-shadow: 0 0 0 3px rgba(168,85,247,.15);
            outline: none;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 1.5px solid #e5e7eb;
            border-right: none;
            background: #f9fafb;
            color: #9ca3af;
        }

        .input-group .form-control {
            border-radius: 0 10px 10px 0;
            border-left: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: #a855f7;
        }

        .btn-login {
            background: linear-gradient(135deg, #a855f7, #ec4899);
            border: none;
            border-radius: 10px;
            padding: 11px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            width: 100%;
            cursor: pointer;
            transition: opacity .2s, transform .2s;
            font-family: 'Poppins', sans-serif;
        }

        .btn-login:hover { opacity: .92; transform: translateY(-1px); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: #9ca3af;
            font-size: 12px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .form-check-input:checked {
            background-color: #a855f7;
            border-color: #a855f7;
        }

        .text-accent { color: #a855f7; }
        .text-accent:hover { color: #9333ea; }

        .alert { border-radius: 10px; font-size: 13px; }

        @media (max-width: 640px) {
            .login-left { display: none; }
            .login-right { border-radius: 24px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Lado izquierdo -->
        <div class="login-left">
            <div class="brand">
                <div class="brand-icon">
                    @if(isset($config) && $config->logo)
                        <img src="{{ asset('storage/' . $config->logo) }}?v={{ $config->updated_at->timestamp }}" alt="Logo" style="width:100%; height:100%; object-fit:cover; border-radius:14px;">
                    @else
                        <i class="fas fa-mobile-alt"></i>
                    @endif
                </div>
                <div class="brand-text">
                    <h1>{{ $config->nombre_tienda ?? 'CRM Celulares' }}</h1>
                    <p>Sistema de Gestión Integral</p>
                </div>
            </div>

            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <div class="feature-text">
                        <h6>Gestión de Clientes</h6>
                        <p>Administra tu cartera de clientes</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="feature-text">
                        <h6>Control de Ventas</h6>
                        <p>Registra y monitorea cada venta</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-tools"></i></div>
                    <div class="feature-text">
                        <h6>Servicio Técnico</h6>
                        <p>Gestiona reparaciones en tiempo real</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="feature-text">
                        <h6>Reportes y Estadísticas</h6>
                        <p>Toma decisiones con datos reales</p>
                    </div>
                </div>
            </div>

            <div style="color: rgba(255,255,255,.5); font-size: 11px; position:relative; z-index:1;">
                © {{ date('Y') }} CRM Tienda Celulares
            </div>
        </div>

        <!-- Lado derecho -->
        <div class="login-right">
            <h2>¡Bienvenido de vuelta!</h2>
            <p class="subtitle">Ingresa tus credenciales para acceder al sistema</p>

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope fa-sm"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}"
                               placeholder="correo@tienda.com" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock fa-sm"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="••••••••" required>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember" style="font-size:13px; color:#6b7280;">
                            Recordarme
                        </label>
                    </div>
                    @php
                        $mensajeOlvidoClave = 'Hola, necesito ayuda para restablecer mi contraseña de acceso al sistema de *'
                            . ($config->nombre_tienda ?? 'la tienda') . '*.';
                        $whatsappOlvidoClave = isset($config) ? $config->whatsappUrl($mensajeOlvidoClave) : null;
                    @endphp
                    @if($whatsappOlvidoClave)
                    <a href="{{ $whatsappOlvidoClave }}" target="_blank" rel="noopener" class="text-accent text-decoration-none" style="font-size:13px;">
                        <i class="fab fa-whatsapp me-1"></i>¿Olvidaste tu contraseña?
                    </a>
                    @endif
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
