<?php
// public/eventos.php - Listado de eventos (panel de administración)
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

// Obtener todos los eventos (ordenados por fecha, los más próximos primero)
$stmt = $pdo->query("SELECT * FROM eventos ORDER BY fecha_evento ASC");
$eventos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Eventos - CMV Asesoría Y  Capacitacio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar {
            background-color: #0B2D4F;
            min-height: 100vh;
            padding: 20px 15px;
            color: white;
        }
        .sidebar .logo { font-size: 24px; font-weight: 700; text-align: center; padding: 15px 0; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .sidebar a:hover { background-color: rgba(255,255,255,0.1); color: white; }
        .sidebar a.active { background-color: rgba(255,255,255,0.15); color: white; }
        .sidebar a i { margin-right: 12px; width: 20px; text-align: center; }
        .sidebar .logout { margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px; }
        .sidebar .logout a { color: #ff7f7f; }
        .content { padding: 30px; }
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-cmv {
            background-color: #0B2D4F;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-cmv:hover { background-color: #1a4b7a; color: white; }
        .table-eventos {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .table-eventos th {
            background-color: #0B2D4F;
            color: white;
            font-weight: 600;
            padding: 15px;
        }
        .table-eventos td { padding: 15px; vertical-align: middle; }
        .table-eventos tr:hover { background-color: #f8f9fa; }
        .badge-cmv { background-color: #0B2D4F; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .sin-eventos { text-align: center; padding: 60px 20px; color: #999; }
        .fecha-evento { font-weight: 600; color: #0B2D4F; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar d-none d-md-block">
            <div class="logo">CMV</div>
            <a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="cursos.php"><i class="fas fa-book"></i> Cursos</a>
            <a href="usuarios.php"><i class="fas fa-users"></i> Usuarios</a>
            <a href="certificados.php"><i class="fas fa-certificate"></i> Certificados</a>
            <a href="eventos.php" class="active"><i class="fas fa-calendar-alt"></i> Eventos</a>
            <div class="logout">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            </div>
        </div>

        <div class="col-md-10 content">
            <div class="header">
                <div>
                    <h4 class="mb-0" style="color: #0B2D4F;">Gestión de Eventos</h4>
                    <p class="text-muted mb-0">Administra los eventos de capacitación de CMV</p>
                </div>
                <?php if ($_SESSION['rol'] == 'admin'): ?>
                    <a href="evento_agregar.php" class="btn btn-cmv">
                        <i class="fas fa-plus"></i> Nuevo Evento
                    </a>
                <?php endif; ?>
            </div>

            <?php if (count($eventos) > 0): ?>
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
                                    <td><span class="badge-cmv">#<?= $evento['id_evento'] ?></span></td>
                                    <td><strong><?= htmlspecialchars($evento['titulo']) ?></strong></td>
                                    <td><span class="fecha-evento"><?= date('d/m/Y', strtotime($evento['fecha_evento'])) ?></span></td>
                                    <td><?= htmlspecialchars($evento['lugar']) ?></td>
                                    <td>
                                        <a href="evento_editar.php?id=<?= $evento['id_evento'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                        <?php if ($_SESSION['rol'] == 'admin'): ?>
                                            <a href="evento_eliminar.php?id=<?= $evento['id_evento'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este evento?')">Eliminar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="sin-eventos">
                    <i class="fas fa-calendar-plus" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
                    <h5>No hay eventos registrados</h5>
                    <p class="text-muted">Haz clic en "Nuevo Evento" para agregar el primer evento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>