<?php

namespace Model;

class Ponente extends ActiveRecord
{
    protected static $tabla = 'ponentes';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'ciudad', 'pais', 'imagen', 'tags', 'redes'];

    public $id;
    public $nombre;
    public $apellido;
    public $ciudad;
    public $pais;
    public $imagen;
    public $tags;
    public $redes;
    public $imagen_actual;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->ciudad = $args['ciudad'] ?? '';
        $this->pais = $args['pais'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
        $this->tags = $args['tags'] ?? '';
        $this->redes = $args['redes'] ?? '';
    }

    public function validar()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if (strlen($this->nombre) > 40) {
            self::$alertas['error'][] = 'El Nombre no puede tener más de 40 caracteres';
        }

        if (!$this->apellido) {
            self::$alertas['error'][] = 'El Apellido es Obligatorio';
        }
        if (strlen($this->apellido) > 40) {
            self::$alertas['error'][] = 'El Apellido no puede tener más de 40 caracteres';
        }

        if (!$this->ciudad) {
            self::$alertas['error'][] = 'El Campo Ciudad es Obligatorio';
        }
        if (strlen($this->ciudad) > 30) {
            self::$alertas['error'][] = 'El Campo Ciudad no puede tener más de 20 caracteres';
        }

        if (!$this->pais) {
            self::$alertas['error'][] = 'El Campo País es Obligatorio';
        }
        if (strlen($this->pais) > 30) {
            self::$alertas['error'][] = 'El Campo País no puede tener más de 20 caracteres';
        }

        if (!$this->imagen) {
            self::$alertas['error'][] = 'La imagen es obligatoria';
        }
        if (!$this->tags) {
            self::$alertas['error'][] = 'El Campo áreas es obligatorio';
        }
        if (strlen($this->tags) > 120) {
            self::$alertas['error'][] = 'El Campo áreas no puede tener más de 120 caracteres';
        }

        return self::$alertas;
    }
}
