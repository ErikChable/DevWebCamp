<?php

namespace Controllers;

use Model\Paquete;
use Model\Registro;
use Model\Usuario;
use MVC\Router;
use Model\Evento;
use Model\Categoria;
use Model\Dia;
use Model\EventosRegistros;
use Model\Hora;
use Model\Ponente;
use Model\Regalo;

class RegistroController
{
    public static function crear(Router $router)
    {
        if (!is_auth()) {
            header('Location: /');
            return;
        }

        // Verificar si el usuario ya tiene un registro
        $registro = Registro::where('usuario_id', $_SESSION['id']);
        if (isset($registro) && ($registro->paquete_id === "3" || $registro->paquete_id === "2")) {
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }

        if (isset($registro) && $registro->paquete_id === "1") {
            header('Location: /finalizar-registro/conferencias');
            return;
        }

        $router->render('registro/crear', [
            'titulo' => 'Finalizar Registro'
        ]);
    }

    public static function gratis(Router $router)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_auth()) {
                header('Location: /login');
                return;
            }

            // Verificar si el usuario ya tiene un registro
            $registro = Registro::where('usuario_id', $_SESSION['id']);
            if (isset($registro) && $registro->paquete_id === "3") {
                header('Location: /boleto?id=' . urlencode($registro->token));
                return;
            }

            $token = substr(md5(uniqid(rand(), true)), 0, 8); // Con substr recortamos desde el inicio (0) hasta los 8 caracteres

            // Crear registro de usuario
            $datos = [
                'paquete_id' => 3,
                'pago_id' => '',
                'token' => $token,
                'usuario_id' => $_SESSION['id']
            ];

            $registro = new Registro($datos);
            $resultado = $registro->guardar();

            if ($resultado) {
                header('Location: /boleto?id=' . urlencode($registro->token));
                return;
            }
        }
    }

    public static function boleto(Router $router)
    {
        if (!is_auth()) {
            header('Location: /login');
            return;
        }

        // Validar la url
        $id = $_GET['id'];

        if (!$id || !strlen($id) === 8) {
            header('Location: /');
            return;
        }

        // Buscar en la BD
        $registro = Registro::where('token', $id);
        if (!$registro || $registro->usuario_id !== $_SESSION['id']) {
            header('Location: /');
            return;
        }

        // Llenar las tablas de referencia
        $registro->usuario = Usuario::find($registro->usuario_id);
        $registro->paquete = Paquete::find($registro->paquete_id);

        $router->render('registro/boleto', [
            'titulo' => 'Asistencia a DevWebCamp',
            'registro' => $registro
        ]);
    }

    public static function pagar()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_auth()) {
                header('Location: /login');
                return;
            }

            // Validar que POST no este vacio
            if (empty($_POST)) {
                echo json_encode([]);
                return;
            }

            // Crear registro
            $datos = $_POST;
            $datos['token'] = substr(md5(uniqid(rand(), true)), 0, 8); // Con substr recortamos desde el inicio (0) hasta los 8 caracteres
            $datos['usuario_id'] = $_SESSION['id'];

            try {
                $registro = new Registro($datos);
                $resultado = $registro->guardar();
                echo json_encode($resultado);
            } catch (\Throwable $th) {
                echo json_encode([
                    'resultado' => 'error'
                ]);
            }
        }
    }

    public static function conferencias(Router $router)
    {
        if (!is_auth()) {
            header('Location: /login');
            return;
        }

        // Validar que el usuario tenga el plan presencial
        $usuario_id = $_SESSION['id'];
        $registro = Registro::where('usuario_id', $usuario_id);

        if (isset($registro) && $registro->paquete_id === "2") {
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }

        // Redireccionar al usuario al boleto virtual en caso de haber finalizado su registro (tener elegidas sus conferencias)
        $registro_completo = EventosRegistros::where('registro_id', $registro->id);
        if (isset($registro_completo)) {
            header('Location: /boleto?id=' . urlencode($registro->token));
        }

        if ($registro->paquete_id !== "1") {
            header('Location: /');
            return;
        }

        $eventos = Evento::ordenar('hora_id', 'ASC');

        $eventos_formateados = [];
        foreach ($eventos as $evento) {
            $evento->categoria = Categoria::find($evento->categoria_id);
            $evento->dia = Dia::find($evento->dia_id);
            $evento->hora = Hora::find($evento->hora_id);
            $evento->ponente = Ponente::find($evento->ponente_id);

            // Conferencias(categoria_id) del dia Viernes(dia_id)
            if ($evento->dia_id === "1" && $evento->categoria_id === "1") {
                $eventos_formateados['conferencias_v'][] = $evento;
            }
            // Conferencias(categoria_id) del dia Sábado(dia_id)
            if ($evento->dia_id === "2" && $evento->categoria_id === "1") {
                $eventos_formateados['conferencias_s'][] = $evento;
            }

            // Workshops(categoria_id) del dia Viernes(dia_id)
            if ($evento->dia_id === "1" && $evento->categoria_id === "2") {
                $eventos_formateados['workshops_v'][] = $evento;
            }
            // Workshops(categoria_id) del dia Sábado(dia_id)
            if ($evento->dia_id === "2" && $evento->categoria_id === "2") {
                $eventos_formateados['workshops_s'][] = $evento;
            }
        }

        $regalos = Regalo::all('ASC');

        // Manejando el registro mediante $_POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Revisar que el usuario este autenticado
            if (!is_auth()) {
                header('Location: /login');
                return;
            }

            $eventos = explode(',', $_POST['eventos']);
            if (empty($eventos)) {
                echo json_encode(['resultado' => false]);
                return;
            }

            // Obtener el registro de usuario
            $registro = Registro::where('usuario_id', $_SESSION['id']);
            if (!isset($registro) || $registro->paquete_id !== '1') {
                echo json_encode(['resultado' => false]);
                return;
            }

            $eventos_array = [];

            // Validar la disponibilidad de los eventos seleccionados
            foreach ($eventos as $evento_id) {
                $evento = Evento::find($evento_id);

                // Comprobar que el evento exista
                if (!isset($evento) || $evento->disponibles === '0') {
                    echo json_encode(['resultado' => false]);
                    return;
                }

                $eventos_array[] = $evento;
            }

            foreach ($eventos_array as $evento) {
                $evento->disponibles -= 1;
                $evento->guardar();

                // Almacenar el registro
                $datos = [
                    'evento_id' => (int) $evento->id,
                    'registro_id' => (int) $registro->id
                ];

                $registro_usuario = new EventosRegistros($datos);
                $registro_usuario->guardar();
            }

            // Almacenar el regalo
            $registro->sincronizar(['regalo_id' => $_POST['regalo_id']]);
            $resultado = $registro->guardar();

            if ($resultado) {
                echo json_encode([
                    'resultado' => $resultado,
                    'token' => $registro->token
                ]);
            } else {
                echo json_encode(['resultado' => false]);
            }

            return;
        }

        $router->render('registro/conferencias', [
            'titulo' => 'Elige Workshops & Conferencias',
            'eventos' => $eventos_formateados,
            'regalos' => $regalos
        ]);
    }

    public static function eventos(Router $router)
    {
        if (!is_auth()) {
            header('Location: /');
            return;
        }

        // Validar que el usuario tenga el plan presencial
        $usuario_id = $_SESSION['id'];
        $registro = Registro::where('usuario_id', $usuario_id);

        if (isset($registro) && $registro->paquete_id === "2") {
            header('Location: /boleto?id=' . urlencode($registro->token));
            return;
        }

        if ($registro->paquete_id !== "1") {
            header('Location: /');
            return;
        }

        $eventos_registros = EventosRegistros::allWhere('registro_id', $registro->id);

        $eventos = [];

        foreach ($eventos_registros as $evento_registro) {
            $evento = Evento::find($evento_registro->evento_id);
            if ($evento) {
                $evento->categoria = Categoria::find($evento->categoria_id);
                $evento->dia = Dia::find($evento->dia_id);
                $evento->hora = Hora::find($evento->hora_id);
                $evento->ponente = Ponente::find($evento->ponente_id);

                $eventos[] = $evento;
            }
        }

        // Ordenamos los eventos antes de mandarlos a la vista
        usort($eventos, function ($a, $b) {
            if ($a->dia_id === $b->dia_id) {
                return $a->hora_id <=> $b->hora_id;
            }
            return $a->dia_id <=> $b->dia_id;
        });

        $router->render('registro/mis-eventos', [
            'titulo' => 'Tus Conferencias / Workshops',
            'eventos' => $eventos
        ]);
    }
}
