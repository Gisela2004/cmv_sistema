<?php
// public/evento_editar.php - Formulario para editar evento
session_start();

if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'editor')) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: eventos.php?error=ID de evento inválido");
    exit();
}

// Obtener datos del evento
$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id_evento = ?");
$stmt->execute([$id]);
$evento = $stmt->fetch();

if (!$evento) {
    header("Location: eventos.php?error=Evento no encontrado");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Evento - CMV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
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
            max-width: 800px;
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
        .imagen-actual {
            max-width: 150px;
            border-radius: 8px;
            border: 2px solid #eee;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        
        <!-- AQUÍ ESTÁ EL SIDEBAR -->
        <?php include 'sidebar.php'; ?>

        <!-- CONTENIDO -->
        <div class="col-md-10 content">
            <div class="header">
                <h4 class="mb-0" style="color: #0B2D4F;"> Editar Evento</h4>
                <p class="text-muted mb-0">Modifica la información del evento</p>
            </div>

            <div class="form-card">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>
                
                <form action="evento_actualizar.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $evento['id_evento'] ?>">
                    
                    <div class="mb-3">
                        <label for="titulo" class="form-label fw-semibold">Título del Evento <span class="required">*</span></label>
                        <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($evento['titulo']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($evento['descripcion']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fecha_evento" class="form-label fw-semibold">Fecha <span class="required">*</span></label>
                            <input type="date" class="form-control" id="fecha_evento" name="fecha_evento" value="<?= $evento['fecha_evento'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hora" class="form-label fw-semibold">Hora</label>
                            <input type="time" class="form-control" id="hora" name="hora" value="<?= htmlspecialchars($evento['hora'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="lugar" class="form-label fw-semibold">Lugar</label>
                        <input type="text" class="form-control" id="lugar" name="lugar" value="<?= htmlspecialchars($evento['lugar'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block">Imagen actual</label>
                        <?php if (!empty($evento['imagen']) && file_exists('assets/img/' . $evento['imagen'])): ?>
                            <img src="assets/img/<?= htmlspecialchars($evento['imagen']) ?>" class="imagen-actual" alt="Imagen del evento">
                            <br>
                            <small class="text-muted">Archivo: <?= htmlspecialchars($evento['imagen']) ?></small>
                        <?php else: ?>
                            <span class="text-muted d-block mb-2">Sin imagen</span>
                        <?php endif; ?>
                        
                        <input type="file" class="form-control mt-2" id="imagen" name="imagen" accept="image/*">
                        <small class="text-muted">Deja este campo vacío si deseas mantener la imagen actual.</small>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-cmv"><i class="fas fa-save me-2"></i> Actualizar Evento</button>
                        <a href="eventos.php" class="btn btn-cmv-secondary"><i class="fas fa-arrow-left me-2"></i> Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>