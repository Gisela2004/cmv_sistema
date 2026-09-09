<?php
// public/usuarios.php - Listado de usuarios (Diseño mejorado)
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Verificar si es administrador
if ($_SESSION['rol'] !== 'admin') {
    header("Location: dashboard.php?error=No tienes permisos");
    exit();
}

require_once '../config/database.php';

// Obtener todos los usuarios (excepto el propio para evitar eliminarse a sí mismo)
$id_usuario_actual = $_SESSION['id_usuario'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario != ? ORDER BY id_usuario DESC");
$stmt->execute([$id_usuario_actual]);
$usuarios = $stmt->fetchAll();

// Mensajes de éxito o error
$mensaje = '';
if (isset($_GET['success'])) {
    $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    ' . htmlspecialchars($_GET['success']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
}
if (isset($_GET['error'])) {
    $mensaje = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ' . htmlspecialchars($_GET['error']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - CMV</title>
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
        .btn-cmv-eliminar {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 13px;
            transition: all 0.3s ease;
        }
        .btn-cmv-eliminar:hover { background-color: #c82333; color: white; }
        .btn-cmv-editar {
            background-color: #ffc107;
            color: #333;
            border: none;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 13px;
            transition: all 0.3s ease;
        }
        .btn-cmv-editar:hover { background-color: #e0a800; color: #333; }
        .table-usuarios {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .table-usuarios th {
            background-color: #0B2D4F;
            color: white;
            font-weight: 600;
            padding: 15px;
        }
        .table-usuarios td { padding: 15px; vertical-align: middle; }
        .table-usuarios tr:hover { background-color: #f8f9fa; }
        .badge-admin { background-color: #0B2D4F; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .badge-editor { background-color: #28a745; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .sin-usuarios { text-align: center; padding: 60px 20px; color: #999; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar d-none d-md-block">
            <div class="logo"> CMV Asesoria Y Capacitación</div>
            <a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="cursos.php"><i class="fas fa-book"></i> Cursos</a>
            <a href="usuarios.php" class="active"><i class="fas fa-users"></i> Usuarios</a>
            <a href="certificados.php"><i class="fas fa-certificate"></i> Certificados</a>
            <a href="eventos.php"><i class="fas fa-calendar-alt"></i> Eventos</a>
            <div class="logout">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            </div>
        </div>

        <!-- Contenido -->
        <div class="col-md-10 content">
            <div class="header">
                <div>
                    <h4 class="mb-0" style="color: #0B2D4F;"> Gestión de Usuarios</h4>
                    <p class="text-muted mb-0">Administra los usuarios del sistema</p>
                </div>
                <a href="usuario_agregar.php" class="btn btn-cmv">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </a>
            </div>

            <!-- Mensajes -->
            <?= $mensaje ?>

            <!-- Tabla de usuarios -->
            <?php if (count($usuarios) > 0): ?>
                <div class="table-responsive table-usuarios">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Rol</th>
                                <th>Fecha de Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><span class="badge-admin">#<?= $usuario['id_usuario'] ?></span></td>
                                    <td><strong><?= htmlspecialchars($usuario['nombre']) ?></strong></td>
                                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td>
                                        <?php if ($usuario['rol'] == 'admin'): ?>
                                            <span class="badge-admin">Administrador</span>
                                        <?php else: ?>
                                            <span class="badge-editor">Editor</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                                    <td>
                                        <a href="usuario_editar.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-cmv-editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="usuario_eliminar.php?id=<?= $usuario['id_usuario'] ?>" class="btn btn-cmv-eliminar" onclick="return confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="sin-usuarios">
                    <i class="fas fa-users" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
                    <h5>No hay usuarios registrados</h5>
                    <p class="text-muted">Haz clic en "Nuevo Usuario" para agregar el primer usuario.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>