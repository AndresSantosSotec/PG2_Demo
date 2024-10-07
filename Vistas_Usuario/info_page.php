<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struct Migraciones - Información</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            color: black;
        }
        .pricing-table-title {
            font-weight: bold;
        }
        .pricing-table-price-currency,
        .pricing-table-price-amount {
            color: #000;
        }
        .pricing-table-features li {
            color: #000;
        }
        .hero {
            background-color: #007DFF;
            color: white;
            padding: 60px 0;
            text-align: center;
        }
        .info-section {
            padding: 60px 0;
            background-color: #f9f9f9;
        }
        .pricing-section {
            padding: 60px 0;
        }
        .btn-demo {
            background-color: #007DFF;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Struct Migraciones</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Precios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary btn-demo" href="#">Iniciar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Automatiza tus Migraciones de Datos con IA</h1>
            <p>Nuestra API permite automatizar migraciones de bases de datos de manera rápida y segura, minimizando pérdidas de datos y garantizando la calidad a través de inteligencia artificial avanzada.</p>
            <a href="#" class="btn btn-light btn-lg mt-3">Explorar la API</a>
        </div>
    </section>

    <!-- Info Section (How the API Works) -->
    <section id="info" class="info-section">
        <div class="container">
            <h2 class="text-center mb-5">¿Cómo Funciona Nuestra API?</h2>
            <div class="row text-center">
                <div class="col-md-4">
                    <h4>1. Conexión a tu Base de Datos</h4>
                    <p>Nuestra API se conecta a las bases de datos relacionales, como MySQL y SQL Server, para extraer los datos de origen.</p>
                </div>
                <div class="col-md-4">
                    <h4>2. Transformación y Validación</h4>
                    <p>Una vez extraídos los datos, nuestra API los transforma para asegurar que sean compatibles con la base de datos destino y valida su integridad.</p>
                </div>
                <div class="col-md-4">
                    <h4>3. Migración Segura</h4>
                    <p>La API realiza la migración de datos asegurando la encriptación en tránsito y en reposo, manteniendo la confidencialidad y seguridad.</p>
                </div>
            </div>
            <div class="row text-center mt-5">
                <div class="col-md-6">
                    <h4>4. Resolución de Conflictos</h4>
                    <p>Durante la migración, si se detectan conflictos o errores, la API genera un informe detallado para que puedas corregir cualquier inconsistencia.</p>
                </div>
                <div class="col-md-6">
                    <h4>5. Notificaciones y Reportes</h4>
                    <p>Al finalizar la migración, recibirás notificaciones sobre el éxito del proceso y un reporte detallado del estado de la migración.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing-section">
        <div class="container">
            <h2 class="text-center mb-5">Planes de Precios</h2>
            <div class="row">
                <!-- Gratis Plan -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="card-title pricing-table-title">Gratis</h3>
                            <p class="card-text">
                                <span class="pricing-table-price-currency">$</span><span class="pricing-table-price-amount h2">0</span>/mes
                            </p>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>Limitado a un solo gestor (MySQL)</li>
                                <li>Migraciones básicas de bases de datos</li>
                                <li>Soporte básico vía email</li>
                                <li>Ideal para pequeñas migraciones o pruebas</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary">Comenzar ahora</a>
                        </div>
                    </div>
                </div>
                <!-- Starter Plan -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="card-title pricing-table-title">Starter</h3>
                            <p class="card-text">
                                <span class="pricing-table-price-currency">$</span><span class="pricing-table-price-amount h2">27</span>/mes
                            </p>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>Acceso a múltiples gestores (MySQL, PostgreSQL, SQL Server)</li>
                                <li>Migraciones de volumen medio (hasta 100,000 registros)</li>
                                <li>Soporte prioritario vía chat</li>
                                <li>Automatización de procesos ETL (Extracción, Transformación y Carga)</li>
                                <li>Incluye notificaciones por correo sobre el estado de la migración</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary">Comenzar ahora</a>
                        </div>
                    </div>
                </div>
                <!-- Professional Plan -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3 class="card-title pricing-table-title">Professional</h3>
                            <p class="card-text">
                                <span class="pricing-table-price-currency">$</span><span class="pricing-table-price-amount h2">97</span>/mes
                            </p>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li>Migraciones a gran escala (más de 1,000,000 de registros)</li>
                                <li>Soporte premium 24/7 vía chat y teléfono</li>
                                <li>Integración con sistemas ERP y CRM</li>
                                <li>Soporte de migraciones avanzadas (esquemas complejos)</li>
                                <li>Automatización avanzada y optimización de consultas</li>
                                <li>Soporte personalizado y reportes detallados de migración</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary">Comenzar ahora</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center py-4">
        <p>&copy; 2024 Struct Migraciones. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
