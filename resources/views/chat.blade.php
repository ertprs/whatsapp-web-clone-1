@extends('layouts.app')

@section('title', 'Chat')

@section('content')

<div class="wrapper">
    <div class="background teal wrapper__top"></div>
    <div class="z-depth-2 wrapper__content">
        <div class="row chat">
            <div class="col s9 chat__content" style="overflow: hidden">
                <ul id="messages"></ul>
                <div class="chat__input">
                    <div class="row valign-wrapper" style="margin-bottom: 0">
                        <div class="col s11">
                            <textarea id="emojionearea" name="message"></textarea>
                        </div>
                        <div class="col chat__send">
                            <button id="send"><i class="material-icons">
                                    send
                                </i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s3 chat__users">
                <p class="teal-text"><?= count($users) ?> participantes</p>
                @foreach ($users as $user)
                @if ($user != session('user'))
                <a href="/chat/private/{{$user}}">
                    @else
                    <a href="#">
                        @endif
                        <div class="row chat__user">
                            <div class="col s3">
                                @if (array_key_exists($user, $images))
                                <img src="{{ url('storage/img/' . $images[$user]) }}" alt="{{$user}} image" class="circle responsive-img" style="width: 49px; height: 49px;">
                                @else
                                <img src="https://drogaspoliticacultura.net/wp-content/uploads/2017/09/placeholder-user.jpg" alt="{{$user}} image" class="circle responsive-img" style="width: 49px; height: 49px;">
                                @endif
                            </div>
                            <div class="col">
                                <?= $user == session('user') ? 'Tú' : $user ?>
                            </div>
                        </div>
                    </a>
                    @endforeach
                    <div class="row" style="margin-top: 4rem">
                        <div class="col">
                            <form action="/signout" method="post">
                                @csrf
                                <button class="waves-effect waves-light btn red" type="submit"><i class="material-icons left"> exit_to_app</i>cerrar sesión</button>
                            </form>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

@endsection