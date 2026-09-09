<?php
// public/evento_guardar.php - Guardar evento en la BD
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$fecha_evento = $_POST['fecha_evento'] ?? '';
$hora = $_POST['hora'] ?? '';
$lugar = trim($_POST['lugar'] ?? '');

if (empty($titulo) || empty($fecha_evento)) {
    header("Location: evento_agregar.php?error=Título y fecha son obligatorios");
    exit();
}

// Procesar imagen
$nombreImagen = '';
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombreImagen = uniqid('evento_') . '.' . $extension;
    $rutaDestino = '../public/assets/img/' . $nombreImagen;
    
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
        header("Location: evento_agregar.php?error=Error al subir la imagen");
        exit();
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO eventos (titulo, descripcion, fecha_evento, hora, lugar, imagen) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $descripcion, $fecha_evento, $hora, $lugar, $nombreImagen]);
    
    header("Location: eventos.php?success=Evento agregado correctamente");
    exit();
} catch (PDOException $e) {
    header("Location: evento_agregar.php?error=Error al guardar: " . $e->getMessage());
    exit();
}
?>