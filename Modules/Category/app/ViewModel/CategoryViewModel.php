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

    public function activePublishers()
    {
        return (new PublisherService())->findActive();
    }
}