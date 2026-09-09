<?php
// public/evento_eliminar.php - Eliminar evento
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: eventos.php?error=ID de evento inválido");
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM eventos WHERE id_evento = ?");
    $stmt->execute([$id]);
    
    header("Location: eventos.php?success=Evento eliminado correctamente");
    exit();
} catch (PDOException $e) {
    header("Location: eventos.php?error=Error al eliminar: " . $e->getMessage());
    exit();
}
?>