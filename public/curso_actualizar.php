<?php
// public/curso_actualizar.php
session_start();

if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'editor')) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$id = intval($_POST['id'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$duracion_online = trim($_POST['duracion_online'] ?? '');
$duracion_presencial = trim($_POST['duracion_presencial'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);

if ($id <= 0 || empty($titulo)) {
    header("Location: curso_editar.php?id=$id&error=Título es obligatorio");
    exit();
}

$stmt = $pdo->prepare("SELECT imagen FROM cursos WHERE id_curso = ?");
$stmt->execute([$id]);
$cursoActual = $stmt->fetch();
$nombreImagen = $cursoActual['imagen'] ?? '';

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombreImagen = uniqid('curso_') . '.' . $extension;
    $rutaDestino = '../public/assets/img/' . $nombreImagen;
    
    if (!is_dir('../public/assets/img')) {
        mkdir('../public/assets/img', 0777, true);
    }
    
    move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
}

try {
    // Actualizamos las dos duraciones
    $stmt = $pdo->prepare("UPDATE cursos SET titulo = ?, descripcion = ?, imagen = ?, duracion_online = ?, duracion_presencial = ?, precio = ? WHERE id_curso = ?");
    $stmt->execute([$titulo, $descripcion, $nombreImagen, $duracion_online, $duracion_presencial, $precio, $id]);
    
    header("Location: cursos.php?success=Curso actualizado correctamente");
    exit();
} catch (PDOException $e) {
    header("Location: curso_editar.php?id=$id&error=Error: " . $e->getMessage());
    exit();
}
?>