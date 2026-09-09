<?php
error_log("Archivo ejecutado: " . __FILE__ . " - Fecha: " . date('Y-m-d H:i:s'));
// public/login_procesar.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// RUTA CORRECTA (sin cmv_sistema)
require_once '../config/database.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: login.php?error=Correo y contraseña son obligatorios");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password'])) {
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['rol'] = $usuario['rol'];
        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: login.php?error=Correo o contraseña incorrectos");
        exit();
    }
} catch (PDOException $e) {
    header("Location: login.php?error=Error al procesar la solicitud");
    exit();
}
?>
