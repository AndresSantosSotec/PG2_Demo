<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo - Struct Migraciones</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="text-center mb-4">Bienvenido al Dashboard</h1>
    
    <div class="row">
        <!-- Demo de Migraciones -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Demo de Migraciones</h3>
                </div>
                <div class="card-body">
                    <p>Accede a la demo interactiva de migraciones de datos.</p>
                    <a href="#" class="btn btn-primary w-100">Ver Demo</a>
                </div>
            </div>
        </div>

        <!-- API Key con uso limitado -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">Gestión de API Key</h3>
                </div>
                <div class="card-body">
                    <p>API Key para el usuario: <strong id="api-key"></strong></p>
                    <p>Usos restantes: <strong id="api-usage"></strong></p>
                    <a href="#" class="btn btn-success w-100">Administrar API Key</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript para actualizar la API key y usos restantes -->
<script>
    // Simular datos recibidos tras el inicio de sesión
    const apiKey = sessionStorage.getItem('api_key') || 'No API Key';
    const usosRestantes = sessionStorage.getItem('usos_restantes') || '0';

    document.getElementById('api-key').innerText = apiKey;
    document.getElementById('api-usage').innerText = usosRestantes;
</script>

</body>
</html>
