jQuery(document).ready(function($){
    var mwtc_avatar_uploader;

    let selects = $(document).find('.select2');

    if(selects.length){
        selects.select2();
    }
    
    
    $("#open_operator_avatar_media").on('click', function(){
        let mwthis = $(this)
        if(mwtc_avatar_uploader)
        {
            mwtc_avatar_uploader.open()
            return;
        }
        mwtc_avatar_uploader = wp.media()
        mwtc_avatar_uploader.open()
        mwtc_avatar_uploader.on('select', function(){
            let attachment = mwtc_avatar_uploader.state().get('selection').first().toJSON()
            if(attachment.sizes.thumbnail)
            {
                mwthis.find('img').attr('src', attachment.sizes.thumbnail.url)
            }else{
                mwthis.find('img').attr('src', attachment.url)
            }
            $('#operator_avatar_image_id').val(attachment.id)
            mwthis.parent().find('#remove_avatar').show()
        })
    })
    $('#remove_avatar').on('click', function(){
        let mwthis = $(this),
            avatar_preview = mwthis.parent().find('.avatar-preview-section img'),
            default_avatar_url = avatar_preview.attr('mwtc_default')
        avatar_preview.attr('src', default_avatar_url)
        mwthis.parent().find('#operator_avatar_image_id').val('')
        mwthis.hide()
    })
    // start handle deps attr
    let has_deps = $('[mwdeps]')
    if(has_deps.length)
    {
        has_deps.each((index, item) => {
            let el = $(item),
                deps = el.attr('mwdeps'),
                deps_el = $('#' + deps)
            if(deps_el.length)
            {
                deps_el.on('change', {item: el}, handle_deps_changes).change()
            }
        })
    }
    function handle_deps_changes(e)
    {
        let mwthis = $(this),
            isChecked = mwthis.is(':checked')
        if(isChecked)
        {
            e.data.item.fadeIn()
        }else{
            e.data.item.find('input[type=checkbox]').prop('checked', false).change()
            e.data.item.fadeOut()
        }
    }
    // end handle deps attr

    // start ticket status field items
    $(document).on('click', '#ticket_custom_status_field #add_new_ticket_status', function(e){
        let el = $(this),
            optionField = $('<div>'),
            slugField = $('<input />'),
            nameField = $('<input />'),
            icon = $('<span>')
            
        optionField.addClass('option_field option_row_field no-padding')
        slugField.attr('type', 'text').attr('placeholder', poshtvan_settings.texts.slug).attr('name', 'mwtc_poshtvan_ticket_custom_status[slug][]')
        nameField.attr('type', 'text').attr('placeholder', poshtvan_settings.texts.name).attr('name', 'mwtc_poshtvan_ticket_custom_status[name][]')
        icon.attr('class', 'dashicons dashicons-trash delete-field')

        optionField.append(slugField).append(nameField).append(icon)
        
        el.closest('#ticket_custom_status_field').find("#custom_status_fields_wrapper").append(optionField)
    })
    $(document).on('click', '#ticket_custom_status_field #custom_status_fields_wrapper .delete-field', function(e){
        if(!confirm(poshtvan_settings.texts.delete_field_alert))
        {
            return false
        }
        $(this).closest('.option_field').remove()
    })
    // end ticket status field items

    // start auto-ticket field items
    $(document).on('click', '#auto_ticket_field #add_new_auto_ticket_item', function(e){
        let el = $(this),
            optionField = $('<div>'),
            selectField = $('<select />'),
            contentField = $('<textarea />'),
            icon = $('<span>')
            
        optionField.addClass('option_field option_row_field no-padding')
        selectField.attr('name', 'mwtc_poshtvan_auto_ticket_item[status][]')
        contentField.attr('placeholder', poshtvan_settings.texts.ticket_content).attr('name', 'mwtc_poshtvan_auto_ticket_item[content][]')
        icon.attr('class', 'dashicons dashicons-trash delete-field')

        optionField.append(selectField).append(contentField).append(icon)

        if(poshtvan_settings.data.ticket_status_list)
        {
            $.each(poshtvan_settings.data.ticket_status_list, (index, item) => {
                selectField.append($('<option>', {value: item.name, text: item.title}))
            })
        }
        
        el.closest("#auto_ticket_field").find("#auto_ticket_items_fields_wrapper").append(optionField)
    })
    $(document).on('click', '#auto_ticket_field #auto_ticket_items_fields_wrapper .delete-field', function(e){
        if(!confirm(poshtvan_settings.texts.delete_field_alert))
        {
            return false
        }
        $(this).closest('.option_field').remove()
    })
    // end auto-ticket field items

    // start sms providers settings
    $(document).on('change', 'select#active_sms_provider', function (e) {
        let el = $(this),
            settingsWrapper = el.closest('#poshtvan-option-panel-form').find('.poshtvan_sms_provider_settings'),
            tmp = $('<span>')

        tmp.addClass('loading')
        tmp.text(poshtvan_settings.texts.sms_provider_settings_loading)
        settingsWrapper.html(tmp);
        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'mw_poshtvan_get_sms_provider_settings',
                provider: this.value
            },
            success: function (response) {
                settingsWrapper.html(response);
            }       
        });
    });
    // end sms providers settings

    $(document).on('click', '#connect-hooshina-btn', function (e){
        e.preventDefault();
        let btn = $(this),
            loader = btn.find('.pv-loader');

       $.ajax({
           url: ajaxurl,
           data: {
               action: 'pv_connect_to_hooshina'
           },
           type: 'POST',
           dataType: 'json',
           beforeSend: function (){
               btn.addClass('pv-has-opacity-loader');
               loader.show();
           },
           complete: function (res){
               res = res.responseJSON;

               if (res.hasOwnProperty('data')){
                   if(res.data.hasOwnProperty('redirect')){
                       window.location.href = res.data.redirect;
                       return true;
                   }
               }

               btn.removeClass('pv-has-opacity-loader');
               loader.hide();
           },
           error: function (){
               btn.removeClass('pv-has-opacity-loader');
               loader.hide();
           }
       });
    });

    $(document).on('click', '#disconnect-hooshina-btn', function (e){
        e.preventDefault();
        let btn = $(this),
            loader = btn.find('.pv-loader');

        $.ajax({
            url: ajaxurl,
            data: {
                action: 'pv_disconnect_hooshina'
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function (){
                btn.addClass('pv-has-opacity-loader');
                loader.show();
            },
            complete: function (res){
                res = res.responseJSON;

                if (res.hasOwnProperty('success')){
                    location.reload();
                    return true;
                }

                btn.removeClass('pv-has-opacity-loader');
                loader.hide();
            },
            error: function (){
                btn.removeClass('pv-has-opacity-loader');
                loader.hide();
            }
        });
    });
})