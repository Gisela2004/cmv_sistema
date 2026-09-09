<?php
// public/certificado_guardar.php - Guardar certificado en la BD
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

// Obtener datos del formulario
$id_usuario = intval($_POST['id_usuario'] ?? 0);
$id_curso = intval($_POST['id_curso'] ?? 0);
$tipo_documento = $_POST['tipo_documento'] ?? 'certificado';
$fecha_emision = $_POST['fecha_emision'] ?? date('Y-m-d');

// Validar campos obligatorios
if ($id_usuario <= 0 || $id_curso <= 0) {
    header("Location: certificado_agregar.php?error=Selecciona un usuario y un curso");
    exit();
}

// Procesar el archivo PDF
if (!isset($_FILES['archivo_pdf']) || $_FILES['archivo_pdf']['error'] !== 0) {
    header("Location: certificado_agregar.php?error=Selecciona un archivo PDF");
    exit();
}

$archivo = $_FILES['archivo_pdf'];
$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if ($extension !== 'pdf') {
    header("Location: certificado_agregar.php?error=Solo se permiten archivos PDF");
    exit();
}

// Generar folio único
$folio = 'CMV-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

// Verificar que el folio no exista
$stmt = $pdo->prepare("SELECT COUNT(*) FROM certificados WHERE folio = ?");
$stmt->execute([$folio]);
if ($stmt->fetchColumn() > 0) {
    // Si el folio ya existe, regenerar
    $folio = 'CMV-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

// Crear nombre único para el archivo
$nombre_archivo = 'certificado_' . $folio . '.pdf';
$ruta_destino = '../public/assets/certificados/' . $nombre_archivo;

// Crear la carpeta si no existe
if (!is_dir('../public/assets/certificados')) {
    mkdir('../public/assets/certificados', 0777, true);
}

// Mover el archivo
if (!move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
    header("Location: certificado_agregar.php?error=Error al subir el archivo");
    exit();
}

// Guardar en la base de datos
try {
    $stmt = $pdo->prepare("INSERT INTO certificados (id_usuario, id_curso, tipo_documento, folio, archivo_pdf, fecha_emision) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_usuario, $id_curso, $tipo_documento, $folio, 'assets/certificados/' . $nombre_archivo, $fecha_emision]);
    
    header("Location: certificados.php?success=Certificado agregado correctamente. Folio: " . $folio);
    exit();
} catch (PDOException $e) {
    header("Location: certificado_agregar.php?error=Error al guardar: " . $e->getMessage());
    exit();
}
?>