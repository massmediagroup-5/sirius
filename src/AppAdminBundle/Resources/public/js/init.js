(function ($) {
    $(document).ready(function () {
        $.fn.uploadFile = function (type) {
            var blockText = {
                'img': {'text': ['Drag Image File Here'], 'name': ['img'], 'id': ['imguploadform']}
            };
 
            this.prepend('<div class="drag_n_drop"></div>');
            var div = this.find(".drag_n_drop");
            div.prepend('<input type="file" class="upload-file" name="' + type + 'file" id="' + type + 'uploadform" data-type="'+ blockText[type].name +'">');
            div.prepend('<i class="fa fa-plus-circle"></i>');
            div.prepend('<p>' + blockText[type].text + '</p>');
            div.addClass('drag_n_drop--' + type + 'Path');
            //this.find('span>div>div.form-group').eq(0).hide();
            $('input', this).hide();
            $('a.btn.btn-success.sonata-ba-action', this).hide();
            $('a', div).hide();
 
            fileDropBlock(div, type);
        };

        var uri = $(location).attr('pathname').split('/');
        var model = uri[uri.indexOf('edit') - 1];
 
        var imgBlock = $('div[class="field-container"][id$="_productModelImages"]');
        imgBlock.uploadFile('img');
 
        $('input[type="file"]').on("change", function () {
            var $_this = $(this),
                type = $_this.data('type'),
                reader,
                file;
            file = this.files[0];
 
            if (window.FormData) {
                formdata = new FormData();
            }
 
            if (window.FileReader) {
                reader = new FileReader();
                reader.readAsDataURL(file);
            }
 
            if (formdata) {
                formdata.append("file", file);
                formdata.append("model", model);
            }
 
            if (!$.inArray(file.type, arrayType[type])) {
                $.ajax({
                    url: Routing.generate('upload_file', {}, true),
                    type: "POST",
                    data: formdata,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        var userData = jQuery.parseJSON(res);
                        $_this.parent().find('input[type="text"]').val(userData.filePath);
                        $('button[type="submit"]').addClass('reload');
                    }
                });
            } else {
                alert('Wrong type')
            }
        });
 
        //var div = imgBlock.find(".drag_n_drop");
        //div.click(function () {
            //fileLoadByDefault('imguploadform', 'img', this);
        //});
        $(function() {
          $( "#sortable" ).sortable({
            //placeholder: "ui-state-highlight"
            stop: function(event, ui) {
                var imageIds = [];
                $(this).find('.product_model_image_item').each(function(){
                    imageIds.push(this.id);
                });
                putSortingImages(imageIds);
            }
          });
          $( "#sortable" ).disableSelection();
        });

        $(function() {
            $('.reload').click(function(event) {
                //location.reload();
                console.log('reload');
                event.preventDefault();
            });
        });

    });
})(jQuery);
