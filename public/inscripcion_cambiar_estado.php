<?php
// public/inscripcion_cambiar_estado.php
// --- ACTIVAR ERRORES PARA DEPURACIÓN ---
error_reporting(E_ALL);
ini_set('display_errors', 1);
// ---------------------------------------

session_start();

// Verificar que sea admin o editor
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'editor')) {
    header("Location: login.php");
    exit();
}

// Conectar a la base de datos (asegúrate que la ruta sea esta)
require_once '../config/database.php';

$id = intval($_GET['id'] ?? 0);
$estado = $_GET['estado'] ?? '';

// Validar que el estado sea correcto
if ($id > 0 && in_array($estado, ['aprobado', 'rechazado'])) {
    try {
        // Consulta para actualizar el estado
        $stmt = $pdo->prepare("UPDATE inscripciones SET estado = ? WHERE id_inscripcion = ?");
        $stmt->execute([$estado, $id]);
        
        $mensaje = ($estado == 'aprobado') ? '¡Pago aprobado y curso desbloqueado!' : 'Comprobante rechazado.';
        
        // Redirigir de vuelta a la tabla
        header("Location: inscripciones_admin.php?success=" . urlencode($mensaje));
        exit();
        
    } catch (PDOException $e) {
        // Si hay un error en la base de datos, lo mostramos
        die("Error en la base de datos: " . $e->getMessage());
    }
} else {
    // Si los datos no son válidos
    header("Location: inscripciones_admin.php?error=Datos inválidos");
    exit();
}
?>