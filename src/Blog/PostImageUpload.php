<?php

namespace App\Blog;

use Framework\Session\FlashService;
use Framework\Upload;

class PostImageUpload extends Upload
{
    protected string $path = '/public/style/upload';
}
