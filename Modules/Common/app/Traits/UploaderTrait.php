<?php

namespace Modules\Common\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

trait UploaderTrait
{
    private string $uploadFolder = 'uploads';

    private function imageManager(): ImageManager
    {
        return ImageManager::usingDriver(new Driver());
    }

    private function buildPath(string $folder, string $fileName): string
    {
        return $this->uploadFolder . '/' . $folder . '/' . $fileName;
    }

    public function uploadImage($file, string $folder, string $disk = 'public', ?int $width = null, ?int $height = null, int $quality = 80): string
    {
        $fileName = $this->generateFileName($file);
        $image = $this->imageManager()->decode($file);

        if ($width || $height) {
            $image->scale(width: $width, height: $height);
        }

        $encoded = $image->encodeUsingFileExtension($file->getClientOriginalExtension(), quality: $quality);

        Storage::disk($disk)->put($this->buildPath($folder, $fileName), (string) $encoded);

        return $fileName;
    }

    public function uploadMultipleImages(array $files, string $folder, string $disk = 'public', ?int $width = null, ?int $height = null, int $quality = 80): array
    {
        $fileNames = [];

        foreach ($files as $file) {
            $fileNames[] = $this->uploadImage($file, $folder, $disk, $width, $height, $quality);
        }

        return $fileNames;
    }

    public function uploadFile($file, string $folder, string $disk = 'public'): string
    {
        $fileName = $this->generateFileName($file);

        Storage::disk($disk)->putFileAs($this->buildPath($folder, ''), $file, $fileName);

        return $fileName;
    }

    public function deleteFile(string $folder, string $fileName, string $disk = 'public'): void
    {
        $path = $this->buildPath($folder, $fileName);

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    public function getFileUrl(string $folder, string $fileName, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($this->buildPath($folder, $fileName));
    }

    private function generateFileName($file): string
    {
        return uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    }
}
