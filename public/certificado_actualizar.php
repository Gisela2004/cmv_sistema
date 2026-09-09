<?php
// public/certificado_actualizar.php - Actualizar certificado
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$id = intval($_POST['id'] ?? 0);
$id_usuario = intval($_POST['id_usuario'] ?? 0);
$id_curso = intval($_POST['id_curso'] ?? 0);
$tipo_documento = $_POST['tipo_documento'] ?? 'certificado';
$fecha_emision = $_POST['fecha_emision'] ?? date('Y-m-d');

if ($id <= 0 || $id_usuario <= 0 || $id_curso <= 0) {
    header("Location: certificado_editar.php?id=$id&error=Datos inválidos");
    exit();
}

// Obtener archivo actual
$stmt = $pdo->prepare("SELECT archivo_pdf FROM certificados WHERE id_certificado = ?");
$stmt->execute([$id]);
$certActual = $stmt->fetch();
$archivo_pdf = $certActual['archivo_pdf'] ?? '';

// Procesar nuevo PDF si se sube
if (isset($_FILES['archivo_pdf']) && $_FILES['archivo_pdf']['error'] === 0) {
    $extension = strtolower(pathinfo($_FILES['archivo_pdf']['name'], PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        header("Location: certificado_editar.php?id=$id&error=Solo se permiten archivos PDF");
        exit();
    }
    $nombre_archivo = 'certificado_' . uniqid() . '.pdf';
    $ruta_destino = '../public/assets/certificados/' . $nombre_archivo;
    
    if (!is_dir('../public/assets/certificados')) {
        mkdir('../public/assets/certificados', 0777, true);
    }
    
    if (move_uploaded_file($_FILES['archivo_pdf']['tmp_name'], $ruta_destino)) {
        $archivo_pdf = 'assets/certificados/' . $nombre_archivo;
    }
}

try {
    $stmt = $pdo->prepare("UPDATE certificados 
                           SET id_usuario = ?, id_curso = ?, tipo_documento = ?, fecha_emision = ?, archivo_pdf = ? 
                           WHERE id_certificado = ?");
    $stmt->execute([$id_usuario, $id_curso, $tipo_documento, $fecha_emision, $archivo_pdf, $id]);
    
    header("Location: certificados.php?success=Certificado actualizado correctamente");
    exit();
} catch (PDOException $e) {
    header("Location: certificado_editar.php?id=$id&error=Error al actualizar: " . $e->getMessage());
    exit();
}
?>