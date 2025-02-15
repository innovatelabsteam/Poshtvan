jQuery(document).ready(function($){
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .content .item-content .topbar .site-data', e => {
        let mw_this = $(e.target).closest('.topbar')
        if(!$(e.target).closest('.data').length)
        {
            mw_this.find('.data').toggleClass('hide')
        }
    })
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .content .item-content .topbar .user-email', e => {
        let val = $(e.target).text()
        showNotice(mwtc.messages.successfully_copied, 'success');
        mwtcCopy(val)
    })
    $(document).on('click', '.admin-mihan-ticket .extra-fields .value', e => {
        let val = $(e.target).text()
        showNotice(mwtc.messages.successfully_copied, 'success')
        mwtcCopy(val)
    })
    function changeTicketStatus(ticketID, status)
    {
        let data = {
            action: 'mwtc_admin_change_ticket_status',
            status: status,
            ticket_id: ticketID
        }
        doAjax(
            data,
            response => {
                let type = 'error'
                if(response.status == 200)
                {
                    type = 'success'
                    $('.admin-mihan-ticket .mwtc_wrapper .sidebar .item[mwtc_id='+ticketID+'] .status').attr('class', `status ${status}`)
                }
                showNotice(response.msg, type)
            },
            err => {}
        )
    }
    $(document).on('change', '.admin-mihan-ticket .mwtc_wrapper .content .item-content .topbar .status select[name=ticket-status]', e => {
        let mw_this = $(e.target),
            value = mw_this.val(),
            ticketID = mw_this.closest('.item-content').attr('mwtc_item')
        changeTicketStatus(ticketID, value)
    })
    // solve btn
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .content .item-content .topbar .solve', e => {
        $(e.target).hide(300);
        let mw_this = $(e.target),
            ticketID = mw_this.closest('.item-content').attr('mwtc_item')
            statusField = mw_this.closest('.topbar').find('.status select[name=ticket-status]')
            statusField.val('answered')
        changeTicketStatus(ticketID, 'answered')
    })
    function handleTicketLink()
    {
        let ticketID = new URLSearchParams(location.search).get('ticket_id')
        if(ticketID)
        {
            reloadItemReplies(ticketID);
        }
    }
    handleTicketLink()
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .content .item-content .topbar .ticket_url', e => {
        let mw_this = $(e.target)
        mwtcCopy(mw_this.attr('mwtc_ticket_url'))
        showNotice(mwtc.messages.successfully_copied, 'success');
    });
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .sidebar .item', e => {
        $('.admin-mihan-ticket .mwtc_wrapper .content').html('<p class="mihanticket-wait">'+mwtc.messages.waiting+'</p>');
        let mw_this = $(e.target).closest('.item'),
            ticketID = mw_this.attr('mwtc_id')
        mw_this.closest('.sidebar').find('.item').removeClass('active')
        mw_this.addClass('active');
        reloadItemReplies(ticketID)
    })
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .sidebar .load-more.active', e => {
        loadMoreSidebarItems()
    });
    $(document).on('input', '.admin-mihan-ticket .mwtc_wrapper .sidebar .search input', e => {
        let mw_this = $(e.target),
            sidebar = mw_this.closest('.sidebar'),
            itemsWrapper = sidebar.find('.items-wrapper'),
            loadMore = sidebar.find('.load-more'),
            searchResultWrapper = sidebar.find('.search-result'),
            data = {
                action: 'mwtc_admin_search_in_ticket',
                search: mw_this.val()
            }
        if(!mw_this.val())
        {
            searchResultWrapper.html('')
            itemsWrapper.show()
            loadMore.show()
            return false
        }
        itemsWrapper.hide()
        loadMore.hide()
        doAjax(
            data,
            response => {
                searchResultWrapper.html(response.items);
            },
            err => {
                showNotice(mwtc.messages.has_error, 'error')
            }
        )
    })
    function reloadItemReplies(ticketID)
    {
        let data = {
            action: 'mwtc_ticket_item_content',
            ticket_id: ticketID
        }
        doAjax(
            data,
            response => {
                if(response.status == 200)
                {
                    $('.admin-mihan-ticket .mwtc_wrapper .content').html(response.content);
                }else{
                    showNotice(response.msg, 'error')
                }
            },
            err => {}
        )
    }
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .content .new-reply .submit-reply', e => {
        $('.admin-mihan-ticket .mwtc_wrapper .content .new-reply').hide();
        let mw_this = $(e.target),
            ticketID = mw_this.closest('.item-content').attr('mwtc_item'),
            ticketContent = mw_this.closest('.new-reply').find('textarea[name=submit-new-reply]').val(),
            fileField = mw_this.closest('.new-reply').find('.file_field input[type=file]')
        let data = {
            action: 'mwtc_admin_submit_new_reply',
            ticket_id: ticketID,
            ticket_content: ticketContent
        }
        let useFormData = false;
        let additionalArgs = {};
        if(fileField.val())
        {
            data.fileField = fileField.prop('files')[0];
            let fileVerification = verifyFile(fileField.prop('files')[0]);
            if(!fileVerification)
            {
                return false;
            }
            additionalArgs = {
                contentType: false,
                processData: false,
                cache: false,
            }
            useFormData = true;
        }
        additionalArgs.beforeSend = () => {
            mw_this.closest('.admin-mihan-ticket').addClass('deactive')
        }
        additionalArgs.complete = () => {
            mw_this.closest('.admin-mihan-ticket').removeClass('deactive')
        }
        doAjax(
            data,
            response => {
                let type = 'error'
                if(response.status == 200)
                {
                    $('.admin-mihan-ticket .mwtc_wrapper .sidebar .item[mwtc_id='+ticketID+'] .status').attr('class', 'status answered')
                    type = 'success'
                }
                showNotice(response.msg, type)
                reloadItemReplies(ticketID)
            },
            err => {},
            'json',
            additionalArgs,
            useFormData
        )
    })
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .content .replies .item .edit-ticket', e => {
        let mw_this = $(e.target),
            textWrapper = mw_this.closest('.item').find('.ticket-content-text'),
            textArea = $('<textarea>'),
            editModeWrapper = $('<div class="edit-wrapper">')
        textArea.val(textWrapper.text())
        editModeWrapper.append(textArea)
        editModeWrapper.append('<span class="action-btn ok dashicons dashicons-yes"></span>')
        editModeWrapper.append('<span class="action-btn cancel dashicons dashicons-no"></span>')
        textWrapper.hide()
        textWrapper.after(editModeWrapper)
    })
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .content .replies .item .edit-wrapper .action-btn.cancel', e => {
        let mw_this = $(e.target)
        mw_this.closest('.item').find('.ticket-content-text').show()
        mw_this.closest('.edit-wrapper').remove()
    })
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .content .replies .item .edit-wrapper .action-btn.ok', e => {
        let mw_this = $(e.target),
            newContent = mw_this.closest('.edit-wrapper').find('textarea').val(),
            ticketID = mw_this.closest('.item').attr('reply-id')
        let args = {
            action: 'mwtc_admin_edit_ticket',
            ticket_id: ticketID,
            new_content: newContent
        }
        doAjax(
            args,
            response => {
                let type = 'error',
                    contentWrapper = mw_this.closest('.item').find('.ticket-content-text')
                if(response.status == 200)
                {
                    type = 'success'
                    contentWrapper.html(response.new_content.replaceAll('\n', '<br>'))
                }
                contentWrapper.show()
                mw_this.closest('.edit-wrapper').remove()
                showNotice(response.msg, type)
            },
            err => {},
        )
    })
    function verifyFile(file)
    {
        if(file.size > mwtc.max_allowed_file_size)
        {
            showNotice(mwtc.messages.invalid_file_size, 'error')
            return false;
        }
        return true;
    }
    function loadMoreSidebarItems()
    {
        let loadMore = $('.admin-mihan-ticket .mwtc_wrapper .sidebar .load-more'),
            offset = loadMore.attr('mwtc_offset'),
            data = {
                action: 'mwtc_load_sidebar_items',
                offset: offset
            },
            filterItems = $('.admin-mihan-ticket .mwtc_wrapper .sidebar .filter-items-wrapper .filter-items input[name=filter-ticket-status]:checked')

        if(filterItems.length)
        {
            let filtersIndex = []
            filterItems.each((index, value) => {
                filtersIndex.push(value.value);
            })
            data.filters = filtersIndex
        }
        doAjax(
            data,
            response => {
                $('.admin-mihan-ticket .mwtc_wrapper .sidebar .items-wrapper').append(response.items)
                if(response.new_offset)
                {
                    loadMore.attr('mwtc_offset', response.new_offset)
                }
                if(response.end)
                {
                    loadMore.text(response.end)
                    loadMore.removeClass('active');
                }
            },
            err => {}
        )
    }
    function loadSidebarItems()
    {
        let itemsWrapper = $('.admin-mihan-ticket .mwtc_wrapper .sidebar .items-wrapper'),
            filterItems = $('.admin-mihan-ticket .mwtc_wrapper .sidebar .filter-items-wrapper .filter-items input[name=filter-ticket-status]:checked'),
            data = {
                action: 'mwtc_load_sidebar_items',
            },
            loadMore = $('.admin-mihan-ticket .mwtc_wrapper .sidebar .load-more')

        if(filterItems.length)
        {
            let filtersIndex = []
            filterItems.each((index, value) => {
                filtersIndex.push(value.value);
            })
            data.filters = filtersIndex
        }
            
        itemsWrapper.html('<p style="color:#fff;font-size:20px;text-align:center;display:block">'+mwtc.messages.loading+'</p>')
        itemsWrapper.trigger('before_start_loading_items')
        doAjax(
            data,
            response => {
                itemsWrapper.html(response.items)
                loadMore.show()
                if(response.new_offset)
                {
                    loadMore.attr('mwtc_offset', response.new_offset)
                }
                if(response.end)
                {
                    loadMore.text(response.end)
                    loadMore.removeClass('active');
                }

                itemsWrapper.trigger('loading_items_request_done')
            },
            err => {}
        )
    }
    loadSidebarItems()
    function doAjax(data, success, error, dataType, additionalArgs, useFormData)
    {
        data['nonce'] = mwtc.nonce
        dataType = dataType !== undefined ? dataType : 'json'
        additionalArgs = additionalArgs !== undefined ? additionalArgs : false;
        useFormData = useFormData !== undefined ? useFormData : false;
        let args = {
            url: mwtc.au,
            type: 'post',
            dataType: dataType,
            data: data,
            success: response => {success(response)},
            error: err => {error(err)}
        }
        if(additionalArgs)
        {
            $.extend(args, additionalArgs);
        }
        if(useFormData)
        {
            let formData = new FormData();
            $.each(args.data, function(index, value){
                formData.append(index, value);
            });
            args.data = formData;
        }
        $.ajax(args)
    }
    function showNotice(msg, type)
    {
        let alertWrapper = $('.admin-mihan-ticket .mihanticket-alerts-wrapper')
        type = type == undefined ? 'success' : type;
        let item = $('<div>');
        item.addClass('alert-item ' + type + '-alert');
        item.text(msg)
        alertWrapper.append(item)
        setTimeout(() => {
            item.fadeOut('slow', function(){
                item.remove()
            })
        }, 3000);
    }
    function mwtcCopy(value)
    {
        let tmp = $('<input />')
        tmp.val(value)
        $('body').append(tmp)
        tmp.select()
        document.execCommand('copy')
        tmp.remove()
    }
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .content .ticket-content-wrapper .new-reply .mihanticket-responses .mihanticket-response .title', function(e){
        let mw_this = $(this),
            response_content = mw_this.parent().find('.content').text()
            reply_content_wrapper = $('#replytextareaid')
            reply_content_wrapper.val(response_content)
    })

    // click on apply filters btn
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .sidebar .filter-items-wrapper span.apply-filters', function(e){
        loadSidebarItems()
    })

    // handle click on filter-item tag
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .sidebar .filter-items-wrapper .filter-items .filter-item input[type=checkbox]', function(e){
        let el = $(this)
        
        if(el.val() == 'all' && el.is(':checked'))
        {
            // deselect other btns
            el.closest('.filter-items').find('.filter-item input[name=filter-ticket-status]:not([value=all])').prop('checked', false)
        }else{
            // deselect "all" btn
            el.closest('.filter-items').find('.filter-item input[name=filter-ticket-status][value=all]').prop('checked', false)
        }
        console.log(el.val(), el.is(':checked'));
    })

    // disable apply filter btn before loading ticket items
    $(document).on('before_start_loading_items', '.admin-mihan-ticket .mwtc_wrapper .sidebar .items-wrapper', function(e){
        $(this).closest('.sidebar').find('.filters-section .apply-filters').addClass('disable')
    })

    // enable apply filter btn before loading ticket items
    $(document).on('loading_items_request_done', '.admin-mihan-ticket .mwtc_wrapper .sidebar .items-wrapper', function(e){
        $(this).closest('.sidebar').find('.filters-section .apply-filters').removeClass('disable')
    })

    // when click on filter btn
    $(document).on('click', '.admin-mihan-ticket .mwtc_wrapper .sidebar .search .filter-icon', function(e){
        let el = $(this)
        el.closest('.sidebar').find('.filters-section').slideToggle()
    })
});