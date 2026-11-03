<?php
// public/verificar_certificado.php - Página pública para verificar certificados
session_start();
require_once '../config/database.php';

$mensaje = '';
$certificado = null;
$folio_buscado = '';

// Si viene un folio desde la URL (desde el panel de usuario), lo procesamos automáticamente
if (isset($_GET['folio_busqueda']) && !empty($_GET['folio_busqueda'])) {
    $_POST['folio'] = $_GET['folio_busqueda'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['folio'])) {
    $folio_buscado = trim($_POST['folio']);
    
    // Buscar el certificado por folio
    $stmt = $pdo->prepare("
        SELECT c.*, u.nombre as usuario_nombre, u.email, cur.titulo as curso_titulo 
        FROM certificados c
        LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
        LEFT JOIN cursos cur ON c.id_curso = cur.id_curso
        WHERE c.folio = ?
    ");
    $stmt->execute([$folio_buscado]);
    $certificado = $stmt->fetch();
    
    if (!$certificado) {
        $mensaje = '<div class="alert alert-danger shadow-sm border-0" style="background-color: #fef2f2; color: #991b1b; font-size: 14px;"><i class="fas fa-times-circle me-2"></i> <strong>No encontrado.</strong> Verifica el folio.</div>';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #F4F6F9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Encabezado */
        .header {
            background: linear-gradient(135deg, #0B2D4F 0%, #1a4b7a 100%);
            padding: 12px 0;
            box-shadow: 0 4px 20px rgba(11, 45, 79, 0.15);
        }
        .logo-container img {
            max-height: 60px;
            width: auto;
            filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.4)); 
        }
        .header-title {
            color: white;
            font-weight: 600;
            font-size: 18px;
            margin-left: 20px;
            border-left: 1px solid rgba(255,255,255,0.3);
            padding-left: 20px;
        }
        .btn-inicio {
            color: white;
            text-decoration: none;
            font-weight: 600;
            border: 2px solid rgba(255,255,255,0.5);
            padding: 8px 24px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-inicio:hover {
            background-color: white;
            color: #0B2D4F;
        }

        /* Contenedor Principal */
        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px 15px;
        }
        
        .card-split {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(11, 45, 79, 0.08);
            border: none;
            max-width: 1000px; 
            margin: 0 auto;
            width: 100%;
            overflow: hidden;
        }

        /* Columna Izquierda: Buscador */
        .col-busqueda {
            padding: 50px 40px;
            text-align: center;
            border-right: 1px solid #e2e8f0;
        }
        
        /* NUEVO ICONO SVG (A PRUEBA DE FALLOS) */
        .icon-shield {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            margin: 0 auto 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }
        .icon-shield svg {
            width: 45px;
            height: 45px;
            fill: white; /* Color del escudo */
        }

        .titulo-principal {
            color: #0B2D4F;
            font-weight: 700; 
            font-size: 28px;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }
        .subtitulo {
            color: #64748b;
            font-size: 15px;
            margin-bottom: 30px;
        }

        /* Buscador Vertical */
        .input-group-vertical {
            position: relative;
            margin-bottom: 20px;
        }
        .search-icon {
            position: absolute;
            left: 15px;
            top: 18px;
            color: #94a3b8;
            font-size: 18px;
        }
        .search-input {
            border-radius: 12px !important;
            padding: 15px 15px 15px 45px;
            font-size: 16px;
            border: 2px solid #e2e8f0;
            background-color: #f8fafc;
            width: 100%;
            height: 55px;
        }
        .search-input:focus {
            border-color: #0B2D4F;
            background-color: white;
            box-shadow: none;
        }
        .btn-consultar {
            background-color: #0B2D4F;
            color: white;
            border-radius: 12px !important;
            padding: 15px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
            height: 55px;
        }
        .btn-consultar:hover {
            background-color: #1a4b7a;
            transform: translateY(-2px);
        }

        /* Columna Derecha: Resultados */
        .col-resultados {
            padding: 50px 40px;
            background-color: #fcfcfd;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .estado-vacio {
            text-align: center;
            color: #94a3b8;
        }
        .estado-vacio i {
            font-size: 60px;
            color: #cbd5e1;
            margin-bottom: 15px;
        }

        .resultado-header {
            background-color: #ecfdf5;
            border: 1px solid #10b981;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            margin-bottom: 25px;
        }
        .resultado-header h4 {
            color: #047857;
            font-weight: 700;
            margin: 0;
            font-size: 18px;
        }
        
        .dato-item {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .dato-icon {
            width: 40px;
            height: 40px;
            background: #e2e8f0;
            color: #0B2D4F;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .dato-content strong {
            display: block;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .dato-content span {
            color: #0f172a;
            font-size: 15px;
            font-weight: 600;
        }
        
        .btn-pdf-verde {
            background-color: #0B2D4F;
            color: white;
            border-radius: 12px;
            padding: 15px;
            font-weight: 600;
            border: none;
            width: 100%;
            display: inline-block;
            text-align: center;
            text-decoration: none;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-pdf-verde:hover { background-color: #1a4b7a; color: white; }

        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
            margin-top: 40px;
            padding-bottom: 20px;
        }

        @media (max-width: 768px) {
            .col-busqueda { border-right: none; border-bottom: 1px solid #e2e8f0; padding: 30px 20px; }
            .col-resultados { padding: 30px 20px; }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="logo-container">
                    <img src="img/logo-limpio.png" alt="CMV">
                </div>
                <span class="header-title d-none d-md-block">Verificación Oficial</span>
            </div>
            <a href="index.html" class="btn-inicio"><i class="fas fa-home me-2"></i> Inicio</a>
        </div>
    </div>

    <div class="container main-container">
        <div class="card-split">
            <div class="row g-0">
                
                <!-- COLUMNA IZQUIERDA -->
                <div class="col-md-5 col-busqueda">
                    
                    <!-- ICONO SVG NATIVO (Siempre se verá) -->
                    <div class="icon-shield">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 00-1.032 0 11.209 11.209 0 01-7.877 3.08.75.75 0 00-.722.515A12.74 12.74 0 002.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 00.374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 00-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08zm3.094 8.016a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <h1 class="titulo-principal">Verificar Documento</h1>
                    <p class="subtitulo">Ingresa el folio único para validar su autenticidad en nuestro sistema.</p>

                    <!-- FORMULARIO CON MÉTODO GET PARA EVITAR EL ERROR DEL NAVEGADOR -->
                    <form method="GET" action="">
                        <?= $mensaje ?>
                        <div class="input-group-vertical">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control search-input" name="folio" 
                                   placeholder="Ej: CMV-2026-1234" required 
                                   value="<?= isset($folio_buscado) ? htmlspecialchars($folio_buscado) : '' ?>">
                        </div>
                        <button type="submit" class="btn btn-consultar">
                            Consultar Ahora
                        </button>
                    </form>
                </div>

                <!-- COLUMNA DERECHA -->
                <div class="col-md-7 col-resultados">
                    <?php if ($certificado): ?>
                        <div class="resultado-header">
                            <h4><i class="fas fa-check-circle me-2"></i> Documento Válido</h4>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dato-item">
                                    <div class="dato-icon"><i class="fas fa-user"></i></div>
                                    <div class="dato-content">
                                        <strong>Participante</strong>
                                        <span><?= htmlspecialchars($certificado['usuario_nombre']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="dato-item">
                                    <div class="dato-icon"><i class="fas fa-book"></i></div>
                                    <div class="dato-content">
                                        <strong>Curso</strong>
                                        <span><?= htmlspecialchars($certificado['curso_titulo']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="dato-item">
                                    <div class="dato-icon"><i class="fas fa-hashtag"></i></div>
                                    <div class="dato-content">
                                        <strong>Folio Único</strong>
                                        <span><?= htmlspecialchars($certificado['folio']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="dato-item">
                                    <div class="dato-icon"><i class="fas fa-calendar-alt"></i></div>
                                    <div class="dato-content">
                                        <strong>Fecha de Emisión</strong>
                                        <span><?= date('d/m/Y', strtotime($certificado['fecha_emision'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="<?= htmlspecialchars($certificado['archivo_pdf']) ?>" class="btn-pdf-verde" target="_blank">
                            <i class="fas fa-file-pdf me-2"></i> Abrir o Descargar PDF
                        </a>
                    <?php else: ?>
                        <div class="estado-vacio">
                            <i class="fas fa-file-search"></i>
                            <h5 class="fw-bold" style="color: #64748b;">Esperando Búsqueda</h5>
                            <p class="mb-0" style="font-size: 14px;">Los datos del certificado aparecerán aquí una vez que ingreses un folio válido.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="footer">
            <p>&copy; 2026 CMV Asesoría y Capacitación - Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>