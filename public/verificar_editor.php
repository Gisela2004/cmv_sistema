<?php
require_once '../config/database.php';

$email = 'editor@cmv.com';
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if ($usuario) {
    echo "✅ Usuario encontrado: " . $usuario['nombre'] . "<br>";
    echo "📧 Email: " . $usuario['email'] . "<br>";
    echo "🔑 Hash: " . $usuario['password'] . "<br>";
    echo "🔐 Verificando 'editor123': ";
    if (password_verify('editor123', $usuario['password'])) {
        echo "✅ CORRECTA";
    } else {
        echo "❌ INCORRECTA";
    }
} else {
    echo "❌ Usuario NO encontrado";
}
?>