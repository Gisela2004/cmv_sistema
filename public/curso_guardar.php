<?php
// public/curso_guardar.php - Guardar curso en la BD
session_start();

if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'editor')) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$duracion_online = trim($_POST['duracion_online'] ?? '');
$duracion_presencial = trim($_POST['duracion_presencial'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);

if (empty($titulo)) {
    header("Location: curso_agregar.php?error=El título es obligatorio");
    exit();
}

$nombreImagen = '';
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombreImagen = uniqid('curso_') . '.' . $extension;
    $rutaDestino = '../public/assets/img/' . $nombreImagen;
    
    if (!is_dir('../public/assets/img')) {
        mkdir('../public/assets/img', 0777, true);
    }
    
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
        header("Location: curso_agregar.php?error=Error al subir la imagen");
        exit();
    }
}

try {
    // Insertamos las dos duraciones
    $stmt = $pdo->prepare("INSERT INTO cursos (titulo, descripcion, imagen, duracion_online, duracion_presencial, precio) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $descripcion, $nombreImagen, $duracion_online, $duracion_presencial, $precio]);
    
    header("Location: cursos.php?success=Curso agregado correctamente");
    exit();
} catch (PDOException $e) {
    header("Location: curso_agregar.php?error=Error al guardar: " . $e->getMessage());
    exit();
}
?>