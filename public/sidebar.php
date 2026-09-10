<!-- public/sidebar.php -->
<style>
    /* --- ESTILOS ESCRITORIO --- */
    .sidebar { 
        background-color: #0B2D4F; 
        min-height: 100vh; 
        padding: 20px 15px; 
        color: white; 
    }
    .sidebar .logo { text-align: center; padding: 20px 0; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
    .sidebar .logo img { width: 100%; max-width: 180px; height: auto; display: block; margin: 0 auto; }
    .sidebar a { color: rgba(255,255,255,0.7); text-decoration: none; display: block; padding: 12px 15px; margin: 5px 0; border-radius: 8px; transition: all 0.3s ease; font-weight: 500; }
    .sidebar a:hover { background-color: rgba(255,255,255,0.1); color: white; }
    .sidebar a.active { background-color: rgba(255,255,255,0.15); color: white; }
    .sidebar a i { margin-right: 12px; width: 20px; text-align: center; }
    .sidebar .logout { margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px; }
    .sidebar .logout a { color: #ff7f7f; }

    /* --- ESTILOS MÓVIL (Ocultos por defecto) --- */
    .btn-menu-movil { display: none; }
    .menu-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1040; }

    /* --- REGLAS PARA PANTALLAS PEQUEÑAS --- */
    @media (max-width: 767.98px) {
        .btn-menu-movil {
            display: block;
            background-color: #0B2D4F;
            color: white;
            border: none;
            padding: 15px 20px;
            width: 100%;
            text-align: left;
            font-size: 20px;
            cursor: pointer;
            position: relative;
            z-index: 1000;
        }
        .btn-menu-movil i { margin-right: 10px; }
        
        .sidebar {
            display: none; /* Ocultar menú por defecto en móvil */
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 260px;
            z-index: 1050;
            overflow-y: auto;
        }
        /* Clases que activa JavaScript */
        .sidebar.activo { display: block; }
        .menu-overlay.activo { display: block; }
    }
</style>

<?php
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>

<!-- Fondo oscuro para cerrar el menú al hacer clic fuera -->
<div class="menu-overlay" id="menuOverlay" onclick="toggleMenu()"></div>

<!-- Botón hamburguesa (solo aparece en móvil) -->
<div class="btn-menu-movil" onclick="toggleMenu()">
    <i class="fas fa-bars"></i> Menú
</div>

<!-- Menú lateral (SIN las clases d-none ni d-md-block para evitar conflictos) -->
<div class="col-md-2 sidebar" id="menuLateral">
    <div class="logo">
        <img src="img/Imagen1.png" alt="Logo CMV">
    </div>
    
    <a href="dashboard.php" class="<?php echo ($pagina_actual == 'dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-home"></i> Inicio</a>
    <a href="cursos.php" class="<?php echo ($pagina_actual == 'cursos.php') ? 'active' : ''; ?>"><i class="fas fa-book"></i> Cursos</a>
    <a href="usuarios.php" class="<?php echo ($pagina_actual == 'usuarios.php') ? 'active' : ''; ?>"><i class="fas fa-users"></i> Usuarios</a>
    <a href="certificados.php" class="<?php echo ($pagina_actual == 'certificados.php') ? 'active' : ''; ?>"><i class="fas fa-certificate"></i> Certificados</a>
    <a href="eventos.php" class="<?php echo ($pagina_actual == 'eventos.php') ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Eventos</a>
    
    <div class="logout">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
    </div>
</div>

<script>
    function toggleMenu() {
        var menu = document.getElementById('menuLateral');
        var overlay = document.getElementById('menuOverlay');
        // Activa y desactiva tanto el menú como el fondo oscuro
        menu.classList.toggle('activo');
        overlay.classList.toggle('activo');
    }
</script>