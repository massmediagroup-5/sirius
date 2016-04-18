function fileDropBlock(block, type, uploadedCallback) {
    var allowType = {
        'img': ['image/jpeg', 'image/png', 'image/gif']
    };

    var uri = $(location).attr('pathname').split('/');
    var model = uri[uri.indexOf('edit') - 1];

    block.filedrop({
        url: Routing.generate('upload_file', {}, true),
        paramname: 'file',
        data: {
            model: model,
        },
        fallbackid: 'upload_button',
        maxfiles: 10,
        maxfilesize: 2,

        error: function (err, file) {
            switch (err) {
                case 'BrowserNotSupported':
                    console.log('Old browser');
                    break;
                case 'FileTooLarge':
                    console.log('File Too Large');
                    break;
                case 'TooManyFiles':
                    console.log('Максимум 10 файлов может быть загружено');
                    break;
                case 'FileTypeNotAllowed':
                    console.log('Wrong file type');
                    break;
                default:
                    console.log('Some error');
            }

        },
        allowedfiletypes: allowType[type],
        dragOver: function () {
            block.addClass('active-drag-block');
        },
        dragLeave: function () {
            block.removeClass('active-drag-block');
        },

        uploadFinished: function (i, file, response) {
            block.removeClass('active-drag-block');
            $('button[type="submit"]').addClass('reload');
            uploadedCallback(response);
        }
    })
}

/**
 * putSortingImages
 *
 * @param mixed $
 */
function putSortingImages(imageIds){

    var data = {
        'imageIds': JSON.stringify(imageIds),
    };
    $.ajax({
        url: Routing.generate('img_sorting', {}, true),
        type: "POST",
        data: data,
        dataType: 'json',
        success: function (res) {
        }
    });

}
