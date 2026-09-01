<?php
// public/usuario_guardar.php - Guardar usuario en la BD
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$rol = $_POST['rol'] ?? 'editor';

// Validar campos obligatorios
if (empty($nombre) || empty($email) || empty($password)) {
    header("Location: usuario_agregar.php?error=Todos los campos son obligatorios");
    exit();
}

// Validar longitud de la contraseña
if (strlen($password) < 6) {
    header("Location: usuario_agregar.php?error=La contraseña debe tener al menos 6 caracteres");
    exit();
}

// Verificar si el email ya existe
$stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    header("Location: usuario_agregar.php?error=El correo electrónico ya está registrado");
    exit();
}

// Hashear contraseña y guardar
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $email, $passwordHash, $rol]);
    
    header("Location: usuarios.php?success=Usuario creado correctamente");
    exit();
} catch (PDOException $e) {
    header("Location: usuario_agregar.php?error=Error al guardar el usuario");
    exit();
}
?>