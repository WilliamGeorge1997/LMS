<?php

namespace Modules\Common\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

trait UploaderTrait
{
    private string $disk = 'public';
    private string $uploadsFolder = 'uploads';

    private function imageManager(): ImageManager
    {
        return ImageManager::usingDriver(new Driver());
    }

    private function buildPath(string $folder, string $fileName, ?string $tenantId = null): string
    {
        $tenantPath = $tenantId ? $tenantId . '/' : 'central/';
        return $this->uploadsFolder . '/' . $tenantPath . $folder . '/' . $fileName;
    }

    public function uploadImage(UploadedFile $file, string $folder, ?int $width = null, ?int $height = null, int $quality = 80, ?string $tenantId = null): string
    {
        $fileName = $this->generateFileName($file);
        $image = $this->imageManager()->decode($file);

        if ($width || $height) {
            $image->scale(width: $width, height: $height);
        }

        $encoded = $image->encodeUsingFileExtension($file->getClientOriginalExtension(), quality: $quality)->toString();

        Storage::disk($this->disk)->put($this->buildPath($folder, $fileName, $tenantId), $encoded);

        return $fileName;
    }

    /**
     * @param UploadedFile[] $files
     */
    public function uploadMultipleImages(array $files, string $folder, ?int $width = null, ?int $height = null, int $quality = 80, ?string $tenantId = null): array
    {
        $fileNames = [];

        foreach ($files as $file) {
            $fileNames[] = $this->uploadImage($file, $folder, $width, $height, $quality, $tenantId);
        }

        return $fileNames;
    }

    public function uploadFile(UploadedFile $file, string $folder, ?string $tenantId = null): string
    {
        $fileName = $this->generateFileName($file);

        Storage::disk($this->disk)->putFileAs($this->buildPath($folder, '', $tenantId), $file, $fileName);

        return $fileName;
    }

    public function deleteFile(string $folder, string $fileName, ?string $tenantId = null): void
    {
        $path = $this->buildPath($folder, $fileName, $tenantId);

        if (Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
        }
    }

    public function getFileUrl(string $folder, string $fileName, ?string $tenantId = null): string
    {
        return Storage::disk($this->disk)->url($this->buildPath($folder, $fileName, $tenantId));
    }

    private function generateFileName(UploadedFile $file): string
    {
        return uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
    }
}
