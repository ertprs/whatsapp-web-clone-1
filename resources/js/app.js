$(document).ready(function() {
    let message;
    let user = sessionStorage.getItem("user");

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
        saveEmojisAs: "unicode",
        events: {
            change: function(editor) {
                // Get editor element to get the message value
                message = { editor: editor[0], text: this.getText() };
            },
            keyup: function(editor, event) {
                if (
                    event.keyCode == 13 ||
                    event.key == "Enter" ||
                    event.code == "Enter"
                ) {
                    sendMessage({ editor: editor[0], text: this.getText() });
                }
            }
        }
    });

    // Simular mensajes en tiempo real
    const regex = /^\/chat$/gm;
    const str = window.location.pathname;

    // Comprobar si estamos en el chat público o en privado
    const isPrivate = !str.match(regex);
    const typeOfChat = isPrivate ? "private" : "public";
    const chatWith = window.location.pathname.split("/")[3];

    setInterval(() => {
        $.get(
            `/chat/messages/${typeOfChat}${isPrivate ? "/" + chatWith : ""}`
        ).done(function(res) {
            document.getElementById("messages").innerHTML = "";

            if (isPrivate) {
                res = filterUsers(res);
                res.forEach(m => {
                    document.getElementById("messages").innerHTML += `
                        <li class="white ${m.user == chatWith ? "me" : ""}">
                            <span 
                            class="chat__content-user" 
                            style="display:${
                                m.user != chatWith ? "block" : "none"
                            }">
                                ${chatWith}
                            </span>
                            ${m.content}
                            <small>${m.time}</small>
                        </li>
                `;
                });
            } else {
                res.forEach(m => {
                    document.getElementById("messages").innerHTML += `
                        <li class="white ${m.user == user ? "me" : ""}">
                            <span 
                            class="chat__content-user" 
                            style="display:${
                                m.user == user ? "none" : "block"
                            }">
                                ${m.user}
                            </span>
                            ${m.content}
                            <small>${m.time}</small>
                        </li>
                `;
                });
            }
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
        const { editor, text } = msg;
        // Send message only if there is text
        if (text.length > 0) {
            // Comprobar si el mensaje es enviado desde una chat privado
            const re = /\/chat\/private\/\w+/g;

            const isPrivate = window.location.pathname.match(re);

            let data = {
                message: text
            };

            if (isPrivate) {
                data.private = true;
                data.chatWith = window.location.pathname.split("/")[3];
            }

            $.post("/chat", {
                ...data
            }).done(() => {
                // Clear textarea
                editor.innerText = "";
                toBottom();
            });
        }
    }

    function filterUsers(users) {
        let res = [];
        users.forEach(item => {
            // Obtener mis mensajes con el usuario X
            if (item.user === user) res.push(item);
            // Obtener mensajes del usuario X conmigo
            if (item.user === chatWith) res.push(item);
        });
        return res;
    }

    // Send message on click
    $("#send").click(function() {
        sendMessage(message);
    });

    // Send private message
    $("#send-private").click(function() {
        sendMessage(message);
    });
});
