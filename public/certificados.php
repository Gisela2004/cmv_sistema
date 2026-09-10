<?php
// public/certificados.php - Listado de certificados
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

// Obtener todos los certificados
$stmt = $pdo->prepare("SELECT * FROM certificados ORDER BY id_certificado DESC");
$stmt->execute();
$certificados = $stmt->fetchAll();

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
    <title>Gestión de Certificados - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Aquí solo va el CSS del contenido (NO del sidebar, porque el sidebar ya trae su propio CSS si usas el sidebar.php correcto) */
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
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
        .sin-usuarios { text-align: center; padding: 60px 20px; color: #999; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        
        <!-- AQUÍ ESTÁ EL INCLUDE DEL SIDEBAR (NO DUPLICAR EL BLOQUE DE AQUÍ PARA ABAJO) -->
        <?php include 'sidebar.php'; ?>
        
        <!-- Contenido -->
        <div class="col-md-10 content">
            <div class="header">
                <div>
                    <h4 class="mb-0" style="color: #0B2D4F;">Gestión de Certificados</h4>
                    <p class="text-muted mb-0">Administra los certificados del sistema</p>
                </div>
                <a href="certificado_agregar.php" class="btn btn-cmv">
                    <i class="fas fa-plus"></i> Nuevo Certificado
                </a>
            </div>

            <!-- Mensajes -->
            <?= $mensaje ?>

            <!-- Tabla de certificados -->
            <?php if (count($certificados) > 0): ?>
                <div class="table-responsive table-usuarios">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Folio (Nombre)</th>
                                <th>Curso (ID)</th>
                                <th>Fecha de Emisión</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($certificados as $certificado): ?>
                                <tr>
                                    <td><span class="badge-admin">#<?= $certificado['id_certificado'] ?></span></td>
                                    <td><strong><?= htmlspecialchars($certificado['folio']) ?></strong></td>
                                    <td><?= htmlspecialchars($certificado['id_curso']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($certificado['fecha_emision'])) ?></td>
                                    <td>
                                        <a href="certificado_editar.php?id=<?= $certificado['id_certificado'] ?>" class="btn btn-cmv-editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="certificado_eliminar.php?id=<?= $certificado['id_certificado'] ?>" class="btn btn-cmv-eliminar" onclick="return confirm('¿Eliminar este certificado?')">
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
                    <i class="fas fa-certificate" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
                    <h5>No hay certificados registrados</h5>
                    <p class="text-muted">Haz clic en "Nuevo Certificado" para agregar el primero.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>