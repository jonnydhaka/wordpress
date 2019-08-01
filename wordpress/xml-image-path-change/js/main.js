/*
 * jQuery File Upload Plugin JS Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

/* global $, window */

$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: 'server/php/'
    });
    $(document).on('click','.get_path',function(e){
        e.preventDefault();
        var radio=$("input.themenameredio[type='radio']:checked");
        var newval=$(radio).data('new')
        var oldval=$(radio).data('old')
        $(this).next().next('#img_new_path').val(newval)
        $(this).next().show();
        $(this).next().next('#img_new_path').show();
        $(this).hide();
    })
    $(document).on('change','.themenameredio',function(e){
        $('#img_new_path').val('')
        $('.file_download').hide();
        $('#img_new_path').hide()
        $('.get_path').show()
    })
    $(document).on('click','.file_download',function(e){
        e.preventDefault();
        var radio=$("input.themenameredio[type='radio']:checked");
        var oldval=$(radio).data('old')
        var newval=$(this).next('#img_new_path').val();
        $.ajax({
            type: 'post',
            url: 'server/php/download.php',
            data:{'old':oldval,'new':newval,'file':$(this).data('file')},
            success: function(data) {
                if(data.indexOf('http')!=-1){
                    window.location.href=data;
                }else{
                    window.location.href=location.protocol+'//'+data
                }
            }
        });
    })

    

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    if (window.location.hostname === 'blueimp.github.io') {
        // Demo settings:
        $('#fileupload').fileupload('option', {
            url: '//jquery-file-upload.appspot.com/',
            // Enable image resizing, except for Android and Opera,
            // which actually support image resizing, but fail to
            // send Blob objects via XHR requests:
            disableImageResize: /Android(?!.*Chrome)|Opera/
                .test(window.navigator.userAgent),
            maxFileSize: 999000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
        });
        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                url: '//jquery-file-upload.appspot.com/',
                type: 'HEAD'
            }).fail(function () {
                $('<div class="alert alert-danger"/>')
                    .text('Upload server currently unavailable - ' +
                            new Date())
                    .appendTo('#fileupload');
            });
        }
    } else {
        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});
        });
    }

});
