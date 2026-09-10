<?php
// public/mi_perfil.php - Perfil del usuario logueado
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$id_usuario = $_SESSION['id_usuario'];

// Obtener datos actuales del usuario
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch();

if (!$usuario) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Mensajes
$mensaje = '';
if (isset($_GET['success'])) {
    $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> ' . htmlspecialchars($_GET['success']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
}
if (isset($_GET['error'])) {
    $mensaje = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_GET['error']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .content { padding: 30px; }
        .header {
            background: white; padding: 20px 30px; border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .card-perfil {
            background: white; padding: 30px; border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px;
        }
        .card-perfil h5 { color: #0B2D4F; font-weight: 600; margin-bottom: 20px; border-bottom: 2px solid #f0f2f5; padding-bottom: 12px; }
        .avatar-circle {
            width: 90px; height: 90px; border-radius: 50%;
            background: linear-gradient(135deg, #0B2D4F, #1a4b7a);
            color: white; display: flex; align-items: center; justify-content: center;
            font-size: 38px; font-weight: 700; margin: 0 auto 15px;
        }
        .form-label { font-weight: 600; color: #333; font-size: 14px; }
        .form-control:focus { border-color: #0B2D4F; box-shadow: 0 0 0 0.2rem rgba(11,45,79,0.15); }
        .btn-cmv {
            background-color: #0B2D4F; color: white; border: none;
            padding: 10px 25px; border-radius: 8px; font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-cmv:hover { background-color: #1a4b7a; color: white; }
        .info-badge {
            background-color: #e8eef5; color: #0B2D4F;
            padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 600;
            display: inline-block;
        }
        .info-item { margin-bottom: 12px; }
        .info-item strong { color: #0B2D4F; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 content">
            <div class="header">
                <div>
                    <h4 class="mb-0" style="color: #0B2D4F;"> Mi Perfil</h4>
                    <p class="text-muted mb-0">Administra tu información personal</p>
                </div>
            </div>

            <?= $mensaje ?>

            <div class="row">
                <!-- Columna izquierda: Info del usuario -->
                <div class="col-md-4">
                    <div class="card-perfil text-center">
                        <div class="avatar-circle">
                            <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
                        </div>
                        <h5 class="mb-1" style="border: none; padding: 0;"><?= htmlspecialchars($usuario['nombre']) ?></h5>
                        <p class="text-muted mb-3"><?= htmlspecialchars($usuario['email']) ?></p>
                        <span class="info-badge">
                            <?= ($usuario['rol'] == 'admin') ? ' Administrador' : ' Editor' ?>
                        </span>
                        <hr style="margin: 20px 0;">
                        <div class="text-start">
                            <div class="info-item">
                                <strong>ID:</strong> #<?= $usuario['id_usuario'] ?>
                            </div>
                            <div class="info-item">
                                <strong>Registro:</strong><br>
                                <?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Formularios -->
                <div class="col-md-8">
                    <!-- Formulario 1: Datos personales -->
                    <div class="card-perfil">
                        <h5><i class="fas fa-user-edit"></i> Datos Personales</h5>
                        <form action="mi_perfil_actualizar.php" method="POST">
                            <input type="hidden" name="accion" value="datos">
                            
                            <div class="mb-3">
                                <label class="form-label">Nombre completo</label>
                                <input type="text" name="nombre" class="form-control" 
                                       value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Correo electrónico</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= htmlspecialchars($usuario['email']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Rol</label>
                                <input type="text" class="form-control" 
                                       value="<?= ($usuario['rol'] == 'admin') ? 'Administrador' : 'Editor' ?>" disabled>
                                <small class="text-muted">El rol solo puede ser cambiado por un administrador.</small>
                            </div>

                            <button type="submit" class="btn btn-cmv">
                                <i class="fas fa-save"></i> Guardar cambios
                            </button>
                        </form>
                    </div>

                    <!-- Formulario 2: Cambiar contraseña -->
                    <div class="card-perfil">
                        <h5><i class="fas fa-key"></i> Cambiar Contraseña</h5>
                        <form action="mi_perfil_actualizar.php" method="POST">
                            <input type="hidden" name="accion" value="password">
                            
                            <div class="mb-3">
                                <label class="form-label">Contraseña actual</label>
                                <input type="password" name="password_actual" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nueva contraseña</label>
                                <input type="password" name="password_nueva" class="form-control" 
                                       minlength="6" required>
                                <small class="text-muted">Mínimo 6 caracteres.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirmar nueva contraseña</label>
                                <input type="password" name="password_confirmar" class="form-control" 
                                       minlength="6" required>
                            </div>

                            <button type="submit" class="btn btn-cmv">
                                <i class="fas fa-lock"></i> Cambiar contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>