<?php
// Iniciar la sesión
session_start();

// Verificar si la sesión tiene los datos necesarios, si no redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Función para cerrar sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo - Struct Migraciones</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            height: 100vh;
            overflow: hidden;
            background-color: #f4f7fc;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #5B61C0, #7A72DC);
            color: white;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: all 0.3s ease;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 15px;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 16px;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar h2 {
            font-size: 22px;
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar.collapsed a {
            justify-content: center;
            text-align: center;
        }
        .sidebar.collapsed a i {
            margin-right: 0;
        }
        .toggle-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: white;
            outline: none;
        }
        .sidebar.collapsed .toggle-btn {
            right: auto;
            left: 20px;
        }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            border-radius: 10px 10px 0 0;
        }
        .card-header.bg-primary {
            background: linear-gradient(135deg, #5B61C0, #7A72DC);
        }
        .card-header.bg-success {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <button class="toggle-btn" id="toggle-btn">
        <i class="fas fa-bars"></i>
    </button>
    <h2>Menú</h2>
    <a href="demo.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="demo.php?logout=true"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
</div>

<!-- Main Content -->
<div class="main-content">
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
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript para actualizar la API key y usos restantes -->
<script>
    const apiKey = sessionStorage.getItem('api_key');
    const usosRestantes = sessionStorage.getItem('usos_restantes');

    if (apiKey && usosRestantes) {
        document.getElementById('api-key').innerText = apiKey;
        document.getElementById('api-usage').innerText = usosRestantes;
    } else {
        alert('No se encontraron datos de API Key, por favor inicia sesión de nuevo.');
        window.location.href = 'login.php';
    }

    // Toggle sidebar functionality
    const toggleBtn = document.getElementById('toggle-btn');
    const sidebar = document.getElementById('sidebar');

    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
    });
</script>

</body>
</html>
