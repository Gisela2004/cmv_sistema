<?php
// public/usuario_actualizar.php - Actualizar usuario
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$id = intval($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$rol = $_POST['rol'] ?? 'editor';

if ($id <= 0 || empty($nombre) || empty($email)) {
    header("Location: usuario_editar.php?id=$id&error=Todos los campos son obligatorios");
    exit();
}

// Verificar que el email no esté en otro usuario
$stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario != ?");
$stmt->execute([$email, $id]);
if ($stmt->fetch()) {
    header("Location: usuario_editar.php?id=$id&error=El correo electrónico ya está registrado");
    exit();
}

// Construir consulta de actualización
if (!empty($password)) {
    // Si se proporcionó nueva contraseña, actualizar todo
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ?, rol = ? WHERE id_usuario = ?");
    $stmt->execute([$nombre, $email, $passwordHash, $rol, $id]);
} else {
    // Si no se cambia la contraseña
    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id_usuario = ?");
    $stmt->execute([$nombre, $email, $rol, $id]);
}

header("Location: usuarios.php?success=Usuario actualizado correctamente");
exit();
?>