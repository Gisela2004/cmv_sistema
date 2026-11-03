<?php
// public/registro_procesar.php - Procesar registro público
session_start();
require_once '../config/database.php';

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirmar = trim($_POST['confirmar'] ?? '');

// Validaciones
if (empty($nombre) || empty($email) || empty($password) || empty($confirmar)) {
    header("Location: registro.php?error=Todos los campos son obligatorios");
    exit();
}

if ($password !== $confirmar) {
    header("Location: registro.php?error=Las contraseñas no coinciden");
    exit();
}

if (strlen($password) < 6) {
    header("Location: registro.php?error=La contraseña debe tener al menos 6 caracteres");
    exit();
}

// Verificar si el email ya existe
$stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    header("Location: registro.php?error=El correo electrónico ya está registrado");
    exit();
}

// Hashear contraseña
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// Insertar usuario con rol 'usuario'
try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'usuario')");
    $stmt->execute([$nombre, $email, $passwordHash]);
    
    header("Location: login.php?success=Registro exitoso. Ahora puedes iniciar sesión");
    exit();
} catch (PDOException $e) {
    header("Location: registro.php?error=Error al registrar: " . $e->getMessage());
    exit();
}
?>