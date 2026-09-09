<?php
// public/curso_agregar.php - Formulario para agregar curso
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Curso - CMV</title>
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
    <h2 style="color: #0B2D4F;"> Agregar Nuevo Curso</h2>
    <hr>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>
    <form action="curso_guardar.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label fw-semibold">Título <span class="required">*</span></label>
            <input type="text" class="form-control" name="titulo" required placeholder="Ej: Trabajos en Alturas">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="4" placeholder="Describe el contenido del curso"></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Duración</label>
                <input type="text" class="form-control" name="duracion" placeholder="Ej: 40 horas">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Precio (MXN)</label>
                <input type="number" class="form-control" name="precio" step="0.01" placeholder="0.00">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Imagen</label>
            <input type="file" class="form-control" name="imagen" accept="image/*">
            <small class="text-muted">Formatos: JPG, PNG, GIF (máx. 2MB)</small>
        </div>
        <button type="submit" class="btn btn-cmv">Guardar Curso</button>
        <a href="cursos.php" class="btn btn-cmv-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>