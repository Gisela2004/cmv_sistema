<?php
// public/evento_editar.php - Formulario para editar evento
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

// Obtener datos del evento
$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id_evento = ?");
$stmt->execute([$id]);
$evento = $stmt->fetch();

if (!$evento) {
    header("Location: eventos.php?error=Evento no encontrado");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Evento - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { max-width: 700px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .btn-cmv { background-color: #0B2D4F; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; }
        .btn-cmv:hover { background-color: #1a4b7a; color: white; }
        .btn-cmv-secondary { background-color: #6c757d; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; }
        .btn-cmv-secondary:hover { background-color: #5a6268; color: white; }
        .required { color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2 style="color: #0B2D4F;">✏️ Editar Evento</h2>
    <hr>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>
    <form action="evento_actualizar.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $evento['id_evento'] ?>">
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Título <span class="required">*</span></label>
            <input type="text" class="form-control" name="titulo" value="<?= htmlspecialchars($evento['titulo']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="4"><?= htmlspecialchars($evento['descripcion']) ?></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Fecha <span class="required">*</span></label>
                <input type="date" class="form-control" name="fecha_evento" value="<?= $evento['fecha_evento'] ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Hora</label>
                <input type="time" class="form-control" name="hora" value="<?= htmlspecialchars($evento['hora']) ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Lugar</label>
            <input type="text" class="form-control" name="lugar" value="<?= htmlspecialchars($evento['lugar']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Imagen</label>
            <input type="file" class="form-control" name="imagen" accept="image/*">
            <?php if (!empty($evento['imagen'])): ?>
                <small class="text-muted">Imagen actual: <?= htmlspecialchars($evento['imagen']) ?></small>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-cmv">Actualizar Evento</button>
        <a href="eventos.php" class="btn btn-cmv-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>