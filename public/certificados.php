<?php
// public/certificados.php - Listado de certificados
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

// Obtener todos los certificados con información del usuario y curso
$stmt = $pdo->query("
    SELECT c.*, u.nombre as usuario_nombre, u.email, cur.titulo as curso_titulo 
    FROM certificados c
    LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
    LEFT JOIN cursos cur ON c.id_curso = cur.id_curso
    ORDER BY c.id_certificado DESC
");
$certificados = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Certificados - CMV</title>
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
        .table-certificados {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .table-certificados th {
            background-color: #0B2D4F;
            color: white;
            font-weight: 600;
            padding: 15px;
        }
        .table-certificados td { padding: 15px; vertical-align: middle; }
        .table-certificados tr:hover { background-color: #f8f9fa; }
        .badge-certificado { background-color: #0B2D4F; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .badge-dc3 { background-color: #28a745; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .sin-certificados { text-align: center; padding: 60px 20px; color: #999; }
        .folio { font-family: monospace; font-weight: bold; color: #0B2D4F; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar d-none d-md-block">
            <div class="logo"> CMV Asesoría Y  Capacitacio</div>
            <a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="cursos.php"><i class="fas fa-book"></i> Cursos</a>
            <a href="usuarios.php"><i class="fas fa-users"></i> Usuarios</a>
            <a href="certificados.php" class="active"><i class="fas fa-certificate"></i> Certificados</a>
            <a href="#"><i class="fas fa-calendar-alt"></i> Eventos</a>
            <div class="logout">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            </div>
        </div>

        <!-- Contenido -->
        <div class="col-md-10 content">
            <div class="header">
                <div>
                    <h4 class="mb-0" style="color: #0B2D4F;">Gestión de Certificados</h4>
                    <p class="text-muted mb-0">Administra los certificados y DC-3 emitidos por CMV</p>
                </div>
                <?php if ($_SESSION['rol'] == 'admin'): ?>
                    <a href="certificado_agregar.php" class="btn btn-cmv">
                        <i class="fas fa-plus"></i> Nuevo Certificado
                    </a>
                <?php endif; ?>
            </div>

            <!-- Tabla de certificados -->
            <?php if (count($certificados) > 0): ?>
                <div class="table-responsive table-certificados">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Curso</th>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($certificados as $cert): ?>
                                <tr>
                                    <td><span class="badge-certificado">#<?= $cert['id_certificado'] ?></span></td>
                                    <td><strong><?= htmlspecialchars($cert['usuario_nombre']) ?></strong><br><small><?= htmlspecialchars($cert['email']) ?></small></td>
                                    <td><?= htmlspecialchars($cert['curso_titulo']) ?></td>
                                    <td><span class="folio"><?= htmlspecialchars($cert['folio']) ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($cert['fecha_emision'])) ?></td>
                                    <td>
                                        <?php if ($cert['tipo_documento'] == 'certificado'): ?>
                                            <span class="badge-certificado">Certificado</span>
                                        <?php else: ?>
                                            <span class="badge-dc3">DC-3</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($_SESSION['rol'] == 'admin'): ?>
                                            <a href="certificado_editar.php?id=<?= $cert['id_certificado'] ?>" class="btn btn-cmv-editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="certificado_eliminar.php?id=<?= $cert['id_certificado'] ?>" class="btn btn-cmv-eliminar" onclick="return confirm('¿Eliminar este certificado?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= htmlspecialchars($cert['archivo_pdf']) ?>" class="btn btn-cmv" target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="sin-certificados">
                    <i class="fas fa-certificate" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
                    <h5>No hay certificados registrados</h5>
                    <p class="text-muted">Haz clic en "Nuevo Certificado" para agregar el primer certificado.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>