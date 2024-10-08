<?php
include 'conexion_bd.php'; // Incluye el archivo de conexión

// Función para generar una API key única
function generarApiKey() {
    return bin2hex(random_bytes(16)); // Genera una clave de 32 caracteres (128 bits)
}

// Función para registrar la API key en la base de datos
function registrarApiKey($userId, $conn) {
    $apiKey = generarApiKey();
    $usosRestantes = 50; // Número de usos disponibles por defecto

    // Inserta la API key en la base de datos
    $sql = "INSERT INTO api_keys (user_id, api_key, usos_restantes) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $userId, $apiKey, $usosRestantes);

    if ($stmt->execute()) {
        return ['api_key' => $apiKey, 'usos_restantes' => $usosRestantes];
    } else {
        throw new Exception('Error al generar la API key: ' . $conn->error);
    }
}

// Función para verificar si un correo ya está registrado
function verificarCorreoExistente($correo_electronico, $conn) {
    $sql = "SELECT * FROM tb_users WHERE correo_electronico = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo_electronico);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0; // Retorna true si el correo existe
}

// Función para obtener la API key de un usuario
function obtenerApiKey($userId, $conn) {
    $sql = "SELECT api_key, usos_restantes FROM api_keys WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Iniciar el procesamiento de la solicitud
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = $_POST['action']; // Recoge el valor del campo oculto para saber qué operación hacer

        if ($action == 'register') {
            // Código para el registro
            $nombre_completo = $_POST['nombre_completo'];
            $correo_electronico = $_POST['correo_electronico'];
            $contrasena = $_POST['contrasena'];
            $telefono = $_POST['telefono'];
            $pais = $_POST['pais'];

            // Validaciones básicas
            if (empty($nombre_completo) || empty($correo_electronico) || empty($contrasena)) {
                echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios']);
                exit();
            }

            // Verificar si el correo ya está registrado
            if (verificarCorreoExistente($correo_electronico, $conn)) {
                echo json_encode(['status' => 'error', 'message' => 'Este correo ya está registrado']);
                exit();
            }

            // Hashear la contraseña
            $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

            // Insertar los datos del usuario
            $sql = "INSERT INTO tb_users (nombre_completo, correo_electronico, contrasena, telefono, pais)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nombre_completo, $correo_electronico, $hashed_password, $telefono, $pais);

            if ($stmt->execute()) {
                $userId = $stmt->insert_id; // Obtén el ID del usuario recién registrado

                // Generar API Key para el nuevo usuario
                $apiKeyData = registrarApiKey($userId, $conn);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Usuario registrado exitosamente',
                    'api_key' => $apiKeyData['api_key'],
                    'usos_restantes' => $apiKeyData['usos_restantes']
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error en el registro: ' . $conn->error]);
            }

            $stmt->close();
            $conn->close();

        } elseif ($action == 'login') {
            // Código para el inicio de sesión
            $correo_electronico = $_POST['correo_electronico'];
            $contrasena = $_POST['contrasena'];

            // Validar los campos
            if (empty($correo_electronico) || empty($contrasena)) {
                echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios']);
                exit();
            }

            // Verificar si el usuario existe
            $sql = "SELECT * FROM tb_users WHERE correo_electronico = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $correo_electronico);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($contrasena, $user['contrasena'])) {
                session_start();
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nombre_completo'] = $user['nombre_completo'];

                // Obtener API Key del usuario
                $apiKeyData = obtenerApiKey($user['id'], $conn);

                // Redirigir al dashboard con los datos
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Inicio de sesión exitoso',
                    'redirect' => '../Vistas_Usuario/demo.php',
                    'api_key' => $apiKeyData['api_key'],
                    'usos_restantes' => $apiKeyData['usos_restantes'],
                    'usuario_id' => $user['id']
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Correo o contraseña incorrectos']);
            }

            $stmt->close();
            $conn->close();
        }
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
