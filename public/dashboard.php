<?php
// public/dashboard.php - Panel de control del administrador
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Incluir conexión a BD
require_once '../config/database.php';

$nombre = $_SESSION['nombre'] ?? 'Usuario';
$rol = $_SESSION['rol'] ?? 'usuario';

// Contar cursos
$stmt = $pdo->query("SELECT COUNT(*) as total FROM cursos");
$total_cursos = $stmt->fetch()['total'];

// Contar usuarios
$stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
$total_usuarios = $stmt->fetch()['total'];

// Contar certificados
$stmt = $pdo->query("SELECT COUNT(*) as total FROM certificados");
$total_certificados = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CMV</title>
    
    <!-- CSS de CMV -->
    <link rel="stylesheet" href="styles.css">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome (íconos) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Estilos del dashboard respetando los colores de CMV */
        body {
            background-color: #F4F6F9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background-color: #0B2D4F;
            min-height: 100vh;
            padding: 20px 15px;
            color: white;
        }
        .sidebar .logo {
            text-align: center;
            padding: 10px 0 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar .logo img {
            max-width: 120px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .sidebar .logo .nombre-empresa {
            color: white;
            font-size: 14px;
            font-weight: 600;
            display: block;
            margin-top: 8px;
            line-height: 1.4;
        }
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
        .sidebar a:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar a.active {
            background-color: rgba(255,255,255,0.15);
            color: white;
        }
        .sidebar a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        .sidebar .logout {
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 15px;
        }
        .sidebar .logout a {
            color: #ff7f7f;
        }
        .sidebar .logout a:hover {
            background-color: rgba(255,127,127,0.1);
        }
        .content {
            padding: 30px;
        }
        .content .header {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .card-dashboard {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s ease;
            border: none;
        }
        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(11,45,79,0.12);
        }
        .card-dashboard .icon {
            font-size: 40px;
            color: #0B2D4F;
            margin-bottom: 10px;
        }
        .card-dashboard .number {
            font-size: 36px;
            font-weight: 700;
            color: #0B2D4F;
        }
        .card-dashboard .label {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }
        .welcome-text {
            color: #0B2D4F;
            font-weight: 600;
        }
        .badge-role {
            background-color: #0B2D4F;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
     <!-- Sidebar -->
<div class="col-md-2 sidebar d-none d-md-block">
    <div class="logo" style="text-align: center; padding: 15px 10px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px;">
        <img src="assets/img/Imagen1.png" alt="CMV" style="max-width: 160px; height: auto; display: block; margin: 0 auto; border-radius: 8px;">
    </div>
    <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Inicio</a>
    <a href="usuarios.php"><i class="fas fa-users"></i> Usuarios</a>
    <a href="cursos.php"><i class="fas fa-book"></i> Cursos</a>
    <a href="certificados.php"><i class="fas fa-certificate"></i> Certificados</a>
    <a href="eventos.php"><i class="fas fa-calendar-alt"></i> Eventos</a>
    <div class="logout">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
    </div>
</div>
        <!-- Contenido -->
        <div class="col-md-10 content">
            <div class="header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="welcome-text">👋 ¡Bienvenido, <?= htmlspecialchars($nombre) ?>!</h4>
                    <p class="text-muted mb-0">Panel de administración de CMV</p>
                </div>
                <div>
                    <span class="badge-role"><?= htmlspecialchars($rol) ?></span>
                </div>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card-dashboard">
                        <div class="icon"><i class="fas fa-book"></i></div>
                        <div class="number"><?= $total_cursos ?></div>
                        <div class="label">Cursos registrados</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-dashboard">
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <div class="number"><?= $total_usuarios ?></div>
                        <div class="label">Usuarios registrados</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-dashboard">
                        <div class="icon"><i class="fas fa-certificate"></i></div>
                        <div class="number"><?= $total_certificados ?></div>
                        <div class="label">Certificados emitidos</div>
                    </div>
                </div>
            </div>

            <!-- Mensaje de bienvenida adicional -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card-dashboard text-start p-4">
                        <h5 style="color: #0B2D4F;">📌 Resumen del sistema</h5>
                        <p class="text-muted mb-0">
                            Este panel te permite administrar los cursos, usuarios y certificados de CMV. 
                            Usa el menú lateral para navegar entre las diferentes secciones.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>