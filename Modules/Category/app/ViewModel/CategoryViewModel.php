<?php

namespace Modules\Category\ViewModel;

use Modules\Admin\Services\AdminService;
use Modules\Publisher\Services\PublisherService;

class CategoryViewModel
{
    public function activeManagers()
    {
        return (new AdminService())->findActiveManagers();
    }

    public function active()
    {
        return (new PublisherService())->findActive();
    }
}