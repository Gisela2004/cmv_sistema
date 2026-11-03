<?php
// public/certificados.php - Listado de certificados
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

// Filtrar certificados según el rol
if ($_SESSION['rol'] == 'usuario') {
    $stmt = $pdo->prepare("
        SELECT c.*, u.nombre AS usuario_nombre, u.email, cur.titulo AS curso_titulo
        FROM certificados c
        LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
        LEFT JOIN cursos cur ON c.id_curso = cur.id_curso
        WHERE c.id_usuario = ?
        ORDER BY c.id_certificado DESC
    ");
    $stmt->execute([$_SESSION['id_usuario']]);
} else {
    $stmt = $pdo->query("
        SELECT c.*, u.nombre AS usuario_nombre, u.email, cur.titulo AS curso_titulo
        FROM certificados c
        LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
        LEFT JOIN cursos cur ON c.id_curso = cur.id_curso
        ORDER BY c.id_certificado DESC
    ");
}
$certificados = $stmt->fetchAll();

$mensaje = '';
if (isset($_GET['success'])) {
    $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($_GET['success']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}
if (isset($_GET['error'])) {
    $mensaje = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_GET['error']) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificados - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .content { padding: 30px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .btn-cmv { background-color: #0B2D4F; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; }
        .btn-cmv:hover { background-color: #1a4b7a; color: white; }
        .btn-cmv-eliminar { background-color: #dc3545; color: white; border: none; padding: 5px 12px; border-radius: 6px; font-size: 13px; }
        .btn-cmv-editar { background-color: #ffc107; color: #333; border: none; padding: 5px 12px; border-radius: 6px; font-size: 13px; }
        
        /* Tabla Admin */
        .table-certificados { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .table-certificados th { background-color: #0B2D4F; color: white; font-weight: 600; padding: 15px; }
        .table-certificados td { padding: 15px; vertical-align: middle; }
        .badge-certificado { background-color: #0B2D4F; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .badge-dc3 { background-color: #28a745; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .folio { font-family: monospace; font-weight: bold; color: #0B2D4F; }
        
        /* ========================================== */
        /* ESTILOS TARJETAS (Con previsualización)    */
        /* ========================================== */
        .card-certificado { 
            background: white; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            transition: 0.3s; 
            height: 100%; 
            border: none;
            display: flex;
            flex-direction: column;
        }
        .card-certificado:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 8px 25px rgba(11,45,79,0.12); 
        }
        /* El contenedor gris que ahora tendrá el PDF */
        .certificado-header {
            background-color: #e9ecef; 
            height: 180px; /* Un poco más alto para ver mejor el PDF */
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #ddd;
        }
        .certificado-header iframe {
            width: 100%;
            height: 100%;
            border: none;
            pointer-events: none; /* Bloquea el scroll y clic dentro del mini pdf */
            transform: scale(1.1); /* Hace un pequeño zoom para que no se vea el borde blanco del PDF */
            transform-origin: top center;
        }
        /* Etiqueta flotante sobre el PDF */
        .pdf-overlay {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(11, 45, 79, 0.85);
            color: white;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            pointer-events: none; /* El clic pasa al contenedor */
        }

        .card-certificado .card-body { 
            padding: 20px; 
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .card-certificado .card-title { 
            color: #0B2D4F; 
            font-weight: 700; 
            font-size: 18px; 
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .card-certificado .card-text { 
            color: #666; 
            font-size: 14px; 
            margin-bottom: 15px;
            flex-grow: 1;
        }
        .folio-badge { 
            background-color: #e8eef5; 
            color: #0B2D4F; 
            font-weight: bold; 
            padding: 5px 12px; 
            border-radius: 8px; 
            font-size: 14px; 
            display: inline-block;
        }
        .btn-pdf { 
            background-color: #0B2D4F; 
            color: white; 
            border-radius: 8px; 
            padding: 10px 20px; 
            font-weight: 600; 
            text-decoration: none; 
            display: inline-block; 
            width: 100%; 
            transition: 0.3s;
            text-align: center;
            z-index: 2; /* Para que quede arriba del hover */
        }
        .btn-pdf:hover { background-color: #1a4b7a; color: white; }
        
        /* Caja de Búsqueda */
        .search-dc3 { background: white; border-left: 5px solid #0B2D4F; border-radius: 8px; padding: 20px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .sin-certificados { text-align: center; padding: 60px 20px; color: #999; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 content">
            <div class="header">
                <div>
                    <h4 class="mb-0" style="color: #0B2D4F;"><?= ($_SESSION['rol'] == 'usuario') ? 'Mis Certificados' : 'Gestión de Certificados' ?></h4>
                    <p class="text-muted mb-0">
                        <?= ($_SESSION['rol'] == 'usuario') ? 'Consulta y descarga tus certificados emitidos' : 'Administra los certificados y DC-3 emitidos por CMV' ?>
                    </p>
                </div>
                <?php if ($_SESSION['rol'] == 'admin'): ?>
                    <a href="certificado_agregar.php" class="btn btn-cmv"><i class="fas fa-plus"></i> Nuevo Certificado</a>
                <?php endif; ?>
            </div>

            <?= $mensaje ?>

            <!-- ADMIN Y EDITOR -->
            <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'editor'): ?>
                <!-- ... (Esta parte es la misma tabla de siempre) ... -->
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
                                        <td><strong><?= htmlspecialchars($cert['usuario_nombre'] ?? 'Sin asignar') ?></strong><br><small class="text-muted"><?= htmlspecialchars($cert['email'] ?? '') ?></small></td>
                                        <td><?= htmlspecialchars($cert['curso_titulo'] ?? 'Curso no encontrado') ?></td>
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
                                                <a href="certificado_editar.php?id=<?= $cert['id_certificado'] ?>" class="btn btn-cmv-editar"><i class="fas fa-edit"></i></a>
                                                <a href="certificado_eliminar.php?id=<?= $cert['id_certificado'] ?>" class="btn btn-cmv-eliminar" onclick="return confirm('¿Eliminar este certificado?')"><i class="fas fa-trash"></i></a>
                                            <?php endif; ?>
                                            <a href="<?= htmlspecialchars($cert['archivo_pdf']) ?>" class="btn btn-cmv" target="_blank"><i class="fas fa-file-pdf"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="sin-certificados"><h5>No hay certificados registrados</h5></div>
                <?php endif; ?>

            <!-- USUARIO -->
            <?php else: ?>
                
                <!-- Caja de Búsqueda -->
                <div class="search-dc3">
                    <div class="row align-items-center">
                        <div class="col-md-7 mb-3 mb-md-0">
                            <h5 class="fw-bold mb-1" style="color: #0B2D4F;"><i class="fas fa-search me-2"></i> ¿Deseas verificar un Certificado o DC-3?</h5>
                            <p class="text-muted mb-0" style="font-size: 14px;">Ingresa el folio único para consultar su validez en nuestro sistema.</p>
                        </div>
                        <div class="col-md-5">
                            <form action="verificar_certificado.php" method="GET" target="_blank">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="folio_busqueda" placeholder="Ej: CMV-2026-XXXX" required>
                                    <button class="btn btn-cmv" type="submit">Consultar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tarjetas con Previsualización de PDF -->
                <div class="row g-4">
                    <?php if (count($certificados) > 0): ?>
                        <?php foreach ($certificados as $cert): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card-certificado">
                                    
                                    <!-- ENCABEZADO CON PREVISUALIZACIÓN DEL PDF -->
                                    <div class="certificado-header">
                                        <!-- Usamos #page=1&view=FitH&toolbar=0 para que solo muestre la primera página limpiecita -->
                                        <iframe src="<?= htmlspecialchars($cert['archivo_pdf']) ?>#page=1&view=FitH&toolbar=0&navpanes=0&scrollbar=0" title="Vista previa del certificado"></iframe>
                                        <div class="pdf-overlay"><i class="fas fa-file-pdf"></i> Vista Previa</div>
                                    </div>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($cert['curso_titulo'] ?? 'Curso no disponible') ?></h5>
                                        
                                        <div class="card-text">
                                            <p class="mb-1">Folio: <strong><?= htmlspecialchars($cert['folio']) ?></strong></p>
                                            <p class="mb-0">
                                                <?php if ($cert['tipo_documento'] == 'dc3'): ?>
                                                    <span class="text-success"><i class="fas fa-check-circle"></i> DC-3 Oficial STPS</span>
                                                <?php else: ?>
                                                    <span class="text-primary"><i class="fas fa-certificate"></i> Certificado</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="folio-badge">
                                                <?= ($cert['tipo_documento'] == 'certificado') ? 'Certificado' : 'DC-3' ?>
                                            </span>
                                            <span class="text-muted" style="font-size: 13px;">
                                                <i class="far fa-calendar-alt me-1"></i> <?= date('d/m/Y', strtotime($cert['fecha_emision'])) ?>
                                            </span>
                                        </div>

                                        <a href="<?= htmlspecialchars($cert['archivo_pdf']) ?>" class="btn-pdf mt-auto" target="_blank">
                                            <i class="fas fa-expand-arrows-alt me-2"></i> Abrir PDF Completo
                                        </a>
                                        
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="sin-certificados bg-white rounded shadow-sm">
                                <h5>No tienes certificados emitidos</h5>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>