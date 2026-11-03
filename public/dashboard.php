<?php
// public/dashboard.php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$nombre = $_SESSION['nombre'] ?? 'Usuario';
$rol = $_SESSION['rol'] ?? 'usuario';
$id_usuario = $_SESSION['id_usuario'];

// --- LÓGICA DE DATOS ---
if ($rol == 'admin' || $rol == 'editor') {
    // Estadísticas globales (Admin/Editor)
    $total_cursos = $pdo->query("SELECT COUNT(*) FROM cursos")->fetchColumn();
    $total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $total_certificados = $pdo->query("SELECT COUNT(*) FROM certificados")->fetchColumn();
} else {
    // Estadísticas personales (Usuario)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM certificados WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $mis_certificados = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #F4F6F9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .content { padding: 30px; }
        .header { background: white; padding: 20px 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .welcome-text { color: #0B2D4F; font-weight: 600; }
        
        .card-dashboard { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; transition: all 0.3s ease; border: none; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-decoration: none; color: inherit; }
        .card-dashboard:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(11,45,79,0.12); color: inherit; }
        .card-dashboard .icon { font-size: 40px; color: #0B2D4F; margin-bottom: 10px; }
        .card-dashboard .number { font-size: 36px; font-weight: 700; color: #0B2D4F; }
        .card-dashboard .label { color: #666; font-size: 14px; font-weight: 500; }
        
        /* Accesos rápidos usuario */
        .btn-acceso { background: white; border: 2px solid #e8eef5; border-radius: 12px; padding: 25px 15px; text-align: center; text-decoration: none; color: #0B2D4F; transition: 0.3s; display: block; height: 100%; }
        .btn-acceso:hover { border-color: #0B2D4F; background: #f8fbff; transform: scale(1.02); color: #0B2D4F; }
        .btn-acceso i { font-size: 35px; margin-bottom: 15px; color: #0B2D4F; }
        .btn-acceso h5 { font-weight: 700; margin: 0; font-size: 16px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <div class="col-md-10 content">
            <div class="header">
                <h4 class="welcome-text">  ¡Hola, <?= htmlspecialchars($nombre) ?>!</h4>
                <p class="text-muted mb-0">
                    <?= ($rol == 'admin' || $rol == 'editor') ? 'Panel de administración de CMV' : 'Bienvenido a tu espacio personal' ?>
                </p>
            </div>

            <?php if ($rol == 'admin' || $rol == 'editor'): ?>
                <!-- ========================================== -->
                <!-- VISTA PARA ADMIN Y EDITOR (Estadísticas)   -->
                <!-- ========================================== -->
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

            <?php else: ?>
                <!-- ========================================== -->
                <!-- VISTA PARA USUARIOS (Accesos Rápidos)      -->
                <!-- ========================================== -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card-dashboard">
                            <div class="icon"><i class="fas fa-certificate"></i></div>
                            <div class="number"><?= $mis_certificados ?></div>
                            <div class="label">Mis certificados</div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-dashboard text-start" style="display: block;">
                            <h5 style="color: #0B2D4F;"> ¡Bienvenido!</h5>
                            <p class="text-muted mb-0">Utiliza los accesos directos de abajo para navegar por el sistema. Aquí podrás consultar tus certificados, actualizar tus datos y ver el catálogo de cursos y eventos disponibles.</p>
                        </div>
                    </div>
                </div>

                <h5 class="mb-3" style="color: #0B2D4F; font-weight: 700;">Accesos Rápidos</h5>
                
                <!-- Cambiado a col-lg-3 para que quepan los 4 botones -->
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <a href="cursos.php" class="btn-acceso">
                            <i class="fas fa-book-open"></i>
                            <h5>Ver Catálogo de Cursos</h5>
                            <p class="text-muted mt-2 mb-0" style="font-size: 13px;">Explora los cursos disponibles</p>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a href="eventos.php" class="btn-acceso">
                            <i class="fas fa-calendar-alt"></i>
                            <h5>Próximos Eventos</h5>
                            <p class="text-muted mt-2 mb-0" style="font-size: 13px;">Descubre nuestras actividades</p>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a href="certificados.php" class="btn-acceso">
                            <i class="fas fa-award"></i>
                            <h5>Mis Certificados</h5>
                            <p class="text-muted mt-2 mb-0" style="font-size: 13px;">Consulta tus documentos emitidos</p>
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a href="mi_perfil.php" class="btn-acceso">
                            <i class="fas fa-user-edit"></i>
                            <h5>Mi Perfil</h5>
                            <p class="text-muted mt-2 mb-0" style="font-size: 13px;">Actualiza tus datos personales</p>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>