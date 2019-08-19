jQuery(document).ready(function ($) {
    'use strict';
    var targetbtn
    $('.repeater-default').repeater();

    $('.repeater-custom-show-hide').repeater({
        show: function () {
            $(this).slideDown();
        },
        hide: function (remove) {
            if (confirm('Are you sure you want to remove this item?')) {
                $(this).slideUp(remove);
            }
        }
    });

    // Set all variables to be used in scope
    var frame,
        metaBox = $('#downloadable_files'), // Your meta box id here
        addImgLink = metaBox.find('.upload_file_button');

    // ADD IMAGE LINK
    $(document).on('click', '.upload_file_button', function (event) {
        targetbtn = $(this);
        event.preventDefault();
        // If the media frame already exists, reopen it.
        if (frame) {
            frame.open();
            return;
        }
        // Create a new media frame
        frame = wp.media({
            title: 'Select or Upload Media Of Your Chosen Persuasion',
            button: {
                text: 'Use this media'
            },
            multiple: false ,// Set to true to allow multiple files to be selected
            library: {
                type: [ 'application/zip' ]
        },
        });
        frame.on( 'open , ready', function() {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: omb_admin_repeater_ajax_object.ajax_url,
                data: {
                    action: 'cvl_envato_add_uploaddir',
                },
                success: function (data) {},
                error: function (jqXHR, textStatus, errorThrown) {
    
                }
            });
        } );
        frame.on( 'close', function() {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: omb_admin_repeater_ajax_object.ajax_url,
                data: {
                    action: 'cvl_envato_remove_uploaddir',
                },
                success: function (data) {},
                error: function (jqXHR, textStatus, errorThrown) {
    
                }
            });
        } );
        // When an image is selected in the media frame...
        frame.on('select', function () {
            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();
            targetbtn.closest('.evl_file_url_choose').prev('.evl_url_name').find('.envato-product-url-id').val(attachment.url);
            targetbtn.closest('.evl_file_url_choose').prev().prev('.evl_file_name').find('.ev_file_hashes_input').val(attachment.id);
        });
        // Finally, open the modal on click
        frame.open();
    });

    $('#envato_licence_btn_load_product').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: omb_admin_repeater_ajax_object.ajax_url,
            data: {
                action: 'cvl_envato_load_product',
            },
            success: function (data) {
				alert('Complete');
				},
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    })

});