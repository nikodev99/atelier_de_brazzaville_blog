<?php

use Framework\upload\ImgUploader;

require "../vendor/autoload.php";

$img = new ImgUploader($_FILES['upload']);
$sizes = getimagesize($_FILES['upload']['tmp_name']);
$filename = $img->newFilename();
$img->upload($filename, $sizes[0], $sizes[1]);

$function_number = $_GET['CKEditorFuncNum'];
$url = '../../style/upload/' . $filename;
$message = 'Image télécharger avec succès';

echo
"<script type=\"text/javascript\">
    window.parent.CKEDITOR.tools.callFunction('${function_number}', '${url}', '${message}')
</script>";
