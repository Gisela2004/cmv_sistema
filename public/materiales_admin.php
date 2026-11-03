<?php
// public/materiales_admin.php - Subir materiales a los cursos
session_start();
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'editor')) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$mensaje = '';

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_curso = intval($_POST['id_curso']);
    $titulo = trim($_POST['titulo']);
    $tipo = $_POST['tipo'];
    $url = trim($_POST['url']);

    if ($id_curso > 0 && !empty($titulo) && !empty($url)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO curso_materiales (id_curso, titulo, tipo, url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$id_curso, $titulo, $tipo, $url]);
            $mensaje = '<div class="alert alert-success">¡Material agregado correctamente!</div>';
        } catch (PDOException $e) {
            $mensaje = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    } else {
        $mensaje = '<div class="alert alert-warning">Por favor, llena todos los campos.</div>';
    }
}

// Obtener todos los cursos para el select
$cursos = $pdo->query("SELECT id_curso, titulo FROM cursos ORDER BY titulo")->fetchAll();

// Obtener todos los materiales subidos
$materiales = $pdo->query("
    SELECT m.*, c.titulo AS curso_nombre 
    FROM curso_materiales m 
    JOIN cursos c ON m.id_curso = c.id_curso 
    ORDER BY m.id_curso DESC, m.id_material ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Materiales de Cursos - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .content { padding: 30px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .card-form { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .table-custom { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .table-custom th { background-color: #0B2D4F; color: white; padding: 15px; }
        .table-custom td { padding: 15px; vertical-align: middle; }
        .btn-cmv { background-color: #0B2D4F; color: white; font-weight: 600; padding: 10px 20px; border-radius: 8px; border: none; }
        .btn-cmv:hover { background-color: #1a4b7a; color: white; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 content">
            <div class="header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0" style="color: #0B2D4F;">Gestión de Materiales (Aula Virtual)</h4>
                    <p class="text-muted mb-0">Sube videos, PDFs y enlaces de Zoom a los cursos</p>
                </div>
            </div>

            <?= $mensaje ?>

            <div class="row">
                <!-- Formulario para agregar -->
                <div class="col-lg-5">
                    <div class="card-form">
                        <h5 style="color: #0B2D4F; font-weight: 700; margin-bottom: 20px;">Agregar Material</h5>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Curso:</label>
                                <select name="id_curso" class="form-select" required>
                                    <option value="">Selecciona un curso...</option>
                                    <?php foreach ($cursos as $c): ?>
                                        <option value="<?= $c['id_curso'] ?>"><?= htmlspecialchars($c['titulo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Título del material:</label>
                                <input type="text" name="titulo" class="form-control" placeholder="Ej: Video de introducción" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo de Material:</label>
                                <select name="tipo" class="form-select" id="tipoMaterial" required>
                                    <option value="video">Video (YouTube / Vimeo)</option>
                                    <option value="pdf">Documento PDF (Drive / URL)</option>
                                    <option value="enlace">Enlace de Videoconferencia (Zoom/Meet)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">URL del Material:</label>
                                <input type="url" name="url" class="form-control" placeholder="https://..." required>
                                <small class="text-muted" id="ayudaUrl">Pega el enlace completo de YouTube o Drive.</small>
                            </div>

                            <button type="submit" class="btn btn-cmv w-100"><i class="fas fa-cloud-upload-alt me-2"></i> Subir Material</button>
                        </form>
                    </div>
                </div>

                <!-- Tabla de materiales existentes -->
                <div class="col-lg-7">
                    <h5 style="color: #0B2D4F; font-weight: 700; margin-bottom: 20px;">Materiales Registrados</h5>
                    <div class="table-responsive table-custom">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <th>Material</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($materiales) > 0): ?>
                                    <?php foreach($materiales as $m): ?>
                                        <tr>
                                            <td style="font-size: 13px;"><?= htmlspecialchars($m['curso_nombre']) ?></td>
                                            <td><strong><?= htmlspecialchars($m['titulo']) ?></strong></td>
                                            <td>
                                                <?php if($m['tipo'] == 'video'): ?>
                                                    <span class="badge bg-danger">Video</span>
                                                <?php elseif($m['tipo'] == 'pdf'): ?>
                                                    <span class="badge bg-warning text-dark">PDF</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary">Enlace</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-muted py-4">No hay materiales subidos aún.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>