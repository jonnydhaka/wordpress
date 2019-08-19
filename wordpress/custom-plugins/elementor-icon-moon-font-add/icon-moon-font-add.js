(function($)
{
    
    "use strict";
    $('body').on('click','.custom-icon-uploder-btn',function(){
        var zip_frame = wp.media({
                library: { type: 'application/zip' },
                button:  { text: ajax_custom_icon_add.custom_icon_upload_zip_upload_text },
            });
        zip_frame.on( 'select update insert', function(event, selection, options){ 
            var state = zip_frame.state(), 
            selection = state.get('selection').first().toJSON();
            $.ajax({
                type: "POST",
                url: ajax_custom_icon_add.ajax_url,
                data:
                {
                    action: 'custom_icon_upload_add_font',
                    security: ajax_custom_icon_add.custom_icon_upload_add_font,
                    values: selection,
                },
                beforeSend: function(){},
                error: function(){},
                success: function(response)
                {
                    if(response.trim()=='success'){
                        location.reload();
                    }else{
                        $('.custom-icon-uploder-modal-content-child').html(response)
                        tb_show(ajax_custom_icon_add.custom_icon_upload_thickbox_title, "#TB_inline?height=150&amp;width=300&amp;inlineId=custom-icon-uploder-modal-content");
                    }
                        
                        
                }
            });
        });
        zip_frame.open();
    })
    $('body').on('click','.remove_custom_icon_group',function(){
        var removefont = confirm(ajax_custom_icon_add.custom_icon_upload_remove_confirm_text);
        if (removefont == true) {
            $.ajax({
                type: "POST",
                url: ajax_custom_icon_add.ajax_url,
                data:
                {
                    action: 'custom_icon_upload_delete_font',
                    security: ajax_custom_icon_add.custom_icon_upload_add_font,
                    values: $(this).data('target'),
                },
                beforeSend: function(){},
                error: function(){console.log(1)},
                success: function(response)
                {
                    if(response.trim()=='success'){
                        location.reload();
                    }
                }
            });
        }
    }) 
   
    $(document).on('click',".border.border-secondary.rounded", function(){
        $('.icon-copy-class-div').replaceWith('');
        $(this).closest('.custom-icon-group').append('<div class="icon-copy-class-div"><input type="text" class="icon-copy-class" value="'+$(this).attr('title')+'"><span>'+ajax_custom_icon_add.copied_text+'</span></div>')
        $( 'input.icon-copy-class' ).select();
        var copytext=document.execCommand('copy');
        setTimeout(function(){ $('.icon-copy-class-div').replaceWith('');},6000)
    })
})(jQuery);