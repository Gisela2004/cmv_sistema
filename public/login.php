<?php
// public/login.php - Pantalla de inicio de sesión
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['id_usuario'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - CMV</title>
    
    <!-- CSS de CMV (los estilos que ya tienes) -->
    <link rel="stylesheet" href="styles.css">
    
    <!-- Bootstrap (para los inputs y botones) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Estilos adicionales para el login (respetando los colores de CMV) */
        body {
            background-color: #F4F6F9; /* Gris claro de CMV */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: white;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(11, 45, 79, 0.15);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .login-box h2 {
            color: #0B2D4F; /* Azul marino de CMV */
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 8px;
        }
        .login-box .subtitle {
            color: #666;
            font-size: 15px;
            margin-bottom: 30px;
        }
        .login-box .logo {
            width: 80px;
            height: 80px;
            background-color: #0B2D4F;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 20px;
            font-weight: bold;
        }
        .btn-cmv {
            background-color: #0B2D4F; /* Azul marino de CMV */
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-cmv:hover {
            background-color: #1a4b7a;
            color: white;
            transform: scale(1.02);
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            border-color: #0B2D4F;
            box-shadow: 0 0 0 0.2rem rgba(11, 45, 79, 0.25);
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 8px;
            font-size: 14px;
        }
        .footer-login {
            margin-top: 25px;
            font-size: 13px;
            color: #999;
        }
        .footer-login strong {
            color: #0B2D4F;
        }
    </style>
</head>
<body>

<div class="login-box">
    <!-- Logo simulado (puedes cambiarlo por el logo real de CMV) -->
    <div class="logo">CMV</div>
    
    <h2>Bienvenido</h2>
    <p class="subtitle">Inicia sesión para administrar el sistema</p>

    <!-- Mensaje de error -->
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <!-- Formulario de login -->
    <form action="login_procesar.php" method="POST">
        <div class="mb-3 text-start">
            <label for="email" class="form-label fw-semibold" style="color: #0B2D4F;">Correo electrónico</label>
            <input type="email" class="form-control" id="email" name="email" 
                   placeholder="admin@cmv.com" required autofocus>
        </div>
        <div class="mb-3 text-start">
            <label for="password" class="form-label fw-semibold" style="color: #0B2D4F;">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" 
                   placeholder="admin123" required>
        </div>
        <button type="submit" class="btn-cmv">Ingresar al sistema</button>
    </form>

    <div class="footer-login">
        <p>Usuario de prueba: <strong>admin@cmv.com</strong><br>Contraseña: <strong>admin123</strong></p>
        <p style="margin-top: 10px;">&copy; 2026 CMV Asesoría y Capacitación</p>
    </div>
</div>

</body>
</html>