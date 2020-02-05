@extends('layouts.app')

@section('title', 'Inicio')

@section('content')

<div class="container valign-wrapper" style="height: 100vh">
    <div class="row">
        <div class="col s6 offset-s3 " style="text-align: center">
            <h1> Mantente conectado en todo momento.</h1>
            <p>Una manera sencilla de enviar mensajes de texto y planificar actividades, todo desde un solo lugar.</p>
            <form class="col s12" action="/user" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">account_circle</i>
                        <input id="icon_prefix" type="text" class="validate" name="username" required>
                        <label for="icon_prefix">Usuario</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field">
                        <input type="file" name="image" aria-describedby="image">
                        <small id="image" style="display: block">El tamaño del fichero no debe ser superior a 2 MB.</small>
                    </div>
                </div>
                <button class="btn waves-effect teal" type="submit" name="action">Iniciar sesión
                    <i class="material-icons right">send</i>
                </button>
            </form>
        </div>
    </div>
</div>

@endsection