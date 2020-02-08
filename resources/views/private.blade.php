@extends('layouts.app')

@section('title', 'Privado')

@section('content')

<div class="wrapper">
    <div class="background teal wrapper__top"></div>
    <div class="z-depth-2 wrapper__content">
        <div class="row chat">
            <div class="col s3 chat__users">

            </div>
            <div class="col s9 chat__content" style="overflow: hidden">
                <ul id="messages">

                </ul>
                <div class="chat__input">
                    <div class="row valign-wrapper" style="margin-bottom: 0">
                        <div class="col s11">
                            <textarea id="emojionearea" name="message"></textarea>
                        </div>
                        <div class="col chat__send">
                            <button id="send-private"><i class="material-icons">
                                    send
                                </i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection