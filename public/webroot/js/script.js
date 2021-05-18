const ck1 = document.querySelector('#content')
const ck_editor = document.querySelector('.cke_editable.cke_editable_themed.cke_contents_ltr.cke_show_borders')

console.log(ck_editor);

flatpickr('.datepicker', {
    enableTime: true,
    altInput: true,
    altFormat: 'J F Y, H:i',
    dateFormat: 'Y-m-d H:i:S',
    "locale": "fr"
})

if (ck1 !== null) {
    console.log(CKEDITOR.replace(ck1))
    CKEDITOR.replace(ck1, {
        filebrowserUploadUrl: '/ck_upload.php?CKEditorFuncNum = 1',
        filebrowserUoloadMethod: 'form',
    })
}