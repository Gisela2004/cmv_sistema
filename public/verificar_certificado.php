<?php
// public/verificar_certificado.php - Página pública para verificar certificados
session_start();
require_once '../config/database.php';

$mensaje = '';
$certificado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['folio'])) {
    $folio = trim($_POST['folio']);
    
    // Buscar el certificado por folio
    $stmt = $pdo->prepare("
        SELECT c.*, u.nombre as usuario_nombre, u.email, cur.titulo as curso_titulo 
        FROM certificados c
        LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
        LEFT JOIN cursos cur ON c.id_curso = cur.id_curso
        WHERE c.folio = ?
    ");
    $stmt->execute([$folio]);
    $certificado = $stmt->fetch();
    
    if ($certificado) {
        $mensaje = '<div class="alert alert-success"> Certificado encontrado</div>';
    } else {
        $mensaje = '<div class="alert alert-danger"> Certificado no encontrado. Verifica el folio e inténtalo de nuevo.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Certificado - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #F4F6F9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header {
            background-color: #0B2D4F;
            color: white;
            padding: 20px 0;
            margin-bottom: 40px;
        }
        .header h1 {
            font-weight: 700;
        }
        .header a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        .header a:hover {
            color: #ddd;
        }
        .card-verificacion {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(11, 45, 79, 0.1);
            padding: 30px;
            max-width: 700px;
            margin: 0 auto;
        }
        .btn-cmv {
            background-color: #0B2D4F;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-cmv:hover {
            background-color: #1a4b7a;
            color: white;
        }
        .folio-resultado {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #0B2D4F;
        }
        .folio-resultado .label {
            font-weight: 600;
            color: #0B2D4F;
        }
        .folio-resultado .valor {
            color: #333;
        }
        .badge-certificado {
            background-color: #0B2D4F;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        .badge-dc3 {
            background-color: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #999;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Cabecera -->
    <div class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1>📜 CMV Asesoría</h1>
                    <p class="mb-0">Verificación de Certificados</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="index.html"><i class="fas fa-home"></i> Inicio</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card-verificacion">
            <h2 class="text-center" style="color: #0B2D4F;"> Verificar Certificado</h2>
            <p class="text-center text-muted">Ingresa el folio de tu certificado para consultar su validez.</p>

            <!-- Mensaje de resultado -->
            <?= $mensaje ?>

            <!-- Formulario -->
            <form method="POST" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="folio" placeholder="Ej: CMV-2026-0001" required>
                    <button class="btn btn-cmv" type="submit">Consultar</button>
                </div>
            </form>

            <!-- Datos del certificado (si se encontró) -->
            <?php if ($certificado): ?>
                <div class="folio-resultado">
                    <h5 style="color: #0B2D4F;"> Datos del Certificado</h5>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><span class="label"> Participante:</span><br><span class="valor"><?= htmlspecialchars($certificado['usuario_nombre']) ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><span class="label"> Correo:</span><br><span class="valor"><?= htmlspecialchars($certificado['email']) ?></span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><span class="label">Curso:</span><br><span class="valor"><?= htmlspecialchars($certificado['curso_titulo']) ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><span class="label"> Fecha de Emisión:</span><br><span class="valor"><?= date('d/m/Y', strtotime($certificado['fecha_emision'])) ?></span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><span class="label"> Folio:</span><br><span class="valor"><strong><?= htmlspecialchars($certificado['folio']) ?></strong></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><span class="label"> Tipo:</span><br>
                                <?php if ($certificado['tipo_documento'] == 'certificado'): ?>
                                    <span class="badge-certificado">Certificado</span>
                                <?php else: ?>
                                    <span class="badge-dc3">DC-3</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= htmlspecialchars($certificado['archivo_pdf']) ?>" class="btn btn-cmv" target="_blank">
                            <i class="fas fa-file-pdf"></i> Ver PDF
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>&copy; 2026 CMV Asesoría y Capacitación - Todos los derechos reservados.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>