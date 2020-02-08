$(document).ready(function() {
    let message;

    // Config ajax headers
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        }
    });

    // Init icons
    $("#emojionearea").emojioneArea({
        pickerPosition: "top",
        filtersPosition: "bottom",
        tones: false,
        autocomplete: false,
        inline: true,
        hidePickerOnBlur: true,
        events: {
            change: function(editor, event) {
                // Get editor element to get the message value
                message = editor[0];
            },
            keyup: function(editor, event) {
                if (
                    event.keyCode == 13 ||
                    event.key == "Enter" ||
                    event.code == "Enter"
                ) {
                    sendMessage(editor[0]);
                }
            }
        }
    });

    // @foreach ($messages as $message)
    // <li class="white <?= $message->user == session('user') ? 'me' : '' ?>">
    //     @if ($message->user <> session('user'))
    //         <span class="chat__content-user">
    //             {{$message->user}}
    //         </span>
    //         @endif
    //         {{$message->content}}
    //         <small>{{$message->time}}</small>
    // </li>
    // @endforeach

    // Simular mensajes en tiempo real
    const regex = /^\/chat$/gm;
    const str = window.location.pathname;

    // Comprobar si estamos en el chat público o en privado
    const typeOfChat = str.match(regex) ? "public" : "private";

    setInterval(() => {
        $.get(`/chat/messages/${typeOfChat}`).done(function(res) {
            document.getElementById("messages").innerHTML = "";
            res.forEach(m => {
                document.getElementById("messages").innerHTML += `
                    <li class="white">
                        <span class="chat__content-user">
                            ${m.user}
                        </span>
                        ${m.content}
                        <small>${m.time}</small>
                    </li>
            `;
            });
        });
    }, 500);

    // Scroll to bottom of messages
    function toBottom() {
        let scroll = $("#messages");
        $("#messages").animate(
            {
                scrollTop: scroll.prop("scrollHeight")
            },
            "slow"
        );
    }

    toBottom();

    // Send message
    function sendMessage(msg) {
        // Send message only if there is text
        if (msg.innerText.length > 0) {
            // Comprobar si el mensaje es enviado desde una chat privado
            const re = /\/chat\/private\/\w+/g;

            const isPrivate = window.location.pathname.match(re);

            let data = {
                message: msg.innerText
            };

            if (isPrivate) {
                data.private = true;
                data.chatWith = window.location.pathname.split("/")[3];
            }

            $.post("/chat", {
                ...data
            }).done(() => {
                // Clear textarea
                msg.innerText = "";
                toBottom();
            });
        }
    }

    // Send message on click
    $("#send").click(function() {
        sendMessage(message);
    });

    // Send private message
    $("#send-private").click(function() {
        console.log("privado");
    });
});
