<?php
// config/database.php - Conexión a la base de datos

// Datos de conexión
define('DB_HOST', 'localhost');
define('DB_NAME', 'cmv_sistema');
define('DB_USER', 'root');
define('DB_PASS', ''); // Si tienes contraseña, ponla aquí

try {
    // Crear la conexión PDO
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configurar el modo de fetch por defecto (array asociativo)
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // (Opcional) Para depuración: descomentar la línea siguiente para ver que la conexión fue exitosa
    // echo "✅ Conexión exitosa a la base de datos";
    
} catch (PDOException $e) {
    // Si hay error, mostrar mensaje y detener la ejecución
    die("❌ Error de conexión a la base de datos: " . $e->getMessage());
}
?>