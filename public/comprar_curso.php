<?php
// public/comprar_curso.php - Pantalla de checkout/pago
session_start();
require_once '../config/database.php';

// 1. Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    // Si no está logueado, lo mandamos al login. 
    // (Más adelante podemos hacer que después del login regrese a este curso)
    header("Location: login.php?error=Inicia sesión para comprar un curso");
    exit();
}

// 2. Obtener el ID del curso
$id_curso = intval($_GET['id'] ?? 0);
if ($id_curso <= 0) {
    header("Location: catalogo.php?error=Curso no válido");
    exit();
}

// 3. Obtener los datos del curso
$stmt = $pdo->prepare("SELECT * FROM cursos WHERE id_curso = ?");
$stmt->execute([$id_curso]);
$curso = $stmt->fetch();

if (!$curso) {
    header("Location: catalogo.php?error=Curso no encontrado");
    exit();
}

// 4. Verificar si el usuario ya está inscrito en este curso
$stmt = $pdo->prepare("SELECT * FROM inscripciones WHERE id_usuario = ? AND id_curso = ?");
$stmt->execute([$_SESSION['id_usuario'], $id_curso]);
$inscripcion_existente = $stmt->fetch();

if ($inscripcion_existente) {
    if ($inscripcion_existente['estado'] == 'aprobado') {
        header("Location: mis_cursos.php?success=Ya tienes acceso a este curso");
        exit();
    } elseif ($inscripcion_existente['estado'] == 'pendiente') {
        $mensaje = '<div class="alert alert-warning">Ya registraste tu pago para este curso. Está en revisión por el administrador.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprar Curso - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .header-checkout { background-color: #0B2D4F; padding: 15px 0; color: white; }
        .card-checkout { max-width: 800px; margin: 50px auto; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .curso-resumen { background: #f8f9fa; padding: 30px; border-right: 1px solid #eee; }
        .form-pago { padding: 30px; }
        .btn-pagar { background-color: #dc3545; color: white; font-weight: 600; width: 100%; padding: 12px; border-radius: 8px; border: none; transition: 0.3s; }
        .btn-pagar:hover { background-color: #bb2d3b; color: white; }
        .banco-info { background: #e8eef5; border-radius: 8px; padding: 15px; font-size: 14px; border-left: 4px solid #0B2D4F; }
    </style>
</head>
<body>

    <div class="header-checkout">
        <div class="container d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-shopping-cart me-2"></i> Finalizar Inscripción</h4>
            <a href="catalogo.php" class="text-white text-decoration-none"><i class="fas fa-times"></i> Cancelar</a>
        </div>
    </div>

    <div class="container">
        <div class="card-checkout">
            <div class="row g-0">
                
                <!-- Resumen del Curso -->
                <div class="col-md-5 curso-resumen">
                    <h5 class="fw-bold text-dark mb-3">Resumen de tu curso</h5>
                    <?php if (!empty($curso['imagen']) && file_exists('assets/img/' . $curso['imagen'])): ?>
                        <img src="assets/img/<?= htmlspecialchars($curso['imagen']) ?>" class="img-fluid rounded mb-3" alt="Curso">
                    <?php endif; ?>
                    <h4 style="color: #0B2D4F; font-weight: 700;"><?= htmlspecialchars($curso['titulo']) ?></h4>
                    <p class="text-muted" style="font-size: 14px;"><?= htmlspecialchars($curso['descripcion']) ?></p>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total a pagar:</span>
                        <span class="fw-bold" style="font-size: 24px; color: #dc3545;">$<?= number_format($curso['precio'], 2) ?> MXN</span>
                    </div>
                </div>

                <!-- Formulario de Pago -->
                <div class="col-md-7 form-pago">
                    <h5 class="fw-bold mb-3" style="color: #0B2D4F;">Método de Pago</h5>
                    
                    <?= $mensaje ?? '' ?>

                    <div class="banco-info mb-4">
                        <p class="mb-1"><strong>Paso 1:</strong> Realiza una transferencia bancaria a la siguiente cuenta:</p>
                        <ul class="mb-0 mt-2">
                            <li><strong>Banco:</strong> BBVA</li>
                            <li><strong>Titular:</strong> CMV Asesoría y Capacitación</li>
                            <li><strong>CLABE:</strong> 0123 4567 8901 2345 67</li>
                        </ul>
                    </div>

                    <p class="fw-bold mb-2">Paso 2: Sube tu comprobante aquí</p>
                    <form action="comprar_procesar.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_curso" value="<?= $id_curso ?>">
                        
                        <div class="mb-3">
                            <input type="file" name="comprobante" class="form-control" accept="image/*, .pdf" required>
                            <small class="text-muted">Formatos aceptados: JPG, PNG o PDF (Máx. 5MB)</small>
                        </div>

                        <button type="submit" class="btn btn-pagar mt-3">
                            <i class="fas fa-upload me-2"></i> Enviar Comprobante y Finalizar
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

</body>
</html>