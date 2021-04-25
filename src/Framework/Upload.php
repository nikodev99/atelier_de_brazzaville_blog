<?php

namespace Framework;

use Psr\Http\Message\UploadedFileInterface;

class Upload
{
    protected string $path;

    protected array $formats;

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }

    public function upload(UploadedFileInterface $uploadedFile, ?string $oldFile = null): string
    {
        $this->delete($oldFile);
        $targetFile = $this->addSuffix($this->getPath() . DIRECTORY_SEPARATOR . $uploadedFile->getClientFilename());
        $dirname = pathinfo($targetFile, PATHINFO_DIRNAME);
        if (!file_exists($dirname)) {
            mkdir($dirname, 777, true);
        }
        $uploadedFile->moveTo($targetFile);
        return pathinfo($targetFile)['basename'];
    }

    protected function getPath(): string
    {
        return dirname(__DIR__, 2) . $this->path;
    }

    private function addSuffix(string $filePath): string
    {
        if (file_exists($filePath)) {
            $info = pathinfo($filePath);
            $filePath = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '-copie.' . $info['extension'];
            return $this->addSuffix($filePath);
        }
        return $filePath;
    }

    private function delete(?string $oldFile = null): void
    {
        if (!is_null($oldFile)) {
            $oldFile = $this->getPath() . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
    }
}
