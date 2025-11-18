<?php
namespace Comandos;

class Ayuda
{
    public function ejecutar($argumentos)
    {
        return "🤖 <strong>Comandos disponibles:</strong><br>
                • <code>saludar [nombre]</code> - Saluda a alguien<br>
                • <code>ayuda</code> - Muestra esta ayuda<br>
                • Cualquier otra pregunta será respondida por IA";
    }
}
