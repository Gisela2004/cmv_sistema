<?php
// public/curso_guardar.php - Guardar curso en la BD
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

// Obtener datos del formulario
$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$duracion = trim($_POST['duracion'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);

// Validar título
if (empty($titulo)) {
    header("Location: curso_agregar.php?error=El título es obligatorio");
    exit();
}

// Procesar imagen
$nombreImagen = '';
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $nombreImagen = uniqid('curso_') . '.' . $extension;
    $rutaDestino = '../public/assets/img/' . $nombreImagen;
    
    // Crear carpeta si no existe
    if (!is_dir('../public/assets/img')) {
        mkdir('../public/assets/img', 0777, true);
    }
    
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
        header("Location: curso_agregar.php?error=Error al subir la imagen");
        exit();
    }
}

// Guardar en la base de datos
try {
    $stmt = $pdo->prepare("INSERT INTO cursos (titulo, descripcion, imagen, duracion, precio) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $descripcion, $nombreImagen, $duracion, $precio]);
    
    header("Location: cursos.php?success=Curso agregado correctamente");
    exit();
} catch (PDOException $e) {
    header("Location: curso_agregar.php?error=Error al guardar: " . $e->getMessage());
    exit();
}
?>