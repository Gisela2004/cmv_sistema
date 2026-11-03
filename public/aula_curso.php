<?php
// public/aula_curso.php - Aula Virtual para el Alumno
session_start();
require_once '../config/database.php';

// 1. Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$id_curso = intval($_GET['id'] ?? 0);

if ($id_curso <= 0) {
    header("Location: mis_cursos.php?error=Curso no válido");
    exit();
}

// 2. Verificar que el alumno tenga el pago APROBADO
$stmt = $pdo->prepare("SELECT * FROM inscripciones WHERE id_usuario = ? AND id_curso = ? AND estado = 'aprobado'");
$stmt->execute([$id_usuario, $id_curso]);
$acceso = $stmt->fetch();

if (!$acceso) {
    header("Location: mis_cursos.php?error=No tienes acceso a este curso o tu pago aún no ha sido aprobado.");
    exit();
}

// 3. Obtener los datos del curso
$stmt = $pdo->prepare("SELECT * FROM cursos WHERE id_curso = ?");
$stmt->execute([$id_curso]);
$curso = $stmt->fetch();

// 4. Obtener los materiales del curso
$stmt = $pdo->prepare("SELECT * FROM curso_materiales WHERE id_curso = ? ORDER BY id_material ASC");
$stmt->execute([$id_curso]);
$materiales = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Aula Virtual - <?= htmlspecialchars($curso['titulo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .content { padding: 30px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;}
        
        .video-container { background: #000; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 20px; position: relative; padding-bottom: 56.25%; /* Relación de aspecto 16:9 */ height: 0; }
        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }
        
        .material-item { background: white; border-radius: 10px; padding: 15px 20px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.03); transition: 0.2s; border-left: 4px solid #0B2D4F; }
        .material-item:hover { transform: translateX(5px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
        .material-title { font-weight: 600; color: #333; margin: 0; font-size: 15px; }
        .btn-material { padding: 6px 15px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; }
        .btn-video { background-color: #dc3545; color: white; }
        .btn-pdf { background-color: #fd7e14; color: white; }
        .btn-enlace { background-color: #0d6efd; color: white; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 content">
            <!-- Encabezado del Aula -->
            <div class="header">
                <div>
                    <h4 class="mb-0" style="color: #0B2D4F;"> <?= htmlspecialchars($curso['titulo']) ?></h4>
                    <p class="text-muted mb-0">Estás en el Aula Virtual. ¡Mucho éxito en tu capacitación!</p>
                </div>
                <a href="mis_cursos.php" class="btn btn-outline-secondary" style="border-radius: 50px;"><i class="fas fa-arrow-left me-1"></i> Volver a Mis Cursos</a>
            </div>

            <div class="row">
                <!-- Columna Izquierda: Reproductor de Video Principal (Opcional) -->
                <div class="col-lg-8">
                    <h5 style="color: #0B2D4F; font-weight: 700; margin-bottom: 15px;">Contenido del Curso</h5>
                    
                    <?php if (count($materiales) > 0): ?>
                        <!-- Tomamos el primer material si es un video para mostrarlo grande -->
                        <?php 
                        $primer_material = $materiales[0];
                        if ($primer_material['tipo'] == 'video'): 
                            // Extraer el ID de YouTube para el iframe si es un enlace de YouTube
                            $video_url = $primer_material['url'];
                            $video_id = '';
                            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match)) {
                                $video_id = $match[1];
                            }
                        ?>
                            <?php if($video_id): ?>
                                <div class="video-container">
                                    <iframe src="https://www.youtube.com/embed/<?= $video_id ?>?rel=0" allowfullscreen></iframe>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">El primer video está en un formato externo. Por favor usa el enlace de la lista de abajo.</div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <!-- Lista de todos los materiales -->
                        <div class="mt-4">
                            <h6 style="color: #0B2D4F; font-weight: 600;">Materiales y Recursos:</h6>
                            <?php foreach ($materiales as $index => $mat): ?>
                                <div class="material-item">
                                    <div>
                                        <?php if ($mat['tipo'] == 'video'): ?>
                                            <i class="fas fa-play-circle text-danger me-2"></i>
                                        <?php elseif ($mat['tipo'] == 'pdf'): ?>
                                            <i class="fas fa-file-pdf text-warning me-2"></i>
                                        <?php else: ?>
                                            <i class="fas fa-link text-primary me-2"></i>
                                        <?php endif; ?>
                                        <span class="material-title"><?= htmlspecialchars($mat['titulo']) ?></span>
                                    </div>
                                    
                                    <?php if ($mat['tipo'] == 'video' && $video_id && $index === 0): ?>
                                        <span class="text-muted" style="font-size:12px;">Reproduciendo arriba</span>
                                    <?php elseif ($mat['tipo'] == 'pdf'): ?>
                                        <a href="<?= htmlspecialchars($mat['url']) ?>" target="_blank" class="btn-material btn-pdf"><i class="fas fa-download me-1"></i> Descargar</a>
                                    <?php else: ?>
                                        <a href="<?= htmlspecialchars($mat['url']) ?>" target="_blank" class="btn-material <?= ($mat['tipo'] == 'video') ? 'btn-video' : 'btn-enlace' ?>">
                                            <i class="fas fa-external-link-alt me-1"></i> Abrir
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning py-4 text-center border-0 shadow-sm" style="border-radius: 12px;">
                            <i class="fas fa-info-circle mb-2" style="font-size: 30px;"></i>
                            <h5 class="fw-bold">El instructor aún no ha subido materiales a este curso.</h5>
                            <p class="mb-0">Por favor, vuelve a revisar más tarde.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Columna Derecha: Información del Curso -->
                <div class="col-lg-4">
                    <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <?php if (!empty($curso['imagen']) && file_exists('assets/img/' . $curso['imagen'])): ?>
                            <img src="assets/img/<?= htmlspecialchars($curso['imagen']) ?>" style="width: 100%; border-radius: 8px; margin-bottom: 15px;" alt="Curso">
                        <?php endif; ?>
                        <h5 style="color: #0B2D4F; font-weight: 700; margin-bottom: 10px;">Sobre este curso</h5>
                        <p class="text-muted" style="font-size: 14px; text-align: justify;"><?= htmlspecialchars($curso['descripcion']) ?></p>
                        
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted" style="font-size: 13px;"><i class="fas fa-clock me-1"></i> Duración:</span>
                            <span class="fw-bold" style="font-size: 13px; color: #0B2D4F;"><?= htmlspecialchars($curso['duracion_online'] ?? 'No definida') ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted" style="font-size: 13px;"><i class="fas fa-medal me-1"></i> Certificación:</span>
                            <span class="fw-bold text-success" style="font-size: 13px;">STPS (DC-3)</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>