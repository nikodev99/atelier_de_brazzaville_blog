const ck1 = document.querySelector('#content')

flatpickr('.datepicker', {
    enableTime: true,
    altInput: true,
    altFormat: 'J F Y, H:i',
    dateFormat: 'Y-m-d H:i:S',
    "locale": "fr"
})

if (ck1 !== null) {
    CKEDITOR.replace(ck1, {
        filebrowserUploadUrl: '/ck_upload.php?CKEditorFuncNum = 1',
        filebrowserUoloadMethod: 'form',
    })
}