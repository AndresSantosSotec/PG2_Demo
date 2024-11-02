<?php
require('../pdf/fpdf186/fpdf.php'); // Asegurate de ajustar la ruta segun corresponda

// Iniciar la sesion y verificar autenticacion
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// Obtener el ID del usuario logueado
$usuario_id = $_SESSION['usuario_id'];

// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'migraciones_pg2');
if ($conexion->connect_error) {
    die("Conexion fallida: " . $conexion->connect_error);
}

$conexion->set_charset("utf8"); // Configurar UTF-8

// Consulta para obtener solo las migraciones con errores del usuario logueado
$sql = "
    SELECT 
        m.id AS migracion_id, 
        COALESCE(ac.nombre_archivo, 'Sin archivo') AS archivo_origen, 
        m.estado, 
        m.fecha_inicio, 
        m.fecha_fin, 
        TIMESTAMPDIFF(SECOND, m.fecha_inicio, m.fecha_fin) AS duracion
    FROM 
        migraciones m
    LEFT JOIN 
        archivos_cargados ac ON ac.id = m.archivo_origen
    WHERE 
        m.user_id = ? AND m.estado != 'Completada'
    ORDER BY 
        m.fecha_inicio DESC
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// Obtener estadisticas solo de migraciones con errores
$total_migraciones_errores = $result->num_rows;
$total_duracion_errores = 0;

while ($row = $result->fetch_assoc()) {
    $total_duracion_errores += $row['duracion'];
}

$duracion_promedio_errores = $total_migraciones_errores > 0 ? round($total_duracion_errores / $total_migraciones_errores, 2) : 0;

// Resetear el puntero de resultados
$result->data_seek(0);

// Verificar si el usuario solicito descargar el PDF
if (isset($_POST['descargar_pdf'])) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(190, 10, utf8_decode('Reporte de Migraciones con Errores'), 0, 1, 'C');
    $pdf->Ln(10);

    // Agregar estadisticas de errores
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 10, 'Estadisticas de Migraciones con Errores:', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(190, 8, 'Total de Migraciones con Errores: ' . $total_migraciones_errores, 0, 1, 'L');
    $pdf->Cell(190, 8, 'Duracion Promedio de Migracion con Error (s): ' . $duracion_promedio_errores, 0, 1, 'L');
    $pdf->Ln(10);

    // Subtitulo
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 10, 'Detalles de Migraciones con Errores', 0, 1, 'L');
    $pdf->Ln(5);

    // Encabezados de la tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(20, 10, 'ID', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Archivo Origen', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Estado', 1, 0, 'C', true);
    $pdf->Cell(35, 10, 'Fecha Inicio', 1, 0, 'C', true);
    $pdf->Cell(35, 10, 'Fecha Fin', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'Duracion (s)', 1, 1, 'C', true);

    // Rellenar los datos de la tabla
    while ($row = $result->fetch_assoc()) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(20, 10, $row['migracion_id'], 1);
        $pdf->Cell(40, 10, utf8_decode($row['archivo_origen']), 1);
        $pdf->Cell(30, 10, utf8_decode($row['estado']), 1);
        $pdf->Cell(35, 10, $row['fecha_inicio'], 1);
        $pdf->Cell(35, 10, $row['fecha_fin'], 1);
        $pdf->Cell(30, 10, $row['duracion'], 1, 1);

        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(190, 10, 'Detalles de Logs para la Migracion ID ' . $row['migracion_id'], 0, 1, 'L');
        $pdf->SetFont('Arial', '', 9);

        // Obtener los logs para cada migracion
        $log_sql = "SELECT mensaje, fecha FROM logs_migraciones WHERE migracion_id = ?";
        $log_stmt = $conexion->prepare($log_sql);
        $log_stmt->bind_param("i", $row['migracion_id']);
        $log_stmt->execute();
        $log_result = $log_stmt->get_result();

        if ($log_result->num_rows > 0) {
            while ($log_row = $log_result->fetch_assoc()) {
                $pdf->Cell(10); // Identacion para los logs
                $pdf->Cell(170, 7, "- " . $log_row['fecha'] . ": " . utf8_decode($log_row['mensaje']), 0, 1);
            }
        } else {
            $pdf->Cell(10);
            $pdf->Cell(170, 7, "No hay logs disponibles para esta migracion.", 0, 1);
        }
        $pdf->Ln(5);
    }

    // Salida del PDF para descarga
    $pdf->Output('D', 'Reporte_Migraciones_Errores.pdf');
    exit();
}
?>
