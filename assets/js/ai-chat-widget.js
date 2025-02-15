jQuery(document).ready(function ($){
    $(document).on('submit', '#pv-send-message-form', function (e){
        e.preventDefault();
        let form = $(this),
            messageContentField = form.find('#pv-chat-msg'),
            messageValue = messageContentField.val(),
            chatList = $('.pv-chat-list'),
            firstContent = chatList.find('.first-chat-item');

        if (form.hasClass('is-locked') || !messageValue)
            return false;

        let addLoaderMessage = function() {
            chatList.append(
                $('<li/>', {class: 'pv-chat-item pv-chat-loading'}).append(
                    $('<div/>', {class: 'pv-chat-content'}).append(
                        $('<div/>', {class: 'pv-chat-text'})
                    )
                )
            );
        }

        let removeLoaderMessage = function (){
            chatList.find('.pv-chat-loading').remove();
        }

        let addMessage = function (message, role){
            let itemTemplate = $('#pv-chat-item-template').html();
            itemTemplate = itemTemplate.replace('{{role}}', role);
            itemTemplate = itemTemplate.replace('{{message}}', marked.parse(message));
            chatList.append(itemTemplate);
            messageContentField.val('');
        }

        $.ajax({
            url: pv_data.au,
            data: "action=pv_chat_with_ai&" + form.serialize(),
            type: 'POST',
            dataType: 'json',
            beforeSend: function (){
                form.addClass('is-locked');
                firstContent.remove();
                addMessage(messageValue, 'user');
                addLoaderMessage();
                pvChatScrollToBottomA();
            },
            complete: function (res){
                form.removeClass('is-locked');

                res = res.responseJSON;

                if(res.hasOwnProperty('data') && res.success){
                    addMessage(res.data.answer, 'bot');
                } else if(res.data.hasOwnProperty('msg')) {
                    pvToastChatMessage(res.data.msg, 'error');
                } else {
                    pvToastChatMessage(pv_data.messages.has_error, 'error');
                }

                removeLoaderMessage();
                pvChatScrollToBottomA();
            },
            error: function (){
                form.removeClass('is-locked');
                removeLoaderMessage();
                pvToastChatMessage(pv_data.messages.has_error, 'error');
                pvChatScrollToBottomA();
            }
        })
    });

    $(document).on('keydown', '#pv-send-message-form textarea',function(event) {
        const enterKey = event.key === 'Enter' || event.keyCode === 13;

        if (enterKey) {
            if (event.shiftKey) {
                event.preventDefault();
                const cursorPosition = this.selectionStart;
                const value = $(this).val();
                $(this).val(value.substring(0, cursorPosition) + '\n' + value.substring(cursorPosition));
                this.selectionStart = this.selectionEnd = cursorPosition + 1;
            } else {
                event.preventDefault();
                $(this).closest('form').submit();
            }
        }
    });
});

const pvToastChatMessage = function (message, type = 'info'){
    let wrap = jQuery('.pv-chat-contents');

    wrap.find('.pv-chat-toast').remove();
    wrap.append(jQuery('<div/>', {class:'pv-chat-toast', 'type': type}).append(
        jQuery('<p/>', {text: message})
    ));

    setTimeout(function (){
        wrap.find('.pv-chat-toast').fadeOut(function (){
            wrap.find('.pv-chat-toast').remove();
        });
    }, 5000);
}

const pvChatScrollToBottomA = function () {
    setTimeout(function() {
        const box = document.querySelector('.pv-chat-contents');
        if (box) {
            box.scrollTo({
                top: box.scrollHeight,
                behavior: 'smooth'
            });
        }
    }, 600);
}