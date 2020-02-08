<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{

    public function index()
    {
        // Redirigir al chat
        if (session('user'))  return redirect('/chat');

        return view('welcome');
    }

    public function store(Request $request)
    {

        // Validar la imagen del usuario
        $request->validate([
            'image' => 'file|max:2048'
        ]);

        function storeImage($request, $name)
        {
            $name = $name . "." . $request->image->getClientOriginalExtension();
            $request->image->storeAs('public/img', $name);
        }

        $username = $request->input('username');

        $users = [];

        $filename = 'users.json';

        // Sí no existe el fichero
        if (!is_file($filename)) {
            $file = fopen($filename, 'w');
            array_unshift($users, $username);
            fwrite($file, json_encode($users));
            fclose($file);

            // Añadir usuario a la sesión
            session(['user' => $username]);

            // Almacenar imagen del usuario
            if ($request->image) storeImage($request, $username);
        } else {
            $activeUsers = json_decode(nl2br(file_get_contents($filename)));

            // Si no es un array, se inicializa
            if (!is_array($activeUsers)) $activeUsers = [];

            $isActive = in_array($username, $activeUsers);

            if ($isActive) {
                return back()->with('info', "El usuario $username ya existe");
            }

            // Añadir usuario si no está en el fichero
            $file = fopen($filename, 'w');
            array_unshift($activeUsers, $username);
            fwrite($file, json_encode($activeUsers));
            fclose($file);

            // Añadir usuario a la sesión
            session(['user' => $username]);

            // Almacenar imagen del usuario
            if ($request->image) storeImage($request, $username);
        }

        // Redirigir al chat
        return redirect('/chat');
    }
}
