<?php
// public/inscripciones_admin.php - Ver y aprobar pagos
session_start();
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'editor')) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

// Consultar todas las inscripciones con datos del usuario y curso
$stmt = $pdo->query("
    SELECT i.*, u.nombre AS usuario_nombre, u.email, c.titulo AS curso_titulo, c.precio 
    FROM inscripciones i
    JOIN usuarios u ON i.id_usuario = u.id_usuario
    JOIN cursos c ON i.id_curso = c.id_curso
    ORDER BY i.fecha_inscripcion DESC
");
$inscripciones = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inscripciones y Pagos - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .content { padding: 30px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .table-custom { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .table-custom th { background-color: #0B2D4F; color: white; padding: 15px; }
        .table-custom td { padding: 15px; vertical-align: middle; }
        .btn-aprobar { background-color: #28a745; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 13px; }
        .btn-aprobar:hover { background-color: #218838; color: white; }
        .btn-rechazar { background-color: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 13px; }
        .btn-rechazar:hover { background-color: #c82333; color: white; }
        .btn-comprobante { background-color: #17a2b8; color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; display: inline-block;}
        .btn-comprobante:hover { background-color: #138496; color: white; }
        
        .badge-pendiente { background-color: #ffc107; color: #333; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold;}
        .badge-aprobado { background-color: #28a745; color: white; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold;}
        .badge-rechazado { background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold;}
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 content">
            <div class="header">
                <h4 class="mb-0" style="color: #0B2D4F;"> Gestión de Inscripciones y Pagos</h4>
                <p class="text-muted mb-0">Revisa los comprobantes y da acceso a los cursos</p>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <div class="table-responsive table-custom">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID Pago</th>
                            <th>Alumno</th>
                            <th>Curso</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Comprobante</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($inscripciones) > 0): ?>
                            <?php foreach ($inscripciones as $ins): ?>
                                <tr>
                                    <td>#<?= $ins['id_inscripcion'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($ins['usuario_nombre']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($ins['email']) ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($ins['curso_titulo']) ?></td>
                                    <td>$<?= number_format($ins['precio'], 2) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($ins['fecha_inscripcion'])) ?></td>
                                    <td>
                                        <?php if (!empty($ins['comprobante'])): ?>
                                            <a href="<?= htmlspecialchars($ins['comprobante']) ?>" target="_blank" class="btn-comprobante">
                                                <i class="fas fa-file-invoice"></i> Ver
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No subido</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($ins['estado'] == 'pendiente'): ?>
                                            <span class="badge-pendiente">Pendiente</span>
                                        <?php elseif ($ins['estado'] == 'aprobado'): ?>
                                            <span class="badge-aprobado">Aprobado</span>
                                        <?php else: ?>
                                            <span class="badge-rechazado">Rechazado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($ins['estado'] == 'pendiente'): ?>
                                            <a href="inscripcion_cambiar_estado.php?id=<?= $ins['id_inscripcion'] ?>&estado=aprobado" class="btn-aprobar" onclick="return confirm('¿Confirmar pago y dar acceso al curso?')">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="inscripcion_cambiar_estado.php?id=<?= $ins['id_inscripcion'] ?>&estado=rechazado" class="btn-rechazar" onclick="return confirm('¿Rechazar este comprobante?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size: 12px;">Completado</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Aún no hay inscripciones registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>