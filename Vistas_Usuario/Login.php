<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struct Migraciones - Login/Register</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Poppins', sans-serif;
        }

        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .auth-card {
            max-width: 400px;
            width: 100%;
            border-radius: 20px;
            padding: 40px;
            background-color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .auth-card:hover {
            transform: translateY(-5px);
        }

        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-header h3 {
            font-size: 24px;
            margin: 0;
            color: #007DFF;
            font-weight: 600;
        }

        .form-header p {
            color: #777;
            margin-top: 5px;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 15px;
            font-size: 14px;
            background-color: #f9f9f9;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #007DFF;
            box-shadow: 0 0 0 2px rgba(0, 125, 255, 0.1);
        }

        .btn-primary, .btn-success {
            padding: 12px;
            font-size: 16px;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #007DFF;
            border-color: #007DFF;
        }

        .btn-primary:hover {
            background-color: #005bb5;
            border-color: #005bb5;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
        }

        .form-footer a {
            color: #007DFF;
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            background-color: #f0f2f5;
            border: 1px solid #ddd;
        }

        .auth-card:hover {
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>

<div class="container auth-container">
    <div class="card auth-card">
        <div class="form-header">
            <h3 id="form-title">Iniciar Sesión</h3>
            <p id="form-description">Por favor, inicia sesión para continuar</p>
        </div>

        <!-- Login Form -->
        <form id="login-form">
            <div class="mb-3">
                <label for="login-email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="login-email" placeholder="Ingresa tu correo" required>
            </div>
            <div class="mb-3">
                <label for="login-password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="login-password" placeholder="Ingresa tu contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
        </form>

        <!-- Register Form (hidden by default) -->
        <form id="register-form" style="display: none;" onsubmit="return validateRegisterForm()">
            <div class="mb-3">
                <label for="register-name" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="register-name" placeholder="Ingresa tu nombre completo" required>
            </div>
            <div class="mb-3">
                <label for="register-email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="register-email" placeholder="Ingresa tu correo" required>
            </div>
            <div class="mb-3">
                <label for="register-password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="register-password" placeholder="Crea una contraseña" required>
            </div>
            <div class="mb-3">
                <label for="register-confirm-password" class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="register-confirm-password" placeholder="Confirma tu contraseña" required>
            </div>
            <div class="mb-3">
                <label for="register-country" class="form-label">País</label>
                <select class="form-control" id="register-country" required>
                    <option value="" selected disabled>Selecciona tu país</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="register-phone" class="form-label">Número de Teléfono</label>
                <div class="input-group">
                    <span class="input-group-text" id="phone-addon">+502</span>
                    <input type="tel" class="form-control" id="register-phone" placeholder="Número de teléfono" required>
                </div>
            </div>
            <button type="submit" class="btn btn-success w-100">Crear Cuenta</button>
        </form>

        <div class="form-footer">
            <span id="login-link">¿No tienes una cuenta? <a href="#" onclick="showRegisterForm()">Regístrate aquí</a></span>
            <span id="register-link" style="display: none;">¿Ya tienes una cuenta? <a href="#" onclick="showLoginForm()">Inicia sesión aquí</a></span>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript to toggle between login and register forms -->
<script>
    function showRegisterForm() {
        document.getElementById('login-form').style.display = 'none';
        document.getElementById('register-form').style.display = 'block';
        document.getElementById('form-title').innerText = 'Crear Cuenta';
        document.getElementById('form-description').innerText = 'Por favor, completa el formulario para registrarte';
        document.getElementById('login-link').style.display = 'none';
        document.getElementById('register-link').style.display = 'block';
    }

    function showLoginForm() {
        document.getElementById('login-form').style.display = 'block';
        document.getElementById('register-form').style.display = 'none';
        document.getElementById('form-title').innerText = 'Iniciar Sesión';
        document.getElementById('form-description').innerText = 'Por favor, inicia sesión para continuar';
        document.getElementById('login-link').style.display = 'block';
        document.getElementById('register-link').style.display = 'none';
    }

    // Validación de formulario de registro
    function validateRegisterForm() {
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm-password').value;

        // Validación de contraseñas
        const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
        if (!passwordRegex.test(password)) {
            alert('La contraseña debe tener al menos 8 caracteres, incluyendo un número, una letra mayúscula, una letra minúscula y un carácter especial.');
            return false;
        }

        if (password !== confirmPassword) {
            alert('Las contraseñas no coinciden.');
            return false;
        }

        return true;
    }
</script>

</body>
</html>
