<?php
// public/cursos.php - Catálogo o Gestión de Cursos según el rol
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$stmt = $pdo->query("SELECT * FROM cursos ORDER BY id_curso DESC");
$cursos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cursos - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .content { padding: 30px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        
        /* Estilos Tabla Admin */
        .btn-cmv { background-color: #0B2D4F; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; }
        .btn-cmv:hover { background-color: #1a4b7a; color: white; }
        .btn-cmv-editar { background-color: #ffc107; color: #333; border: none; padding: 5px 12px; border-radius: 6px; }
        .btn-cmv-eliminar { background-color: #dc3545; color: white; border: none; padding: 5px 12px; border-radius: 6px; }
        .table-cursos { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .table-cursos th { background-color: #0B2D4F; color: white; padding: 15px; }
        .table-cursos td { padding: 15px; vertical-align: middle; }
        
        /* Estilos Catálogo Visual */
        .card-curso { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: 0.3s; height: 100%; border: none; }
        .card-curso:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(11,45,79,0.12); }
        .card-curso img { width: 100%; height: 180px; object-fit: cover; }
        .card-curso .card-body { padding: 20px; display: flex; flex-direction: column; height: calc(100% - 180px); }
        .card-curso .card-title { color: #0B2D4F; font-weight: 700; font-size: 18px; }
        .card-curso .card-text { color: #666; font-size: 14px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; flex-grow: 1; }
        .badge-precio { background-color: #e8eef5; color: #0B2D4F; font-weight: bold; padding: 5px 12px; border-radius: 8px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 content">
            
            <!-- ========================================== -->
            <!-- VISTA PARA ADMIN Y EDITOR (TABLA)          -->
            <!-- ========================================== -->
            <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'editor'): ?>
                
                <div class="header">
                    <div>
                        <h4 class="mb-0" style="color: #0B2D4F;">Gestión de Cursos</h4>
                        <p class="text-muted mb-0">Administra los cursos de capacitación de CMV</p>
                    </div>
                    <a href="curso_agregar.php" class="btn btn-cmv"><i class="fas fa-plus"></i> Nuevo Curso</a>
                </div>

                <div class="table-responsive table-cursos">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Duraciones</th>
                                <th>Precio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cursos as $curso): ?>
                                <tr>
                                    <td><span class="badge bg-primary">#<?= $curso['id_curso'] ?></span></td>
                                    <td><strong><?= htmlspecialchars($curso['titulo']) ?></strong></td>
                                    
                                    <!-- AQUÍ MOSTRAMOS LAS DOS DURACIONES EN LA TABLA -->
                                    <td>
                                        <div style="font-size: 13px;">
                                            <div class="mb-1"><i class="fas fa-laptop text-primary me-1"></i> <strong>Online:</strong> <?= htmlspecialchars($curso['duracion_online'] ?? 'No asignada') ?></div>
                                            <div><i class="fas fa-chalkboard-teacher text-success me-1"></i> <strong>Presencial:</strong> <?= htmlspecialchars($curso['duracion_presencial'] ?? 'No asignada') ?></div>
                                        </div>
                                    </td>
                                    
                                    <td>$<?= number_format($curso['precio'], 2) ?></td>
                                    <td>
                                        <a href="curso_editar.php?id=<?= $curso['id_curso'] ?>" class="btn-cmv-editar"><i class="fas fa-edit"></i></a>
                                        <?php if ($_SESSION['rol'] == 'admin'): ?>
                                            <a href="curso_eliminar.php?id=<?= $curso['id_curso'] ?>" class="btn-cmv-eliminar" onclick="return confirm('¿Eliminar este curso?')"><i class="fas fa-trash"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <!-- ========================================== -->
            <!-- VISTA PARA USUARIO (CATÁLOGO VISUAL)       -->
            <!-- ========================================== -->
            <?php else: ?>
                
                <div class="header">
                    <div>
                        <h4 class="mb-0" style="color: #0B2D4F;">Catálogo de Cursos</h4>
                        <p class="text-muted mb-0">Explora los cursos que CMV tiene para ti</p>
                    </div>
                </div>

                <div class="row g-4">
                    <?php foreach ($cursos as $curso): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card-curso">
                                <?php if (!empty($curso['imagen']) && file_exists('assets/img/' . $curso['imagen'])): ?>
                                    <img src="assets/img/<?= htmlspecialchars($curso['imagen']) ?>" alt="<?= htmlspecialchars($curso['titulo']) ?>">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/400x200?text=Curso+CMV" alt="Curso">
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($curso['titulo']) ?></h5>
                                    <p class="card-text mb-3"><?= htmlspecialchars($curso['descripcion']) ?></p>
                                    
                                    <div class="d-flex justify-content-between align-items-end mt-auto">
                                        <span class="badge-precio">$<?= number_format($curso['precio'], 2) ?></span>
                                        
                                        <!-- AQUÍ MOSTRAMOS LAS DOS DURACIONES EN LA TARJETA DEL USUARIO -->
                                        <div class="text-muted" style="font-size: 12px; text-align: right; line-height: 1.4;">
                                            <div><i class="fas fa-laptop text-primary me-1"></i> Online: <?= htmlspecialchars($curso['duracion_online'] ?? 'N/A') ?></div>
                                            <div><i class="fas fa-chalkboard-teacher text-success me-1"></i> Presencial: <?= htmlspecialchars($curso['duracion_presencial'] ?? 'N/A') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>