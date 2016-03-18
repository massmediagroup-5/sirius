(function ($) {
    /**
     * Upload control class
     */
    var UploadFile = (function () {
        function UploadFile(blockName, type) {
            this.type = type == null ? 'img' : type;
            this.blockName = blockName;
            this.blockText = {
                'img': {'text': ['Drag Image File Here'], 'name': ['img'], 'id': ['imguploadform']}
            };
            this.$hoder = $('div[class="form-group"][id$="_' + blockName + '"]');

            this.$hoder.on('sonata.add_element', this.initContent.bind(this));

            this.$reloadButton = false;

            this.newImage = false;

            this.initContent();
        }

        UploadFile.prototype.initContent = function () {
            if(this.$content && this.$content.data('isInit')) return;

            this.$content = $('div[class="field-container"][id$="_' + this.blockName + '"]');

            this.$content.data('isInit', true);

            if(this.newImage) {
                var $newImageBlock = $('<span class="help-block sonata-ba-field-help"></span>');
                $newImageBlock.append('<img class="admin-preview" src="'+this.newImage+'" />');
                this.$hoder.find('.product_model_image_item:last-child [id$="_file"] .sonata-ba-field')
                    .append($newImageBlock);
                this.$hoder.find('.product_model_image_item:last-child input[id$="_link"]')
                    .val(this.newImage);
                this.$hoder.find('.product_model_image_item:last-child select[id$="_productModels"]')
                    .val(this.modelId).trigger('change');
                this.newImage = false;
            }

            this.$content.prepend('<div class="drag_n_drop"></div>');
            var div = this.$content.find(".drag_n_drop");
            div.prepend('<input type="file" class="upload-file" name="' + this.type + 'file" id="' + this.type + 'uploadform" data-type="'+ this.blockText[this.type].name +'">');
            div.prepend('<i class="fa fa-plus-circle"></i>');
            div.prepend('<p>' + this.blockText[this.type].text + '</p>');
            div.addClass('drag_n_drop--' + this.type + 'Path');
            //this.find('span>div>div.form-group').eq(0).hide();
            this.$content.find('.form-group[id$="_productModels"]').hide();
            this.$content.find('.form-group[id$="_link"]').hide();
            this.$content.find('.form-group[id$="_priority"]').hide();
            this.$content.find('input').hide();
            this.$reloadButton = this.$content.find('a.btn.btn-success.sonata-ba-action');
            this.$reloadButton.hide();
            div.find('a');

            // Init sorting
            // todo Sorting not save
            this.$hoder.find( "#sortable" ).sortable({
                //placeholder: "ui-state-highlight"
                stop: function(event, ui) {
                    var imageIds = [];
                    $(this).find('.product_model_image_item').each(function(){
                        imageIds.push(this.id);
                    });
                    putSortingImages(imageIds);
                }
            });
            this.$hoder.find( "#sortable" ).disableSelection();

            fileDropBlock(div, this.type, this.uploaded.bind(this));
        };

        UploadFile.prototype.upload = function () {
            var uri = $(location).attr('pathname').split('/');
            var model = uri[uri.indexOf('edit') - 1];

            var $fileInput = this.$content.find('input[type="file"]'),
                type = $fileInput.data('type'),
                reader,
                file;
            file = $fileInput[0].files[0];

            var formdata = new FormData();

            if (window.FileReader) {
                reader = new FileReader();
                reader.readAsDataURL(file);
            }

            formdata.append("file", file);
            formdata.append("model", model);

            if (!$.inArray(file.type, arrayType[type])) {
                $.ajax({
                    url: Routing.generate('upload_file', {}, true),
                    type: "POST",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        var userData = jQuery.parseJSON(res);
                        $fileInput.parent().find('input[type="text"]').val(userData.filePath);
                        $('button[type="submit"]').addClass('reload');
                    }
                });
            } else {
                alert('Wrong type')
            }
        };

        UploadFile.prototype.uploaded = function (response) {
            console.log('uploaded');
            console.log(this.$reloadButton);
            this.newImage = response.filePath;
            this.$reloadButton.click();
        };

        return UploadFile;
    })();

    $(document).ready(function () {

        new UploadFile('productModelImages');

        $(function() {
            $('.reload').click(function(event) {
                //location.reload();
                console.log('reload');
                event.preventDefault();
            });
        });

    });
})(jQuery);
