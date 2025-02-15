jQuery(document).ready(function($){
    let selects = $(document).find('.mihanticket-select');

    if(selects.length){
        selects.select2({
            width: '100%'
        });
    }

    function gotoStep(newStep)
    {
        let allStepsList = $(`.mihanticket-step-list`),
            stepItem = allStepsList.find(`#mwtc-step-${newStep}`),
            allSteps = $('.mihanticket .mihanticket-step'),
            stepSection = $(`.mihanticket .mihanticket-step[step=${newStep}]`)

        allStepsList.find('> span').removeClass('active')
        stepItem.addClass('active')

        allSteps.removeClass('active')
        stepSection.addClass('active')
    }
    function toggleNewTicketForm()
    {
        let newTicketSection = $('.mihanticket .new-ticket'),
            newTicketForm = newTicketSection.find('.form'),
            newTicketBtnHandler = newTicketSection.find('.open-new-ticket-form')
        newTicketForm.slideToggle(function(){
            newTicketBtnHandler.toggleClass('close-mode')
            if(newTicketBtnHandler.hasClass('close-mode'))
            {
                newTicketBtnHandler.text(newTicketBtnHandler.attr('mwtc_close_mode_text'))
                newTicketSection.trigger('mwtc_new_ticket_form_status_changed', 'open')
            }else{
                newTicketBtnHandler.text(newTicketBtnHandler.attr('mwtc_open_mode_text'))
                newTicketSection.trigger('mwtc_new_ticket_form_status_changed', 'close')
            }
        })
    }
    $(document).on('mwtc_new_ticket_form_status_changed', '.mihanticket .new-ticket', function(e, status){
        let ticketList = $('.mihanticket .mihanticket-list'),
            loadMore = $('.mihanticket .load-more')
        if(status === 'open')
        {
            // unchecked orders
            $(document).find('.mihanticket .mihanticket-step .mwtc-orders-list input[name=mihanticket_woocommerce_order]:checked').prop('checked', false)
            
            ticketList.slideUp()
            loadMore.hide()
        }else{
            ticketList.slideDown()
            loadMore.show()
        }
    })
    $('.open-new-ticket-form').on('click', e => {
        toggleNewTicketForm()
    })

    // faq items click
    $(document).on('click', '.mihanticket .mihanticket-step[step=faq-items] .mwtc-faq-item', function(e){
        let el = $(this)
        // el.closest('.mwtc-faq-items').find('.mwtc-faq-item').removeClass('open')
        // el.addClass('open')
        if(el.hasClass('open'))
        {
            el.closest('.mwtc-faq-items').find('.mwtc-faq-item .mwtc-faq-item-content').slideUp()
            el.removeClass('open')
            el.find('.mwtc-faq-icon').removeClass('mwtc-rotate')
        }else{
            el.closest('.mwtc-faq-items').find('.mwtc-faq-item').removeClass('open')
            el.closest('.mwtc-faq-items').find('.mwtc-faq-item .mwtc-faq-item-content').slideUp()
            el.find('.mwtc-faq-item-content').slideDown()
            el.addClass('open')
            el.find('.mwtc-faq-icon').addClass('mwtc-rotate')
        }

    });
    $(document).on('click', '.mihanticket .mihanticket-step .change-step-btn', function(e){
        let el = $(this),
            step = el.attr('step')

        gotoStep(step)
    })
    // select order items
    $(document).on('click', '.mihanticket .mihanticket-step .mwtc-orders-list input[name=mihanticket_woocommerce_order]', function(e){
        $(this).change()
    })
    $(document).on('change', '.mihanticket .mihanticket-step .mwtc-orders-list input[name=mihanticket_woocommerce_order]', function(e){
        let el = $(this)
        if(el.hasClass('mwtc_doing'))
        {
            return
        }

        // get FAQ content via ajax
        // AND put FAQ content to FAQ Step Section

        let productID = el.attr('prid')
        let data = {
            action: 'mwtc_get_product_faq_items',
            product_id: productID
        }
        doAjax(
            data,
            response => {
                if(response.status == 200)
                {
                    $('.mihanticket .mihanticket-step[step=faq-items]').html(response.faq_view)
                }
            },
            error => {
                alert('Has error')
            },
            'json',
            {
                beforeSend: () => {
                    el.addClass('mwtc_doing');
                    // show spinner in faq-step content
                    let spinner = $('<span>'),
                        spinnerWrapper = $('<div>')

                    spinnerWrapper.addClass('mwtc-spinning-wrapper')
                    spinner.addClass('dashicons dashicons-update mwtc-spinning')
                    spinnerWrapper.html(spinner)

                    $('.mihanticket .mihanticket-step[step=faq-items]').html(spinnerWrapper)
                },
                complete: () => {
                    el.removeClass('mwtc_doing')
                }
            }
        )
        // goto FAQ step
        gotoStep('faq-items')
    })
    // search subject in website content
    $(document).on('input', '.mihanticket .mihanticket-step[step=ticket-subject] input[name=ticket-subject]', function(e){
        let el = $(this),
            data= {
                action: 'mwtc_search_subject',
                search: el.val()
            }
        doAjax(
            data,
            response => {
                if(response.status == 200)
                {
                    el.closest('.mihanticket-step').find('.ticket-search-result-wrapper').html(response.view)
                }
            },
            error => {
                alert('Has error')
            },
        )
    })
    $('.submit-new-ticket').on('click', function(e){
        let mw_this = $(this),
            formWrapper = mw_this.closest('.form'),
            title = formWrapper.find('input[name=ticket-subject]').val(),
            content = formWrapper.find('textarea[name=ticket-content]').val(),
            fileField = formWrapper.find('.file_field input[type=file]'),
            extraFields = formWrapper.find('.extra_fields input, .extra_fields select'),
            product_id = $(document).find('.mihanticket .mihanticket-step .mwtc-orders-list input[name=mihanticket_woocommerce_order]:checked')
        let data = {
            action: 'mwtc_submit_new_ticket',
            title: title,
            content: content,
            product_id: product_id.attr('prid'),
            mihanticket_woocommerce_order: product_id.attr('orderid'),
        }
        if(extraFields.length)
        {
            extraFields.each((index, item) => {
                let item_el = $(item)
                if(item_el.val() && item_el.val().length)
                {
                    data[item_el.attr('name')] = item_el.val()
                }
            })
        }
        let useFormData = false,
            additionalArgs = {};
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
        let btnText = mw_this.find('.btn-text')
        btnText.hide()
        mw_this.append('<span class="dashicons dashicons-ellipsis"></span>')
        mw_this.addClass('loading')
        doAjax(
            data,
            response => {
                let type = 'error'
                if(response.status == 200)
                {
                    type = 'success'
                    showTicketList()
                    toggleNewTicketForm()
                    $('.mihanticket .mihanticket-list').slideDown()
                    $('.mihanticket .open-new-ticket-form').hide()
                }
                if(typeof response.msg === 'object')
                {
                    response.msg.forEach(msgItem => {
                        showNotice(msgItem, type)
                    })
                }else{
                    showNotice(response.msg, type)
                }
                btnText.show()
                mw_this.find('.dashicons').remove()
                mw_this.removeClass('loading')
            },
            err => {
                showNotice('Sorry, has error in your request', 'error')
            },
            'json',
            additionalArgs,
            useFormData
        )
    })
    $(document).on('click', '.mihanticket-list .items .item', e => {
        let mw_this = $(e.target).closest('.item'),
            inner = mw_this.find('.inner-data'),
            loadMore = $('.mihanticket .load-more')
        let ticketStatus = mw_this.attr('ticket-item-status')
        if(!$(e.target).closest('.inner-data').length)
        {
            let allItems = $('.mihanticket-list .items .item')
            allItems.attr('ticket-item-status', 'close').find('.inner-data').slideUp()
            if(ticketStatus == 'open')
            {
                // must collapse
                mw_this.attr('ticket-item-status', 'close')
                inner.slideUp()
                loadMore.show()
                mw_this.trigger('mwtc_ticket_item_collapse')
            }else{
                // must expand
                mw_this.attr('ticket-item-status', 'open')
                inner.slideDown()
                loadMore.hide()
                mw_this.trigger('mwtc_ticket_item_expand')
            }
        }
    })
    $(document).on('mwtc_ticket_item_expand', '.mihanticket-list .items .item', e => {
        // load replies
        // hide other items
        let mw_this = $(e.target)
        mw_this.closest('.items').find('.item').not(mw_this).hide()
        reloadReplies(mw_this)
    })
    $(document).on('mwtc_ticket_item_collapse', '.mihanticket-list .items .item', e => {
        // show all items
        let mw_this = $(e.target)
        mw_this.closest('.items').find('.item').show()
    })
    $(document).on('click', '.mihanticket-list .items .item .action-bar .back-btn', e=>{
        $(e.target).closest('.item').trigger('click')
    })
    $(document).on('click', '.mihanticket-list .items .item .new-reply .submit-reply', e => {
        let mw_this = $(e.target),
            contentElem = mw_this.closest('.new-reply').find('textarea[name=new_reply]'),
            ticketElem = mw_this.closest('.item'),
            ticketId = ticketElem.attr('mwtc_id'),
            fileField = mw_this.closest('.new-reply').find('input[type=file]')

        let data = {
            action: 'mwtc_submit_reply',
            ticket_id: ticketId,
            content: contentElem.val()
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
        doAjax(
            data,
            response => {
                let type = 'error'
                if(response.status == 200)
                {
                    $(".new-reply").hide(300);
                    type = 'success'
                    reloadReplies(ticketElem)
                    contentElem.val('')
                    fileField.val('')
                }
                showNotice(response.msg, type)
            },
            err => {},
            'json',
            additionalArgs,
            useFormData
        )
    });
    $(document).on('click', '.mihanticket-list .items .item .action-bar .solve-btn', e => {
        $(e.target).hide(300);
        let mw_this = $(e.target),
            ticketId = mw_this.closest('.item').attr('mwtc_id')
        let data = {
            action: 'mwtc_ticket_solved',
            ticket_id: ticketId
        }
        doAjax(
            data,
            response => {
                if(response.status == 200)
                {
                    mw_this.addClass('solved')
                    let statusElem = mw_this.closest('.item').find('.status')
                    statusElem.html('<span>' + response.status_data['title'] + '</span>')
                    statusElem.attr('class', 'status ' + response.status_data['name'])
                }
            },
            err => {}
        )
    })

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
    function verifyFile(file)
    {
        if(file.size > mwtc.max_allowed_file_size)
        {
            showNotice(mwtc.messages.invalid_file_size, 'error')
            return false;
        }
        return true;
    }
    function showNotice(msg, type)
    {
        let alertWrapper = $('.mihanticket .mihanticket-alerts-wrapper')
        type = type == undefined ? 'success' : type;
        let item = $('<div>');
        item.addClass('alert-item ' + type);
        item.text(msg)
        alertWrapper.append(item)
        setTimeout(() => {
            item.fadeOut('slow', function(){
                item.remove()
            })
        }, 5000);
    }
    function reloadItemsWrapper(newContent, append)
    {
        let items = $('.mihanticket .mihanticket-list > .items')
        append = append !== undefined ? true : false;
        if(append)
        {
            items.append(newContent)
        }else{
            items.html(newContent)
        }
    }
    function reloadReplies(itemElem)
    {
        // load replies
        let repliesWrapper = itemElem.find('.inner-data .replies')
        repliesWrapper.html('<p class="mihanticket-wait">'+mwtc.messages.waiting+'</p>')
            ticketId = itemElem.attr('mwtc_id')

        let data = {
            action: 'get_ticket_replies',
            ticket_id: ticketId
        }
        doAjax(
            data,
            response => {
                let statusElem = itemElem.find('.status')
                statusElem.html('<span>' + response.ticket_status['title'] + '</span>')
                statusElem.attr('class', 'status ' + response.ticket_status['name'])
                repliesWrapper.html(response.replies)
            },
            err => {}
        )
    }
    $(document).on('click', '.mihanticket .load-more .action', e => {
        loadMoreTicket()
    })
    function loadMoreTicket()
    {
        let loadMore = $('.mihanticket .load-more'),
            offset = loadMore.attr('mwtc_offset'),
            data = {
                action: 'show_user_ticket_list',
                offset: offset
            }
        let actionContent = loadMore.find('.action')
        actionContent.hide()
        loadMore.append('<span class="loading dashicons dashicons-ellipsis"></span>');
        doAjax(
            data,
            response => {
                if(response.items)
                {
                    reloadItemsWrapper(response.items, true)
                }
                if(response.new_offset)
                {
                    loadMore.attr('mwtc_offset', response.new_offset)
                }
                if(response.end)
                {
                    actionContent.removeClass('action').text(response.end)
                }
                loadMore.find('.loading').remove()
                actionContent.show()
            },
            err => {},
        );
    }
    function showTicketList()
    {
        let loadMore = $('.mihanticket .load-more'),
            data = {
                action: 'show_user_ticket_list'
            }
        doAjax(
            data,
            response => {
                reloadItemsWrapper(response.items)
                if(response.new_offset)
                {
                    loadMore.attr('mwtc_offset', response.new_offset)
                }
                if(response.end)
                {
                    loadMore.find('.action').removeClass('action').text(response.end)
                }
            },
            err => {}
        );
    }
    showTicketList();


    $(document).on('click', '.submit-ticket-order', function(){
        let btn = $(this),
            select = btn.parent().find('select#mihanticket_woocommerce_order'),
            selected = select.find('option:selected'),
            ticket_id = btn.data('ticket-id'),
            order_id = selected.val(),
            product_id = selected.data('product-id');

        if(!order_id || !product_id){
            showNotice(mwtc.messages.invalid_order, 'error');
            return false;
        }

        $.ajax({
            url: mwtc.au,
            data: {
                action: 'poshtvan_resubmit_ticket_order_number',
                ticket_id: ticket_id,
                product_id: product_id,
                order_id: order_id,
                nonce: mwtc.nonce
            },
            dataType: 'json',
            type: 'POST',
            beforeSend: function(){
                showNotice(mwtc.messages.waiting, 'warning');
                select.parent().parent().addClass('has-loading');
            },
            success: function(res){
                if(res.status == 200){
                    if(res.has_error === undefined){
                        location.reload();
                    }
                } else {
                    select.parent().parent().removeClass('has-loading');
                }

                showNotice(res.msg, res.type);
            },
            error: function(){
                showNotice(mwtc.messages.invalid_order, 'error');
                select.parent().parent().removeClass('has-loading');
            },
        });
    });
    $(document).on('change', '.mihanticket .uploading_file', function(e){
        let el = $(this),
            progressBar = el.closest('.file_field').find('.progress_bar'),
            fileName = e.target.files.length > 0 ? e.target.files[0].name : ''

        progressBar.addClass('show')
        el.closest('.file_field').find('label').text(fileName)

        setTimeout(() => {
            progressBar.fadeOut('fast', function(){
                progressBar.removeClass('show')
                setTimeout(() => {
                    progressBar.show()
                }, 200);
            })
        }, 1000);
    })
});
