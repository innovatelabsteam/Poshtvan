jQuery(document).ready(function ($) {
    $('#mwtc_faq_items .faq-items-wrapper').sortable(
        {
            animation: 150,
            containment: "parent",
            cursor: "move",
            scroll: false,
            axis: "y",
            start: function (e, ui) {
                $(ui.item).find('textarea').each(function () {
                    tinymce.execCommand('mceRemoveEditor', false, $(this).attr('id'));
                });
            },
            stop: function (e, ui) {
                $(ui.item).find('textarea').each(function () {
                    tinymce.execCommand('mceAddEditor', true, $(this).attr('id'));
                });
            }
        });


    $(document).on('click', '#mwtc_faq_items .faq-toolbar #faq-add-new-item', function (e) {
        let el = $(this),
            itemsWrapper = $('#mwtc_faq_items .faq-items-wrapper'),
            randomID = Math.random().toString(36).substring(2)

        $.ajax({
            url: mwtc_faq.au,
            dataType: 'json',
            type: 'post',
            data: {
                action: 'mwtc_admin_faq_get_new_item_view',
                new_id: randomID,
            },
            beforeSend: () => {
                el.addClass('deactive')
                el.find('.icon .dashicons').attr('class', 'dashicons dashicons-update-alt')
            },
            complete: () => {
                el.removeClass('deactive')
                el.find('.icon .dashicons').attr('class', 'dashicons dashicons-plus')
            },
            success: response => {
                if (response.status == 200) {
                    itemsWrapper.append(response.field_view)
                    wp.editor.initialize(
                        'faq-item-content-' + randomID,
                        {
                            tinymce: {
                                wpautop: true,
                                plugins: 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
                                toolbar1: 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright | link unlink | wp_more | spellchecker'
                            },
                            quicktags: true
                        }
                    );
                }
            },
        })
    })

    $(document).on('click', '#mwtc_faq_items .faq-items-wrapper .faq-item .faq-item-title .trash-icon', function (e) {
        let el = $(this),
            items = el.closest('.faq-items-wrapper').find('.faq-item')

        if (items.length > 1) {
            $(this).closest('.faq-item').remove()
        } else {
            alert(mwtc_faq.msg.items_min_error)
            return false
        }
    })

    $(document).on('input', '#mwtc_faq_items .faq-items-wrapper .faq-item .faq-item-content .faq-item-title-field', function (e) {
        let el = $(this)
        el.closest('.faq-item').find('.faq-item-title .value').text(el.val())
    })

    $(document).on('click', '#mwtc_faq_items .faq-items-wrapper .faq-item .faq-item-title', function (e) {
        let el = $(this)

        let titles = el.closest('.faq-items-wrapper').find('.faq-item .faq-item-title'),
            contents = el.closest('.faq-items-wrapper').find('.faq-item .faq-item-content'),
            currentContent = el.closest('.faq-item').find('.faq-item-content')

        if (el.hasClass('open')) {
            currentContent.slideUp()
            el.removeClass('open')
            return
        }
        titles.removeClass('open')
        contents.slideUp()


        el.closest('.faq-item').find('.faq-item-content').slideToggle()
        el.addClass('open')
    })
})