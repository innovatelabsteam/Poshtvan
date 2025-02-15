jQuery(document).ready(function($){
    // functions
    function update_confirm_notice_state(show=true)
    {
        let update_confirm = $('.notice-wrapper .update-confirm')
        if(show)
        {
            update_confirm.slideDown();
        }else{
            update_confirm.slideUp()
        }
    }
    function showNotice(msg, type='error')
    {
        let noticeBar = $('.notice-wrapper .notices')
        if(typeof msg == 'object')
        {
            msg.forEach(element => {
                noticeBar.append(`<span class="alert ${type}">${element}</span>`)
            });
        }else if(typeof msg == 'string')
        {
            noticeBar.append(`<span class="alert ${type}">${msg}</span>`)
        }
        noticeBar.slideDown()
        setTimeout(() => {
            noticeBar.slideUp('slow', function(){
                noticeBar.html('')
            })
        }, 3000);
    }
    // events
    $('#new-item-btn').on('click', function(e){
        let form = $('#new-record-form')
        if(form.hasClass('hide'))
        {
            form.slideDown()
        }else{
            form.slideUp()
        }
        form.toggleClass('hide')
    })
    $(document).on('input', '.mw_sortable input', function(e){
        update_confirm_notice_state()
    })
    $(document).on('change', '.mw_sortable select', function(e){
        update_confirm_notice_state()
    })
    $(document).on('keypress', '.mw_sortable input', function(e){
        if(e.which == 13)
        {
            e.preventDefault();
            $('.mw_ajax_update_fields_data').trigger('click');
        }
    });

    $('.mw_ajax_update_fields_data').on('click', function(e){
        let field_data = $('.mw_sortable .mw_field_item')
        let form_data = [];
        field_data.each((index, value) => {
            let this_value = $(value),
                form = this_value.serialize()
            form_data.push(form)
        })
        $.ajax({
            url: mwtc.au,
            type: 'post',
            dataType: 'json',
            data: {
                action: 'mwtc_admin_fields_update',
                fields: form_data,
            },
            success: response => {
                let type = 'error';
                if(response.status == 200)
                {
                    type = 'success';
                }
                showNotice(response.msg, type)
                update_confirm_notice_state(false)
            },
            error: err => {
                alert('Has Error')
            }
        })
    })
    $('.mw_sortable .mw_field_item .remove').on('click', function(e){
        let mwthis = $(this),
            fieldWrapper = mwthis.closest('.mw_field_item'),
            field_id = fieldWrapper.find('input[name=id]').val()
        if(!field_id)
        {
            return
        }
        // dashicons-ellipsis
        mwthis.find('.dashicons').attr('class', 'dashicons dashicons-ellipsis')
        $.ajax({
            url: mwtc.au,
            type: 'post',
            dataType: 'json',
            data: {
                action: 'mwtc_admin_delete_field_item',
                field_id: field_id
            },
            success: response => {
                let type = 'error'
                if(response.status == 200)
                {
                    type = 'success'
                    fieldWrapper.fadeOut('slow', function(){
                        fieldWrapper.remove()
                    })
                }else{
                    mwthis.find('.dashicons').attr('class', 'dashicons dashicons-no')
                }
                showNotice(response.msg, type)
            },
            error: err => {
                alert('Has Error')
            }
        })
    })

    // invoke
    $('.mw_sortable').sortable({
        cursor: 'move',
        handle: '.dashicons-menu',
        update: update_confirm_notice_state
    });
})