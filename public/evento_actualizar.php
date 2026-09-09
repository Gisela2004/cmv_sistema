<?php
// public/evento_actualizar.php - Actualizar evento en la BD
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

// Obtener datos del formulario
$id = intval($_POST['id'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$fecha_evento = $_POST['fecha_evento'] ?? '';
$hora = $_POST['hora'] ?? '';
$lugar = trim($_POST['lugar'] ?? '');

// Validar campos obligatorios
if ($id <= 0 || empty($titulo) || empty($fecha_evento)) {
    header("Location: evento_editar.php?id=$id&error=Título y fecha son obligatorios");
    exit();
}

// Obtener la imagen actual para mantenerla si no se sube una nueva
$stmt = $pdo->prepare("SELECT imagen FROM eventos WHERE id_evento = ?");
$stmt->execute([$id]);
$eventoActual = $stmt->fetch();
$nombreImagen = $eventoActual['imagen'] ?? '';

// Procesar nueva imagen si se sube
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombreImagen = uniqid('evento_') . '.' . $extension;
    $rutaDestino = '../public/assets/img/' . $nombreImagen;
    
    // Crear carpeta si no existe
    if (!is_dir('../public/assets/img')) {
        mkdir('../public/assets/img', 0777, true);
    }
    
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
        header("Location: evento_editar.php?id=$id&error=Error al subir la imagen");
        exit();
    }
}

// Actualizar en la base de datos
try {
    $stmt = $pdo->prepare("UPDATE eventos 
                           SET titulo = ?, descripcion = ?, fecha_evento = ?, hora = ?, lugar = ?, imagen = ? 
                           WHERE id_evento = ?");
    $stmt->execute([$titulo, $descripcion, $fecha_evento, $hora, $lugar, $nombreImagen, $id]);
    
    header("Location: eventos.php?success=Evento actualizado correctamente");
    exit();
} catch (PDOException $e) {
    header("Location: evento_editar.php?id=$id&error=Error al actualizar: " . $e->getMessage());
    exit();
}
?>