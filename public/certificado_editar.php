<?php
// public/certificado_editar.php - Editar certificado
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: certificados.php?error=ID inválido");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM certificados WHERE id_certificado = ?");
$stmt->execute([$id]);
$certificado = $stmt->fetch();

if (!$certificado) {
    header("Location: certificados.php?error=Certificado no encontrado");
    exit();
}

// Obtener usuarios y cursos para los selects
$usuarios = $pdo->query("SELECT id_usuario, nombre, email FROM usuarios ORDER BY nombre")->fetchAll();
$cursos = $pdo->query("SELECT id_curso, titulo FROM cursos ORDER BY titulo")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Certificado - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { max-width: 700px; margin: 50px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .btn-cmv { background-color: #0B2D4F; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; }
        .btn-cmv:hover { background-color: #1a4b7a; color: white; }
        .btn-cmv-secondary { background-color: #6c757d; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; }
        .btn-cmv-secondary:hover { background-color: #5a6268; color: white; }
    </style>
</head>
<body>
<div class="container">
    <h2 style="color: #0B2D4F;">✏️ Editar Certificado</h2>
    <hr>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>
    <form action="certificado_actualizar.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $certificado['id_certificado'] ?>">
        
        <div class="mb-3">
            <label class="form-label fw-semibold">Usuario</label>
            <select class="form-select" name="id_usuario" required>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['id_usuario'] ?>" <?= $u['id_usuario'] == $certificado['id_usuario'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['nombre']) ?> - <?= htmlspecialchars($u['email']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Curso</label>
            <select class="form-select" name="id_curso" required>
                <?php foreach ($cursos as $c): ?>
                    <option value="<?= $c['id_curso'] ?>" <?= $c['id_curso'] == $certificado['id_curso'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['titulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Tipo de Documento</label>
            <select class="form-select" name="tipo_documento" required>
                <option value="certificado" <?= $certificado['tipo_documento'] == 'certificado' ? 'selected' : '' ?>>Certificado</option>
                <option value="dc3" <?= $certificado['tipo_documento'] == 'dc3' ? 'selected' : '' ?>>DC-3</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Fecha de Emisión</label>
            <input type="date" class="form-control" name="fecha_emision" value="<?= $certificado['fecha_emision'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Nuevo PDF (opcional)</label>
            <input type="file" class="form-control" name="archivo_pdf" accept=".pdf">
            <small class="text-muted">Deja vacío para mantener el PDF actual.</small>
        </div>
        <button type="submit" class="btn btn-cmv">Actualizar</button>
        <a href="certificados.php" class="btn btn-cmv-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>