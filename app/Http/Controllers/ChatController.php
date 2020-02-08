<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function index()
    {

        $isLogged = session('user');

        // Volver al inicio si el usuario no está registrado
        if (!$isLogged) {
            return back()->with('info', "Debes iniciar sesión");
        }

        if (!is_file('users.json')) {
            $file = fopen('users.json', 'w');
            fclose($file);
        }

        $users = json_decode(nl2br(file_get_contents('users.json')));

        // Obtener contenido el fichero "messages"
        if (!is_file('public-messages.json')) {
            $file = fopen('public-messages.json', 'w');
            fclose($file);
        }
        $messages = json_decode(nl2br(file_get_contents('public-messages.json')));

        // Si no es un array, se inicializa
        if (!is_array($messages)) $messages = [];

        // Comprobar si el usuario tiene imagen
        $imagesDir = scandir(storage_path('app/public/img'));

        // Obtener la imagen del usuario
        $images = [];
        foreach ($imagesDir as $fl) {
            $imgName = explode(".", $fl);
            $exts = ['jpg', 'png'];
            if (in_array($imgName[1], $exts))  $images[$imgName[0]] = $fl;
        }

        return view('chat', ['users' => $users, 'messages' => $messages, 'images' => $images]);
    }

    public function store(Request $request)
    {
        function createMessage($type, $request)
        {
            $filename = "$type-messages.json";

            // Sí no existe el fichero
            if (!is_file($filename)) {
                $file = fopen($filename, 'w');
                fclose($file);
            } else {
                // Obtener contenido el fichero "messages"
                $messages = json_decode(nl2br(file_get_contents($filename)));

                // Si no es un array, se inicializa
                if (!is_array($messages)) $messages = [];

                // Se crear nuevo mensaje
                $message = [];
                $message['time'] = date('H:i');
                $message['content'] = $request->input('message');

                if ($type === 'private') {
                    $message['user'] = $request->chatWith;
                    $messages[][session('user')] = $message;
                } else {
                    $message['user'] = session('user');
                    $messages[] = $message;
                }

                // Se guarda mensajes en el fichero
                $file = fopen($filename, 'w');
                fwrite($file, json_encode($messages));
                fclose($file);
            }

            // Mensajes publicos
            return json_decode(nl2br(file_get_contents($filename)));
        }

        if (!$request->private) {
            // Añadir mensaje publico
            return createMessage('public', $request);
        } else {
            // Añadir mensaje privado
            return createMessage('private', $request);
        }
    }


    public function privateChat($user)
    {
        $filename = "private-messages.json";

        // Sí no existe el fichero
        if (!is_file($filename)) {
            $file = fopen($filename, 'w');
            fclose($file);
        }

        // Obtener contenido el fichero "messages"
        $messages = json_decode(nl2br(file_get_contents($filename)));

        // Si no es un array, se inicializa
        if (!is_array($messages)) $messages = [];


        // Mensajes privados
        $privateMessages = [];

        // Obtener mensajes que he enviado
        foreach ($messages as $message) {
            try {
                if ($message->{session('user')}) $privateMessages[] = $message;
            } catch (Exception $e) {
                echo 'Excepción capturada: ',  $e->getMessage(), "\n";
            }
        }


        // Obtener mensajes que me han enviado
        foreach ($messages as $message) {
            try {
                if ($message->{$user}) $privateMessages[] = $message;
            } catch (Exception $e) {
                echo 'Excepción capturada: ',  $e->getMessage(), "\n";
            }
        }

        return view('private', ['user' => $user, 'messages' => $privateMessages]);
    }

    public function signout(Request $request)
    {
        // Obtener usuarios
        $users = json_decode(nl2br(file_get_contents('users.json')));

        // Buscar el índice del usuario
        $index = array_search(session('user'), $users);
        if ($index) {
            // Eliminar usuario
            array_splice($users, $index, 1);
        }

        // Actualizar fichero
        $file = fopen('users.json', 'w');
        fwrite($file, json_encode($users));
        fclose($file);

        // Borrar usuario de la sesión
        $request->session()->forget('user');

        // Redirigir al inicio
        return redirect('/');
    }
}
