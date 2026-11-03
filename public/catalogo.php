<?php
// public/catalogo.php - Catálogo público completo de cursos
session_start();
require_once '../config/database.php'; 

$stmt = $pdo->query("SELECT * FROM cursos ORDER BY id_curso DESC");
$cursos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Catálogo de Cursos | CMV Asesoría y Capacitación</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- ¡AQUÍ ESTÁ LA MAGIA QUE FALTABA! (Bootstrap) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="icon" href="img/logo-limpio.png" type="image/png">
    <link rel="stylesheet" href="styles.css">

    <style>
        body { background-color: #F4F6F9; font-family: 'Poppins', sans-serif; }
        
        /* Header */
        .public-header {
            background-color: #0B2D4F;
            padding: 15px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .public-header img { max-height: 50px; filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.5)); }
        .btn-inicio { 
            color: white; 
            text-decoration: none; 
            font-weight: 500; 
            transition: 0.3s; 
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 20px;
            border-radius: 50px;
        }
        .btn-inicio:hover { background-color: white; color: #0B2D4F; }

        /* Banner */
        .catalog-banner {
            background: linear-gradient(135deg, #0B2D4F 0%, #1a4b7a 100%);
            padding: 50px 0;
            color: white;
            text-align: center;
            margin-bottom: 40px;
        }
        .catalog-banner h1 { font-weight: 800; font-size: 36px; margin-bottom: 10px; }
        .catalog-banner p { font-size: 16px; color: rgba(255,255,255,0.8); }

        /* Buscador */
        .search-container { max-width: 600px; margin: -30px auto 40px; position: relative; z-index: 10; }
        .search-input {
            width: 100%; padding: 15px 20px 15px 50px; border-radius: 50px; border: 2px solid #e2e8f0;
            font-size: 16px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); outline: none;
        }
        .search-input:focus { border-color: #0B2D4F; }
        .search-icon { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: #0B2D4F; font-size: 18px; }

        /* Tarjetas */
        .card-curso { 
            background: white; border-radius: 15px; overflow: hidden; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.04); transition: all 0.3s ease; 
            height: 100%; display: flex; flex-direction: column; border: none;
        }
        .card-curso:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(11,45,79,0.15); }
        
        .img-wrapper { height: 160px; overflow: hidden; }
        .card-curso img { width: 100%; height: 100%; object-fit: cover; }
        
        .card-body { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
        .card-title { color: #0B2D4F; font-weight: 700; font-size: 16px; margin-bottom: 10px; line-height: 1.3; }
        .card-text { color: #666; font-size: 13px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 15px; flex-grow: 1; }
        
        .card-footer-custom { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .precio { font-size: 20px; font-weight: 800; color: #0B2D4F; }
        .duracion { background: #e8eef5; color: #0B2D4F; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        
        /* Botón Rojo */
        .btn-inscribirse {
            background-color: #dc3545; color: white; width: 100%; border-radius: 10px;
            padding: 10px; font-weight: 600; transition: 0.3s; text-align: center; text-decoration: none;
            display: block; border: none; font-size: 14px;
        }
        .btn-inscribirse:hover { background-color: #bb2d3b; color: white; }
        
        .sin-resultados { display: none; text-align: center; padding: 50px; background: white; border-radius: 15px; }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="public-header">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Logo a la izquierda -->
            <a href="index.php"><img src="img/logo-limpio.png" alt="CMV"></a>
            <!-- Botón a la derecha -->
            <a href="index.php" class="btn-inicio"><i class="fas fa-arrow-left me-2"></i> Volver al Inicio</a>
        </div>
    </header>

    <!-- Banner -->
    <div class="catalog-banner">
        <div class="container">
            <h1>Nuestro Catálogo de Cursos</h1>
            <p>Encuentra la capacitación perfecta para ti y comienza a estudiar hoy mismo.</p>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="container pb-5">
        
        <!-- Buscador -->
        <div class="search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="buscadorCursos" class="search-input" placeholder="Busca por curso (Ej. Montacargas, Alturas...)">
        </div>

        <!-- Cuadrícula de Cursos -->
        <div class="row g-4" id="listaCursos">
            
            <?php if (count($cursos) > 0): ?>
                <?php foreach ($cursos as $curso): ?>
                    <!-- col-lg-3 hace que se vean 4 cursos por fila -->
                    <div class="col-md-6 col-lg-4 col-xl-3 curso-item">
                        <div class="card-curso">
                            
                            <div class="img-wrapper">
                                <?php if (!empty($curso['imagen']) && file_exists('assets/img/' . $curso['imagen'])): ?>
                                    <img src="assets/img/<?= htmlspecialchars($curso['imagen']) ?>" alt="<?= htmlspecialchars($curso['titulo']) ?>">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/400x200?text=Curso+CMV" alt="Curso">
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($curso['titulo']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($curso['descripcion']) ?></p>
                                
                                <div class="card-footer-custom mt-auto">
                                    <span class="precio">$<?= number_format($curso['precio'], 2) ?></span>
                                    <span class="duracion"><i class="far fa-clock me-1"></i> <?= htmlspecialchars($curso['duracion_online'] ?? 'N/A') ?></span>
                                </div>
                                
                                <!-- Botón Rojo -->
                                <a href="comprar_curso.php?id=<?= $curso['id_curso'] ?>" class="btn-inscribirse">
                                    <i class="fas fa-shopping-cart me-1"></i> Inscribirme
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h4 class="text-muted">Próximamente tendremos nuevos cursos disponibles.</h4>
                </div>
            <?php endif; ?>

        </div>

        <div class="sin-resultados" id="mensajeVacio">
            <h4 style="color: #0B2D4F; font-weight: 700;">No encontramos resultados</h4>
            <p class="text-muted">Intenta buscar con otra palabra clave.</p>
        </div>

    </div>

    <!-- Script Buscador -->
    <script>
        document.getElementById('buscadorCursos').addEventListener('keyup', function() {
            let filtro = this.value.toLowerCase();
            let cursos = document.getElementsByClassName('curso-item');
            let contadorVisibles = 0;

            for (let i = 0; i < cursos.length; i++) {
                let titulo = cursos[i].getElementsByClassName('card-title')[0].textContent.toLowerCase();
                let desc = cursos[i].getElementsByClassName('card-text')[0].textContent.toLowerCase();
                
                if (titulo.includes(filtro) || desc.includes(filtro)) {
                    cursos[i].style.display = 'block';
                    contadorVisibles++;
                } else {
                    cursos[i].style.display = 'none';
                }
            }

            document.getElementById('mensajeVacio').style.display = (contadorVisibles === 0) ? 'block' : 'none';
        });
    </script>
</body>
</html>