$(document).ready(function() {
    function fetchMessages() {
        $.ajax({
            url: 'handle.php',
            method: 'POST',
            data: { chatId: chatId },
            dataType: 'json',
            success: function(response) {
                let chatContent = $('.chat-content');
                chatContent.empty();

                response.forEach(function(message) {
                    if (message.sent_message) {
                        chatContent.append(
                            '<div class="chat-message user-message text-right mb-2">' +
                                '<div class="message-content d-inline-block bg-primary text-white p-2 rounded">' +
                                    message.sent_message +
                                '</div>' +
                                '<div class="message-time small text-muted mt-1">' +
                                    message.time_of_message +
                                '</div>' +
                            '</div>'
                        );
                    }
                    if (message.reply_message) {
                        chatContent.append(
                            '<div class="chat-message admin-message text-left mb-2">' +
                                '<div class="message-content d-inline-block bg-light p-2 rounded">' +
                                    message.reply_message +
                                '</div>' +
                                '<div class="message-time small text-muted mt-1">' +
                                    message.time_of_message +
                                '</div>' +
                            '</div>'
                        );
                    }
                });

                chatContent.scrollTop(chatContent[0].scrollHeight);
            },
            error: function(xhr, status, error) {
                alert('Failed to fetch messages: ' + error);
            }
        });
    }

    setInterval(fetchMessages, 3000);

    $('#messageform').on('submit', function(e) {
        e.preventDefault();
        let message = $('input[name="message"]').val().trim();
        if (message) {
            $.ajax({
                url: 'handle.php',
                method: 'POST',
                data: { chatId: chatId, message: message },
                success: function(response) {
                    let newMessage = '<div class="chat-message user-message text-right mb-2">' +
                                        '<div class="message-content d-inline-block bg-primary text-white p-2 rounded">' +
                                            message +
                                        '</div>' +
                                        '<div class="message-time small text-muted mt-1">' +
                                        new Date().toLocaleString() +
                                        '</div>' +
                                    '</div>';
                    $('.chat-content').append(newMessage);
                    $('input[name="message"]').val('');
                    $('.chat-content').scrollTop($('.chat-content')[0].scrollHeight);
                },
                error: function(xhr, status, error) {
                    alert('Failed to send message: ' + error);
                }
            });
        }
    });

    fetchMessages();
});