<?php
// public/curso_eliminar.php - Eliminar curso
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: cursos.php?error=ID de curso inválido");
    exit();
}

// Verificar si el curso existe y obtener la imagen para eliminarla
$stmt = $pdo->prepare("SELECT imagen FROM cursos WHERE id_curso = ?");
$stmt->execute([$id]);
$curso = $stmt->fetch();

if (!$curso) {
    header("Location: cursos.php?error=Curso no encontrado");
    exit();
}

// Eliminar la imagen física si existe
if (!empty($curso['imagen'])) {
    $rutaImagen = '../public/assets/img/' . $curso['imagen'];
    if (file_exists($rutaImagen)) {
        unlink($rutaImagen);
    }
}

// Eliminar de la base de datos
try {
    $stmt = $pdo->prepare("DELETE FROM cursos WHERE id_curso = ?");
    $stmt->execute([$id]);
    
    header("Location: cursos.php?success=Curso eliminado correctamente");
    exit();
} catch (PDOException $e) {
    header("Location: cursos.php?error=Error al eliminar: " . $e->getMessage());
    exit();
}
?>