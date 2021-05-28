<?php

namespace Framework;

use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;

class Upload
{
    protected string $path;

    protected array $formats = [];

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }

    public function upload(UploadedFileInterface $uploadedFile, ?string $oldFile = null, string $fileName = null): string
    {
        $this->delete($oldFile);
        $targetPath = $this->addSuffix($this->getPath() . DIRECTORY_SEPARATOR . ($fileName ?: $uploadedFile->getClientFilename()), 'copie');
        $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
        if (!file_exists($dirname)) {
            mkdir($dirname, 777, true);
        }
        $uploadedFile->moveTo($targetPath);
        $this->generateFormat($targetPath);
        return pathinfo($targetPath)['basename'];
    }

    public function delete(?string $oldFile = null): void
    {
        if (!is_null($oldFile)) {
            $oldFile = $this->getPath() . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            foreach ($this->formats as $format => $_s) {
                $oldFileWithSuffix = $this->getPathWithSuffix($oldFile, $format);
                if (file_exists($oldFileWithSuffix)) {
                    unlink($oldFileWithSuffix);
                }
            }
        }
    }

    protected function getPath(): string
    {
        return dirname(__DIR__, 2) . $this->path;
    }

    private function generateFormat(string $targetPath)
    {
        foreach ($this->formats as $format => $size) {
            $destination = $this->addSuffix($targetPath, $format);
            $manager = new ImageManager(['driver' => 'gd']);
            [$width, $height] = $size;
            $manager->make($targetPath)->fit($width, $height)->save($destination);
        }
    }

    private function addSuffix(string $filePath, string $suffix): string
    {
        if (file_exists($filePath)) {
            $filePath = $this->getPathWithSuffix($filePath, $suffix);
            return $this->addSuffix($filePath, $suffix);
        }
        return $filePath;
    }

    private function getPathWithSuffix(string $path, string $suffix): string
    {
        $info = pathinfo($path);
        return $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '-' . $suffix . '.' . $info['extension'];
    }
}
