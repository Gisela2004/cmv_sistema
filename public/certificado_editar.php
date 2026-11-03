<?php
// public/certificado_editar.php - Formulario para editar certificado
session_start();

if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'editor')) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Certificado - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .content { padding: 30px; }
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            max-width: 800px;
        }
        .btn-cmv {
            background-color: #0B2D4F;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-cmv:hover { background-color: #1a4b7a; color: white; }
        .btn-cmv-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-cmv-secondary:hover { background-color: #5a6268; color: white; }
        .form-control:focus, .form-select:focus {
            border-color: #0B2D4F;
            box-shadow: 0 0 0 0.2rem rgba(11, 45, 79, 0.25);
        }
        .required { color: red; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        
        <!-- AQUÍ ESTÁ EL SIDEBAR -->
        <?php include 'sidebar.php'; ?>

        <!-- CONTENIDO -->
        <div class="col-md-10 content">
            <div class="header">
                <h4 class="mb-0" style="color: #0B2D4F;">Editar Certificado</h4>
                <p class="text-muted mb-0">Modifica los datos del documento</p>
            </div>

            <div class="form-card">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>
                
                <form action="certificado_actualizar.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $certificado['id_certificado'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usuario <span class="required">*</span></label>
                        <select class="form-select" name="id_usuario" required>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id_usuario'] ?>" <?= $u['id_usuario'] == $certificado['id_usuario'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($u['nombre']) ?> - <?= htmlspecialchars($u['email']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Curso <span class="required">*</span></label>
                        <select class="form-select" name="id_curso" required>
                            <?php foreach ($cursos as $c): ?>
                                <option value="<?= $c['id_curso'] ?>" <?= $c['id_curso'] == $certificado['id_curso'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['titulo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de Documento <span class="required">*</span></label>
                        <select class="form-select" name="tipo_documento" required>
                            <option value="certificado" <?= $certificado['tipo_documento'] == 'certificado' ? 'selected' : '' ?>>Certificado</option>
                            <option value="dc3" <?= $certificado['tipo_documento'] == 'dc3' ? 'selected' : '' ?>>DC-3</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha de Emisión <span class="required">*</span></label>
                        <input type="date" class="form-control" name="fecha_emision" value="<?= $certificado['fecha_emision'] ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nuevo PDF (opcional)</label>
                        <input type="file" class="form-control" name="archivo_pdf" accept=".pdf">
                        <small class="text-muted">Deja vacío para mantener el PDF actual.</small>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-cmv"><i class="fas fa-save me-2"></i> Actualizar</button>
                        <a href="certificados.php" class="btn btn-cmv-secondary"><i class="fas fa-arrow-left me-2"></i> Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>