<?php
// public/chat.php
session_start();

// Debug completo
echo "<pre>";
echo "Directorio actual: " . __DIR__ . "\n";
echo "Ruta vendor/autoload.php: " . realpath('../vendor/autoload.php') . "\n";
echo "Archivo existe: " . (file_exists('../vendor/autoload.php') ? 'SI' : 'NO') . "\n";

if (!file_exists('../vendor/autoload.php')) {
    die('ERROR CRÍTICO: No se encuentra vendor/autoload.php. Ejecuta: composer install');
}

require_once '../vendor/autoload.php';

echo "Autoload cargado\n";
echo "App\\Asistente existe: " . (class_exists('App\\Asistente') ? 'SI' : 'NO') . "\n";
echo "Comandos\\Saludar existe: " . (class_exists('Comandos\\Saludar') ? 'SI' : 'NO') . "\n";
echo "Comandos\\Ayuda existe: " . (class_exists('Comandos\\Ayuda') ? 'SI' : 'NO') . "\n";
echo "</pre>";

// Si llegamos aquí, todo debería funcionar
use App\Asistente;
use Comandos\Saludar;
use Comandos\Ayuda;

// Inicializar historial de chat
if (!isset($_SESSION['chat'])) {
    $_SESSION['chat'] = [];
}

// Procesar mensaje si se envía
if ($_POST['mensaje'] ?? false) {
    $mensaje = trim($_POST['mensaje']);
    $_SESSION['chat'][] = ['usuario' => $mensaje, 'tipo' => 'usuario'];

    $asistente = new Asistente();
    $asistente->registrarComando('saludar', Saludar::class);
    $asistente->registrarComando('ayuda', Ayuda::class);
    
    $respuesta = $asistente->procesarMensaje($mensaje);
    $_SESSION['chat'][] = ['usuario' => $respuesta, 'tipo' => 'asistente'];
    
    header('Location: chat.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🤖 Asistente Virtual</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🤖 Asistente Virtual</h1>
            <p>Tu asistente inteligente con IA local</p>
        </header>

        <div class="chat-container">
            <div class="chat-messages" id="chat-messages">
                <?php if (empty($_SESSION['chat'])): ?>
                    <div class="message asistente">
                        <strong>Asistente:</strong> ¡Hola! Soy tu asistente virtual. Escribe "ayuda" para ver los comandos disponibles.
                    </div>
                <?php else: ?>
                    <?php foreach ($_SESSION['chat'] as $mensaje): ?>
                        <div class="message <?= htmlspecialchars($mensaje['tipo']) ?>">
                            <strong><?= ucfirst(htmlspecialchars($mensaje['tipo'])) ?>:</strong> 
                            <?= nl2br(htmlspecialchars($mensaje['usuario'])) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form method="POST" class="chat-form">
                <input type="text" name="mensaje" placeholder="Escribe tu mensaje..." required>
                <button type="submit">Enviar</button>
            </form>
        </div>

        <div class="acciones">
            <a href="?limpiar=1" class="btn-limpiar">Limpiar Chat</a>
        </div>
    </div>

    <?php
    if (isset($_GET['limpiar'])) {
        $_SESSION['chat'] = [];
        header('Location: chat.php');
        exit;
    }
    ?>

    <script>
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    </script>
</body>
</html>
