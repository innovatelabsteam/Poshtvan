<div class="pv-chat-widget-wrap is-<?php echo is_rtl() ? 'rtl' : 'ltr' ?>">
    <div class="pv-chat-contents">
        <ul class="pv-chat-list">
            <li class="first-chat-item">
                <div>
                    <?php esc_html_e('You can start chatting with the bot.', 'poshtvan') ?>
                </div>
            </li>
        </ul>
    </div>

    <form id="pv-send-message-form">
        <div>
            <textarea required id="pv-chat-msg" name="chat_message" rows="1" placeholder="<?php esc_html_e('Your question...', 'poshtvan') ?>"></textarea>
            <button type="submit">
                <svg fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                </svg>
            </button>
        </div>
    </form>
</div>

<script type="text/html" id="pv-chat-item-template">
    <li class="pv-chat-item pv-chat-{{role}}-item">
        <div class="pv-chat-content">
            <div class="pv-chat-text">
                {{message}}
            </div>
        </div>
    </li>
</script>