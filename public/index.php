<?php
// public/index.php
session_start();

// Cargar Composer autoloader
require_once '../vendor/autoload.php';

// Importar clases con namespaces correctos
use App\Asistente;
use Comandos\Saludar;
use Comandos\Ayuda;

// Inicializar historial de chat
if (!isset($_SESSION['chat'])) {
    $_SESSION['chat'] = [];
}

// Procesar mensaje si se envía
if ($_POST['mensaje'] ?? false) {
    try {
        $mensaje = trim($_POST['mensaje']);
        
        // Guardar mensaje del usuario
        $_SESSION['chat'][] = ['usuario' => $mensaje, 'tipo' => 'usuario'];

        // Procesar con el asistente
        $asistente = new Asistente();
        $asistente->registrarComando('saludar', Saludar::class);
        $asistente->registrarComando('ayuda', Ayuda::class);
        
        $respuesta = $asistente->procesarMensaje($mensaje);
        
        // Guardar respuesta del asistente
        $_SESSION['chat'][] = ['usuario' => $respuesta, 'tipo' => 'asistente'];
        
        // Redirigir para evitar reenvío de formulario
        header('Location: index.php');
        exit;
        
    } catch (Exception $e) {
        $error_msg = 'Error: ' . $e->getMessage();
        $_SESSION['chat'][] = ['usuario' => $error_msg, 'tipo' => 'asistente'];
        header('Location: index.php');
        exit;
    } catch (Error $e) {
        $error_msg = 'Error fatal: ' . $e->getMessage();
        $_SESSION['chat'][] = ['usuario' => $error_msg, 'tipo' => 'asistente'];
        header('Location: index.php');
        exit;
    }
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
    // Limpiar chat
    if (isset($_GET['limpiar'])) {
        $_SESSION['chat'] = [];
        header('Location: index.php');
        exit;
    }
    ?>

    <script>
        // Auto-scroll al final del chat
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    </script>
</body>
</html>
