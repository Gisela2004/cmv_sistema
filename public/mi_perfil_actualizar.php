<?php
// public/mi_perfil_actualizar.php - Procesa los cambios del perfil
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$id_usuario = $_SESSION['id_usuario'];
$accion = $_POST['accion'] ?? '';

// ============================================
// ACCIÓN 1: Actualizar datos personales
// ============================================
if ($accion === 'datos') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validaciones
    if (empty($nombre) || empty($email)) {
        header("Location: mi_perfil.php?error=Todos los campos son obligatorios");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: mi_perfil.php?error=El correo no es válido");
        exit();
    }

    // Verificar que el correo no esté registrado por OTRO usuario
    $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario != ?");
    $stmt->execute([$email, $id_usuario]);
    if ($stmt->fetch()) {
        header("Location: mi_perfil.php?error=Ese correo ya está registrado por otro usuario");
        exit();
    }

    // Actualizar
    try {
        $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id_usuario = ?");
        $stmt->execute([$nombre, $email, $id_usuario]);

        // Actualizar la sesión con el nuevo nombre
        $_SESSION['nombre'] = $nombre;

        header("Location: mi_perfil.php?success=Datos actualizados correctamente");
        exit();
    } catch (PDOException $e) {
        header("Location: mi_perfil.php?error=Error al actualizar: " . $e->getMessage());
        exit();
    }
}

// ============================================
// ACCIÓN 2: Cambiar contraseña
// ============================================
if ($accion === 'password') {
    $password_actual = $_POST['password_actual'] ?? '';
    $password_nueva = $_POST['password_nueva'] ?? '';
    $password_confirmar = $_POST['password_confirmar'] ?? '';

    // Validaciones
    if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
        header("Location: mi_perfil.php?error=Todos los campos de contraseña son obligatorios");
        exit();
    }

    if (strlen($password_nueva) < 6) {
        header("Location: mi_perfil.php?error=La nueva contraseña debe tener al menos 6 caracteres");
        exit();
    }

    if ($password_nueva !== $password_confirmar) {
        header("Location: mi_perfil.php?error=Las contraseñas nuevas no coinciden");
        exit();
    }

    // Obtener la contraseña actual del usuario
    $stmt = $pdo->prepare("SELECT password FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        header("Location: mi_perfil.php?error=Usuario no encontrado");
        exit();
    }

    // Verificar contraseña actual
    // NOTA: Si en tu login usas password_verify() con hash, esto funciona.
    // Si usas texto plano (no recomendado), cambia la línea por: if ($password_actual !== $usuario['password'])
    if (!password_verify($password_actual, $usuario['password'])) {
        header("Location: mi_perfil.php?error=La contraseña actual es incorrecta");
        exit();
    }

    // Actualizar contraseña con hash seguro
    try {
        $nuevo_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?");
        $stmt->execute([$nuevo_hash, $id_usuario]);

        header("Location: mi_perfil.php?success=Contraseña cambiada correctamente");
        exit();
    } catch (PDOException $e) {
        header("Location: mi_perfil.php?error=Error al cambiar la contraseña");
        exit();
    }
}

// Si llega aquí sin acción válida
header("Location: mi_perfil.php");
exit();
?>