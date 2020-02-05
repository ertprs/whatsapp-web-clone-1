<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        if (!is_file('messages.json')) {
            $file = fopen('messages.json', 'w');
            fclose($file);
        }
        $messages = json_decode(nl2br(file_get_contents('messages.json')));

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

        $filename = 'messages.json';

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
            $message['user'] = session('user');
            $message['content'] = $request->input('message');

            // Se agrega mensaje
            $messages[] = $message;

            // Se guarda mensajes en el fichero
            $file = fopen($filename, 'w');
            fwrite($file, json_encode($messages));
            fclose($file);
        }

        return json_decode(nl2br(file_get_contents($filename)));
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
