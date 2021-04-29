<?php

namespace Framework\upload;

use Framework\Session\FlashService;
use Framework\Session\PHPSession;

class ImgUploader
{

    /** @var array|resource */
    private $file_to_upload;

    private $file;
    private $fileName;
    private $fileTmp;
    private $fileSize;
    private $fileError;
    private $fileExt;

    public function __construct(?array $file_to_upload = [])
    {
        $this->file_to_upload = $file_to_upload;
        if (isset($this->file_to_upload)) {
            $this->file = $this->file_to_upload;
            if (!empty($this->file)) {
                $this->fileName = $this->file['name'];
                $this->fileTmp = $this->file['tmp_name'];
                $this->fileSize = $this->file['size'];
                $this->fileError = $this->file['error'];
                $this->fileExt = pathinfo($this->fileName, PATHINFO_EXTENSION);
            }
        }
    }

    public function newFilename(): ?string
    {
        $resizeFileName = 'bg-img' . uniqid('', true);
        $resizeFileName = str_replace('.', strtolower(self::generateIds()), $resizeFileName);
        if (!in_array(strtolower($this->fileExt), ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
            return null;
        }
        return $resizeFileName . '.' . $this->fileExt;
    }

    public function upload(?string $resizeName, $width = 200, int $height = 200)
    {
        if ($this->fileExt == 'svg') {
            $this->uploadSVG($resizeName);
        } else {
            $this->uploadIMG($resizeName, $width, $height);
        }
    }

    private function uploadSVG(?string $fileName): void
    {
        $this->errors($fileName);
        $destination = $this->getRoute(basename($fileName));
        $this->uploadFile($this->fileTmp, $destination);
    }

    private function uploadIMG(?string $resizeName, $width = 200, int $height = 200): void
    {
        $this->errors($resizeName);

        $sourceProperties = getimagesize($this->fileTmp);
        $destination = $this->getRoute($resizeName);

        switch ($sourceProperties[2]) {
            case IMAGETYPE_JPEG:
                $resourceType = imagecreatefromjpeg($this->fileTmp);
                $image = $this->resizing($resourceType, $sourceProperties[0], $sourceProperties[1], $width, $height);
                imagejpeg($image, $destination);
                break;
            case IMAGETYPE_GIF:
                $resourceType = imagecreatefromgif($this->fileTmp);
                $image = $this->resizing($resourceType, $sourceProperties[0], $sourceProperties[1], $width, $height);
                imagegif($image, $destination);
                break;
            case IMAGETYPE_PNG:
                $resourceType = imagecreatefrompng($this->fileTmp);
                $image = $this->resizing($resourceType, $sourceProperties[0], $sourceProperties[1], $width, $height);
                imagepng($image, $destination);
                break;
        }

        $this->uploadFile('', $destination);
    }

    private function uploadFile(string $fileName, string $destination): void
    {
        move_uploaded_file($fileName, $destination);
    }

    private function errors(string $filename): void
    {
        $flash = new FlashService(new PHPSession());
        if ($this->fileSize > 5000000) {
            $flash->info('Votre image a une taille plus grande que la taille limite.');
        }
        if ($this->fileError !== 0) {
            $flash->error('Erreur rencontrer lors du téléchargement de votre image.');
        }
        if (is_null($filename)) {
            $flash->info("L'extension de votre fichier ne correspond pas. Assurez vous que l'extension soit .png, .jpg, .gif");
        }
    }

    private function resizing($resourceType, int $imageWidth, int $imageHeight, int $resizeWidth = 200, int $resizeHeight = 200)
    {
        $image = imagecreatetruecolor($resizeWidth, $resizeHeight);
        imagecopyresampled($image, $resourceType, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $imageWidth, $imageHeight);
        return $image;
    }

    private function getRoute(?string $file = null): string
    {
        $uploadPath = dirname(__DIR__, 3) . '/public/style/upload';
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath);
        }
        if (is_null($file)) {
            return $uploadPath;
        } else {
            return $uploadPath . '/' . $file;
        }
    }

    public static function generateIds(int $length = 3, bool $elevate = false): string
    {
        $digits = 'AZERTYUIOPQSDFGHJKLMWXCVBN';
        if ($elevate) {
            $digits .= '1234567890';
            $length = 5;
        }
        return substr(str_shuffle(str_repeat($digits, $length)), 0, $length) . time();
    }
}
