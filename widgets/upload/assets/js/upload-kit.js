
$(document).ready(function(){
    $('body').on('click','#rabnitAttachmrntUpdateBtn', function () {
        var loader = $(this).find('.loader');
        loader.show()
        $('#RabintEditAttachmentInfo').find('.alert-success').hide();
        $('#RabintEditAttachmentInfo').find('.alert-danger').hide();

        let altValue = $('#RabintEditAttachmentInfo').find('#update-attachment-alt').val();
        let descriptionValue = $('#RabintEditAttachmentInfo').find('#update-attachment-description').val();
        var url = $('#RabintEditAttachmentInfo').find('form').attr('action'); // Get the URL from the form's action attribute


        formData = {
            Attachment:{
                alt: altValue,
                description: descriptionValue
            }
        }

        // Perform an AJAX request to send form data to the obtained URL
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                loader.hide();
                // Show success alert and hide error alert
                $('#RabintEditAttachmentInfo').find('.alert-success').show();
                $('#RabintEditAttachmentInfo').find('.alert-danger').hide();

                // Handle the success response from your server
                console.log('Form data sent successfully:', response);
                //$('#RabintEditAttachmentInfo').find('').modal('hide');
            },
            error: function (xhr, status, error) {
                loader.hide();
                try {
                    // Attempt to parse the JSON response
                    var jsonResponse = JSON.parse(xhr.responseText);
                    $('#RabintEditAttachmentInfo').find('.alert-danger').html(jsonResponse.message)
                } catch (e) {
                }
                // Show error alert and hide success alert
                $('#RabintEditAttachmentInfo').find('.alert-success').hide();


                $('#RabintEditAttachmentInfo').find('.alert-danger').show();

                // Handle any errors that occur during the AJAX request
                //console.error('Error sending form data:', error);
            }
        });
    });
});

