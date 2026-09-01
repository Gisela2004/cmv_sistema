<?php
// public/usuario_eliminar.php - Eliminar usuario
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: usuarios.php?error=ID de usuario inválido");
    exit();
}

// Verificar que no sea el propio usuario logueado
if ($id == $_SESSION['id_usuario']) {
    header("Location: usuarios.php?error=No puedes eliminar tu propio usuario");
    exit();
}

// Eliminar de la BD
try {
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    header("Location: usuarios.php?success=Usuario eliminado correctamente");
    exit();
} catch (PDOException $e) {
    header("Location: usuarios.php?error=Error al eliminar el usuario");
    exit();
}
?>