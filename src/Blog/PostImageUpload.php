<?php

namespace App\Blog;

use Framework\Session\FlashService;
use Framework\Upload;

class PostImageUpload extends Upload
{
    protected string $path = '/public/style/upload';

    protected array $formats = [
        'main'  =>  [800, 460],
        'left'  =>  [534, 468],
        'great_middle'  =>  [533, 261],
        'thumb_middle'  =>  [800, 598],
        'primary'   =>  [1024, 550],
        'maternelle'    =>  [690, 1024],
        'thumb' =>  [800, 800]
    ];
}
