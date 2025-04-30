<?php

namespace Classes;

class Paginacion
{
    public $pagina_actual;
    public $registros_por_pagina;
    public $total_registros;

    public function __construct($pagina_actual = 1, $registros_por_pagina = 10, $total_registros = 0)
    {
        $this->pagina_actual = (int) $pagina_actual;
        $this->registros_por_pagina = (int) $registros_por_pagina;
        $this->total_registros = (int) $total_registros;
    }

    public function offset()
    {
        return $this->registros_por_pagina * ($this->pagina_actual - 1);
    }

    public function total_paginas()
    {
        return ceil($this->total_registros / $this->registros_por_pagina); // ceil redondea siempre hacia arriba
    }

    public function pagina_anterior()
    {
        $anterior = $this->pagina_actual - 1;
        return ($anterior > 0) ? $anterior : false;
    }

    public function pagina_siguiente()
    {
        $siguiente = $this->pagina_actual + 1;
        return ($siguiente <= $this->total_paginas()) ? $siguiente : false;
    }

    public function enlace_anterior()
    {
        $html = '';
        if ($this->pagina_anterior()) {
            $html .= "<a class=\"paginacion__enlace paginacion__enlace--texto\" href=\"?page={$this->pagina_anterior()}\">&laquo; Anterior </a>";
        }
        return $html;
    }

    public function enlace_siguiente()
    {
        $html = '';
        if ($this->pagina_siguiente()) {
            $html .= "<a class=\"paginacion__enlace paginacion__enlace--texto\" href=\"?page={$this->pagina_siguiente()}\">Siguiente &raquo;</a>";
        }
        return $html;
    }

    public function numeros_paginas()
    {
        $html = '';
        $total_paginas = $this->total_paginas();

        if ($total_paginas <= 10) {
            // Mostrar todos los números si hay 10 o menos paginas
            $html .= $this->generar_enlaces_paginas(1, $total_paginas);
        } else {
            // Cuando no hay mas de 10 paginas
            if ($this->pagina_actual <= 5) {
                // Mostrar los primeros 8 números y los últimos
                $html .= $this->generar_enlaces_paginas(1, 8);
                $html .= '...';
                $html .= $this->generar_enlaces_paginas($total_paginas - 1, $total_paginas);
            } else if ($this->pagina_actual >= $total_paginas - 4) {
                // Mostrar los primeros y últimos 8 números
                $html .= $this->generar_enlaces_paginas(1, 1);
                $html .= '...';
                $html .= $this->generar_enlaces_paginas($total_paginas - 7, $total_paginas);
            } else {
                // Mostrar números en el medio
                $html .= $this->generar_enlaces_paginas(1, 1);
                $html .= '...';
                $html .= $this->generar_enlaces_paginas($this->pagina_actual - 2, $this->pagina_actual + 2);
                $html .= '...';
                $html .= $this->generar_enlaces_paginas($total_paginas - 1, $total_paginas);
            }
        }
        return $html;
    }

    private function generar_enlaces_paginas($desde, $hasta)
    {
        $html = '';
        for ($i = $desde; $i <= $hasta; $i++) {
            if ($i === $this->pagina_actual) {
                $html .= "<span class=\"paginacion__enlace paginacion__enlace--actual\">{$i}</span>";
            } else {
                $html .= "<a class=\"paginacion__enlace paginacion__enlace--numero\" href=\"?page={$i}\">{$i}</a>";
            }
        }
        return $html;
    }

    public function paginacion()
    {
        $html = '';
        if ($this->total_registros > 1) {
            $html .= '<div class="paginacion">';
            $html .= $this->enlace_anterior();
            $html .= $this->numeros_paginas();
            $html .= $this->enlace_siguiente();
            $html .= '</div>';
        }
        return $html;
    }
}
