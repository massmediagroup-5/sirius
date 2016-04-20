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

            this.newImage = [];
            this.imagesQueue = [];

            this.initContent();
        }

        UploadFile.prototype.initContent = function () {
            var newImage, processNextImage;
            this.$content = $('div[class="field-container"][id$="_' + this.blockName + '"]');

            if (this.$content && this.$content.data('isInit')) return;

            this.$content.data('isInit', true);

            console.log('imagesQueue', this.newImage);

            this.$reloadButton = this.$content.find('a.btn.btn-success.sonata-ba-action');
            this.$reloadButton.hide();

            if (newImage = this.newImage.shift()) {
                var $newImageBlock = $('<span class="help-block sonata-ba-field-help"></span>');

                $newImageBlock.append('<img class="admin-preview" src="' + newImage.filePath + '" />');
                this.$hoder.find('.product_model_image_item:last-child [id$="_file"] .sonata-ba-field')
                    .append($newImageBlock);
                this.$hoder.find('.product_model_image_item:last-child input[id$="_link"]')
                    .val(newImage.filePath);
                this.$hoder.find('.product_model_image_item:last-child input[id$="_priority"]')
                    .val(newImage.priority);
                this.$hoder.find('.product_model_image_item:last-child select[id$="_products"]')
                    .val(this.modelId).trigger('change');

                if(processNextImage = this.imagesQueue.shift()) {
                    processNextImage();
                }
            }

            this.$content.prepend('<div class="drag_n_drop"></div>');
            var div = this.$content.find(".drag_n_drop");
            div.prepend('<input type="file" class="upload-file" name="' + this.type + 'file" id="' + this.type + 'uploadform" data-type="' + this.blockText[this.type].name + '">');
            div.prepend('<i class="fa fa-plus-circle"></i>');
            div.prepend('<p>' + this.blockText[this.type].text + '</p>');
            div.addClass('drag_n_drop--' + this.type + 'Path');
            //this.find('span>div>div.form-group').eq(0).hide();
            this.$content.find('.form-group[id$="_products"]').hide();
            this.$content.find('.form-group[id$="_link"]').hide();
            this.$content.find('.form-group[id$="_priority"]').hide();
            this.$content.find('input').hide();
            div.find('a');

            // Init sorting
            // todo Sorting not save
            this.$hoder.find("#sortable").sortable({
                //placeholder: "ui-state-highlight"
                stop: function (event, ui) {
                    $(this).find('.product_model_image_item').each(function () {
                        var $this = $(this);
                        $this.find('input[id$=_priority]').val($this.index() + 1);
                    });
                }
            });
            this.$hoder.find("#sortable").disableSelection();

            fileDropBlock(div, this.type, this.uploaded.bind(this));
        };

        UploadFile.prototype.uploaded = function (response) {
            this.newImage.push(response);

            // wait when previous image processed
            this.imagesQueue.push(function () {
                this.$reloadButton.click()
            }.bind(this));

            if(this.newImage.length == 1) {
                this.imagesQueue.shift()();
            }
        };

        return UploadFile;
    })();

    $(document).ready(function () {

        new UploadFile('images');

        $(function () {
            $('.reload').click(function (event) {
                //location.reload();
                console.log('reload');
                event.preventDefault();
            });
        });

    });
})(jQuery);
