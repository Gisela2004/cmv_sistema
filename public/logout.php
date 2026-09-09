<?php
error_log("Archivo ejecutado: " . __FILE__ . " - Fecha: " . date('Y-m-d H:i:s'));
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
