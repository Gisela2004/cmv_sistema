<?php
// public/eventos.php - Gestión o Catálogo de Eventos según el rol
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

// Obtener todos los eventos
$stmt = $pdo->query("SELECT * FROM eventos ORDER BY fecha_evento ASC");
$eventos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eventos - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .content { padding: 30px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        
        /* --- ESTILOS TABLA ADMIN --- */
        .btn-cmv { background-color: #0B2D4F; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; }
        .btn-cmv:hover { background-color: #1a4b7a; color: white; }
        .btn-cmv-editar { background-color: #ffc107; color: #333; border: none; padding: 5px 12px; border-radius: 6px; }
        .btn-cmv-eliminar { background-color: #dc3545; color: white; border: none; padding: 5px 12px; border-radius: 6px; }
        .table-eventos { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .table-eventos th { background-color: #0B2D4F; color: white; padding: 15px; }
        .table-eventos td { padding: 15px; vertical-align: middle; }
        .fecha-evento { font-weight: 600; color: #0B2D4F; }
        
        /* --- ESTILOS CATÁLOGO VISUAL USUARIO --- */
        .card-evento { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: all 0.3s ease; height: 100%; border: none; }
        .card-evento:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(11,45,79,0.12); }
        .card-evento img { width: 100%; height: 200px; object-fit: cover; }
        .card-evento .card-body { padding: 20px; }
        .card-evento .card-title { color: #0B2D4F; font-weight: 700; font-size: 18px; margin-bottom: 10px; }
        .card-evento .card-text { color: #666; font-size: 14px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 15px; }
        .evento-dato { font-size: 14px; color: #444; margin-bottom: 8px; display: flex; align-items: center;}
        .evento-dato i { color: #0B2D4F; width: 25px; text-align: center; margin-right: 8px; font-size: 16px;}
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
                        <h4 class="mb-0" style="color: #0B2D4F;">Gestión de Eventos</h4>
                        <p class="text-muted mb-0">Administra los eventos de capacitación de CMV</p>
                    </div>
                    <a href="evento_agregar.php" class="btn btn-cmv"><i class="fas fa-plus"></i> Nuevo Evento</a>
                </div>

                <div class="table-responsive table-eventos">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Evento</th>
                                <th>Fecha</th>
                                <th>Lugar</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($eventos as $evento): ?>
                                <tr>
                                    <td><span class="badge bg-primary">#<?= $evento['id_evento'] ?></span></td>
                                    <td><strong><?= htmlspecialchars($evento['titulo']) ?></strong></td>
                                    <td><span class="fecha-evento"><?= date('d/m/Y', strtotime($evento['fecha_evento'])) ?></span></td>
                                    <td><?= htmlspecialchars($evento['lugar']) ?></td>
                                    <td>
                                        <a href="evento_editar.php?id=<?= $evento['id_evento'] ?>" class="btn-cmv-editar"><i class="fas fa-edit"></i></a>
                                        <?php if ($_SESSION['rol'] == 'admin'): ?>
                                            <a href="evento_eliminar.php?id=<?= $evento['id_evento'] ?>" class="btn-cmv-eliminar" onclick="return confirm('¿Eliminar este evento?')"><i class="fas fa-trash"></i></a>
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
                        <h4 class="mb-0" style="color: #0B2D4F;">Próximos Eventos</h4>
                        <p class="text-muted mb-0">Descubre las actividades y talleres que CMV tiene para ti</p>
                    </div>
                </div>

                <div class="row g-4">
                    <?php if (count($eventos) > 0): ?>
                        <?php foreach ($eventos as $evento): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card-evento">
                                    <!-- Imagen del evento -->
                                    <?php if (!empty($evento['imagen']) && file_exists('assets/img/' . $evento['imagen'])): ?>
                                        <img src="assets/img/<?= htmlspecialchars($evento['imagen']) ?>" alt="<?= htmlspecialchars($evento['titulo']) ?>">
                                    <?php else: ?>
                                        <!-- Imagen por defecto si no tiene -->
                                        <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Evento CMV">
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($evento['titulo']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($evento['descripcion']) ?></p>
                                        
                                        <div class="evento-dato">
                                            <i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($evento['fecha_evento'])) ?>
                                        </div>
                                        <?php if (!empty($evento['hora'])): ?>
                                        <div class="evento-dato">
                                            <i class="far fa-clock"></i> <?= htmlspecialchars($evento['hora']) ?> hrs
                                        </div>
                                        <?php endif; ?>
                                        <div class="evento-dato">
                                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($evento['lugar']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5 bg-white rounded shadow-sm">
                            <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 40px;"></i>
                            <h5 class="text-muted">Por el momento no hay eventos programados.</h5>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>