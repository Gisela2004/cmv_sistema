<?php
// public/comprar_procesar.php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$id_curso = intval($_POST['id_curso'] ?? 0);

if ($id_curso <= 0 || !isset($_FILES['comprobante']) || $_FILES['comprobante']['error'] !== 0) {
    header("Location: catalogo.php?error=Error al procesar el pago");
    exit();
}

// Crear carpeta de comprobantes si no existe
if (!is_dir('assets/comprobantes')) {
    mkdir('assets/comprobantes', 0777, true);
}

// Subir el archivo
$archivo = $_FILES['comprobante'];
$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
$nombre_comprobante = 'pago_' . $id_usuario . '_' . $id_curso . '_' . time() . '.' . $extension;
$ruta_destino = 'assets/comprobantes/' . $nombre_comprobante;

if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
    // Guardar en la base de datos
    try {
        $stmt = $pdo->prepare("INSERT INTO inscripciones (id_usuario, id_curso, estado, comprobante) VALUES (?, ?, 'pendiente', ?)");
        $stmt->execute([$id_usuario, $id_curso, $ruta_destino]);
        
        header("Location: dashboard.php?success=Comprobante recibido. Tu inscripción está en revisión.");
        exit();
    } catch (PDOException $e) {
        header("Location: catalogo.php?error=Error en la BD: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: catalogo.php?error=Error al subir el archivo");
    exit();
}
?>