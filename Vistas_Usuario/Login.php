<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struct Migraciones - Login/Register</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../Assets/css/login_styles.css" rel="stylesheet">
    <!-- SweetAlert CSS & JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container auth-container">
    <div class="card auth-card">
        <div class="form-header">
            <h3 id="form-title">Iniciar Sesión</h3>
            <p id="form-description">Por favor, inicia sesión para continuar</p>
        </div>

        <!-- Alert success message (hidden by default) -->
        <div id="register-success" class="alert alert-success" role="alert" style="display:none;">
            ¡Usuario registrado correctamente! Ahora puedes iniciar sesión.
        </div>

        <!-- Login Form -->
        <form id="login-form" action="../Backend/auth.php" method="POST" onsubmit="handleLogin(event)">
            <input type="hidden" name="action" value="login">
            <div class="mb-3">
                <label for="login-email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" name="correo_electronico" id="login-email" placeholder="Ingresa tu correo" required>
            </div>
            <div class="mb-3">
                <label for="login-password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="contrasena" id="login-password" placeholder="Ingresa tu contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
        </form>

        <!-- Register Form -->
        <form id="register-form" action="../Backend/auth.php" method="POST" style="display: none;" onsubmit="handleRegister(event)">
            <input type="hidden" name="action" value="register">
            <div class="mb-3">
                <label for="register-name" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" name="nombre_completo" id="register-name" placeholder="Ingresa tu nombre completo" required>
            </div>
            <div class="mb-3">
                <label for="register-email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" name="correo_electronico" id="register-email" placeholder="Ingresa tu correo" required>
            </div>
            <div class="mb-3">
                <label for="register-password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="contrasena" id="register-password" placeholder="Crea una contraseña" required>
            </div>
            <div class="mb-3">
                <label for="register-confirm-password" class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control" id="register-confirm-password" placeholder="Confirma tu contraseña" required>
            </div>
            <div class="mb-3">
                <label for="register-country" class="form-label">País</label>
                <select class="form-control" name="pais" id="register-country" required>
                    <option value="" selected disabled>Selecciona tu país</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="register-phone" class="form-label">Número de Teléfono</label>
                <div class="input-group">
                    <span class="input-group-text" id="phone-addon">+502</span>
                    <input type="tel" class="form-control" name="telefono" id="register-phone" placeholder="Número de teléfono" pattern="[\d\s]{8,15}" required>
                </div>
            </div>
            <div id="register-alert" class="alert alert-danger" role="alert" style="display:none;"></div>
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

    // Función para manejar el login
    function handleLogin(event) {
        event.preventDefault();
        const formData = new FormData(document.getElementById('login-form'));

        fetch('../Backend/auth.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Guardar la API Key, usos restantes y el ID de usuario en sessionStorage
                sessionStorage.setItem('api_key', data.api_key);
                sessionStorage.setItem('usos_restantes', data.usos_restantes);
                sessionStorage.setItem('usuario_id', data.usuario_id);

                // Redirigir al dashboard
                window.location.href = data.redirect;
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un problema en el servidor.');
        });
    }

    function handleRegister(event) {
        event.preventDefault();
        const formData = new FormData(document.getElementById('register-form'));

        fetch('../Backend/auth.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('register-success').style.display = 'block'; // Mostrar mensaje de éxito
                document.getElementById('register-alert').style.display = 'none'; // Ocultar alert de error
                showLoginForm(); // Mostrar formulario de login tras registro
            } else {
                document.getElementById('register-alert').style.display = 'block';
                document.getElementById('register-alert').innerText = data.message;
                document.getElementById('register-success').style.display = 'none'; // Ocultar el mensaje de éxito
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un problema en el servidor.');
        });
    }

    window.onload = function() {
        const countrySelect = document.getElementById('register-country');
        const phoneAddon = document.getElementById('phone-addon');

        const allowedCountries = {
            "GT": "Guatemala",
            "AR": "Argentina",
            "BR": "Brasil",
            "CL": "Chile",
            "CO": "Colombia",
            "CR": "Costa Rica",
            "CU": "Cuba",
            "DO": "República Dominicana",
            "EC": "Ecuador",
            "SV": "El Salvador",
            "ES": "España",
            "MX": "México",
            "PA": "Panamá",
            "PE": "Perú",
            "PR": "Puerto Rico",
            "UY": "Uruguay",
            "VE": "Venezuela",
            "US": "Estados Unidos",
            "GB": "Reino Unido"
        };

        for (const [code, country] of Object.entries(allowedCountries)) {
            const option = document.createElement('option');
            option.value = code;
            option.text = country;
            countrySelect.appendChild(option);
        }

        countrySelect.addEventListener('change', function() {
            const selectedCountry = this.value;

            fetch(`https://restcountries.com/v3.1/alpha/${selectedCountry}`)
                .then(response => response.json())
                .then(data => {
                    let countryCode = `${data[0].idd.root}${data[0].idd.suffixes[0]}`;
                    if (countryCode.startsWith('+')) {
                        countryCode = countryCode.slice(1);
                    }
                    phoneAddon.textContent = `+${countryCode}`;
                });
        });
    };
</script>

</body>
</html>
