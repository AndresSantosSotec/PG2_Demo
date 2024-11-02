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

// Consulta para obtener los datos de migraciones del usuario logueado
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
        m.user_id = ?
    ORDER BY 
        m.fecha_inicio DESC
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

// Obtener estadisticas
$total_migraciones = $result->num_rows;
$total_duracion = 0;
$migraciones_exitosas = 0;
$migraciones_errores = 0;

while ($row = $result->fetch_assoc()) {
    $total_duracion += $row['duracion'];
    if ($row['estado'] === 'Completada') {
        $migraciones_exitosas++;
    } else {
        $migraciones_errores++;
    }
}

$duracion_promedio = $total_migraciones > 0 ? round($total_duracion / $total_migraciones, 2) : 0;
$porcentaje_exitosas = $total_migraciones > 0 ? round(($migraciones_exitosas / $total_migraciones) * 100, 2) : 0;
$porcentaje_errores = 100 - $porcentaje_exitosas;

// Resetear el puntero de resultados
$result->data_seek(0);

// Verificar si el usuario solicito descargar el PDF
if (isset($_POST['descargar_pdf'])) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(190, 10, utf8_decode('Reporte Detallado de Migraciones por Usuario'), 0, 1, 'C');
    $pdf->Ln(10);

    // Agregar estadisticas
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 10, 'Estadisticas Generales:', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(190, 8, 'Total de Migraciones: ' . $total_migraciones, 0, 1, 'L');
    $pdf->Cell(190, 8, 'Duracion Promedio de Migracion (s): ' . $duracion_promedio, 0, 1, 'L');
    $pdf->Cell(190, 8, 'Migraciones Exitosas: ' . $migraciones_exitosas . ' (' . $porcentaje_exitosas . '%)', 0, 1, 'L');
    $pdf->Cell(190, 8, 'Migraciones con Errores: ' . $migraciones_errores . ' (' . $porcentaje_errores . '%)', 0, 1, 'L');
    $pdf->Ln(10);

    // Subtitulo
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 10, 'Resumen de Migraciones', 0, 1, 'L');
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
    $pdf->Output('D', 'Reporte_Detallado_Migraciones.pdf');
    exit();
}
?>
