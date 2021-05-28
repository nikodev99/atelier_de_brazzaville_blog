<?php

namespace App\Shop\Upload;

use Framework\Upload;

class UploadProductImage extends Upload
{
    protected string $path = "/public/style/upload/products";

    protected array $formats = [
        'main'  =>  [800, 460],
        'left'  =>  [534, 468],
        'thumb' =>  [320, 180]
    ];
}
