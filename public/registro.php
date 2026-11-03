<?php
// public/registro.php - Formulario de registro público (Diseño profesional)
session_start();
require_once '../config/database.php';

$mensaje = '';
if (isset($_GET['error'])) {
    $mensaje = '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}
if (isset($_GET['success'])) {
    $mensaje = '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            background-color: #F4F6F9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .contenedor-registro {
            display: flex;
            min-height: 100vh;
            max-width: 1200px;
            margin: 0 auto;
            box-shadow: 0 10px 40px rgba(11, 45, 79, 0.15);
            background: white;
        }
        /* Panel Izquierdo - Marca */
        .panel-marca {
            flex: 1;
            background: linear-gradient(135deg, #0B2D4F 0%, #1a4b7a 100%);
            color: white;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .panel-marca::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .panel-marca::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }
        .panel-marca .logo-marca {
         max-width: 220px;
         margin-bottom: 30px;
    /* Sin fondo, sin padding, sin border-radius */
        }
        .panel-marca h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            line-height: 1.3;
            z-index: 1;
        }
        .panel-marca p {
            font-size: 15px;
            color: rgba(255,255,255,0.8);
            line-height: 1.6;
            margin-bottom: 30px;
            z-index: 1;
        }
        .panel-marca ul {
            list-style: none;
            padding: 0;
            z-index: 1;
        }
        .panel-marca ul li {
            margin-bottom: 15px;
            font-size: 14px;
            color: rgba(255,255,255,0.9);
        }
        .panel-marca ul li i {
            color: #5FD3F3;
            margin-right: 10px;
            font-size: 16px;
        }
        /* Panel Derecho - Formulario */
        .panel-formulario {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .panel-formulario h2 {
            color: #0B2D4F;
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 8px;
        }
        .panel-formulario .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 35px;
        }
        .form-label {
            font-weight: 600;
            color: #0B2D4F;
            font-size: 14px;
            margin-bottom: 6px;
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #0B2D4F;
            box-shadow: 0 0 0 0.2rem rgba(11, 45, 79, 0.15);
        }
        .input-icon {
            position: relative;
        }
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 14px;
        }
        .input-icon input {
            padding-left: 40px;
        }
        .btn-cmv {
            background-color: #0B2D4F;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 15px;
            margin-top: 10px;
        }
        .btn-cmv:hover {
            background-color: #1a4b7a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(11, 45, 79, 0.3);
        }
        .link-login {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .link-login a {
            color: #0B2D4F;
            font-weight: 600;
            text-decoration: none;
        }
        .link-login a:hover {
            text-decoration: underline;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .contenedor-registro {
                flex-direction: column;
            }
            .panel-marca {
                padding: 40px 30px;
            }
            .panel-marca h1 {
                font-size: 24px;
            }
            .panel-formulario {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
<div class="contenedor-registro">
    <!-- Panel Izquierdo - Marca -->
    <div class="panel-marca">
        <img src="img/Imagen1.png" alt="CMV" class="logo-marca">
        <h1>Bienvenido a CMV Asesoría y Capacitación</h1>
        <p>Regístrate para acceder a nuestros servicios de capacitación y consultoría profesional.</p>
        <ul>
            <li><i class="fas fa-check-circle"></i> Accede a más de 150 cursos STPS</li>
            <li><i class="fas fa-check-circle"></i> Consulta tus certificados en línea</li>
            <li><i class="fas fa-check-circle"></i> Recibe información de nuevos eventos</li>
            <li><i class="fas fa-check-circle"></i> Gestiona tu perfil de usuario</li>
        </ul>
    </div>

    <!-- Panel Derecho - Formulario -->
    <div class="panel-formulario">
        <h2>Crear Cuenta</h2>
        <p class="subtitle">Completa el formulario para registrarte</p>

        <?= $mensaje ?>

        <form action="registro_procesar.php" method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre completo</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Juan Pérez">
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="ejemplo@correo.com">
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Mínimo 6 caracteres">
                </div>
            </div>
            <div class="mb-3">
                <label for="confirmar" class="form-label">Confirmar contraseña</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" id="confirmar" name="confirmar" required placeholder="Repite la contraseña">
                </div>
            </div>
            <button type="submit" class="btn btn-cmv">
                <i class="fas fa-user-plus me-2"></i> Registrarme
            </button>
        </form>

        <div class="link-login">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>
</div>
</body>
</html>