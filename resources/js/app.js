$(document).ready(function() {
    let message;

    // Scroll to bottom of messages
    function toBottom() {
        let messagesHeight = $("#messages").height();
        $("#messages").animate({
            scrollTop: messagesHeight
        });
        console.log(messagesHeight)
    }

    toBottom();

    // Send message
    function sendMessage(msg) {
        // Send message only if there is text
        if (msg.innerText.length > 0) {
            const messages = document.getElementById("messages");
            $.post("/chat", {
                message: msg.innerText
            }).done(data => {
                const { content, time } = data.pop();
                messages.insertAdjacentHTML(
                    "beforeEnd",
                    `<li class="me">
                    ${content}
                    <small>${time}</small>
                </li>`
                );
                // Clear textarea
                msg.innerText = "";
                toBottom();
            });
        }
    }

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

    // Config ajax headers
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        }
    });

    // Send message on click
    $("#send").click(function() {
        sendMessage(message);
    });
});
