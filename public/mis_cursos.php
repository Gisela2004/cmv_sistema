<?php
// public/mis_cursos.php - Área del Alumno (Estado de pagos y Acceso a cursos)
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';
$id_usuario = $_SESSION['id_usuario'];

// Consultar las inscripciones del usuario logueado
$stmt = $pdo->prepare("
    SELECT i.*, c.titulo, c.descripcion, c.imagen, c.duracion_online 
    FROM inscripciones i
    JOIN cursos c ON i.id_curso = c.id_curso
    WHERE i.id_usuario = ?
    ORDER BY i.fecha_inscripcion DESC
");
$stmt->execute([$id_usuario]);
$mis_cursos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Cursos - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .content { padding: 30px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        
        /* Tarjetas de mis cursos */
        .card-mi-curso { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s; height: 100%; border: none; }
        .card-mi-curso:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(11,45,79,0.15); }
        .card-mi-curso img { width: 100%; height: 160px; object-fit: cover; }
        .card-body { padding: 20px; display: flex; flex-direction: column; height: calc(100% - 160px); }
        .card-title { color: #0B2D4F; font-weight: 700; font-size: 16px; margin-bottom: 10px; }
        .card-text { color: #666; font-size: 13px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 15px; flex-grow: 1; }

        /* Botones de estado */
        .btn-aula { background-color: #28a745; color: white; font-weight: 600; width: 100%; border-radius: 8px; padding: 10px; text-decoration: none; text-align: center; display: block; transition: 0.3s; }
        .btn-aula:hover { background-color: #218838; color: white; }
        
        .btn-pendiente { background-color: #ffc107; color: #333; font-weight: 600; width: 100%; border-radius: 8px; padding: 10px; text-align: center; display: block; cursor: not-allowed; }
        
        .btn-rechazado { background-color: #f8d7da; color: #721c24; font-weight: 600; width: 100%; border-radius: 8px; padding: 10px; text-align: center; display: block; cursor: not-allowed; }
        
        .estado-badge { font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 700; position: absolute; top: 10px; right: 10px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 content">
            <div class="header">
                <h4 class="mb-0" style="color: #0B2D4F;"> Mis Cursos</h4>
                <p class="text-muted mb-0">Consulta el estado de tus inscripciones y accede a tu contenido</p>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_GET['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <?php if (count($mis_cursos) > 0): ?>
                    <?php foreach ($mis_cursos as $curso): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card-mi-curso position-relative">
                                
                                <!-- Etiqueta de estado (Aprobado/Pendiente/Rechazado) -->
                                <?php if ($curso['estado'] == 'aprobado'): ?>
                                    <span class="estado-badge" style="background:#28a745; color:white;">Aprobado</span>
                                <?php elseif ($curso['estado'] == 'pendiente'): ?>
                                    <span class="estado-badge" style="background:#ffc107; color:#333;">En Revisión</span>
                                <?php else: ?>
                                    <span class="estado-badge" style="background:#dc3545; color:white;">Rechazado</span>
                                <?php endif; ?>

                                <?php if (!empty($curso['imagen']) && file_exists('assets/img/' . $curso['imagen'])): ?>
                                    <img src="assets/img/<?= htmlspecialchars($curso['imagen']) ?>" alt="Curso">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/400x200?text=Curso+CMV" alt="Curso">
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($curso['titulo']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($curso['descripcion']) ?></p>
                                    
                                    <!-- Lógica de Botones según el estado -->
                                    <?php if ($curso['estado'] == 'aprobado'): ?>
                                        <a href="aula_curso.php?id=<?= $curso['id_curso'] ?>" class="btn-aula mt-auto">
                                            <i class="fas fa-play-circle me-1"></i> Entrar al Aula
                                        </a>
                                    
                                    <?php elseif ($curso['estado'] == 'pendiente'): ?>
                                        <div class="btn-pendiente mt-auto">
                                            <i class="fas fa-clock me-1"></i> Pago en Revisión
                                        </div>
                                        <small class="text-muted text-center d-block mt-2" style="font-size:11px;">El administrador validará tu comprobante pronto.</small>
                                    
                                    <?php else: ?>
                                        <div class="btn-rechazado mt-auto">
                                            <i class="fas fa-times-circle me-1"></i> Pago Rechazado
                                        </div>
                                        <small class="text-muted text-center d-block mt-2" style="font-size:11px;">Contacta a soporte o vuelve a subir tu comprobante.</small>
                                    <?php endif; ?>
                                    
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Si el usuario aún no ha comprado nada -->
                    <div class="col-12 text-center py-5 bg-white rounded shadow-sm">
                        <i class="fas fa-book-open text-muted mb-3" style="font-size: 50px; opacity: 0.5;"></i>
                        <h5 style="color: #0B2D4F; font-weight: 700;">Aún no te has inscrito a ningún curso</h5>
                        <p class="text-muted mb-4">Explora nuestro catálogo y encuentra la capacitación que necesitas.</p>
                        <a href="catalogo.php" class="btn" style="background-color: #0B2D4F; color: white; padding: 10px 25px; border-radius: 50px; font-weight: 600;">
                            Ver Catálogo de Cursos
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>