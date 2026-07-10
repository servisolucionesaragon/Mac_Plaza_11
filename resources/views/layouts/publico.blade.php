<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Recibo') — {{ $config->nombre_tienda ?? 'CRM Celulares' }}</title>
    @if(isset($config) && $config->logo)
        <link rel="icon" type="{{ \Illuminate\Support\Facades\Storage::disk('public')->mimeType($config->logo) ?? 'image/png' }}" href="{{ asset('storage/' . $config->logo) }}?v={{ $config->updated_at->timestamp }}">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f0fb; color: #1e1b4b; min-height: 100vh; padding: 24px 16px; }
        .publico-wrap { max-width: 900px; margin: 0 auto; }
        .card { border: none; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .btn-primary { background: linear-gradient(135deg,#a855f7,#ec4899); border: none; border-radius: 8px; }
        .btn-primary:hover { opacity: .9; }
        @media print {
            body { background: #fff; padding: 0; }
            .btn-acciones { display: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="publico-wrap">
        @yield('content')
    </div>
</body>
</html>