(function ($) {
    jQuery.fn.yiiUploadKit = function (options) {
        var $input = this;
        var $container = $input.parent('div');
        var $files = $('<ul>', {"class": "files"}).insertBefore($input);
        var $emptyInput = $container.find('.empty-value');

        var methods = {
            init: function () {
                if (options.multiple) {
                    $input.attr('multiple', true);
                    $input.attr('name', $input.attr('name') + '[]');
                }
                $container.addClass('upload-kit');
                if (options.sortable) {
                    $files.sortable({
                        placeholder: "upload-kit-item sortable-placeholder",
                        tolerance: "pointer",
                        forcePlaceholderSize: true,
                        update: function () {
                            methods.updateOrder()
                        }
                    })
                }
                $input.wrapAll($('<li class="upload-kit-input"></div>'))
                        .after($('<span class="fas fa-plus add"></span>'))
                        .after($('<span class="fas fa-arrow-circle-down drag"></span>'))
                        .after($('<span/>', {"data-toggle": "popover", "class": "fas fa-exclamation-triangle error-popover"}))
                        .after(
                                '<div class="progress">' +
                                '<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>' +
                                '</li>'
                                );
                $files.on('click', '.upload-kit-item .remove', methods.removeItem);
                $files.on('click', '.upload-kit-item .update', methods.updateItem);
                $files.on('click', '.upload-kit-item .download', methods.downloadItem);
                methods.checkInputVisibility();
                methods.fileuploadInit();
                methods.dragInit();
                if(!options.editable){
                    methods.disableFileUploader();
                }
                if (options.acceptFileTypes && !(options.acceptFileTypes instanceof RegExp)) {
                    options.acceptFileTypes = new RegExp(eval(options.acceptFileTypes))
                }


                var modalHtml = `
                    <div class="modal fade" id="RabintEditAttachmentInfo" tabindex="-1" role="dialog" aria-labelledby="attachmentModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="attachmentModalLabel">اطلاعات فایل</h5>
                                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="image-info">
                                        <img src="" class="img-thumbnail"  />
                                    </div>
                                    <form method="post" action="" >
                                        <input type="hidden" class="attach_id" name="Attachment[id]" value="">
                                        <div class="form-group">
                                            <label for="update-attachment-alt">شرح:</label>
                                            <input type="text" class="form-control" id="update-attachment-alt" name="Attachment[alt]">
                                            <p class="hint-block">
                                                <small>
                                                    جهت بهینه بودن تصویر برای موتور های جستجو این فیلد را تکمیل نمایید 
                                                </small>
                                            </p>
                                        </div>
                                        <div class="form-group">
                                            <label for="update-attachment-description">توضیحات:</label>
                                            <textarea class="form-control" id="update-attachment-description" name="Attachment[description]"></textarea>
                                            <p class="hint-block">
                                                <small>
                                                    توضیحات به کاربر نمایش داده می شود.  
                                                </small>
                                            </p>
                                        </div>
                                        <div class="form-group text-center">
                                            
                                        </div>
                                    </form>
                                    <!-- Success and Error Alerts -->
                                    <div class="alert alert-success" style="display: none;">
                                        اطلاعات فایل با موفقیت ذخیره شد
                                    </div>
                                    <div class="alert alert-danger" style="display: none;">
                                        متاسفانه هنگام ذخیره اطلاعات خطایی رخ داده است.
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">بستن</button>
                                    <button type="button" id="rabnitAttachmrntUpdateBtn" class="btn btn-primary ">
                                            <i class="loader fa fa-sync-alt fa-spin" style="display:none"></i> 
                                                ذخیره
                                            </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;


                if($('body').find('#RabintEditAttachmentInfo').length ==0){
                    $('body').append(modalHtml);
                }

            },
            fileuploadInit: function () {
                var $fileupload = $input.fileupload({
                    name: options.name || 'file',
                    url: options.url,
                    dropZone: $input.parents('.upload-kit-input'),
                    dataType: 'json',
                    singleFileUploads: false,
                    multiple: options.multiple,
                    maxNumberOfFiles: options.maxNumberOfFiles,
                    maxFileSize: options.maxFileSize, // 5 MB
                    acceptFileTypes: options.acceptFileTypes,
                    minFileSize: options.minFileSize,
                    messages: options.messages,
                    process: true,
                    getNumberOfFiles: methods.getNumberOfFiles,
                    editable:options.editable,
                    start: function (e, data) {
                        $container.find('.upload-kit-input')
                                .removeClass('error')
                                .addClass('in-progress');
                        $input.trigger('start');
                        if (options.start !== undefined)
                            options.start(e, data);
                    },
                    processfail: function (e, data) {
                        if (data.files.error) {
                            methods.showError(data.files[0].error);
                        }
                    },
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $container.find('.progress-bar').attr('aria-valuenow', progress).css(
                                'width',
                                progress + '%'
                                ).text(progress + '%');
                    },
                    done: function (e, data) {
                        $.each(data.result.files, function (index, file) {
                            if (!file.error) {
                                var item = methods.createItem(file);
                                item.appendTo($files);
                            } else {
                                methods.showError(file.errors)
                            }

                        });
                        methods.handleEmptyValue();
                        methods.checkInputVisibility();
                        $input.trigger('done');
                        if (options.done !== undefined)
                            options.done(e, data);
                    },
                    fail: function (e, data) {
                        methods.showError(data.errorThrown);
                        if (options.fail !== undefined)
                            options.fail(e, data);
                    },
                    always: function (e, data) {
                        $container.find('.upload-kit-input').removeClass('in-progress');
                        $input.trigger('always');
                        if (options.always !== undefined)
                            options.always(e, data);
                    }
                });
                if (options.files) {
                    options.files.sort(function (a, b) {
                        return parseInt(a.order) - parseInt(b.order);
                    });
                    $fileupload.fileupload('option', 'done').call($fileupload, $.Event('done'), {result: {files: options.files}});
                    methods.handleEmptyValue();
                    methods.checkInputVisibility();
                }
            },
            dragInit: function () {
                $(document).on('dragover', function ()
                {
                    $('.upload-kit-input').addClass('drag-highlight');
                });
                $(document).on('dragleave drop', function ()
                {
                    $('.upload-kit-input').removeClass('drag-highlight');
                });
            },
            showError: function (error) {
                if ($.fn.popover) {
                    $container.find('.error-popover').attr('data-content', error).popover({html: true, trigger: "hover"});
                }
                $container.find('.upload-kit-input').addClass('error');
            },
            removeItem: function (e) {
                var $this = $(this);
                var url = $this.data('url');
                if (url) {
                    $.ajax({
                        url: url,
                        type: 'DELETE'
                    })
                }
                $this.parents('.upload-kit-item').remove();
                if (options.remove !== undefined) {
                    options.remove(e, $this.data);
                }
                methods.handleEmptyValue();
                methods.checkInputVisibility();
            },

            downloadItem: function (e) {
                var $this = $(this);
                var url = $this.data('url');
                window.open(url, '_blank');
                
            },
            createItem: function (file) {

                var name = options.name;
                var index = methods.getNumberOfFiles();
                if (options.multiple) {
                    name += '[' + index + ']';
                }
                var item = $('<li>', {"class": "upload-kit-item done"});
                switch (options.returnType) {
                    case 'id':
                        item.append($('<input/>', {"name": name, "value": file.attachment_id, "type": "hidden"}))
                        break;
                    case 'path':
                    case 'dir':
                        item.append($('<input/>', {"name": name, "value": file.path, "type": "hidden"}))
                        break;
                    case 'url':
                        item.append($('<input/>', {"name": name, "value": file.base_url + '/' + file.path, "type": "hidden"}))
                        break;
                    case 'full':
                        item.append($('<input/>', {"name": name + '[attachment_id]', "value": file.attachment_id, "type": "hidden"}))
                                .append($('<input/>', {"name": name + '[path]', "value": file.path, "type": "hidden"}))
                                .append($('<input/>', {"name": name + '[name]', "value": file.name, "type": "hidden"}))
                                .append($('<input/>', {"name": name + '[size]', "value": file.size, "type": "hidden"}))
                                .append($('<input/>', {"name": name + '[type]', "value": file.type, "type": "hidden"}))
                                .append($('<input/>', {"name": name + '[order]', "value": file.order, "type": "hidden", "data-role": "order"}))
                                .append($('<input/>', {"name": name + '[base_url]', "value": file.base_url, "type": "hidden"}));
                    default:
                }
                item.append($('<span/>', {"class": "name", "title": file.name}));
                if(options.editable){
                    item.append($('<span/>', {"class": "fas fa-times-circle remove", "data-url": file.delete_url}));
                    item.append($('<span/>', {
                        "class": "fas fa-info-circle update",
                        "data-url": file.update_url,
                        "data-attachment_id": file.attachment_id,
                        "data-path": file.path,
                        "data-name": file.name,
                        "data-size": file.size,
                        "data-type": file.type,
                        "data-order": file.order,
                        "data-base_url": file.base_url
                    }));
                }

                item.append($('<span/>', {"class": "fas fa-arrow-circle-down download", "data-url": '/attachment/default/download?id='+file.attachment_id}));
                if (!file.type || file.type.search(/image\/.*/g) !== -1) {
                    item.removeClass('not-image').addClass('image');
                    item.prepend($('<img/>', {src: file.base_url + '/' + file.path}));
                    item.find('span.type').text('');
                } else {
                    item.removeClass('image').addClass('not-image');
                    item.css('backgroundImage', '');
                    item.find('span.name').text(file.name);
                }
                return item;
            },

            updateItem: function (e) {

                var $this = $(this);
                var url = $this.data('url');
                $('#RabintEditAttachmentInfo').find('form').attr('action',$this.data('url'));
                imgurl =  $this.data('base_url')+"/"+$this.data('path');


                $('#RabintEditAttachmentInfo').find('.image-info img').attr('src',imgurl);
                $('#RabintEditAttachmentInfo').find('.attach_id').attr('value',$this.data('attachment_id'));
                $('#RabintEditAttachmentInfo').find('.alert-success').hide();
                $('#RabintEditAttachmentInfo').find('.alert-danger').hide();
                $('#rabnitAttachmrntUpdateBtn').find('.loader').hide();

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        $('#RabintEditAttachmentInfo').find('#update-attachment-alt').val(response.alt);
                        $('#RabintEditAttachmentInfo').find('#update-attachment-description').val(response.description);
                    },
                });
                // "data-attachment_id": file.attachment_id,
                // "data-path": file.path,
                // "data-name": file.name,
                // "data-size": file.size,
                // "data-type": file.type,
                // "data-order": file.order,
                // "data-base_url": file.base_url

                $('#RabintEditAttachmentInfo').modal('show');
            },

            checkInputVisibility: function () {
                var inputContainer = $container.find('.upload-kit-input');
                if (options.maxNumberOfFiles && (methods.getNumberOfFiles() >= options.maxNumberOfFiles)) {
                    inputContainer.hide();
                } else {
                    inputContainer.show();
                }
            },
            disableFileUploader: function () {
                var inputContainer = $container.find('.upload-kit-input');
                    inputContainer.hide();
            },
            handleEmptyValue: function () {
                if (methods.getNumberOfFiles() > 0) {
                    $emptyInput.val(methods.getNumberOfFiles())
                } else {
                    $emptyInput.removeAttr('value');
                }
            },
            getNumberOfFiles: function () {
                return $container.find('.files .upload-kit-item').length;
            },
            updateOrder: function () {
                $files.find('.upload-kit-item').each(function (index, item) {
                    $(item).find('input[data-role=order]').val(index);
                })
            }
        };

        methods.init.apply(this);
        return this;
    };

})(jQuery);
