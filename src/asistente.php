<?php
namespace App;

class Asistente
{
    private $comandos = [];

    public function registrarComando($nombre, $clase)
    {
        $this->comandos[$nombre] = $clase;
    }

    public function procesarMensaje($input)
    {
        $partes = explode(' ', trim(strtolower($input)));
        $comando = array_shift($partes);
        $argumentos = $partes;

        if (isset($this->comandos[$comando])) {
            $clase = $this->comandos[$comando];
            $instancia = new $clase();
            return $instancia->ejecutar($argumentos);
        }

        return $this->preguntarAOllama($input);
    }

    private function preguntarAOllama($pregunta)
    {
        $url = 'http://localhost:11434/api/generate';
        $data = json_encode([
            'model' => 'glm-4.6:cloud',
            'prompt' => $pregunta,
            'stream' => false
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $data
            ]
        ]);

        $respuesta = file_get_contents($url, false, $context);
        $respuesta = json_decode($respuesta, true);

        return $respuesta['response'] ?? 'No entiendo esa pregunta.';
    }
}
