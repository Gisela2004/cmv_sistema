<?php
// public/curso_actualizar.php - Actualizar curso en la BD
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$id = intval($_POST['id'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$duracion = trim($_POST['duracion'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);

if ($id <= 0 || empty($titulo)) {
    header("Location: curso_editar.php?id=$id&error=Título es obligatorio");
    exit();
}

// Obtener imagen actual
$stmt = $pdo->prepare("SELECT imagen FROM cursos WHERE id_curso = ?");
$stmt->execute([$id]);
$cursoActual = $stmt->fetch();
$nombreImagen = $cursoActual['imagen'] ?? '';

// Procesar nueva imagen si se sube
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombreImagen = uniqid('curso_') . '.' . $extension;
    $rutaDestino = '../public/assets/img/' . $nombreImagen;
    
    if (!is_dir('../public/assets/img')) {
        mkdir('../public/assets/img', 0777, true);
    }
    
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
        header("Location: curso_editar.php?id=$id&error=Error al subir la imagen");
        exit();
    }
}

try {
    $stmt = $pdo->prepare("UPDATE cursos 
                           SET titulo = ?, descripcion = ?, imagen = ?, duracion = ?, precio = ? 
                           WHERE id_curso = ?");
    $stmt->execute([$titulo, $descripcion, $nombreImagen, $duracion, $precio, $id]);
    
    header("Location: cursos.php?success=Curso actualizado correctamente");
    exit();
} catch (PDOException $e) {
    header("Location: curso_editar.php?id=$id&error=Error al actualizar: " . $e->getMessage());
    exit();
}
?>