<?php
// public/certificado_agregar.php - Formulario para agregar certificado
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

// Obtener lista de usuarios y cursos para los selects
$usuarios = $pdo->query("SELECT id_usuario, nombre, email FROM usuarios ORDER BY nombre")->fetchAll();
$cursos = $pdo->query("SELECT id_curso, titulo FROM cursos ORDER BY titulo")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Certificado - CMV</title>
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
        }
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            max-width: 700px;
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
        .btn-cmv:hover { background-color: #1a4b7a; color: white; }
        .btn-cmv-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }
        .btn-cmv-secondary:hover { background-color: #5a6268; color: white; }
        .form-control:focus {
            border-color: #0B2D4F;
            box-shadow: 0 0 0 0.2rem rgba(11, 45, 79, 0.25);
        }
        .required { color: red; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar d-none d-md-block">
            <div class="logo">📋 CMV</div>
            <a href="dashboard.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="cursos.php"><i class="fas fa-book"></i> Cursos</a>
            <a href="usuarios.php"><i class="fas fa-users"></i> Usuarios</a>
            <a href="certificados.php" class="active"><i class="fas fa-certificate"></i> Certificados</a>
            <a href="#"><i class="fas fa-calendar-alt"></i> Eventos</a>
            <div class="logout">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            </div>
        </div>

        <div class="col-md-10 content">
            <div class="header">
                <h4 class="mb-0" style="color: #0B2D4F;">➕ Agregar Nuevo Certificado</h4>
                <p class="text-muted mb-0">Registra un nuevo certificado o DC-3 en el sistema</p>
            </div>

            <div class="form-card">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>
                <form action="certificado_guardar.php" method="POST" enctype="multipart/form-data">
                    <!-- Usuario -->
                    <div class="mb-3">
                        <label for="id_usuario" class="form-label fw-semibold">Usuario <span class="required">*</span></label>
                        <select class="form-select" id="id_usuario" name="id_usuario" required>
                            <option value="">Selecciona un usuario...</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id_usuario'] ?>">
                                    <?= htmlspecialchars($u['nombre']) ?> - <?= htmlspecialchars($u['email']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Curso -->
                    <div class="mb-3">
                        <label for="id_curso" class="form-label fw-semibold">Curso <span class="required">*</span></label>
                        <select class="form-select" id="id_curso" name="id_curso" required>
                            <option value="">Selecciona un curso...</option>
                            <?php foreach ($cursos as $c): ?>
                                <option value="<?= $c['id_curso'] ?>">
                                    <?= htmlspecialchars($c['titulo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tipo de documento -->
                    <div class="mb-3">
                        <label for="tipo_documento" class="form-label fw-semibold">Tipo de Documento <span class="required">*</span></label>
                        <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                            <option value="certificado">Certificado</option>
                            <option value="dc3">DC-3</option>
                        </select>
                    </div>

                    <!-- Fecha de emisión -->
                    <div class="mb-3">
                        <label for="fecha_emision" class="form-label fw-semibold">Fecha de Emisión</label>
                        <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" value="<?= date('Y-m-d') ?>">
                    </div>

                    <!-- Archivo PDF -->
                    <div class="mb-3">
                        <label for="archivo_pdf" class="form-label fw-semibold">Archivo PDF <span class="required">*</span></label>
                        <input type="file" class="form-control" id="archivo_pdf" name="archivo_pdf" accept=".pdf" required>
                        <small class="text-muted">Solo archivos PDF (máx. 5MB)</small>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-cmv"><i class="fas fa-save"></i> Guardar Certificado</button>
                        <a href="certificados.php" class="btn btn-cmv-secondary"><i class="fas fa-arrow-left"></i> Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>