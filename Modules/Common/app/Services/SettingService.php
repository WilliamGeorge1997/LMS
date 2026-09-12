<?php

namespace Modules\Common\Services;

use Illuminate\Http\UploadedFile;
use Modules\Common\DTOs\SettingDto;
use Modules\Common\Models\Setting;
use Modules\Common\Traits\UploaderTrait;

class SettingService
{
    use UploaderTrait;

    private string $uploadFolder = 'settings/app';

    /**
     * Get the setting for the given tenant ID.
     */
    public function findByTenantId(?string $tenantId): ?Setting
    {
        if (! $tenantId) {
            return null;
        }

        return Setting::where('tenant_id', $tenantId)->first();
    }

    /**
     * Get the setting using the implicitly initialized tenant context.
     */
    public function getSetting(): ?Setting
    {
        return Setting::first();
    }

    /**
     * Update or create the setting for the given tenant ID.
     */
    public function updateOrCreate(string $tenantId, SettingDto $dto): Setting
    {
        $setting = $this->findByTenantId($tenantId);
        $data = $dto->toArray();

        if ($dto->app_path instanceof UploadedFile) {
            if ($setting && $setting->getRawOriginal('app_path')) {
                $this->deleteFile($this->uploadFolder, $setting->getRawOriginal('app_path'), tenantId: $tenantId);
            }
            $data['app_path'] = $this->uploadFile($dto->app_path, $this->uploadFolder, tenantId: $tenantId);
        } else {
            unset($data['app_path']);
        }

        return Setting::updateOrCreate(
            ['tenant_id' => $tenantId],
            $data
        );
    }
}
