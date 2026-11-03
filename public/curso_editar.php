<?php
// public/curso_editar.php
session_start();

if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'editor')) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: cursos.php?error=ID inválido");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM cursos WHERE id_curso = ?");
$stmt->execute([$id]);
$curso = $stmt->fetch();

if (!$curso) {
    header("Location: cursos.php?error=Curso no encontrado");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Curso - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .content { padding: 30px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 800px; }
        .btn-cmv { background-color: #0B2D4F; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; }
        .btn-cmv:hover { background-color: #1a4b7a; color: white; }
        .btn-cmv-secondary { background-color: #6c757d; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; }
        .imagen-actual { max-width: 150px; border-radius: 8px; border: 2px solid #eee; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 content">
            <div class="header">
                <h4 class="mb-0" style="color: #0B2D4F;">✏️ Editar Curso</h4>
                <p class="text-muted mb-0">Modifica los datos del curso</p>
            </div>

            <div class="form-card">
                <form action="curso_actualizar.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $curso['id_curso'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título *</label>
                        <input type="text" class="form-control" name="titulo" value="<?= htmlspecialchars($curso['titulo']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="4"><?= htmlspecialchars($curso['descripcion']) ?></textarea>
                    </div>
                    
                    <!-- AQUÍ ESTÁN LOS DOS CAMPOS DE DURACIÓN -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Duración en Línea</label>
                            <input type="text" class="form-control" name="duracion_online" value="<?= htmlspecialchars($curso['duracion_online'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Duración Presencial</label>
                            <input type="text" class="form-control" name="duracion_presencial" value="<?= htmlspecialchars($curso['duracion_presencial'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Precio (MXN)</label>
                        <input type="number" class="form-control" name="precio" step="0.01" value="<?= $curso['precio'] ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Imagen actual</label><br>
                        <?php if (!empty($curso['imagen']) && file_exists('assets/img/' . $curso['imagen'])): ?>
                            <img src="assets/img/<?= $curso['imagen'] ?>" class="imagen-actual mb-2"><br>
                        <?php else: ?>
                            <span class="text-muted">Sin imagen</span>
                        <?php endif; ?>
                        <input type="file" class="form-control mt-2" name="imagen" accept="image/*">
                        <small class="text-muted">Deja vacío para mantener la imagen actual.</small>
                    </div>
                    <button type="submit" class="btn btn-cmv">Actualizar Curso</button>
                    <a href="cursos.php" class="btn btn-cmv-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>