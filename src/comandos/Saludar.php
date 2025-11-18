<?php
namespace Comandos;

class Saludar
{
    public function ejecutar($argumentos)
    {
        $nombre = !empty($argumentos[0]) ? $argumentos[0] : 'estimao';
        return "Hello, {$nombre}! Que necesitas hoy ?";
    }
}
